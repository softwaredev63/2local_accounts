<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserWalletService;
use Illuminate\Console\Command;

/**
 * Class ResetPasswordForAnUser
 * @package App\Console\Commands
 * @author Bojte Szabolcs
 */
class ResetPasswordForAnUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:user-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset an user password by email and encrypt the user private key with new password';

    /**
     * @name $userWalletService
     * @var UserWalletService
     */
    protected $userWalletService;

    /**
     * Create a new command instance.
     *
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService)
    {
        $this->userWalletService = $userWalletService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $userEmail = $this->ask('User email');
        $newPassword = $this->secret('User new password');
        $awsApiKey = $this->secret('AWS API Key');
        $awsSecretKey = $this->secret('AWS secret key');

        $errors = [];
        if(!$userEmail) {
            $errors['user-email'] = 'You must specify the user email';
        }
        if(!$newPassword) {
            $errors['new-password'] = 'You must specify the new password';
        }
        if(!$awsApiKey) {
            $errors['aws-api-key'] = 'You must specify the aws api key';
        }
        if(!$awsSecretKey) {
            $errors['aws-secret-key'] = 'You must specify the aws secret key';
        }

        if($errors) {
            $this->output->error($errors);
            return;
        }

        $user = User::where('email', $userEmail)->first();

        if(!$user) {
            $this->output->error("User not found! Email: {$userEmail}");
            return;
        }

        $userWallet = $user->wallet;

        if(!$userWallet) {
            $this->output->error("User wallet not found! Email: {$userEmail}");
            return;
        }

        $this->userWalletService->handleUserPasswordChange($this->output, $userWallet, $newPassword, $awsApiKey, $awsSecretKey);
    }

}
