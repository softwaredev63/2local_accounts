$(document).ready(function () {
    /* ========================================================================
    *   Scrolled Header
    * ======================================================================== */
    function resize() {
        if ($(document).scrollTop() > 10) {
            $("body").addClass("scrolled");
        }
        else {
            $("body").removeClass("scrolled");
        }
    }
    resize();
    $(window).resize(resize);

    $(window).scroll(function () {
        resize();
        $(window).resize(resize);
    });

    /* ========================================================================
    * Header Responsive Interaction
    * ======================================================================== */
    $('.navbar-toggler').on('click', function () {
        $('body').toggleClass('menu-open');
    });

    $(window).resize(function () {
        var vw = $(window).innerWidth();

        if ($('body').hasClass('menu-open') && vw >= 576) {
            $('body').removeClass('menu-open');
        }
    });

    /* ========================================================================
    * User set phrases - START
    * ======================================================================== */


    const redirectToBalance = (timeout) => {
        setTimeout(() => {
            window.location = `${window.location.origin}/balance`;
        }, timeout || 1000)
    }

    const userPhrasesModalSelector = "#generatePhrases",
        userPhrasesWizardSelector = "#userPhrasesWizard",
        userPhrasesTagSelector = "#userPhrasesWizard .private-phrases",
        userPhrasesGenerateButtonSelector = "#userPhrasesWizard .btn.generate-phrases",
        userPhrasesInputPhrasesSelector = "#userPhrasesWizard input[name='phrases']",
        userPhrasesInputPasswordSelector = "#userPhrasesWizard input[name='password']",
        userPhrasesNextButtonSelector = "#userPhrasesWizard .btn.sw-btn-next",
        userPhrasesPrevButtonSelector = "#userPhrasesWizard .btn.sw-btn-prev",
        userPhrasesErrorPhrasesSelector = "#userPhrasesWizard .something-went-wrong.phrases",
        userPhrasesErrorPasswordSelector = "#userPhrasesWizard .something-went-wrong.password";

    const userPhrasesModal = $(userPhrasesModalSelector),
        userPhrasesWizard = $(userPhrasesWizardSelector),
        userPhrasesTag = $(userPhrasesTagSelector),
        generatePhrasesButton = $(userPhrasesGenerateButtonSelector);


    const startStepIsDefined = typeof startStep != 'undefined';

    /**
     * Init user phrases wizard modal
     */
    $('#userPhrasesWizard').smartWizard({
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
            if ($(userPhrasesInputPasswordSelector).data('for') == 'secret-phrase') errorMsg = 'The temporary secret phrase is required';
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
    $('#userPhrasesWizard').on("showStep",function(e, anchorObject, stepIndex, stepDirection) {
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
            url: "/user/generate-phrases",
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
            url: "/user/set-phrases",
            type: "PUT",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                phrases: phrases,
                password: password,
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
    $(userPhrasesTagSelector).on("cut", function(e) {
        console.log('Cut not allowed!');
        e.preventDefault();
    });
    $(userPhrasesTagSelector).on("copy", function(e) {
        console.log('Copy not allowed!');
        e.preventDefault();
    });
    $(userPhrasesInputPhrasesSelector).on("paste", function(e) {
        console.log('Paste not allowed!');
        e.preventDefault();
    });

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
