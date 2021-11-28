<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1">

    <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- BEGIN Meta info for Open Graph -->
    <meta name="keywords" content="{{ config('app.keywords') }}">
    <meta name="description" content="{{ config('app.description') }}">
    
    <meta property="og:title" content="{{ config('app.name') }} @yield('page-title')" />
    <meta property="og:description" content="{{ config('app.description') }}" />
    <meta property="og:image" content="{{ asset(config('app.image')) }}" />
    <meta property="og:type" content="website" />

    <meta name="twitter:title" content="{{ config('app.name') }} @yield('page-title')" />
    <meta name="twitter:image" content="{{ asset(config('app.image')) }}" />
    <meta name="twitter:description" content="{{ config('app.description') }}" />
    <meta name="twitter:card" content="summary" />

    <!-- END Meta info for Open Graph -->

    <title>@yield('page-title', '2local')</title>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-4.6.0.min.css') }}">
    <!-- SmartWizard -->
    <link href="{{ asset('css/smart_wizard_all.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Jquery + popper + bootstrap -->
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/popper-1.16.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-4.6.0.min.js') }}"></script>
</head>

<body>
    <div>
        @yield('header')

        <main role="main">
        @yield('content')
        </main>

        @yield('footer')
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/jquery-smart-wizard.min.js') }}" type="text/javascript"></script>

    <!-- Custom JavaScript -->
     <script type="text/javascript" src="{{mix('js/app.js')}}"></script>

</body>

</html>
