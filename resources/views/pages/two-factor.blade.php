@extends('layouts.auth')

@section('page-title', 'Two Factor')

@section('content')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Two factor authentication</h1>
                    <p>Please enter your authentication code to log in!</p>
                    <form method="POST" action="{{ url('/two-factor-challenge') }}">
                        @csrf
                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme" placeholder=" " type="text" required name="code">
                                <span>Enter your code</span>
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
