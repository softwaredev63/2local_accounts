<?php

namespace App\Actions\Fortify;

use App\Rules\CustomPassword;
use Illuminate\Support\Facades\Auth;

trait PasswordValidationRules
{
    protected function passwordRules($user = null)
    {
        $customPassword = new CustomPassword();

        $customPassword->minLength(8)
            ->maxLength(21)
            ->startWithLetter()
            ->requireUppercase()
            ->requireLowercase()
            ->requireNumeric()
            ->requireSpecialCharacter()
            ->notCommon();

        if ($user) {
            if ($user->id === Auth::id()) {
                // A logged in user tries to change the password
                $customPassword->requireDifferentFrom($user->password);
            } else if (!$user->has_changed_password/* || !$user->wallet || !$user->wallet->phrases_created_at*/) {
                $customPassword->alwaysFail();
            }
        }

        return ['required', 'string', 'confirmed', $customPassword];
    }
}
