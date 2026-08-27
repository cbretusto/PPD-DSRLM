@php
    $isLogin = false;
    if(isset($_SESSION['rapidx_user_id'])){
        $isLogin = true;
    }
@endphp

@if($isLogin)
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>SYSTEM NAME | @yield('title')</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" type="image/png" href="">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <!-- CSS LINKS -->
            @include('shared.css_links.css_links')
        </head>
        <body class="layout-fixed bg-body-tertiary">
            <div class="wrapper">
                <form action="{{ url('../RapidX') }}">
                {{-- <form action="{{ url('https:pornhub.com') }}"> --}}
                    <button type="submit" class="btn btn-warning position-absolute mt-3 ml-3"><i class="fa fa-xl fa-arrow-left"></i>&nbsp; <strong>Go back to RapidX and log-in again</strong></button>
                </form>
                <img src="{{ asset('public/images/no_access.jpg') }}" style="height:100%; width:100%;">
            </div>

            <!-- JS LINKS -->
            @include('shared.js_links.js_links')
            @yield('js_content')
        </body>
    </html>
@else
    <script type="text/javascript">
        window.location = "../RapidX/";
    </script>
@endif
