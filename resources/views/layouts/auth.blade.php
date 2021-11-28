<body class="login">
    @extends('layouts.master')

    @section('header')
    @include('partials.auth.header')
    @endsection

    @section('content')
    @yield('authContent')
    @endsection

    @section('footer')
    @include('partials.auth.footer')
    @endsection
</body>