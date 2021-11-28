@extends('layouts.auth')

@section('page-title', 'Login')

@section('authContent')
<input type="hidden" id="user_id" />
<script type="text/javascript" src="js/custom_1.js"></script>
<section class="onboarding" id="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Log in</h1>
                    <p>Welcome to 2local. Enter to reset the user info.</p>

                    @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                    @endif

                    <form method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('email') is-invalid @enderror" placeholder=" " type="email" required name="email" id="email" autofocus>
                                <span>Email address</span>

                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-email-message"></strong>
                                </span>

                            </label>
                        </div>

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('password') is-invalid @enderror" placeholder=" " type="password" required name="password" id="password">
                                <span>Password</span>

                                <span class="invalid-feedback" role="alert">
                                    <strong id="error-pwd-message"></strong>
                                </span>

                            </label>
                        </div>

                        <button type="button" class="btn btn-fill" id="continue">Continue</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>
<section class="onboarding" id="reset-container" style="display: none;">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Reset Account/h1>
                    <p>Welcome to 2local. Enter to reset the user info.</p>

                    <form method="POST">
                        @csrf

                        <div class="form-group">
                            <label class="twolocal-input">
                                <input class="watchme @error('name') is-invalid @enderror" placeholder=" " type="name" required name="name" id="name" autofocus>
                                <span>Name</span>

                            </label>
                        </div>

                        <div class="form-group">
                            <label class="twolocal-input with-pass-strength">
                                <span>Password</span>
                                <input class="@error('password') @enderror" placeholder="" type="password" required id="reset_password" name="reset_password" for="reset_password">
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="twolocal-input with-pass-strength">
                                <span>Repeat password</span>
                                <input class="" placeholder="" type="password" required id="reset_password_confirmation" name="reset_password_confirmation" for="reset_password_confirmation">
                            </label>
                        </div>

                        <button type="button" class="btn btn-fill" id="continue_1">Continue</button>
                    </form>
                </article>
            </div>
        </div>
    </div>
</section>
<section class="onboarding" id="phrase-container" style="display: none;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Set phrase</h1>
                    @include('partials.user.phrases-wizard_1')
                </article>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="js/pwstrength.js"></script>
<script type="text/javascript">
    var options = {};
    options.ui = {
        container: "#pwd-container",
    };
    $('#reset_password, #reset_password_confirmation').pwstrength(options);
</script>
<script>
    var user_id = "";
    jQuery(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#continue').click(function(e){
            e.preventDefault();

            var data = {
                "email": $('#email').val(),
                "password": $('#password').val()
            };

            $.ajax({
                type: "POST",
                url: '/check-login-status',
                data: data,
                success: function(response) {
                    if(response.login_status){
                        $('#login-container').hide();
                        $('#phrase-container').hide();
                        $('#reset-container').show();
                    } else{
                        alert(response.message)
                        // if(response.message == "This email account is not existing!"){
                        //     $('#error-pwd-message').html("")
                        //     $('#error-email-message').html('This email account is not existing!');
                        // } else if (response.message == "Password doesn't match!" ) {
                        //     $('#error-email-message').html("")
                        //     $('#error-pwd-message').html("Password doesn't match!");
                        // }
                    }
                    user_id = response.user_id;
                    $('#user_id').val(user_id);
                }
            });
            
        });

        $('#continue_1').click(function(e){
            var new_name = $('#name').val();
            var new_password = $('#reset_password').val();
            var new_repeat_password = $('#reset_password_confirmation').val();
            if(!new_name || new_name == ""){
                alert('Input the New Name!');
                return;
            }
            if(!new_password || new_password == ""){
                alert('Input the New Password!');
                return;
            }
            if(!new_repeat_password || new_repeat_password == ""){
                alert('Input the Confirmation Password!');
                return;
            }

            if(new_repeat_password !== new_password){
                alert('New password should be the same with the Confirmation one!');
                return;
            }

            if(!user_id || user_id == ""){
                $('#login-container').show();
                $('#reset-container').hide();
                $('#phrase-container').hide();
                return;
            } 

            var data = {
                "user_id": user_id,
                "new_name": new_name,
                "new_password": new_password
            };

            $.ajax({
                type: "POST",
                url: '/reset-user-info',
                data: data,
                success: function(response) {
                    if(response.code === 200){
                        alert(response.message);
                        $('#login-container').hide();
                        $('#reset-container').hide();
                        $('#phrase-container').show();
                    } else {
                        alert(response.message);
                    }
                }
            });
            
        });
    });
</script>
@endsection
