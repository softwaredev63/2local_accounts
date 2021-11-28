<?php

namespace App\Console\Commands;

use App\Services\UserWalletService;
use Illuminate\Console\Command;

/**
 * Class SendTokenToUsers
 *
 * @package App\Console\Commands
 * @author Bojte Szabolcs
 */
class SendTokenToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bsc-token:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send tokens to users';

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
        $privateKey = $this->secret('Sender address private key');
        $gasPrice = $this->ask('Gas price. (Default is 50000)');

        if(!$privateKey) {
            $this->error('You must specify the sender address private key! This is needed for transaction signing!');
            return;
        }
        $gas = $gasPrice ? $gasPrice : "50000";

        $this->info('### L2C BSC TOKEN SENDING - START ###');

        $this->userWalletService->sendTokensToUsers($this->output, $privateKey, $gas);

        $this->info('### L2C BSC TOKEN SENDING - END ###');
    }
}
