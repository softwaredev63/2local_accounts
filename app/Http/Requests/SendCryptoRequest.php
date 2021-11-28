<?php

namespace App\Http\Requests;

use App\Services\UserWalletService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class SendCryptoRequest extends FormRequest
{
    protected $userWalletService;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->userWalletService = new UserWalletService();
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

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
            'toAddress' => 'required|string',
            'tokenSymbol' => 'required|string|in:BNB,2LC',
            'amount' => 'required|numeric',
            'gasPrice' => 'required|numeric',
            'gasLimit' => 'required|numeric',
            'secretPhrase' => 'required|string',
            'password' => 'required|string',
        ];
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    public function withValidator($validator)
    {
        $this->user = auth()->user();

        if (!$this->amount) {
            $validator->after(function ($validator) {
                $validator->errors()->add('amount', 'Amount has to be greater than 0');
            });
        }

        if (!$this->gasPrice) {
            $validator->after(function ($validator) {
                $validator->errors()->add('gasPrice', 'Gas price has to be greater than 0');
            });
        }

        if (!$this->gasLimit) {
            $validator->after(function ($validator) {
                $validator->errors()->add('gasLimit', 'Gas limit has to be greater than 0');
            });
        }

        if ($this->secretPhrase) {
            if (!$this->userWalletService->compareUserSecretPhraseAndGetPrivateKey($this->user, $this->secretPhrase)) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('secretPhrase', 'Wrong secret phrase');
                });
            }
        }

        if ($this->password) {
            if (!Hash::check($this->password, $this->user->password)) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('password', 'Wrong password');
                });
            }
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $this->userWalletService->resolveErrorMessages((new ValidationException($validator))->errors());

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
