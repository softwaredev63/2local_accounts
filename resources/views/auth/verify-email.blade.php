@extends('layouts.auth')

@section('page-title', 'Login')

@section('authContent')
    <section class="onboarding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6 col-md-8">
                    <article>
                        <h1>Log in</h1>
                        @if (session('status') != 'verification-link-sent')
                            <p>Please click 'continue' to send you a verification e-mail.</p>

                            <form method="POST" action="/email/verification-notification">
                                @csrf

                                <button type="submit" class="btn btn-fill">Continue</button>
                            </form>
                        @endif

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                A new email verification link has been emailed to you!
                            </div>

                            <form method="POST" action="/email/verification-notification">
                                @csrf

                                <button type="submit" class="btn btn-fill">Resend</button>
                            </form>
                        @endif

                        <div class="bot">
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection
