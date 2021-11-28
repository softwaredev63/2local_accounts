<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \App\Imports\UsersImport as UsersImportImports;

/**
 * Class UsersImport
 *
 * @package App\Console\Commands
 * @author Bojte Szabolcs
 */
class UsersImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users {--file-name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import users from csv. Csv file have to located in storage/import';

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
        if(!$fileName) {
            $this->error("You must specify the file name!");
            return;
        }
        $filePath = storage_path("import/{$fileName}");

        if(!file_exists($filePath)) {
            $this->error("The file does not exists!");
            return;
        }
        $this->output->title("### USERS IMPORT - START ###");

        (new UsersImportImports)->withOutput($this->output)->import($filePath);

        $this->output->success("### USERS IMPORT - END ###");
    }
}
