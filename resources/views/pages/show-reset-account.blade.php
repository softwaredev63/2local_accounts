@extends('layouts.auth')

@section('page-title', 'Login')

@section('authContent')
<input type="hidden" id="user_id" />
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
<!-- <section class="onboarding" id="reset-container" style="display: none;">
<div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <article>
                    <h1>Reset Account</h1>
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
</section> -->
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
                        $('#phrase-container').show();
                        // $('#reset-container').show();
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

        // $('#continue_1').click(function(e){
        //     var new_name = $('#name').val();
        //     var new_password = $('#reset_password').val();
        //     var new_repeat_password = $('#reset_password_confirmation').val();
        //     if(!new_name || new_name == ""){
        //         alert('Input the New Name!');
        //         return;
        //     }
        //     if(!new_password || new_password == ""){
        //         alert('Input the New Password!');
        //         return;
        //     }
        //     if(!new_repeat_password || new_repeat_password == ""){
        //         alert('Input the Confirmation Password!');
        //         return;
        //     }

        //     if(new_repeat_password !== new_password){
        //         alert('New password should be the same with the Confirmation one!');
        //         return;
        //     }

        //     if(!user_id || user_id == ""){
        //         $('#login-container').show();
        //         // $('#reset-container').hide();
        //         $('#phrase-container').hide();
        //         return;
        //     } 

        //     var data = {
        //         "user_id": user_id,
        //         "new_name": new_name,
        //         "new_password": new_password
        //     };

        //     $.ajax({
        //         type: "POST",
        //         url: '/reset-user-info',
        //         data: data,
        //         success: function(response) {
        //             if(response.code === 200){
        //                 alert(response.message);
        //                 $('#login-container').hide();
        //                 // $('#reset-container').hide();
        //                 $('#phrase-container').show();
        //             } else {
        //                 alert(response.message);
        //             }
        //         }
        //     });
            
        // });
    });
</script>
<script type="text/javascript">
    const redirectToBalance = (timeout) => {
        setTimeout(() => {
            window.location = `${window.location.origin}/balance`;
        }, timeout || 1000)
    }
    
    $(document).ready(function () {

        /* ========================================================================
        * User set phrases - START
        * ======================================================================== */

        const userPhrasesModalSelector = "#generatePhrases-1",
            userPhrasesWizardSelector = "#userPhrasesWizard-1",
            userPhrasesTagSelector = "#userPhrasesWizard-1 .private-phrases",
            userPhrasesGenerateButtonSelector = "#userPhrasesWizard-1 .btn.generate-phrases",
            userPhrasesInputPhrasesSelector = "#userPhrasesWizard-1 input[name='phrases-1']",
            userPhrasesInputPasswordSelector = "#userPhrasesWizard-1 input[name='password-1']",
            userPhrasesNextButtonSelector = "#userPhrasesWizard-1 .btn.sw-btn-next",
            userPhrasesPrevButtonSelector = "#userPhrasesWizard-1 .btn.sw-btn-prev",
            userPhrasesErrorPhrasesSelector = "#userPhrasesWizard-1 .something-went-wrong.phrases",
            userPhrasesErrorPasswordSelector = "#userPhrasesWizard-1 .something-went-wrong.password";

        const userPhrasesModal = $(userPhrasesModalSelector),
            userPhrasesWizard = $(userPhrasesWizardSelector),
            userPhrasesTag = $(userPhrasesTagSelector),
            generatePhrasesButton = $(userPhrasesGenerateButtonSelector);


        const startStepIsDefined = typeof startStep != 'undefined';

        /**
         * Init user phrases wizard modal
         */
        $('#userPhrasesWizard-1').smartWizard({
            selected: startStepIsDefined ? 0 : 0,
            theme: "custom",
            autoAdjustHeight: false,
            transitionEffect:'fade',
            justified:true,
            lang: {
                next:'Next',
                previous:'Back'
            },
            hiddenSteps: [],
            errorSteps: [],
            disabledSteps: [],
        });

        if(startStepIsDefined && startStep === 3) {
            redirectToBalance(100);
        }

        /**
         * Handles input changes - user phrases
         * @param e
         */
        const inputPhrasesChange = (e) => {
            const confirmPhrases = e?.target ? $(e.target).val() : $(userPhrasesInputPhrasesSelector).val();
            if(confirmPhrases && confirmPhrases === userPhrasesWizard.attr('user-phrases')) {
                $(userPhrasesNextButtonSelector).attr("disabled", false);
                $(userPhrasesErrorPhrasesSelector).hide();
            } else {
                $(userPhrasesErrorPhrasesSelector).html("The phrases are not identical!").show();
                $(userPhrasesNextButtonSelector).attr("disabled", true);
            }
        }

        /**
         * Handles input changes - phrases password
         * @param e
         */
        const inputPhrasesPasswordChange = (e) => {
            const password = e?.target ? $(e.target).val() : $(userPhrasesInputPasswordSelector).val();
            if(!password) {
                let errorMsg = "The password is required!";
                if ($(userPhrasesInputPasswordSelector).data('for') == 'secret-phrase-1') errorMsg = 'The temporary secret phrase is required';
                $(userPhrasesErrorPasswordSelector).html(errorMsg).show();
                $(userPhrasesNextButtonSelector).attr("disabled", true);
            } else {
                $(userPhrasesErrorPasswordSelector).html("").hide();
                $(userPhrasesNextButtonSelector).attr("disabled", false);
            }
        }

        /**
         * Handles user phrases wizard modal step changes
         */
        $('#userPhrasesWizard-1').on("showStep",function(e, anchorObject, stepIndex, stepDirection) {
            if(stepIndex === 0) {
                $(userPhrasesPrevButtonSelector).addClass('show btn-fill');
                $(userPhrasesNextButtonSelector).attr("disabled", true);
                inputPhrasesChange();
            } else if(stepIndex === 1 && stepDirection === "backward") {
                $(userPhrasesPrevButtonSelector).removeClass('show btn-fill');
                $(userPhrasesNextButtonSelector).attr("disabled", false);
            } else if(stepIndex === 2 && stepDirection === "forward") {
                $(userPhrasesNextButtonSelector).removeClass('show');
                $(userPhrasesPrevButtonSelector).removeClass('show');
                saveUserPhrases();
            } else if(stepIndex === 3) {
                $(userPhrasesPrevButtonSelector).addClass('show btn-fill');
                $(userPhrasesNextButtonSelector).addClass('show btn-fill');
            } else if (stepIndex === 1 && stepDirection === "forward"){
                $(userPhrasesNextButtonSelector).attr("disabled", false);
                inputPhrasesPasswordChange();
            } else if (stepIndex === 2 && stepDirection === "backward"){
                inputPhrasesChange();
            }
        });

        userPhrasesModal.on('hidden.bs.modal', (event) => {
            resetUserPhrasesState();
        });

        /**
         * Reset phrases wizard data
         */
        const resetUserPhrasesState = () => {
            $(userPhrasesWizardSelector).smartWizard("reset");
            generatePhrasesButton.show();
            $(userPhrasesInputPhrasesSelector).val("");
            $(userPhrasesInputPasswordSelector).val("");
            userPhrasesTag.html("").hide();
            userPhrasesWizard.find('.something-went-wrong').html('');
            $(userPhrasesNextButtonSelector).removeClass('show');
            $(userPhrasesPrevButtonSelector).removeClass('show');
        }

        /**
         * Shows the phrases
         *
         * @param phrasesString
         */
        const showUserPhrases = (phrasesString) => {
            generatePhrasesButton.hide();
            userPhrasesTag.html(phrasesString).show();
            userPhrasesWizard.attr('user-phrases', phrasesString);
            $(userPhrasesNextButtonSelector).addClass('show btn-fill');
            $(userPhrasesNextButtonSelector).attr('disabled', false);
            $(userPhrasesInputPhrasesSelector).keyup(inputPhrasesChange);
            $(userPhrasesInputPasswordSelector).keyup(inputPhrasesPasswordChange);
        }

        /**
         * Generates a new phrases
         */
        const getPhrases = ()=> {
            $.ajax({
                url: "/reset-infos/generate-phrases",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "GET",
                success: (response) => showUserPhrases(response),
                error: (errorResp) => {
                    console.log('Generate phrases error');
                }
            });
        }

        generatePhrasesButton.on('click', getPhrases);

        /**
         * Save the user phrases
         */
        const saveUserPhrases = () => {
            const password = $(userPhrasesInputPasswordSelector).val();
            const phrases = $(userPhrasesInputPhrasesSelector).val();
            const generatedPhrases = userPhrasesWizard.attr('user-phrases');
            if(!phrases || !generatedPhrases || phrases !== generatedPhrases) {
                $(userPhrasesErrorPhrasesSelector).html("The phrases are not identical!").show();
                $(userPhrasesWizardSelector).smartWizard("prev");
            } else {
                $(userPhrasesErrorPhrasesSelector).html("").hide();
            }
            $(userPhrasesNextButtonSelector).attr("disabled", true);
            $(userPhrasesPrevButtonSelector).attr("disabled", true);
            $(userPhrasesWizardSelector).smartWizard("loader", "show");
            $.ajax({
                url: "/reset-infos/set-phrases",
                type: "PUT",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    phrases: phrases,
                    password: password,
                    user_id: $('#user_id').val()
                },
                success: (response) => {
                    $(userPhrasesWizardSelector).smartWizard("loader", "hide");
                    $(userPhrasesWizardSelector).smartWizard("next");
                    $(userPhrasesNextButtonSelector).removeClass('show');
                    $(userPhrasesPrevButtonSelector).removeClass('show');
                    $(userPhrasesNextButtonSelector).attr("disabled", false);
                    $(userPhrasesPrevButtonSelector).attr("disabled", false);
                    redirectToBalance();
                },
                error:(errorResp) => {
                    var error = errorResp.responseJSON.errors;
                    console.log('Error set phrass', errorResp);
                    $(userPhrasesWizardSelector).smartWizard("prev");
                    $(userPhrasesWizardSelector).smartWizard("loader", "hide");
                    $(userPhrasesNextButtonSelector).attr("disabled", false);
                    $(userPhrasesPrevButtonSelector).attr("disabled", false);
                    $(userPhrasesErrorPasswordSelector).html("The password is wrong!").show();
                }
            });
        }

        // Disables ctrl+v, ctrl+x, ctrl+c.
        // $(userPhrasesTagSelector).on("cut", function(e) {
        //     console.log('Cut not allowed!');
        //     e.preventDefault();
        // });
        // $(userPhrasesTagSelector).on("copy", function(e) {
        //     console.log('Copy not allowed!');
        //     e.preventDefault();
        // });
        // $(userPhrasesInputPhrasesSelector).on("paste", function(e) {
        //     console.log('Paste not allowed!');
        //     e.preventDefault();
        // });

        // Disables right-click.
        $(userPhrasesTagSelector).mousedown(function(e) {
            if (e.button == 2) {
                e.preventDefault();
                alert('right-click is disabled!');
            }
        });


        /* ========================================================================
        * User set phrases - END
        * ======================================================================== */

    });

</script>
@endsection
