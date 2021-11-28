<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Console\OutputStyle;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Class UserWalletService
 *
 * @package App\Services
 * @author Bojte Szabolcs
 */
class UserWalletService
{
    /**
     * Generates a new token address
     *
     * @param $userPassword
     * @return PromiseInterface|Response
     */
    protected function createWallet($userPassword)
    {
        return Http::withHeaders(["Content-Type" => "application/json"])
            ->post(config('services.token.base_url') . '/get-wallet', ["password" => $userPassword]);
    }


    /**
     * Returns the decrypted user wallet private key
     *
     * @param UserWallet $userWallet
     * @param $userPassword
     * @return false|string
     */
    protected function decryptUserWalletPrivateKey(UserWallet $userWallet, $userPassword)
    {
        
        try {
            $response = Http::withHeaders(["Content-Type" => "application/json"])
                ->post(config('services.token.base_url') . '/decrypt-key', ["password" => $userPassword, "iv" => $userWallet->iv, "privateKey" => $userWallet->private_key]);

            if (!$response->successful()) {
                throw new Exception("User wallet id: {$userWallet->id}. Response status: {$response->status()}");
            }
            $response = $response->json();
            
            return $response['privateKey'];
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("User wallet decryption error. Error message: {$ex->getMessage()}");
            return null;
        }
    }

    /**
     * Returns the decrypted user wallet private key
     *
     * @param UserWallet $userWallet
     * @param $accessKeyId
     * @param $secretAccessKey
     * @return PromiseInterface|Response
     */
    protected function decryptUserPrivateKyByKMS(UserWallet $userWallet, $accessKeyId, $secretAccessKey)
    {
        return Http::withHeaders(["Content-Type" => "application/json"])
            ->post(
                config('services.token.base_url') . '/retrieve-kms-key',
                [
                    "accessKeyId" => $accessKeyId,
                    "secretAccessKey" => $secretAccessKey,
                    "encryptedData" => $userWallet->double_encrypted
                ]
            );
    }

    /**
     * Returns the encrypted private key
     *
     * @param $password
     * @param $privateKey
     * @return PromiseInterface|Response
     */
    protected function encryptPrivateKyWithPassword($password, $privateKey)
    {
        return Http::withHeaders(["Content-Type" => "application/json"])
            ->post(config('services.token.base_url') . '/encrypt-key', ["password" => $password, "privateKey" => $privateKey]);
    }

    /**
     * Sends tokens to user wallet address (returns the transaction tx)
     *
     * @param $senderPrivateKey
     * @param $toAddress
     * @param $amount
     * @param $gas
     * @return PromiseInterface|Response
     */
    protected function sendTokenToAddress($senderPrivateKey, $toAddress, $amount, $gasPrice, $gas)
    {
        return Http::withHeaders(["Content-Type" => "application/json"])
            ->post(
                config('services.token.base_url') . '/send-token-to-address',
                [
                    "sender_private_key" => $senderPrivateKey,
                    "to" => $toAddress,
                    "amount" => (string)$amount,
                    "gas_price" => (string)$gasPrice,
                    "gas" => (string)$gas
                ]
            );
    }

    /**
     * Sends bnb to an address
     *
     * @param $senderPrivateKey
     * @param $toAddress
     * @param $amount
     * @param $gas
     * @return PromiseInterface|Response
     */
    protected function sendBnbToAddress($senderPrivateKey, $toAddress, $amount, $gasPrice, $gas)
    {
        return Http::withHeaders(["Content-Type" => "application/json"])
            ->post(
                config('services.token.base_url') . '/send-bnb-to-address',
                [
                    "sender_private_key" => $senderPrivateKey,
                    "to" => $toAddress,
                    "amount" => (string)$amount,
                    "gas_price" => (string)$gasPrice,
                    "gas" => (string)$gas
                ]
            );
    }

    /**
     * @param Response $response
     * @param $output
     * @return mixed|null
     * @throws Exception
     */
    protected function checkResponse(Response $response, $output)
    {
        $response = $response->json();
        $errors = $response["error"];
        $transactionHash = $response['transactionHash'] ?? null;
        if ($errors || !$transactionHash) {
            if ($errors) {
                if (array_key_exists('sign', $errors)) {
                    $output->error("SIGN -> {$errors['sign']}");
                }
                if (array_key_exists('send', $errors)) {
                    $output->error("SEND -> {$errors['send']}");
                }
                if (array_key_exists('balance', $errors)) {
                    $output->error("BALANCE -> {$errors['balance']}");
                }
                if (array_key_exists('gas', $errors)) {
                    $output->error("GAS -> {$errors['gas']}");
                }
                if (array_key_exists('catch', $errors) || array_key_exists('general', $errors)) {
                    if (array_key_exists('catch', $errors)) {
                        $message = $errors['catch'];
                    } else {
                        $message = array_key_exists('general', $errors) ? $errors['general'] : '';
                    }
                    $output->error("GENERAL -> $message");
                }
            }

            if (!$transactionHash) {
                $output->error("SEND -> No transaction hash");
            }

            throw new Exception('Error from token sender microservice. Transaction canceled');
        }
        $output->success("Transaction hash: {$transactionHash}");
        return $transactionHash;
    }

    /**
     * Handles sending tokens to users
     *
     * @param OutputStyle $output
     * @param $senderPrivateKey
     * @param string $gas
     */
    public function sendTokensToUsers(OutputStyle $output, $senderPrivateKey, $gas)
    {
        $usersWithoutTokens = UserWallet::where([
            ['token_sent', false],
            ['locked', false],
            ['token_quantity', '>', 0],
        ])->get();

        try {
            $output->progressStart(count($usersWithoutTokens));
            foreach ($usersWithoutTokens as $userWallet) {
                $user = User::find($userWallet->user_id);
                if ((bool)$user->email_verified_at) {
                    $response = $this->sendTokenToAddress($senderPrivateKey, $userWallet->address, $userWallet->token_quantity, $gas);
                    if (!$response->successful()) {
                        $output->error($response->json());
                        throw new Exception("Sending user tokens error! User id: {$userWallet->user_id}. Response status: {$response->status()}");
                    }
                    $transactionHash = $this->checkResponse($response, $output);
                    $userWallet->transaction_hash = $transactionHash;
                    $userWallet->token_sent = true;
                }
                $userWallet->save();
                $output->progressAdvance();
            }
            $output->progressFinish();
        } catch (Exception $ex) {
            $output->error($ex->getMessage());
            Log::channel('user-wallet')->error("COMMAND - Send Token To Users Error : {$ex->getMessage()}");
        }
    }

    /**
     * Returns the decrypted user wallet private key
     *
     * @param User $user
     * @param $userPassword
     * @return false|string
     */
    public function getUserWalletPrivateKey(User $user, $userPassword)
    {
        $userWallet = UserWallet::where(['user_id' => $user->id])->first();
        return $this->decryptUserWalletPrivateKey($userWallet, $userPassword);
    }

    /**
     * @param User $user
     * @param $salt
     * @return mixed
     * @throws Exception
     */
    protected function createUserWallet(User $user, $salt)
    {
        $response = $this->createWallet($salt);
        if (!$response->successful()) {
            throw new Exception("Wallet creation response error! User id: {$user->id}. Response status: {$response->status()}");
        }
        $response = $response->json();

        return UserWallet::create([
            'user_id' => $user->id,
            'address' => $response['address'],
            'private_key' => $response['privateKey'],
            'double_encrypted' => $response['doubleEncrypted'],
            'iv' => $response['iv'],
            'token_quantity' => $user->l2l,
            'locked' => $user->locked
        ]);
    }

    /**
     * Handles user password change from command line
     *
     * @param OutputStyle $output
     * @param $userWallet
     * @param $newPassword
     * @param $accessKeyId
     * @param $secretAccessKey
     */
    public function handleUserPasswordChange(OutputStyle $output, $userWallet, $newPassword, $accessKeyId, $secretAccessKey)
    {
        try {
            $user = User::find($userWallet->user_id);
            $user->password = Hash::make($newPassword);
            $user->save();

            if (!$this->userHasSecretPhrases($userWallet)) {
                $this->reEncryptPrivateKey($userWallet, $newPassword, $accessKeyId, $secretAccessKey);
                $output->success('User password is changed with success!');
            } else {
                $output->warning('User password is changed with success! User has secret phrases!');
            }
        } catch (Exception $ex) {
            $errorMessage = "User password change error! {$ex->getMessage()}";
            $output->error($errorMessage);
            Log::channel('user-wallet')->error($errorMessage);
        }
    }

    /**
     * @param UserWallet $userWallet
     * @param string $password
     * @param string $accessKeyId
     * @param string $secretAccessKey
     * @throws Exception
     */
    public function reEncryptPrivateKey(UserWallet $userWallet, string $password, string $accessKeyId, string $secretAccessKey): void
    {
        $response = $this->decryptUserPrivateKyByKMS($userWallet, $accessKeyId, $secretAccessKey);
        if (!$response->successful()) {
            throw new Exception("Decrypt user private key by KMS! Response status: {$response->status()}");
        }
        $response = $response->json();

        if (!$response['privateKey']) {
            throw new Exception('Decrypted user private key by KMS is UNDEFINED!');
        }

        $encryptedResponse = $this->encryptPrivateKyWithPassword($password, $response['privateKey']);

        if (!$encryptedResponse->successful()) {
            throw new Exception("Encrypt user private key by KMS! Response status: {$response->status()}");
        }

        $encryptedResponse = $encryptedResponse->json();

        if (!$encryptedResponse['privateKey'] && !$encryptedResponse['iv']) {
            throw new Exception("Newly encrypted user private key by KMS is UNDEFINED! Response status: {$response->status()}");
        }

        $userWallet->private_key = $encryptedResponse['privateKey'];
        $userWallet->iv = $encryptedResponse['iv'];
        $userWallet->save();
    }

    /**
     * @param UserWallet $userWallet
     * @return bool
     */
    protected function userHasSecretPhrases(UserWallet $userWallet): bool
    {
        return (bool)$userWallet->phrases_created_at;
    }

    /**
     * @param User $user
     * @param $newPassword
     */
    protected function setUserPassword(User $user, $newPassword)
    {
        $user->password = Hash::make($newPassword);
        $user->save();
    }

    /**
     * Get user balance
     *
     * @param $wallet
     * @return void
     */
    public function updateWalletBalance($wallet)
    {
        try {
            $response = Http::withHeaders(["Content-Type" => "application/json"])
                ->post(
                    config('services.token.base_url') . '/get-balance',
                    [
                        "sender_address" => $wallet->address,
                    ]
                );

            if (!$response->successful()) {
                Log::channel('user-wallet')->error("Error getting balance: {$response->status()}");
                return;
            }

            $content = $response->json();

            $error = $content["error"];
            if ($error) {
                Log::channel('user-wallet')->error("Error getting balance: {$error}");
                return;
            }
            $balance2lc = $content['balance2lc'];
            $balanceBnb = $content['balanceBnb'];
            $balanceLocked2lc = $content['balanceLocked2lc'];
            if (!is_numeric($balance2lc) || !is_numeric($balanceBnb) || !is_numeric($balanceLocked2lc)) {
                Log::channel('user-wallet')->error('Error getting balance');
                return;
            }

            $wallet->balance_2lc = $balance2lc;
            $wallet->balance_bnb = $balanceBnb;
            $wallet->balance_locked_2lc = $balanceLocked2lc;
            $wallet->save();
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("User wallet get balance error. Error message: {$ex->getMessage()}");
        }
    }

    public function createUserWalletWithPhrases(User $user, $phrases)
    {
        try {
            $userWallet = $this->createUserWallet($user, $phrases);
            $userWallet->phrases_created_at = date('Y-m-d H:i:s');
            $userWallet->save();
            $user->secret_phrase_hashed = Hash::make($phrases);
            $user->save();
            return response()->json(true, 200);
        } catch (Exception $ex) {
            $errorMessage = "User wallet create with phrases! {$ex->getMessage()}";
            Log::channel('user-wallet')->error($errorMessage);
            return response()->json(["error" => $errorMessage], 400);
        }
    }

    public function changePrivateKeySaltToPhrases(UserWallet $userWallet, $password, $phrases, $privateKey)
    {
        try {
            $privateKey = !$privateKey ? $this->decryptUserWalletPrivateKey($userWallet, $password) : $privateKey;
            if (!$privateKey) {
                throw new Exception("Decrypt user private key error! Response: {$privateKey}");
            }

            $encryptedResponse = $this->encryptPrivateKyWithPassword($phrases, $privateKey);

            if (!$encryptedResponse->successful()) {
                throw new Exception("Encrypt user private key by phrases! Response status: {$encryptedResponse->status()}");
            }

            $encryptedResponse = $encryptedResponse->json();

            if (!$encryptedResponse['privateKey'] && !$encryptedResponse['iv']) {
                throw new Exception("Newly encrypted user private key by KMS is UNDEFINED!");
            }

            $userWallet->private_key = $encryptedResponse['privateKey'];
            $userWallet->iv = $encryptedResponse['iv'];
            if ($userWallet->phrases_created_at) {
                $userWallet->phrases_updated_at = date('Y-m-d H:i:s');
            } else {
                $userWallet->phrases_created_at = date('Y-m-d H:i:s');
            }

            $userWallet->save();
            $userWallet->user->secret_phrase_hashed = Hash::make($phrases);
            $userWallet->user->is_temporary_secret_phrase = false;
            $userWallet->user->save();
            return response()->json(true, 200);

        } catch (Exception $ex) {
            $errorMessage = "User password change error! {$ex->getMessage()}";
            Log::channel('user-wallet')->error($errorMessage);
            return response()->json(["error" => $errorMessage], 400);
        }
    }

    public function getActiveUsers()
    {
        return User::has('wallet')->with([
            'affiliateBy',
            'affiliates' => function ($subQuery) {
                $subQuery->has('wallet');
            },
        ])->get();
    }

    /**
     * @param User $user
     * @param $secretPhrase
     * @return bool
     */
    public function compareUserSecretPhraseAndGetPrivateKey(User $user, $secretPhrase)
    {
        if ($user->secret_phrase_hashed) {
            return Hash::check($secretPhrase, $user->secret_phrase_hashed) ? $this->getUserWalletPrivateKey($user, $secretPhrase) : null;
        }
        $decryptedPvKey = $this->getUserWalletPrivateKey($user, $secretPhrase);
        return str_starts_with($decryptedPvKey, "0x") ? $decryptedPvKey : null;
    }

    /**
     * @param UserWallet $userWallet
     * @param $rawData
     * @return WalletTransaction | null
     */
    public function createWalletTransaction(UserWallet $userWallet, $rawData)
    {
        try {
            $transaction = WalletTransaction::create([
                'user_wallet_id' => $userWallet->id,
                'token_symbol' => $rawData['tokenSymbol'],
                'status' => "PENDING",
                'sender_address' => $userWallet->address,
                'receiver_address' => $rawData['toAddress']
            ]);

            return $transaction->fresh();
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("### UserWalletTransaction - Create ### File: {$ex->getFile()} | Line: {$ex->getLine()} | Message: {$ex->getMessage()}");
            return null;
        }
    }

    /**
     * @param $walletTransaction
     * @param $sendResponse
     */
    public function updateWalletTransaction($walletTransaction, $sendResponse)
    {
        try {
            if($sendResponse && $walletTransaction) {
                $txHash = $sendResponse['transactionHash'] ?? null;
                $walletTransaction->update([
                    'status' => $txHash ? 'SUCCESS' : 'FAILED',
                    'transaction_hash' => $txHash,
                    'blockchain_data' => $sendResponse
                ]);
            }
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("### UserWalletTransaction - Update ### File: {$ex->getFile()} | Line: {$ex->getLine()} | Message: {$ex->getMessage()}");
        }
    }

    /**
     * @param UserWallet $userWallet
     * @param $rawTrData
     * @return array|mixed|null
     */
    public function sendCryptoToAddress(UserWallet $userWallet, $rawTrData)
    {
        $errors = null;
        try {
            $decryptedPrivateKey = $this->decryptUserWalletPrivateKey($userWallet, $rawTrData['secretPhrase']);
            if (!$decryptedPrivateKey) {
                throw new Exception("Decrypt user private key error! Response: $decryptedPrivateKey");
            }

            $transaction = $this->createWalletTransaction($userWallet, $rawTrData);

            $response = null;
            if ($rawTrData['tokenSymbol'] === "2LC") {
                $response = $this->sendTokenToAddress($decryptedPrivateKey, $rawTrData["toAddress"], $rawTrData["amount"], $rawTrData["gasPrice"], $rawTrData["gasLimit"]);
            } else if ($rawTrData['tokenSymbol'] === "BNB") {
                $response = $this->sendBnbToAddress($decryptedPrivateKey, $rawTrData["toAddress"], $rawTrData["amount"], $rawTrData["gasPrice"], $rawTrData["gasLimit"]);
            }

            if(!$response) {
                $transaction->update(['status' => 'ERROR', 'blockchain_data' => ['status' => 'No response from node service']]);
                throw new Exception('Something went wrong!Please try again later.');
            } else if (!$response->successful()) {
                $errors = $response->json();
                $transaction->update(['status' => 'ERROR', 'blockchain_data' => $errors]);
                throw new Exception(sprintf("User wallet id: %s. Response status: %s.", $userWallet->id, $response ? $response->status() : 'No response!'));
            }
            $response = $response->json();

            $this->updateWalletTransaction($transaction, $response);

            return $response;
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("Send Crypto To Address Error. File: {$ex->getFile()} | Line: {$ex->getLine()} | Message: {$ex->getMessage()}");
            return $errors;
        }
    }

    /**
     * @param array $messages
     * @param $key
     * @param $message
     * @return array
     */
    protected function addMessageValidator(array $messages, $key, $message): array
    {
        if(str_contains($message, 'required')) {
            $messages[$key]['required'] = $message;
        } else if(str_contains($message, 'insufficient funds')) {
            $messages["amount"]['customError'] = str_replace("Returned error:", "", $message);
        } else {
            switch ($key) {
                case 'amount':
                case 'gasLimit':
                case 'gas':
                    if ($key === 'gas') {
                        $messages['gasLimit']['customError'] = $message;
                    } else {
                        $messages[$key]['customError'] = $message;
                    }
                    break;
                default:
                    $messages[$key]['customError'] = $message;
            }
        }
        return  $messages;
    }

    /**
     * @param array $errors
     * @return array
     */
    public function resolveErrorMessages(array $errors): array
    {
        $messages = [];
        foreach ($errors as $key => $message) {
            if(is_array($message)) {
                foreach ($message as $msgString) {
                    $messages = $this->addMessageValidator($messages, $key, $msgString);
                }
            } else {
                $messages = $this->addMessageValidator($messages, $key, $message);
            }
        }
        return $messages;
    }

    /**
     * @param User $user
     * @return array|array[]
     */
    public function getAvailableTokens(User $user): array
    {

        $this->updateWalletBalance($user->wallet);
        return [
            [
                'symbol' => '2LC',
                'name' => '2local',
                'logoSrc' => asset('assets/2local_symbol_circle.svg'),
                'balance' => (float)$user->wallet->balance_2lc,
                'address' => $user->wallet->address ?? null,
            ],
            [
                'symbol' => 'BNB',
                'name' => 'Binance Coin',
                'logoSrc' => asset('assets/BNB.svg'),
                'balance' => (float)$user->wallet->balance_bnb,
                'address' => $user->wallet->address ?? null,
            ]
        ];
    }

    public function getGasPrice() {
        try {
            $response = Http::withHeaders(["Content-Type" => "application/json"])
                ->get(config('services.token.base_url') . '/get-gas-price');

            if (!$response->successful()) {
                throw new Exception("Get gas price error. Response status: {$response->status()}");
            }
            $response = $response->json();

            return $response;
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("Get gas price error. Error message: {$ex->getMessage()}");
            return null;
        }
    }

    public function getGasLimit(UserWallet $userWallet, $request) {
        try {
            $postData = ["fromAddress" => $userWallet->address, "tokenSymbol" => $request->tokenSymbol, "toAddress" => $request->toAddress, "amount" => $request->amount];
            $response = Http::withHeaders(["Content-Type" => "application/json"])
                ->post(config('services.token.base_url') . '/get-gas-limit', $postData);

            if (!$response->successful()) {
                throw new Exception("Get gas limit error. Response status: {$response->status()}");
            }
            $response = $response->json();

            return $response;
        } catch (Exception $ex) {
            Log::channel('user-wallet')->error("Get gas limit error. Error message: {$ex->getMessage()}");
            return null;
        }
    }

    public function createUserWalletWithPhrasesForReset(User $user, $phrases)
    {
        try {

            $response = $this->createWallet($phrases);

            if (!$response->successful()) {
                throw new Exception("Wallet creation response error! User id: {$user->id}. Response status: {$response->status()}");
            }

            $response = $response->json();

            $user_wallet = UserWallet::where('user_id', $user->id)->first();
            Log::error(json_encode($response));
            $user_wallet->address = $response['address'];
            $user_wallet->private_key = $response['privateKey'];
            $user_wallet->double_encrypted = $response['doubleEncrypted'];
            $user_wallet->iv = $response['iv'];
            $user_wallet->phrases_updated_at = date('Y-m-d H:i:s');

            $user_wallet->save();

            $user->secret_phrase_hashed = Hash::make($phrases);
            $user->save();

            return response()->json(true, 200);
        } catch (Exception $ex) {
            $errorMessage = "User wallet create with phrases! {$ex->getMessage()}";
            Log::channel('user-wallet')->error($errorMessage);
            return response()->json(["error" => $errorMessage], 400);
        }
    }
}
