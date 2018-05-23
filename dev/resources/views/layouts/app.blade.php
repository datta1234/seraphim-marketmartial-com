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
    @yield('header-scripts')
    </head>
    <body>
        <div id="geoBackdrop"></div>
      <div id="app">
        <div class="wrapper">
            @include('layouts.elements.navigation')
            
            {{-- Layout --}}
            <main class="container">
                <div class="page-body-container mt-3">
                    {{--Alerts--}}
                    <div>
                        <div class="toast-holder"></div>
                        @include('layouts.elements.alerts')
                    </div>
                    @yield('content')
                </div>
            </main> {{-- Main end --}}
            <div class="push"></div>
        </div>
        <div class="push-down"></div>
    </div>

    <div class="site-sub-footer mt-5 mb-0">
        @yield('sub-footer')
    </div>
    
    @include('layouts.elements.footer')
    
     <!-- JavaScripts -->
    <script src="{{ asset('js/public.js') }}"></script>
    @yield('footer-scripts')
</body>
</html>
