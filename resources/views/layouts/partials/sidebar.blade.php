 <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">القائمة الرئيسية</li>

                        <li>
                            <a href="/dashboard/index" class="waves-effect">
                                <i class="mdi mdi-view-dashboard"></i>
                                <span>لوحة التحكم</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard.categories.index') }}" class="waves-effect">
                                <i class="mdi mdi-folder-multiple"></i>
                                <span>الأقسام</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard.packages.index') }}" class="waves-effect">
                                <i class="mdi mdi-package-variant-closed"></i>
                                <span>الباقات</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard.courses.index') }}" class="waves-effect">
                                <i class="mdi mdi-book-open-page-variant"></i>
                                <span>الكورسات</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard.lessons.index') }}" class="waves-effect">
                                <i class="mdi mdi-play-circle-outline"></i>
                                <span>الدروس</span>
                            </a>
                        </li>

                        <li class="menu-title">المستخدمين</li>

                        <li>
                            <a href="{{ route('dashboard.students.index') }}" class="waves-effect">
                                <i class="mdi mdi-account-group"></i>
                                <span>الطلاب</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('dashboard.instructors.index') }}" class="waves-effect">
                                <i class="mdi mdi-account-tie"></i>
                                <span>المحاضرين</span>
                            </a>
                        </li>

                        <li class="menu-title">الإعدادات</li>

                        <li>
                            <a href="{{ route('dashboard.settings.edit') }}" class="waves-effect">
                                <i class="mdi mdi-settings-outline"></i>
                                <span>إعدادات الموقع</span>
                            </a>
                        </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->