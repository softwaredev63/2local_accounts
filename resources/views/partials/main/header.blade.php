<script>
    if (typeof window.ethereum !== 'undefined') {
        console.log('MetaMask is installed!');
        ethereum.request({ method: 'eth_requestAccounts' });
    } else {
        console.log('MetaMask not installed!');
    }
</script>
<header class="navbar-expand-sm fixed-top" id="header">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-2 col-8 order-2 order-sm-1">
                <a class="logo" href="/">
                    <img src="assets/2local-logo.svg" class="img-fluid" alt="2Local logo">
                </a>
            </div>

            <div class="col-sm-9 col-2 order-1 order-sm-2">
                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbar2local" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center text-center" id="navbar2local">
                    <div class="navbar-nav me-auto mb-2 mb-sm-0 mr-auto">
                        <a href="{{ route('balance') }}" class="{{ Route::is('balance') ? 'active' : ''}}">Balance</a>
                    </div>

                    <send-token-button :tokens="{{json_encode($tokens)}}" :two-f-a-enabled="{{ Auth::user()->two_factor_secret ? 'true' : 'false' }}"></send-token-button>
                    @if(!Route::is('buy-page'))
                        <div>
                            <a class="btn btn-fill btn-buy" href="{{ route('buy-page') }}">Buy</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-sm-1 col-2 order-3 order-sm-4 text-right">
                <div class="user">
                    <button class="btn btn-user" type="button" id="userAcc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="https://picsum.photos/200" class="img-fluid" alt="user">
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userAcc">
                        <div class="info">
                            <div class="content">
                                <h4>{{ Auth::user()->name }}</h4>
                            </div>
                        </div>

                        <a href="{{ route('settings') }}" class="dropdown-item">
                            <span class="material-icons-outlined">
                                manage_accounts
                            </span>
                            <span>Settings</span>
                        </a>

                        <form id="settings-form" action="{{ route('settings') }}" method="GET" class="d-none">
                            @csrf
                        </form>

                        <hr>

                        <a href="{{ route('logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                            <span class="material-icons">logout</span>
                            <span>Log out</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
