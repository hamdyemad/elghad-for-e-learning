
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="/" class="logo logo-dark">
                            <span class="logo-sm">
                                <span class="text-logo-sm">الغد</span>
                            </span>
                            <span class="logo-lg">
                                <span class="text-logo-lg">منصة الغد التعليمية</span>
                            </span>
                        </a>

                        <a href="/" class="logo logo-light">
                            <span class="logo-sm">
                                <span class="text-logo-sm">الغد</span>
                            </span>
                            <span class="logo-lg">
                                <span class="text-logo-lg">منصة الغد التعليمية</span>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
                </div>

                <div class="d-flex">

                     <!-- App Search-->
                     {{-- <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="Search...">
                            <span class="fa fa-search"></span>
                        </div>
                    </form> --}}

                    {{-- <div class="dropdown d-inline-block d-lg-none ml-2">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0"
                            aria-labelledby="page-header-search-dropdown">
                    
                            <form class="p-3">
                                <div class="form-group m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div> --}}

                    {{-- <div class="dropdown d-none d-md-block ml-2">
                        <button type="button" class="btn header-item waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="mr-2" src="{{ URL::asset('/images/flags/us_flag.jpg')}}" alt="Header Language" height="16"> English <span class="mdi mdi-chevron-down"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <img src="{{ URL::asset('/images/flags/germany_flag.jpg')}}" alt="user-image" class="mr-1" height="12"> <span class="align-middle"> German </span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <img src="{{ URL::asset('/images/flags/italy_flag.jpg')}}" alt="user-image" class="mr-1" height="12"> <span class="align-middle"> Italian </span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <img src="{{ URL::asset('/images/flags/french_flag.jpg')}}" alt="user-image" class="mr-1" height="12"> <span class="align-middle"> French </span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <img src="{{ URL::asset('/images/flags/spain_flag.jpg')}}" alt="user-image" class="mr-1" height="12"> <span class="align-middle"> Spanish </span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <img src="{{ URL::asset('/images/flags/russia_flag.jpg')}}" alt="user-image" class="mr-1" height="12"> <span class="align-middle"> Russian </span>
                            </a>
                        </div>
                    </div> --}}

                    <div class="dropdown d-none d-lg-inline-block">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="mdi mdi-fullscreen font-size-24"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block ml-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="mdi mdi-bell"></i>
                            <span class="badge badge-danger badge-pill notification-badge" id="notification-count" style="display: none;">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0"
                            aria-labelledby="page-header-notifications-dropdown">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="m-0">الإشعارات</h5>
                                    </div>
                                    <div class="col-auto">
                                        <a href="javascript:void(0)" id="mark-all-read" class="text-primary small">تحديد الكل كمقروء</a>
                                    </div>
                                </div>
                            </div>
                            <div data-simplebar style="max-height: 230px;" id="notifications-list">
                                <div class="text-center p-3">
                                    <i class="mdi mdi-bell-off-outline text-muted"></i>
                                    <p class="text-muted mb-0">لا توجد إشعارات</p>
                                </div>
                            </div>
                            <div class="p-2 border-top">
                                <a class="btn btn-sm btn-link font-size-14 btn-block text-center" href="{{ route('dashboard.notifications.index') }}">
                                    عرض الكل
                                </a>
                            </div>
                        </div>
                    </div>
            

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="{{ URL::asset('/images/users/user-4.jpg')}}"
                                alt="Header Avatar">
                        </button>
                        <div class="dropdown-menu dropdown-menu-left">
                            <a class="dropdown-item text-danger" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="mdi mdi-power font-size-17 text-muted align-middle mr-1 text-danger"></i> 
                                {{ __('تسجيل الخروج') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </div>

                    {{-- <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                            <i class="mdi mdi-spin mdi-settings"></i>
                        </button>
                    </div> --}}
            
                </div>
            </div>
        </header>