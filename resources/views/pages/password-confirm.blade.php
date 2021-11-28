@extends('layouts.auth')

@section('page-title', 'Confirm Password')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Confirm password</h1>
                    <form method="POST" action="{{ url('user/confirm-password') }}">
                        @csrf
                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('password') is-invalid @enderror" placeholder=" " type="password" required id="password" name="password" for="password">
                                <span>Password</span>

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </label>
                        </div>

                        <button type="submit" class="btn btn-fill">Continue</button>
                    </form>
                    <div class="bot">
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
