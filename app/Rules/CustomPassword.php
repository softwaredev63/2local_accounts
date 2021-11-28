<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomPassword implements Rule
{
    protected $alwaysFail = false;
    protected $minLength = null;
    protected $maxLength = null;
    protected $startWithLetter = false;
    protected $requireUppercase = false;
    protected $requireLowercase = false;
    protected $requireNumeric = false;
    protected $requireSpecialCharacter = false;
    protected $notCommon = false;
    protected $differentFrom = null;
    protected $message;

    public function passes($attribute, $value): bool
    {
        if ($this->alwaysFail) {
            $this->message = 'You can\'t reset your password. Please contact an admin.';
            return false;
        }

        if ($this->minLength && Str::length($value) < $this->minLength) {
            $this->message = "The password must be at least $this->minLength length";
            return false;
        }

        if ($this->maxLength && Str::length($value) > $this->maxLength) {
            $this->message = "The password can be at most $this->maxLength length";
            return false;
        }

        if ($this->startWithLetter && is_numeric($value[0])) {
            $this->message = 'The password must start with a letter';
            return false;
        }

        if ($this->requireUppercase && Str::lower($value) === $value) {
            $this->message = 'The password must contain at least one uppercase letter';
            return false;
        }

        if ($this->requireLowercase && Str::upper($value) === $value) {
            $this->message = 'The password must contain at least one lowercase letter';
            return false;
        }

        if ($this->requireNumeric && !preg_match('/[0-9]/', $value)) {
            $this->message = 'The password must contain at least one number';
            return false;
        }

        if ($this->requireSpecialCharacter && !preg_match('/[@$!%*#=?&+-]/', $value)) {
            $this->message = 'The password must contain at least one special character from the following: *+-!?=$@#%';
            return false;
        }

        $exists = DB::table('common_passwords')->where('password', $value)->first();
        if ($this->notCommon && $exists) {
            $this->message = 'This password is way too common to use. Choose something else!';
            return false;
        }

        if ($this->differentFrom && Hash::check($value, $this->differentFrom)) {
            $this->message = 'You are not allowed to use your old password. Please think of a new one';
            return false;
        }

        return true;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function alwaysFail(): CustomPassword
    {
        $this->alwaysFail = true;

        return $this;
    }

    public function minLength(int $minLength): CustomPassword
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function maxLength(int $maxLength): CustomPassword
    {
        $this->maxLength = $maxLength;

        return $this;
    }

    public function startWithLetter(): CustomPassword
    {
        $this->startWithLetter = true;

        return $this;
    }

    public function requireUppercase(): CustomPassword
    {
        $this->requireUppercase = true;

        return $this;
    }

    public function requireLowercase(): CustomPassword
    {
        $this->requireLowercase = true;

        return $this;
    }

    public function requireNumeric(): CustomPassword
    {
        $this->requireNumeric = true;

        return $this;
    }

    public function requireSpecialCharacter(): CustomPassword
    {
        $this->requireSpecialCharacter = true;

        return $this;
    }

    public function notCommon(): CustomPassword
    {
        $this->notCommon = true;

        return $this;
    }

    public function requireDifferentFrom(string $differentFrom): CustomPassword
    {
        $this->differentFrom = $differentFrom;

        return $this;
    }
}
