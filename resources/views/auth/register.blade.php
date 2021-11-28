@extends('layouts.auth')

@section('page-title', 'Register')

@section('authContent')
<section class="onboarding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Create new account</h1>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('name') is-invalid @enderror" placeholder=" " type="name" required name="name" id="name" autofocus>
                                <span>Name</span>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror

                            </label>
                        </div>

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
                                <span>Password</span>
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
                                <span>Repeat password</span>
                                <input class="" placeholder="" type="password" required id="password_confirmation" name="password_confirmation" for="password_confirmation">
                            </label>
                        </div>

                        @if($affiliateByCode)
                            <div class="form-group">
                                <label class="twolocal-input affiliate-code">
                                    <span>Affiliate by code</span>
                                    <input class="" readonly type="text" required id="affiliate_by_code"  value="{{$affiliateByCode}}" name="affiliate_by_code" for="affiliate_by_code">
                                </label>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-fill">Create</button>
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
