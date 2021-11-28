<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordService
{
    /**
     * Updates the users(from csv) password with a single password
     *
     * @param $email
     * @param $newPassword
     * @return array
     */
    public function updateUserPasswordWithResult($email, $newPassword)
    {
        $user = User::where('email', $email)->first();
        $result = ['email' => $email];
        if($user) {
            // if(!$user->wallet) {
                $this->updateAndSavePassword($user, $newPassword, false);
                $result['status'] = "Done";
            // } else {
            //    $result['status'] = $this->getMessage($email, 'user-has-wallet');
            // }
        } else {
            $result['status'] = $this->getMessage($email,'user-not-found');
        }
        return $result;
    }

    /**
     * @param $email
     * @param $newPassword
     * @param $output
     */
    public function updateUserPasswordWithOutput($email, $newPassword, $output)
    {
        $user = User::where('email', $email)->first();
        if($user) {
            if(!$user->wallet) {
                $this->updateAndSavePassword($user, $newPassword);
            } else {
                $output->error($this->getMessage($email, 'user-has-wallet'));
            }
        } else {
            $output->error($this->getMessage($email,'user-not-found'));
        }

    }

    protected function getMessage($email, $type) : string
    {
        switch ($type) {
            case "user-not-found":
                return "User with this email '{$email}' is not found!";
            case "user-has-wallet":
                return "User with this email: '{$email}' has a wallet! You can not change the password!";
            default:
                return "Something went wrong!";
        }
    }

    /**
     * @param User $user
     * @param $newPassword
     * @param bool $withHash
     */
    protected function updateAndSavePassword(User $user, $newPassword, $withHash = true)
    {
        $user->password = $withHash ? Hash::make($newPassword) : $newPassword;
        $user->save();
    }
}
