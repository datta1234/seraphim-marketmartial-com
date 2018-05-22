<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Market Martial</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
            ]) !!};
        </script>
    <script src="{{ asset('js/fss.js') }}"></script>
    </head>
    <body>
        <div id="trade_app">
            @include('layouts.elements.navigation')
            
            @yield('content')
        </div>
    
    @include('layouts.elements.trade_footer')
    
     <!-- JavaScripts -->
    
</body>
</html>
