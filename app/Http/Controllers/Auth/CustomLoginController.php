<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Services\UserWalletService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Socialite;
use Auth;
use App\Models\User;

class CustomLoginController extends Controller
{

    /**
     * @name $userWalletService
     * @var UserWalletService
     */
    private $userWalletService;

    /**
     * CustomLoginController constructor.
     *
     * @param UserWalletService $userWalletService
     */
    public function __construct(UserWalletService $userWalletService)
    {
        $this->userWalletService = $userWalletService;
    }

    public function showLoginForm()
    {
        return view('pages.login');
    }

    public function showChangePasswordForm(Request $request)
    {
        $authUser = auth()->user();
        if($authUser->has_changed_password) return redirect(route('balance'));
        return view('pages.change-password');
    }

    public function showSetPhrasesWizard()
    {
        $authUser = auth()->user();

        return view('pages.set-phrases', [
            "userEmail" => $authUser->email,
            "stepIndex" => $authUser->wallet && $authUser->wallet->phrases_created_at && !$authUser->is_temporary_secret_phrase ? 3 : 0,
            "isTemporarySecretPhrase" => $authUser->is_temporary_secret_phrase,
        ]);
    }

    public function showTwoFactorForm()
    {
        return view('pages.two-factor');
    }

    /**
     * @SuppressWarnings(PHPMD)
     * @param ChangePasswordRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function doChangePassword(ChangePasswordRequest $request)
    {
        $authUser = auth()->user();

        $authUser->password             = bcrypt($request->password);
        $authUser->has_changed_password = 1;
        $authUser->save();

        return redirect(route('set-user-phrases'));
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();

            if($finduser) {
                Auth::login($finduser);
                return redirect('/balance');
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id' => $user->id,
                    'password' => bcrypt($user->id),
                    'has_changed_password' => 0,
                    'email_verified_at' => date("Y-m-d H:i:s")
                ]);

                Auth::login($newUser);

                return redirect()->back();
            }

        } catch (Exception $e) {
            return redirect('auth/google');
        }

    }
}
