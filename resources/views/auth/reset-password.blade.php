@extends('layouts.auth')

@section('page-title', 'Reset Password')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Reset password</h1>
                    <p>Password requirements:</p>
                    <ul>
                        <li>Start with a letter</li>
                        <li>At least one uppercase letter (A-Z)</li>
                        <li>At least one lowercase letter (a-z)</li>
                        <li>At least one number (0-9)</li>
                        <li>At least one special character (@$!%*#=?&+-)</li>
                        <li>Between 8-21 characters</li>
                    </ul>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ request()->route('token') }}">

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
                            <label class="twolocal-input with-pass-strength">
                                <span>New password</span>
                                <input class="@error('password') @enderror" placeholder="" type="password" required id="password" name="password" for="password">

                                @error('password')
                                <label class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </label>
                                @enderror
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="twolocal-input with-pass-strength">
                                <span>Repeat new password</span>
                                <input class="" placeholder="" type="password" required id="password_confirmation" name="password_confirmation" for="password_confirmation">
                            </label>
                        </div>

                        <button type="submit" class="btn btn-fill">Reset</button>
                    </form>

                    <div class="bot">
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
<script src="{{asset('js/jquery-3.3.1.min.js')}}"></script>
<script type="text/javascript" src="js/pwstrength.js"></script>
<script type="text/javascript">
    var options = {};
    options.ui = {
        container: "#pwd-container",
    };
    $(':password').pwstrength(options);
</script>
@endsection
