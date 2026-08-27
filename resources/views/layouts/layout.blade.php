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
            <title>PPD - DSRLM | @yield('title')</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" type="image/png" href="">
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <!-- CSS LINKS -->
            @include('shared.css_links.css_links')
        </head>
        <input type="hidden" id="loginEmployeeId" value="<?php echo $_SESSION["rapidx_user_id"]; ?>">
        <body class="layout-fixed bg-body-tertiary">
            <div class="app-wrapper">
                @include('shared.pages.header')
                @include('shared.pages.nav')
                @include('shared.pages.footer')
                @yield('content_page')
            </div>

            <!-- JS LINKS -->
            @include('shared.js_links.js_links')
            @yield('js_content')
        </body>
        <script>
            let loginEmployeeId = $('#loginEmployeeId').val();
            // VerifyUser(loginEmployeeId);
        </script>
    </html>
@else
    <script type="text/javascript">
        window.location = "../RapidX/";
    </script>
@endif
