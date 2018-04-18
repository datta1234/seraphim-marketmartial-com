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
    </head>
    <body>

      <div id="app">
        <div class="wrapper">
            @include('layouts.elements.navigation')
            {{-- Layout --}}
            <main class="user-content">
                <div class="container page-body-container mt-3">
                    {{--Alerts--}}
                    <div>
                        <div class="toast-holder"></div>
                        <div class="alert alert-danger" id="alert-custom-danger" style="display: none;">
                            <a class="close" aria-label="close" data-dismiss="alert" id="alert-custom-danger-close">&times;</a>
                            <p id="alert-custom-danger-content">

                            </p>
                        </div>
                        @include('layouts.elements.alerts')
                    </div>
                    @yield('content')
                </div>
            </main> {{-- Main end --}}
            <div class="push"></div>
        </div>
        <div class="push-down"></div>
    </div>
    <div class="site-footer mt-3">
        @include('layouts.elements.footer')
        @yield('script_footer')
    </div>
</body>
</html>
