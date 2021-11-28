@extends('layouts.main')

@section('page-title', 'Balance')

@section('mainContent')
<section class="intro">
    <div class="container-fluid">
        <div class="justify-content-center">
            <h1 class="balance-text">Balance</h1>
            @if($twoFAEnabled === false)
                <div class="alert alert-danger" role="alert">
                    Warning 2FA is turned off - Please secure your account on the <a href="/settings">settings</a> page!
                </div>
            @endif
            @if($isEmailVerified === false)
                <div class="alert alert-danger" role="alert">
                    <form action="/email/verification-notification" method="post">
                        @csrf
                        <label>In order to receive your tokens you first need to verify your email address:
                            <button class="btn btn-info" type=submit>Verify!</button>
                        </label>
                    </form>
                </div>
            @endif
            <div class="row">
                <div class="col-sm-2 affiliate">
                    <span class="default-text">Affiliate link: </span><span class="material-icons-outlined copy-btn m-1 d-block d-sm-none">content_copy</span>
                </div>
                <div class="col-sm-10 affiliate">
                    <span id="affiliate-link">{{$affiliateLink}}</span><span class="material-icons-outlined copy-btn m-1 d-none d-sm-block">content_copy</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="balance">
    <div class="container-fluid">
        @include('partials.crypto.balances')
    </div>
</section>

<section class="balance">
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-12 ">
                <p class="default-text table-title">Locked tokens</p>
            </div>
            <div class="col-xl-12 ">
                @include('partials.crypto.locked-balances')
            </div>
        </div>
    </div>
</section>

@include('modals.password-confirm')


<script type="application/javascript">

    function clearSelection() {
        if (window.getSelection) {
            window.getSelection().removeAllRanges();
        } else if (document.selection) {
            document.selection.empty();
        }
    }

    function copyToClipboard(textToCopy) {
        const elem = document.createElement("textarea");
        document.body.appendChild(elem);
        elem.value = textToCopy;
        elem.select();
        console.log(textToCopy);
        document.execCommand("copy");
        document.body.removeChild(elem);
        clearSelection();
    }

    $('.copy-this').on('click', function (event) {
        const addressToCopy = $(event.currentTarget).attr('data-address');
        copyToClipboard(addressToCopy);
    })

    $('.affiliate .copy-btn').click(function () {
        const copyText = document.getElementById("affiliate-link").innerText;
        copyToClipboard(copyText);
    })
</script>

<script type="application/javascript">
    $(function () {
        // when the modal is closed
        var twoFAEnabled = @json($twoFAEnabled);
        $('#confirmPasswordModal').on('hidden.bs.modal', function () {
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
        });
    });
</script>

@endsection
