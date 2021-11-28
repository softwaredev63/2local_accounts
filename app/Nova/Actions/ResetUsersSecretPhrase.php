<?php

namespace App\Nova\Actions;

use App\Mail\SecretPhraseResetMail;
use App\Mail\SecretPhraseResetReportMail;
use App\Models\User;
use App\Services\SecretPhraseService;
use App\Services\UserWalletService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;

class ResetUsersSecretPhrase extends Action
{
    use InteractsWithQueue, Queueable;

    private $userWalletService;
    private $secretPhraseService;

    public function __construct(UserWalletService $userWalletService, SecretPhraseService $secretPhraseService)
    {
        $this->userWalletService = $userWalletService;
        $this->secretPhraseService = $secretPhraseService;
    }

    public function handle(ActionFields $fields, Collection $models): void
    {
        $resetResults = [];

        foreach ($models as $user) {
            $resetResult = ['email' => $user->email];
            try {
                $this->handleUser($user, $fields);
                $resetResult['status'] = 'Done';
            } catch (Exception $ex) {
                $errorMessage = "Secret phrase reset error: {$ex->getMessage()}";
                Log::channel('user-wallet')->error($errorMessage);
                $resetResult['status'] = $errorMessage;
            }
            $resetResults[] = $resetResult;
        }

        $secretPhraseResetReportMail = new SecretPhraseResetReportMail($resetResults);
        Mail::to(config('mail.admin_notification'))
            ->send($secretPhraseResetReportMail);
    }

    public function fields(): array
    {
        return [
            Text::make('AWS API Key', 'aws_api_key')
                ->rules('required', 'string'),
            Text::make('AWS secret key', 'aws_secret_key')
                ->rules('required', 'string'),
        ];
    }

    public function name(): string
    {
        return "Reset secret phrase";
    }

    /**
     * @throws Exception
     */
    private function handleUser(User $user, ActionFields $fields): void
    {
        $userWallet = $user->wallet;
        if (!$userWallet) {
            throw new Exception('User has no wallet!');
        }
        if (!$userWallet->phrases_created_at) {
            throw new Exception('User has no secret phrase!');
        }

        $temporarySecretPhrase = $this->secretPhraseService->generateSecretPhrase();
        $this->userWalletService->reEncryptPrivateKey($userWallet, $temporarySecretPhrase, $fields->aws_api_key, $fields->aws_secret_key);

        $user->secret_phrase_hashed = Hash::make($temporarySecretPhrase);
        $user->is_temporary_secret_phrase = true;
        $user->save();

        $secretPhraseResetMail = new SecretPhraseResetMail($user->name, $temporarySecretPhrase);
        Mail::to($user->email)
            ->send($secretPhraseResetMail);
    }
}
