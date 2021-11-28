<?php

namespace App\Imports;

use App\Services\PasswordService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

/**
 * Class ResetPassword
 * @package App\Imports
 */
class ResetPassword implements ToCollection
{
    use Importable;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @var boolean
     */
    private $userHasPassword;

    /**
     * @var PasswordService
     */
    private $passwordService;

    /**
     * ResetPassword constructor.
     * @param $newPassword
     * @param $userHasPassword
     * @param PasswordService $passwordService
     */
    public function __construct($newPassword, $userHasPassword, PasswordService $passwordService)
    {
        $this->newPassword = $newPassword;
        $this->userHasPassword = $userHasPassword;
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
                if($email) {
                    $newPassword = $this->userHasPassword ? $row[1] : $this->newPassword;
                    $this->passwordService->updateUserPasswordWithOutput($email, $newPassword, $this->output);
                }
            } catch (\Exception $ex) {
                Log::channel('user-password-reset')->error("User password change Error. Error message: {$ex->getMessage()}");
            }
        }
    }
}
