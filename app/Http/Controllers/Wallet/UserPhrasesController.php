<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchUserPhrasesRequest;
use App\Http\Requests\PutUserPhrasesRequest;
use App\Services\SecretPhraseService;
use App\Services\UserWalletService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class UserPhrasesController
 * @package App\Http\Controllers\Api
 */
class UserPhrasesController extends Controller
{
    /**
     * @var UserWalletService
     */
    protected $userWalletService;
    protected $secretPhraseService;

    /**
     * BalanceController constructor.
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService, SecretPhraseService $secretPhraseService)
    {
        $this->userWalletService = $userWalletService;
        $this->secretPhraseService = $secretPhraseService;
    }

    /**
     * @SuppressWarnings(PHPMD)
     * @param FetchUserPhrasesRequest $request
     * @return string
     */
    public function getUserPhrases(FetchUserPhrasesRequest $request)
    {
        return $this->secretPhraseService->generateSecretPhrase();
    }

    /**
     * @param PutUserPhrasesRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function saveUserPhrases(PutUserPhrasesRequest $request)
    {
        $user = $request->user;
        if(!$user->wallet) {
            return $this->userWalletService->createUserWalletWithPhrases($user, $request->phrases);
        }

        return $this->userWalletService->changePrivateKeySaltToPhrases($user->wallet,$request->password, $request->phrases, null);
    }
}
