<div class="col balances-container">
    <div class="d-none d-sm-none d-md-block">
        <div class="row balance-tab">
            <div class="col-3 column">
                <span class="text-label">Tokens</span>
            </div>
            <div class="col-2 column">
                <span class="text-label">Balance</span>
            </div>
            <div class="col-7 column">
                <span class="text-label">Address</span>
            </div>
        </div>
    </div>
    @foreach($lockedTokens as $token)
        <div class="row">
            <div class="col-12">
                <div class="line"></div>
            </div>
        </div>
        <div class="row balance-tab">
            <div class="col-8 col-sm-3 column">
                <div class="tokens-column">
                    <span class="logo">
                        <img src="{{$token['logoSrc']}}" alt="2Local logo">
                    </span>
                    <span class="symbol">{{$token['symbol']}}</span>
                    <span class="name d-sm-none d-md-none d-lg-block">{{$token['name']}}</span>
                </div>
            </div>
            <div class="col-4 col-sm-2 col-md-2 column">
                <span class="default-text">{{$token['balance']}}</span>
            </div>
            <div class="col-sm-7 column">
                <span class="default-text address-container">{{$token['address']}}</span>
            </div>
        </div>
    @endforeach
</div>
