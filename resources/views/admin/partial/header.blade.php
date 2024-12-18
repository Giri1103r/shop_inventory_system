<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">

        <li class="dropdown d-inline-block d-lg-none">
            <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown"
                href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-search noti-icon"></i>
            </a>
            <div class="dropdown-menu dropdown-lg dropdown-menu-end p-0">
                <form class="p-3">
                    <input type="text" class="form-control" placeholder="Search ..."
                        aria-label="Recipient's username">
                </form>
            </div>
        </li>

        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#"
                role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
  
                @if ($unreadCount > 0)
                    <span class="badge bg-danger rounded-circle noti-icon-badge">{{ $unreadCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">
                        @if ($unreadCount > 0)
                            <span class="float-end">
                                <a href="{{ admin_url('notification/readall') }}" class="text-dark">
                                    <small>Mark as Read</small>
                                </a>

                            </span>
                        @endif
                        Notifications
                    </h5>
                </div>

                <div class="noti-scroll" data-simplebar>

                    @if (count($notification_list) > 0)
                        @foreach ($notification_list as $notification)
                            <!-- item-->
                            <a href="{{ admin_url('notification/view/' . encryptId($notification['id'])) }}"
                                class="dropdown-item notify-item @if ($notification['read_status'] == 0) active @endif ">
                                <div class="notify-icon">
                                    <img src="{{ $notification['icon'] }}" class="img-fluid rounded-circle"
                                        alt="" />
                                </div>
                                <p class="notify-details">{{ $notification['title'] }}</p>
                                <p class="text-muted mb-0 user-msg">
                                    {{ $notification['message'] }}
                                </p>
                                <p class="text-muted mb-0 user-msg">
                                    <small> {{ $notification['time'] }}</small>
                                </p>

                            </a>
                        @endforeach
                    @else
                        <a href="javascript:void(0);" class="dropdown-item notify-item active">
                            <div class="mt-3" style="text-align: center">
                                <h6> We couldn't find any notification</h6>
                            </div>
                        </a>

                    @endif

                </div>

                <!-- All-->
                <a href="{{ admin_url('notification/list') }}"
                    class="dropdown-item text-center text-primary notify-item notify-all">
                    View all
                    <i class="fe-arrow-right"></i>
                </a>

            </div>
        </li>

        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown"
                href="#" role="button" aria-haspopup="false" aria-expanded="false">

                    <img src="{{ url(profileImage(Auth::id())) }}" alt="mage" class="rounded-circle" width="36"
                        height="36">

                <span class="pro-user-name ms-1">
                    {{ Auth::user()->name }} <i class="mdi mdi-chevron-down"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <!-- item-->
                <a href="{{ admin_url('profile') }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>My Account</span>
                </a>


                <div class="dropdown-divider"></div>

                <!-- item-->
                <a href="" onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    class="dropdown-item notify-item">
                    <i class="fe-log-out"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ admin_url('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>

            </div>
        </li>

        <li class="dropdown notification-list" style="display: none;">
            <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                <i class="fe-settings noti-icon"></i>
            </a>
        </li>

    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="{{ url('/dashboard') }}" class="logo logo-light text-center">
            <span class="logo-sm">
                <img src="{{ url('public/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" alt="" height="16">
            </span>
        </a>
        <a href="{{ url('/dashboard') }}" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="{{ url('public/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" alt="" height="16">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <li>
            <button class="button-menu-mobile disable-btn waves-effect">
                <i class="fe-menu"></i>
            </button>
        </li>

        <li>
            <h4 class="page-title-main"> @yield('title') </h4>
        </li>

    </ul>

    <div class="clearfix"></div>

</div>
