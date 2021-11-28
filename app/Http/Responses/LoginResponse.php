<?php

namespace App\Http\Responses;

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  $request
     * @return mixed
     * @SuppressWarnings(PHPMD)
     */
    public function toResponse($request)
    {
        $home = auth()->user()->has_changed_password ? RouteServiceProvider::HOME : '/change-pass';

        return redirect()->intended($home);
    }
}
