<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

/**
 * Class PutUserPhrasesRequest
 * @package App\Http\Requests
 */
class PutUserPhrasesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phrases' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function withValidator($validator)
    {
        $this->user = auth()->user();

        if ($this->password) {
            if (!$this->user) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('password', 'Not authenticated!');
                });
                return;
            }

            $toVerifyAgainst = $this->user->is_temporary_secret_phrase ? $this->user->secret_phrase_hashed : $this->user->password;
            $errorMessage = $this->user->is_temporary_secret_phrase ? 'Wrong temporary secret phrase!' : 'Wrong password!';
            if (!(Hash::check($this->password, $toVerifyAgainst))) {
                $validator->after(function ($validator) use ($errorMessage) {
                    $validator->errors()->add('password', $errorMessage);
                });
            }
        }
    }
}
