@extends('layouts.auth')

@section('page-title', 'Login')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Log in</h1>
                    <p>Welcome to 2local. Enter to get a 2LC wallet BEP20 based.</p>

                    @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('email') is-invalid @enderror" placeholder=" " type="email" required name="email" id="email" autofocus>
                                <span>Email address</span>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                            </label>
                        </div>

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('password') is-invalid @enderror" placeholder=" " type="password" required name="password" id="password">
                                <span>Password</span>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                            </label>
                        </div>

                        <div class="twolocal-checkbox">
                            <input class="form-check-input" type="checkbox" id="gridCheck">
                            <label class="form-check-label" for="gridCheck">
                                <span class="icon material-icons-outlined">
                                    check
                                </span>
                                <p>Remember me</p>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-fill">Continue</button>
                    </form>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                    @endif

                    @if (Route::has('register'))
                    <a href="{{ route('register') }}">Create new account</a>
                    @endif

                    <div class="bot">
                    </div>
                    <a href="/auth/google" class="btn btn-auth-google mt-2">Login with Google account</a>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
