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
    <?php $activity_types = config('marketmartial.logging.activity_types.list'); ?>
    <script type="text/javascript">
        window.activity_types = {!! json_encode($activity_types) !!};
    </script>
    <script src="{{ mix('js/activity-log.js') }}"></script>
    @include('partials.toast_messsage')
    @yield('footer-scripts')
</body>
</html>
