<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchPrivateKeyRequest;
use App\Http\Requests\SendCryptoRequest;
use App\Services\UserWalletService;
use Illuminate\Http\Request;

/**
 * Class WalletController
 * @package App\Http\Controllers\Wallet
 * @author Bojte Szabolcs
 */
class WalletController extends Controller
{
    /**
     * @var UserWalletService
     */
    protected $userWalletService;

    /**
     * BalanceController constructor.
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService)
    {
        $this->userWalletService = $userWalletService;
    }

    /**
     * @SuppressWarnings(PHPMD)
     * @param FetchPrivateKeyRequest $request
     * @return false|string|null
     */
    public function getPrivateKey(FetchPrivateKeyRequest $request)
    {
        $user = auth()->user();
        $decryptedPrivateKey = $this->userWalletService->compareUserSecretPhraseAndGetPrivateKey($user, $request->phrases);
        if($decryptedPrivateKey) {
            return response()->json(["privateKey" => $decryptedPrivateKey], 200);
        }
        return response()->json(["errors" => ["phrases" => __("The phrases are not identical!")]], 422);
    }

    public function buyWithSimplex()
    {
        $user = auth()->user();

        $tokens = $this->userWalletService->getAvailableTokens($user);

        return view('pages.simplex')->with([
            'tokens' => $tokens,
            'simplexPublicKey' => config('crypto.simplex.publicKey'),
            'simplexScriptSrc' => config('crypto.simplex.scriptSrc'),
            'simplexFormScriptSrc' => config('crypto.simplex.formScriptSrc'),
        ]);
    }

    /**
     * @param SendCryptoRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCryptoToAddress(SendCryptoRequest $request)
    {
        $user = $request->user;
        $transactionResponse = $this->userWalletService->sendCryptoToAddress($user->wallet, $request->validated());

        if(!$transactionResponse || (array_key_exists('error', $transactionResponse) && $transactionResponse['error'])) {
            $errors = ["failed" => "Something went wrong!"];
            $responseCode = 500;
            if($transactionResponse) {
                $errors = $this->userWalletService->resolveErrorMessages($transactionResponse['error']);
                $responseCode = 422;
            }
            return response()->json(['errors' => $errors], $responseCode);
        }

        return response()->json($transactionResponse);
    }

    public function getGasPrice() {
        return response()->json($this->userWalletService->getGasPrice());
    }

    public function getGasLimit(Request $request) {
        $user = auth()->user();

        $gasResponse = $this->userWalletService->getGasLimit($user->wallet, $request);

        return response()->json($gasResponse);
    }
}
