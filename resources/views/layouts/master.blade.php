<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta charset="utf-8" />
    <title> @yield('title')  | {{ config('app.name', 'Elghad') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Lexa Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('/images/logo.webp')}}">
    
     <!-- headerCss -->
    @yield('headerCss')

    <!-- Bootstrap Css -->
    <link href="{{ URL::asset('/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ URL::asset('/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- App RTL Css-->
    <link href="{{ URL::asset('/css/app-rtl.min.css')}}" id="app-style" rel="stylesheet" type="text/css" />
    
    @stack('styles')

    <!-- Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

    <style>
        /* ===== RTL COMPREHENSIVE OVERRIDE ===== */
        html, body {
            overflow-x: hidden !important;
            direction: rtl !important;
            text-align: right !important;
        }

        /* 1. Main Content Shift */
        .main-content {
            margin-right: 240px !important;
            margin-left: 0 !important;
            transition: all 0.3s ease-out;
            padding: 20px !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
        }

        /* Make page-content grow to fill space, pushing footer down */
        .main-content .page-content {
            flex: 1 !important;
        }

        /* 2. Topbar Alignment */
        #page-topbar {
            right: 240px !important;
            left: 0 !important;
            width: auto !important;
            transition: all 0.3s ease-out;
            background: #fff !important;
            box-shadow: 0 2px 4px rgba(0,0,0,.08) !important;
        }

        /* Mobile Reset */
        @media (max-width: 991.98px) {
            .main-content {
                margin-right: 0 !important;
            }
            #page-topbar {
                right: 0 !important;
                left: 0 !important;
            }
            #vertical-menu-btn {
                display: block !important;
                color: #333 !important;
            }
        }

        .navbar-brand-box {
            position: fixed !important;
            right: 0 !important;
            top: 0 !important;
            width: 240px !important;
            z-index: 1005 !important;
            background: #2b3a4a !important;
            text-align: center !important;
        }

        @media (max-width: 991.98px) {
            .navbar-brand-box {
                position: static !important;
                width: auto !important;
                background: transparent !important;
                float: right !important;
                padding: 0 15px !important;
            }
            .logo-dark span, .logo-light span {
                color: #333 !important;
            }
        }

        /* 3. Sidebar RTL Fixes */
        .vertical-menu {
            right: 0 !important;
            left: auto !important;
            width: 240px !important;
            text-align: right !important;
            z-index: 1001 !important;
            background: #2b3a4a !important;
        }

        #side-menu,
        #side-menu ul {
            padding-right: 0 !important;
            padding-left: 0 !important;
            margin-right: 0 !important;
            margin-left: 0 !important;
            list-style: none !important;
        }

        #side-menu li a {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            padding: 12px 20px !important;
            color: #bbc4cc;
            transition: all 0.3s ease-out;
            text-decoration: none !important;
        }

        #side-menu li a i {
            margin-left: 12px !important;
            margin-right: 0 !important;
            font-size: 1.1rem;
            min-width: 1.5rem;
            text-align: center;
        }

        #side-menu li a:hover {
            color: #fff !important;
            background: rgba(255,255,255,0.05);
        }

        /* 4. Collapsed State Shift (Desktop) */
        @media (min-width: 992px) {
            body.vertical-collpsed .main-content,
            body.vertical-collapsed .main-content {
                margin-right: 70px !important;
            }
            body.vertical-collpsed #page-topbar,
            body.vertical-collapsed #page-topbar {
                right: 70px !important;
            }
            body.vertical-collpsed .vertical-menu,
            body.vertical-collapsed .vertical-menu,
            body.vertical-collpsed .navbar-brand-box,
            body.vertical-collapsed .navbar-brand-box {
                width: 70px !important;
            }
        }

        /* Hide "Ghost" items */
        .right-bar, .rightbar-overlay { display: none !important; }

        /* Global Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }

        /* RTL Breadcrumb */
        .breadcrumb {
            flex-wrap: wrap;
            direction: rtl;
        }

        .breadcrumb-item {
            display: inline-flex;
            align-items: center;
        }

        /* Remove default Bootstrap separator */
        .breadcrumb-item::before {
            content: "";
            display: none;
        }

        /* Add RTL separator after each non-last item - arrow points right (›) */
        .breadcrumb-item:not(:last-child)::after {
            content: "›";
            margin: 0 0.75rem;
            color: #6c7575;
        }

        .breadcrumb-item.active {
            font-weight: 500;
        }
    </style>
</head>

<body data-sidebar="dark">

    <!-- Preloader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">

         @include('layouts/partials/header')

         @include('layouts/partials/sidebar')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                  <!-- content -->
                   @yield('content')


                  @include('layouts/partials/footer')

                </div>
                <!-- end main content-->
            </div>
            <!-- END layout-wrapper -->

             @include('layouts/partials/rightbar')
   
            <!-- JAVASCRIPT -->
            <script src="{{ URL::asset('/libs/jquery/jquery.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/bootstrap/bootstrap.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/metismenu/metismenu.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/simplebar/simplebar.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/node-waves/node-waves.min.js')}}"></script>
            <script src="{{ URL::asset('/libs/jquery-sparkline/jquery-sparkline.min.js')}}"></script>

            <!-- footerScript -->
             @yield('footerScript')

            <!-- App js -->
            <script src="{{ URL::asset('/js/app.min.js')}}"></script>

            <!-- Global Datepicker Enhancer -->
            <script src="{{ URL::asset('/js/global/datepicker-enhancer.js') }}"></script>

            @stack('scripts')
            

            <script>
                // Hide preloader when page is fully loaded
                window.addEventListener('load', function() {
                    const preloader = document.getElementById('preloader');
                    if (preloader) {
                        preloader.style.transition = 'opacity 0.3s';
                        preloader.style.opacity = '0';
                        setTimeout(function() {
                            preloader.style.display = 'none';
                        }, 300);
                    }
                });
                
                // Fallback: hide after 2 seconds if load event doesn't fire
                setTimeout(function() {
                    const preloader = document.getElementById('preloader');
                    if (preloader && preloader.style.display !== 'none') {
                        preloader.style.display = 'none';
                    }
                }, 2000);
            </script>
</body>

</html>
