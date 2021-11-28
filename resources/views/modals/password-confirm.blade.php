<div class="modal fade show" id="confirmPasswordModal" tabindex="-1" role="dialog" aria-labelledby="confirmPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmPasswordModalLabel">Show private key</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="confirm-password">
                    <form id="passwordConfirm" onsubmit="return false;">
                        @csrf
                        <div class="form-group">
                            <div id='passwordLabel'>
                                Confirm your secret phrase
                                <label class="twolocal-input">
                                    <input class="watchme @error('phrases') is-invalid @enderror" placeholder=" " type="phrases" id="phrases" name="phrases" for="phrases" required>
                                    <span>Secret phrase</span>
                                </label>
                            </div>

                            @if($twoFAEnabled)
                            <div id="2faCodeLabel" style="display: none;">
                                Two factor authentication code
                                <label class="twolocal-input">
                                    <input class="watchme" placeholder=" " type="text" required name="code" id="code" for="code">
                                    <span>Enter your code</span>
                                </label>
                            </div>
                            @endif
                            <span id="something-went-wrong" class="something-went-wrong">
                            </span>
                        </div>
                    </form>
                    <div id="key" class="key">

                    </div>
                    <div class="centered-modal-buttons">
                        <button type="button" class="btn btn-stroke" data-dismiss="modal">Close</button>
                        @if($twoFAEnabled)
                        <button id="nextButton" onclick="next();" type="button" class="btn btn-fill">Next</button>
                        <button id="continueButton" style="display: none;" onclick="getPrivateKey();" type="button" class="btn btn-fill">Continue</button>
                        @else
                        <button id="continueButton" onclick="getPrivateKey();" type="button" class="btn btn-fill">Continue</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    function next() {
        $("#continueButton").show();
        $("#nextButton").hide();
        $("#passwordLabel").hide();
        $("#2faCodeLabel").show();
        $('.something-went-wrong').html("");
    }

    function getPrivateKey() {
        var phrases = document.getElementById("phrases").value
        var code = "";

        var twoFAEnabled = @json($twoFAEnabled);
        if (twoFAEnabled) {
            code = document.getElementById("code").value
        }

        $.ajax({
            url: "/user/private-key",
            type: "GET",
            data: {
                phrases: phrases,
                code: code
            },
            success: function(response) {
                var pvKey = response.privateKey;
                if (pvKey) {
                    $("#passwordConfirm").hide();
                    $("#continueButton").hide();
                    $('.key').html('<h3 class="private-key">' + pvKey + '</h3>')
                }
            },
            error: function(response) {
                var errors = response.responseJSON.errors;
                if (errors) {
                    Object.keys(errors).forEach((key) => {
                        if(key === 'code') {
                            $('.something-went-wrong').html(errors[key]);
                        } else {
                            if (twoFAEnabled) {
                                $("#nextButton").show();
                                $("#continueButton").hide();
                            } else {
                                $("#nextButton").hide();
                                $("#continueButton").show();
                            }
                            $("#passwordConfirm").show();
                            $("#passwordLabel").show();
                            $("#2faCodeLabel").hide();
                            $("#key").html("");
                            $("#something-went-wrong").html("");
                            $("#confirmPasswordModal #phrases").val("");
                            $("#confirmPasswordModal #code").val("");
                            $('.something-went-wrong').html(errors[key]);
                        }
                    })
                } else {
                    $('.something-went-wrong').html("Something went wrong. Please try again later!");
                }
            }
        });
    }
</script>
