@extends('layouts.main')

@section('page-title', 'Balance')

@section('mainContent')
<section class="intro">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12">
                <h1 class="settings-text">Settings</h1>
                <h2 class="two-factor-title">
                    Two factor authentication
                </h2>
                @if(!auth()->user()->two_factor_secret)
                    <h3 class="two-factor-status">2FA status: Disabled</h3>
                    <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                        @csrf
                        <button type="submit" class="btn btn-fill two-factor-action">
                            Enable
                        </button>
                    </form>
                @else
                    <h3 class="two-factor-status">2FA status: Enabled</h3>
                    <form method="POST" action="{{ url('user/two-factor-authentication') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-fill two-factor-action">
                            Disable
                        </button>
                    </form>
                @endif

                <div class="two-factor-action">
                    @if(session('status') == 'two-factor-authentication-enabled')
                        <h4>
                            You have enabled the 2FA.
                            Please scan the QR code with your phone on 2FA code generator like <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en&gl=US">Google Authenticator</a>!
                        </h4>
                        <p>
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </p>
                        <h4>
                            Or input the following secret key manually
                        </h4>
                        <p>
                            {!! decrypt(auth()->user()->two_factor_secret) !!}
                        </p>
                        <h4 class="text-danger"><i>Please backup these scan code and secret key as we show only this time to you!</i></h4>
{{--                        <h4>--}}
{{--                            These are your recovery codes. Back them up, as this is the only time we will show them to you--}}
{{--                            and you might need them in order to recover your account should something unexpected happen.--}}
{{--                            Once you refresh the page they will be gone forever.--}}
{{--                        </h4>--}}
{{--                        <p>--}}
{{--                            @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes, true)) as $code)--}}
{{--                                {{ trim($code)}} <br>--}}
{{--                            @endforeach--}}
{{--                        </p>--}}
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
