@extends('layouts.auth')

@section('page-title', 'Reset Password')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Email address</h1>
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('email') is-invalid @enderror" placeholder=" " type="email" required name="email" id="email" autofocus>
                                <span>Email address</span>

                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message == trans('passwords.throttled') ? $message : '' }}</strong>
                                </span>
                                @enderror
                            </label>
                        </div>

                        <button type="submit" class="btn btn-fill">Reset</button>
                    </form>

                    @error('email')
                    <div class="mb-4 font-medium text-sm text-green-600">
                        <strong>{{ $message == trans('passwords.throttled') ? '' : trans('passwords.sent') }}</strong>
                    </div>
                    @enderror

                    @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        <strong>{{ trans('passwords.sent') }}</strong>
                    </div>
                    @endif

                    <div class="bot">
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
