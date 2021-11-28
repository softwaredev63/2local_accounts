<?php

namespace App\Http\Controllers\Wallet;

use App\Http\Controllers\Controller;
use App\Services\UserWalletService;

class BalanceController extends Controller
{
    /**
     * @var UserWalletService
     */
    protected $userWalletService;

    /**
     * BalanceController constructor.
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService)
    {
        $this->userWalletService = $userWalletService;
    }

    public function showBalancePage()
    {
        $user = auth()->user();

        $tokens = $this->userWalletService->getAvailableTokens($user);

        $lockedTokens = [
            [
                'symbol' => '2LC',
                'name' => '2local',
                'logoSrc' => asset('assets/2local_symbol_circle.svg'),
                'balance' => (float) $user->wallet->balance_locked_2lc,
                'address' => $user->wallet->address ?? null,
            ],
        ];

        return view('pages.balance')->with([
            'userEmail'         => $user->email,
            'twoFAEnabled'      => $user->two_factor_secret ? true : false,
            'isEmailVerified'   => $user->email_verified_at ? true : false,
            'tokenSent'         => !!$user->wallet->token_sent,
            'affiliateLink'     => route('register', ['code' => $user->affiliate_code]),
            'tokens'            => $tokens,
            'lockedTokens'      => $lockedTokens,
        ]);
    }

    public function showSettingsPage()
    {
        $user = auth()->user();

        $tokens = $this->userWalletService->getAvailableTokens($user);

        return view('pages.settings')->with([
            'tokens'            => $tokens,
        ]);
    }
}
