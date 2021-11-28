<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class FetchPrivateKeyRequest extends FormRequest
{
    private $user;

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
            'code' => 'nullable|string'
        ];
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function withValidator($validator)
    {
        $this->user = auth()->user();

        if ($this->phrases) {
            if (!$this->user) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('phrases', 'Wrong phrases!');
                });
            }
        }

        if ($this->user->two_factor_secret) {
            if (!$this->code || !$this->hasValidCode()) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('code', 'Two factor authentication failed!');
                });
            }
        }
        return;
    }

    private function hasValidCode()
    {
        return $this->code && app(TwoFactorAuthenticationProvider::class)->verify(
            decrypt($this->user->two_factor_secret),
            $this->code
        );
    }
}
