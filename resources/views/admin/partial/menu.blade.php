<header>

    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            <div class="top-menu-left d-none d-lg-block">
                <ul class="nav">

                </ul>
            </div>

            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if ($unreadCount > 0)
                                <span class="alert-count">{{ $unreadCount }}</span>
                            @endif
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    @if ($unreadCount > 0)
                                        <a class="msg-header-clear ms-auto" href="{{ admin_url('notification/readall') }}">
                                            <p >Marks all as read</p>
                                        </a>
                                    @endif
                                </div>
                            </a>
                            <div class="header-notifications-list">
                                @if (count($notification_list) > 0)
                                    @foreach ($notification_list as $notification)
                                        <a class="dropdown-item" href="{{ admin_url('notification/view/'.encryptId($notification['id'])) }}"
                                            @if ($notification['read_status'] == 0) style="background-color: #ccc" @endif>
                                            <div class="d-flex align-items-center">
                                                <div class="notify bg-light-primary text-primary">
                                                    <img class="w-100 p-2" src="{{ $notification['icon'] }}"
                                                        alt="">
                                                </div>
                                                <div class="flex-grow-1" style="text-wrap: wrap;">
                                                    <p class="msg-info ">{{ $notification['title'] }}
                                                        <span
                                                            class="msg-time float-end">{{ $notification['time'] }}</span>
                                                    </p>
                                                    <p class="msg-info">{{ $notification['message'] }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="mt-5" style="text-align: center">
                                        <h6> We couldn't find1 any notification</h6>
                                    </div>

                                @endif

                            </div>
                            @if (count($notification_list) > 0)
                                <a href="{{admin_url('notification/list')}}">
                                    <div class="text-center msg-footer">View All Notifications</div>
                                </a>
                            @endif
                        </div>
                    </li>

                </ul>
            </div>
            <div class="user-box dropdown">
                <a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ url(profileImage(Auth::id() )) }}" class="user-img"
                        alt="user avatar">
                    <div class="user-info ps-3">
                        <p class="user-name mb-0"> {{ Auth::user()->name }} </p>
                        <p class="designattion mb-0">{{ Auth::user()?->designationInfo?->designation_name }} </p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href=" {{ admin_url('profile') }} "><i
                                class="bx bx-user"></i><span>Profile</span></a></li>
                    {{-- <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-cog"></i><span>Settings</span></a> --}}
                    </li>
                    <li>
                        <div class="dropdown-divider mb-0"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href=""
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class='bx bx-log-out-circle'></i><span>Logout</span></a>
                        <form id="logout-form" action="{{ admin_url('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

    </div>
</header>
