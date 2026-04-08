<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <title> @yield('title')  | {{ config('app.name', 'Elghad') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="منصة الغد - منصة تعليمية متكاملة" name="description" />
    <meta content="Ibtikar Tech" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('/images/logo.webp')}}"> 
    
    <!-- Bootstrap Css -->
    <link href="{{ URL::asset('/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ URL::asset('/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ URL::asset('/css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        /* ===== RTL OVERRIDE FOR AUTH PAGES ===== */
        html, body {
            direction: rtl !important;
            text-align: right !important;
        }

        .form-control {
            text-align: right;
        }

        .custom-checkbox .custom-control-input {
            float: right;
            margin-right: -1.5rem;
            margin-left: 0;
        }

        .custom-checkbox .custom-control-label {
            margin-right: 1.5rem;
            margin-left: 0;
        }

        .text-right {
            text-align: right !important;
        }

        .text-left {
            text-align: left !important;
        }

        .float-right {
            float: right !important;
        }

        .float-left {
            float: left !important;
        }

        /* Preloader RTL */
        #preloader {
            left: 0;
            right: auto;
        }
    </style>

</head>

<body>

    @yield('content')

    <!-- JAVASCRIPT -->
    <script src="{{ URL::asset('/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{ URL::asset('/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('/libs/metismenu/metismenu.min.js')}}"></script>
    <script src="{{ URL::asset('/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ URL::asset('/libs/node-waves/node-waves.min.js')}}"></script>
    <script src="{{ URL::asset('/libs/jquery-sparkline/jquery-sparkline.min.js')}}"></script>
    <!-- App js -->
    <script src="{{ URL::asset('/js/app.min.js')}}"></script>
</body>

</html>