<?php

namespace App\Imports;

use App\Services\PasswordService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

/**
 * Class ResetPasswordFromNova
 * @package App\Imports
 */
class ResetPasswordFromNova implements ToCollection
{
    use Importable;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @var boolean
     */
    private $importResult = [];

    /**
     * @var PasswordService
     */
    private $passwordService;

    /**
     * ResetPassword constructor.
     * @param $newPassword
     * @param PasswordService $passwordService
     */
    public function __construct($newPassword, PasswordService $passwordService)
    {
        $this->newPassword = $newPassword;
        $this->passwordService = $passwordService;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach($rows as $row) {
            try {
                $email = $row ? $row[0] : null;
                $this->importResult[] = $this->passwordService->updateUserPasswordWithResult($email, $this->newPassword);
            } catch (\Exception $ex) {
                $email = $row[0];
                if($email) {
                    $this->addToResult($email, "Error! {$ex->getMessage()}");
                }
                Log::channel('user-password-reset')->error("User password change Error. Error message: {$ex->getMessage()}");
            }
        }
    }

    /**
     * @return array|bool
     */
    public function getImportResult()
    {
        return [$this->importResult];
    }

    /**
     * @param $email
     * @param $status
     */
    protected function addToResult($email, $status)
    {
        $this->importResult[] = [
            'email' => $email,
            'status' => $status
        ];
    }
}
