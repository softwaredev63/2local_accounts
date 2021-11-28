<div id="userPhrasesWizard-1">
    <ul class="nav" style="display: none;">
        <li><a class="nav-link" href="#step-1-1">Step 1<br /><small>Generate phrase</small></a></li>
        <li><a class="nav-link" href="#step-2-1">Step 2<br /><small>Confirm phrase</small></a></li>
        <li><a class="nav-link" href="#step-3-1">Step 3<br /><small>Confirm password</small></a></li>
        <li><a class="nav-link" href="#step-4-1">Step 4<br /><small>Status</small></a></li>
    </ul>
    <div class="tab-content">
        <div id="step-1-1" class="tab-pane" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-12">
                    <h3>Generate phrase</h3>
                    <p class="text-primary text-center mt-2">Please memorize the whole phrase with all its details(ex. spaces between words)</p>
                    <p class="text-primary text-center mt-2">Losing your Secret Phrase will result in being unable in retrieving your private key and transferring tokens.</p>
                    <p class="text-primary text-center mt-2">Secret Phrase reset can be done after KYC and a payment of $50.</p>
                    <div class="show-phrases">
                        <button type="button" class="btn btn-fill generate-phrases">GENERATE PHRASE</button>
                        <h3 class="private-phrases"></h3>
                    </div>
                </div>
            </div>
        </div>
        <div id="step-2-1" class="tab-pane" role="tabpanel">
            <div class="row mb-3">
                <div class="col-md-12">
                    <p>Confirm phrase</p>
                    <label class="twolocal-input">
                        <input class="watchme" placeholder=" " type="text" name="phrases-1" for="phrases-1" required>
                        <span>Phrase</span>
                    </label>
                    <span class="something-went-wrong phrases">
                </div>
            </div>
        </div>
        <div id="step-3-1" class="tab-pane" role="tabpanel">
            <div class="row">
                <div class="col-md-12">
                    @if($isTemporarySecretPhrase)
                        <p>Confirm temporary secret phrase</p>
                    @else
                        <p>Confirm password</p>
                    @endif
                    <label class="twolocal-input">
                        @if($isTemporarySecretPhrase)
                            <input class="watchme" placeholder="" type="text" name="password-1" for="password-1" required data-for="secret-phrase-1">
                            <span>Secret phrase</span>
                        @else
                            <input class="watchme" placeholder="" type="password" name="password-1" for="password-1" required>
                            <span>Password</span>
                        @endif
                    </label>
                    <span class="something-went-wrong password">
                </div>
            </div>
        </div>
        <div id="step-4-1" class="tab-pane" role="tabpanel">
            <div class="row">
                <div class="col-md-12"> <span class="step-finished-message">Phrase has been saved with success!</span> </div>
            </div>
        </div>
    </div>
</div>
