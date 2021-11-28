@extends('layouts.main')

@section('page-title', 'Balance')

@section('mainContent')
<script>
    window.simplexAsyncFunction = function () {
        Simplex.init({public_key: "{{$simplexPublicKey}}"})
    };
</script>
<script src="{{$simplexScriptSrc}}" async></script>
<section class="intro">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 buy-container">
                <form id="simplex-form" class="width-500">
                    <div id="checkout-element" class="width-450">
                    </div>
                </form>
                <script src='{{$simplexFormScriptSrc}}' type="text/javascript"></script>
                <script>
                    var promise = window.simplex.createForm();
                    promise.then(function (data) {
                        var currencies = data.supportedCryptoCurrencies; console.log('>>>>>>>>> Simplex Supported Crypto Currencies', currencies);
                        window.simplex.updateCryptoCurrency('BNB');
                    });

                    window.simplex.on('submit', function(event){
                        $('#simplex-form').removeClass('width-500');
                        $('#checkout-element').removeClass('width-450');
                    });
                </script>
            </div>
        </div>
    </div>
</section>
@if (env('APP_ENV') === 'production')
    <script src="https://checkout.simplexcc.com/splx.js"></script>
@endif
@endsection
