<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if(Auth::check())
        <meta name="viewer-type" content="{{ Auth::user()->isViewer() }}">
    @endif
    
    <title>Market Martial</title>
    @yield('favicon', \View::make('layouts.elements.favicon'))

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    </head>
    <body id="canvas_body" class="canvas-body">
        <div id="canvas_app">
            @include('layouts.elements.navigation', [ 'layout' => [ 'fluid' => true ] ])
            
            @yield('content')
        </div>
    
    @include('layouts.elements.trade_footer', [ 'layout' => [ 'fluid' => true ] ])
    
     <!-- JavaScripts -->
    <script src="{{ mix('js/canvas.js') }}"></script>
    @include('partials.toast_messsage')
    @yield('footer-scripts')
</body>
</html>
