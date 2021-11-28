<?php

namespace App\Console\Commands;

use App\Services\PasswordService;
use Illuminate\Console\Command;
use App\Imports\ResetPassword as ResetPasswordImport;

/**
 * Class ResetPassword
 * @package App\Console\Commands
 */
class ResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:password {--file-name=} {--new-password=} {--user-has-password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets password of users, from the given csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $fileName = $this->option('file-name');
        $newPassword = $this->option('new-password');
        $userHasPassword = $this->option('user-has-password');

        if(!$fileName) {
            $this->error("You must specify the file name!");
            return;
        }

        if(!$userHasPassword) {
            if(!$newPassword) {
                $this->error("You must specify the new password for users!");
                return;
            }
        }

        $filePath = storage_path("import/{$fileName}");

        if(!file_exists($filePath)) {
            $this->error("The file does not exists!");
            return;
        }
        $this->output->title("### RESET PASSWORD FOR USERS - START ###");

        (new ResetPasswordImport($newPassword, $userHasPassword, new PasswordService()))->withOutput($this->output)->import($filePath);

        $this->output->success("### RESET PASSWORD FOR USERS  - END ###");
    }
}
