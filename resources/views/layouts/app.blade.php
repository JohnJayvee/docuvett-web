<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="theme-color" content="#000">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320"/>

        <meta name="apple-mobile-web-app-title" content="{{ config('app.name', 'Laravel') }}">
        <meta name="application-name" content="{{ config('app.name', 'Laravel') }}">

        {{-- CSRF Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="canonical" href="https://{{ request()->getHost() }}">

        {{-- Styles --}}
        <link href="{{ mix('styles/compiled/sass_part.css') }}" rel="stylesheet">

        <link rel="manifest" href="{{ asset('manifest.json') }}">

        {{-- Icons --}}
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

        {{-- Scripts --}}
        <!--<script src="https://maps.googleapis.com/maps/api/js?key={{-- config('google.maps.api_key') --}}&libraries=places" defer async></script>-->
        <script>
            window.@include('includes.common-js-variables')

            if (navigator && 'serviceWorker' in navigator) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker.register('/service-worker.js').then(function(registration) {
                    }).catch(function(err) {
                        if (Laravel.appDebug) {
                            console.log('ServiceWorker registration failed: ', err);
                        }
                    });
                });
            }
        </script>

        <style type="text/css">
            body {
                margin: 0;
            }
            .loading-screen > .loading-text > p {
                font-family: "Montserrat", sans-serif;
                -webkit-font-smoothing: antialiased;
            }

            #animated-gif {
                -webkit-animation: pulsate 1s linear infinite;
                -moz-animation: pulsate 1s linear infinite;
                animation: pulsate 1s linear infinite;
            }
            @-webkit-keyframes pulsate {
                0% {opacity: 0}
                50% {opacity: 1}
                100% {opacity: 0}
            }
            @keyframes pulsate {
                0% {opacity: 0}
                50% {opacity: 1}
                100% {opacity: 0}
            }
            @-moz-keyframes pulsate {
                0% {opacity: 0}
                50% {opacity: 1}
                100% {opacity: 0}
            }
        </style>
    </head>
    <body>
        <div id="app">
            <style>
                body {
                    overflow: hidden;
                }
            </style>
            <div class="loading-screen">
                <div class="loading-text"><p>Loading...</p></div>
                @yield('content')
            </div>
        </div>

        {{-- Transparent 1x1 GIF --}}
        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" id="animated-gif">
        {{-- Scripts --}}
        <script src="{{ config('broadcasting.connections.pusher.options.scheme').'://'.Request::getHost().':'.config('broadcasting.connections.pusher.options.port').'/socket.io/socket.io.js' }}"></script>
        <script async src="https://static.codepen.io/assets/embed/ei.js"></script>

        <script src="https://js.stripe.com/v3/"></script>
        <script src="{{ mix('js/compiled/manifest.js') }}"></script>
        <script src="{{ mix('js/compiled/vendor.js') }}"></script>
        <script src="{{ mix('js/compiled/app.js') }}"></script>
    </body>
</html>
