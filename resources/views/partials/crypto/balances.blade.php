<div class="col balances-container">
    <div class="d-none d-sm-none d-md-block">
        <div class="row balance-tab">
            <div class="col-3 column">
                <span class="text-label">Tokens</span>
            </div>
            <div class="col-2 column">
                <span class="text-label">Balance</span>
            </div>
            <div class="col-5 column">
                <span class="text-label">Address</span>
            </div>
            <div class="col-2 col-lg-1 column">
                <span class="text-label m-auto">Actions</span>
            </div>
        </div>
    </div>
    @foreach($tokens as $token)
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
            <div class="col-8 col-sm-5 column">
                <span class="default-text address-container">{{$token['address']}}</span>
            </div>
            <div class="col-4 col-sm-2 col-lg-1 column">
                <div class="tokens-column action">
                    <div class="btn btn-info balance-button copy-this" data-address="{{$token['address']}}">
                        <span class="material-icons-outlined" title="Copy address">content_copy</span>
                    </div>
                    <div class="btn btn-info balance-button" data-toggle="modal" data-target="#confirmPasswordModal">
                        <span class="material-icons-outlined" title="Show private key">lock</span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
