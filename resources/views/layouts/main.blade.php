<body class="centered">
    @extends('layouts.master')

    @section('header')
    @include('partials.main.header')
    @endsection

    @section('content')
    @yield('mainContent')
    @endsection

    @section('footer')
    @include('partials.main.footer')
    @endsection
</body>