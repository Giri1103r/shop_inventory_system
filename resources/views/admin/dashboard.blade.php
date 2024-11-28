@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


    </style>
@endpush

@section('content')

    <div class="content-body default-height ">
        <div class="container-fluid" style="padding-top: 30px !important;padding-bottom: 20px !important;">
            <div class="row">
                <div class="col-xl-12">
                    <div class="coin-warpper d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center dz-head-title">
                            <h4 class="m-0 " style="padding-left: 10px;">Welcome Back {{ Auth::user()->name }}!
                            </h4>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card view_card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="redirectopermanage('{{ isset($masterLink[0]['link']) ? $masterLink[0]['link'] : '' }}')"
                                style="cursor: pointer;">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-primary text-primary">
                                            <!-- <i class="ti-user"></i> -->
                                            <svg width="50" height="50" viewBox="0 0 50 50" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M49.2858 19.9989C49.0608 19.832 48.8003 19.7193 48.5246 19.6697C48.249 19.6202 47.9655 19.6351 47.6966 19.7132L32.1438 24.3915V21.4274C32.1439 21.1502 32.0794 20.8767 31.9554 20.6288C31.8315 20.3808 31.6514 20.1652 31.4296 19.9989C31.2046 19.832 30.9441 19.7193 30.6685 19.6697C30.3928 19.6202 30.1094 19.6351 29.8404 19.7132L12.9841 24.7665L12.502 15.9634C12.4754 15.5084 12.2755 15.0808 11.9434 14.7687C11.6113 14.4565 11.1722 14.2834 10.7164 14.2849H3.57392C3.11812 14.2834 2.67904 14.4565 2.34693 14.7687C2.01482 15.0808 1.81491 15.5084 1.7883 15.9634L0.00268023 48.1045C-0.0105505 48.3487 0.0252984 48.5929 0.108107 48.823C0.190916 49.053 0.319003 49.2641 0.484797 49.4438C0.653716 49.6189 0.856208 49.7582 1.08017 49.8533C1.30414 49.9484 1.54498 49.9974 1.7883 49.9973H48.2144C48.6875 49.9959 49.1409 49.8073 49.4754 49.4727C49.81 49.1382 49.9986 48.6848 50 48.2117V21.4274C50.0001 21.1502 49.9356 20.8767 49.8116 20.6288C49.6876 20.3808 49.5076 20.1652 49.2858 19.9989ZM28.5726 46.4261H14.1983L13.1984 28.4449L28.5726 23.8201V46.4261ZM46.4288 46.4261H32.1438V28.1056L46.4288 23.8201V46.4261Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M25.0018 32.1411H17.8594V35.7124H25.0018V32.1411Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M25.0018 39.2836H17.8594V42.8548H25.0018V39.2836Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M42.8583 32.1411H35.7158V35.7124H42.8583V32.1411Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M42.8583 39.2836H35.7158V42.8548H42.8583V39.2836Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M5.3603 10.7137C5.36139 9.76689 5.73799 8.85917 6.40749 8.18967C7.07699 7.52016 7.98472 7.14356 8.93154 7.14247H9.92488C9.28163 8.22318 8.93869 9.45608 8.93154 10.7137V12.4993H12.5028V10.7137C12.5039 9.76689 12.8805 8.85917 13.55 8.18967C14.2195 7.52016 15.1272 7.14356 16.074 7.14247H30.359C32.2526 7.14044 34.0682 6.38728 35.4072 5.04825C36.7462 3.70921 37.4994 1.89368 37.5014 0H33.9302C33.9291 0.946817 33.5525 1.85454 32.883 2.52405C32.2135 3.19355 31.3058 3.57015 30.359 3.57124H29.3656C30.0089 2.49053 30.3518 1.25763 30.359 0H26.7877C26.7866 0.946817 26.41 1.85454 25.7405 2.52405C25.071 3.19355 24.1633 3.57015 23.2165 3.57124H8.93154C7.03786 3.57327 5.22232 4.32643 3.88329 5.66546C2.54426 7.0045 1.79109 8.82003 1.78906 10.7137V12.4993H5.3603V10.7137Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">
                                                {{ isset($masterLink[0]['name']) ? $masterLink[0]['name'] : '' }}
                                            </p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[0]['count']) ? $masterLink[0]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="redirectopermanage('{{ isset($masterLink[1]['link']) ? $masterLink[1]['link'] : '' }}')"
                                style="cursor: pointer;">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-warning text-warning">
                                            <svg width="50" height="35" viewBox="0 0 50 35" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11.4681 29.0458H5.50488V35.009H11.4681V29.0458Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M49.9995 35.009V14.3669H35.9997H18.3301V35.009H35.9997H49.9995ZM43.339 16.6605H47.0087C47.3941 16.6605 47.706 16.9724 47.706 17.3577C47.706 17.7431 47.3941 18.055 47.0087 18.055H43.339C42.9537 18.055 42.6418 17.7431 42.6418 17.3577C42.6418 16.9724 42.9537 16.6605 43.339 16.6605ZM43.339 22.165H47.0087C47.3941 22.165 47.706 22.477 47.706 22.8623C47.706 23.2476 47.3941 23.5595 47.0087 23.5595H43.339C42.9537 23.5595 42.6418 23.2476 42.6418 22.8623C42.6418 22.477 42.9537 22.165 43.339 22.165ZM43.339 27.6696H47.0087C47.3941 27.6696 47.706 27.9815 47.706 28.3668C47.706 28.7521 47.3941 29.0641 47.0087 29.0641H43.339C42.9537 29.0641 42.6418 28.7521 42.6418 28.3668C42.6418 27.9815 42.9537 27.6696 43.339 27.6696ZM35.9997 16.6605H39.6693C40.0547 16.6605 40.3666 16.9724 40.3666 17.3577C40.3666 17.7431 40.0547 18.055 39.6693 18.055H35.9997C35.6143 18.055 35.3024 17.7431 35.3024 17.3577C35.3024 16.9724 35.6143 16.6605 35.9997 16.6605ZM35.9997 22.165H39.6693C40.0547 22.165 40.3666 22.477 40.3666 22.8623C40.3666 23.2476 40.0547 23.5595 39.6693 23.5595H35.9997C35.6143 23.5595 35.3024 23.2476 35.3024 22.8623C35.3024 22.477 35.6143 22.165 35.9997 22.165ZM24.9906 29.0457H21.3209C20.9356 29.0457 20.6236 28.7338 20.6236 28.3485C20.6236 27.9632 20.9356 27.6512 21.3209 27.6512H24.9906C25.3759 27.6512 25.6878 27.9632 25.6878 28.3485C25.6878 28.7338 25.3759 29.0457 24.9906 29.0457ZM24.9906 23.5412H21.3209C20.9356 23.5412 20.6236 23.2293 20.6236 22.8439C20.6236 22.4586 20.9356 22.1467 21.3209 22.1467H24.9906C25.3759 22.1467 25.6878 22.4586 25.6878 22.8439C25.6878 23.2293 25.3759 23.5412 24.9906 23.5412ZM24.9906 18.0366H21.3209C20.9356 18.0366 20.6236 17.7247 20.6236 17.3394C20.6236 16.9541 20.9356 16.6422 21.3209 16.6422H24.9906C25.3759 16.6422 25.6878 16.9541 25.6878 17.3394C25.6878 17.7247 25.3759 18.0366 24.9906 18.0366ZM32.33 29.0457H28.6603C28.2749 29.0457 27.963 28.7338 27.963 28.3485C27.963 27.9632 28.2749 27.6512 28.6603 27.6512H32.33C32.7153 27.6512 33.0272 27.9632 33.0272 28.3485C33.0272 28.7338 32.7153 29.0457 32.33 29.0457ZM32.33 23.5412H28.6603C28.2749 23.5412 27.963 23.2293 27.963 22.8439C27.963 22.4586 28.2749 22.1467 28.6603 22.1467H32.33C32.7153 22.1467 33.0272 22.4586 33.0272 22.8439C33.0272 23.2293 32.7153 23.5412 32.33 23.5412ZM32.33 18.0366H28.6603C28.2749 18.0366 27.963 17.7247 27.963 17.3394C27.963 16.9541 28.2749 16.6422 28.6603 16.6422H32.33C32.7153 16.6422 33.0272 16.9541 33.0272 17.3394C33.0272 17.7247 32.7153 18.0366 32.33 18.0366ZM35.3208 28.3485C35.3208 27.9632 35.6327 27.6512 36.018 27.6512H39.6877C40.073 27.6512 40.3849 27.9632 40.3849 28.3485C40.3849 28.7338 40.073 29.0457 39.6877 29.0457H36.018C35.6143 29.0457 35.3208 28.7338 35.3208 28.3485Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M18.3486 6.33022V12.9907H35.321V6.78893L18.3486 0V6.33022Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M0 12.9907H16.9723V6.78893L0 0V12.9907Z" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M0 35.009H4.12841V28.3485C4.12841 27.9632 4.44033 27.6512 4.82565 27.6512H12.165C12.5504 27.6512 12.8623 27.9632 12.8623 28.3485V35.009H16.9907V14.3669H0V35.009Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">
                                                {{ isset($masterLink[1]['name']) ? $masterLink[1]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[1]['count']) ? $masterLink[1]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3  col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="redirectopermanage('{{ isset($masterLink[2]['link']) ? $masterLink[2]['link'] : '' }}')"
                                style="cursor: pointer;">
                                <div class="card-body  p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-danger text-danger">

                                            <svg width="50" height="39" viewBox="0 0 50 39" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M50 35.049V38.2353H0V0H3.125V35.049H50ZM40.625 9.55882L46.875 31.8627H6.25V17.5245L17.1875 3.18627L31.25 17.5245L40.625 9.55882Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">
                                                {{ isset($masterLink[2]['name']) ? $masterLink[2]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[2]['count']) ? $masterLink[2]['count'] : 0 }}</h4>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="redirectopermanage('{{ isset($masterLink[3]['link']) ? $masterLink[3]['link'] : '' }}')"
                                style="cursor: pointer;">
                                <div class="card-body p-4">
                                    <div class="media ai-icon">
                                        <span class="me-3 bgl-success text-success">
                                            <svg width="34" height="50" viewBox="0 0 34 50" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M20 6.66667V0H13.3333V6.66667H0V50H33.3333V6.66667H20ZM16.6667 1.66667C17.1087 1.66667 17.5326 1.84226 17.8452 2.15482C18.1577 2.46738 18.3333 2.89131 18.3333 3.33333C18.3333 3.77536 18.1577 4.19928 17.8452 4.51184C17.5326 4.82441 17.1087 5 16.6667 5C16.2246 5 15.8007 4.82441 15.4882 4.51184C15.1756 4.19928 15 3.77536 15 3.33333C15 2.89131 15.1756 2.46738 15.4882 2.15482C15.8007 1.84226 16.2246 1.66667 16.6667 1.66667ZM30 46.6667H3.33333V10H13.3333V13.3333H20V10H30V46.6667Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M16.667 16.6666C12.0637 16.6666 8.33366 20.3966 8.33366 25C8.33366 29.6033 12.0637 33.3333 16.667 33.3333C21.2703 33.3333 25.0003 29.6033 25.0003 25C25.0003 20.3966 21.2703 16.6666 16.667 16.6666ZM16.667 30C13.9103 30 11.667 27.7566 11.667 25C11.667 22.2433 13.9103 20 16.667 20C19.4237 20 21.667 22.2433 21.667 25C21.667 27.7566 19.4237 30 16.667 30ZM10.0003 36.6666C9.11627 36.6666 8.26842 37.0178 7.6433 37.6429C7.01818 38.2681 6.66699 39.1159 6.66699 40V43.3333H26.667V40C26.667 39.1159 26.3158 38.2681 25.6907 37.6429C25.0656 37.0178 24.2177 36.6666 23.3337 36.6666H10.0003Z"
                                                    fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1">
                                                {{ isset($masterLink[3]['name']) ? $masterLink[3]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[3]['count']) ? $masterLink[3]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            {{-- <div class="row">

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-4">Total Revenue</h4>

                        <div class="widget-chart-1">
                            <div class="widget-chart-box-1 float-start" dir="ltr">
                                <input data-plugin="knob" data-width="70" data-height="70" data-fgColor="#f05050 "
                                    data-bgColor="#F9B9B9" value="58" data-skin="tron" data-angleOffset="180"
                                    data-readOnly=true data-thickness=".15" />
                            </div>

                            <div class="widget-detail-1 text-end">
                                <h2 class="fw-normal pt-2 mb-1"> 256 </h2>
                                <p class="text-muted mb-1">Revenue today</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-3">Sales Analytics</h4>

                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="badge bg-success rounded-pill float-start mt-3">32% <i
                                        class="mdi mdi-trending-up"></i> </span>
                                <h2 class="fw-normal mb-1"> 8451 </h2>
                                <p class="text-muted mb-3">Revenue today</p>
                            </div>
                            <div class="progress progress-bar-alt-success progress-sm">
                                <div class="progress-bar bg-success" role="progressbar" aria-valuenow="77" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 77%;">
                                    <span class="visually-hidden">77% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-4">Statistics</h4>

                        <div class="widget-chart-1">
                            <div class="widget-chart-box-1 float-start" dir="ltr">
                                <input data-plugin="knob" data-width="70" data-height="70" data-fgColor="#ffbd4a"
                                    data-bgColor="#FFE6BA" value="80" data-skin="tron" data-angleOffset="180"
                                    data-readOnly=true data-thickness=".15" />
                            </div>
                            <div class="widget-detail-1 text-end">
                                <h2 class="fw-normal pt-2 mb-1"> 4569 </h2>
                                <p class="text-muted mb-1">Revenue today</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-3">Daily Sales</h4>

                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="badge bg-pink rounded-pill float-start mt-3">32% <i
                                        class="mdi mdi-trending-up"></i> </span>
                                <h2 class="fw-normal mb-1"> 158 </h2>
                                <p class="text-muted mb-3">Revenue today</p>
                            </div>
                            <div class="progress progress-bar-alt-pink progress-sm">
                                <div class="progress-bar bg-pink" role="progressbar" aria-valuenow="77"
                                    aria-valuemin="0" aria-valuemax="100" style="width: 77%;">
                                    <span class="visually-hidden">77% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0">Daily Sales</h4>

                        <div class="widget-chart text-center">
                            <div id="morris-donut-example" dir="ltr" style="height: 245px;" class="morris-chart">
                            </div>
                            <ul class="list-inline chart-detail-list mb-0">
                                <li class="list-inline-item">
                                    <h5 style="color: #ff8acc;"><i class="fa fa-circle me-1"></i>Series A
                                    </h5>
                                </li>
                                <li class="list-inline-item">
                                    <h5 style="color: #5b69bc;"><i class="fa fa-circle me-1"></i>Series B
                                    </h5>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>
                        <h4 class="header-title mt-0">Statistics</h4>
                        <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart"></div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>
                        <h4 class="header-title mt-0">Total Revenue</h4>
                        <div id="morris-line-example" dir="ltr" style="height: 280px;" class="morris-chart"></div>
                    </div>
                </div>
            </div><!-- end col -->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body widget-user">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-lg me-3">
                                <img src="{{ url('public/assets/images/users/user-3.jpg') }}"
                                    class="img-fluid rounded-circle" alt="user">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="mt-0 mb-1">Chadengle</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                                <small class="text-warning"><b>Admin</b></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body widget-user">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-lg me-3">
                                <img src="{{ url('public/assets/images/users/user-2.jpg') }}"
                                    class="img-fluid rounded-circle" alt="user">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="mt-0 mb-1"> Michael Zenaty</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                                <small class="text-pink"><b>Support Lead</b></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body widget-user">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-lg me-3">
                                <img src="{{ url('public/assets/images/users/user-1.jpg') }}"
                                    class="img-fluid rounded-circle" alt="user">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="mt-0 mb-1">Stillnotdavid</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                                <small class="text-success"><b>Designer</b></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body widget-user">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 avatar-lg me-3">
                                <img src="{{ url('public/assets/images/users/user-10.jpg') }}"
                                    class="img-fluid rounded-circle" alt="user">
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h5 class="mt-0 mb-1">Tomaslau</h5>
                                <p class="text-muted mb-2 font-13 text-truncate">coderthemes@gmail.com</p>
                                <small class="text-info"><b>Developer</b></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mb-3">Inbox</h4>

                        <div class="inbox-widget">

                            <div class="inbox-item">
                                <a href="#">
                                    <div class="inbox-item-img"><img
                                            src="{{ url('public/assets/images/users/user-1.jpg') }}"
                                            class="rounded-circle" alt=""></div>
                                    <h5 class="inbox-item-author mt-0 mb-1">Chadengle</h5>
                                    <p class="inbox-item-text">Hey! there I'm available...</p>
                                    <p class="inbox-item-date">13:40 PM</p>
                                </a>
                            </div>

                            <div class="inbox-item">
                                <a href="#">
                                    <div class="inbox-item-img"><img
                                            src="{{ url('public/assets/images/users/user-2.jpg') }}"
                                            class="rounded-circle" alt=""></div>
                                    <h5 class="inbox-item-author mt-0 mb-1">Tomaslau</h5>
                                    <p class="inbox-item-text">I've finished it! See you so...</p>
                                    <p class="inbox-item-date">13:34 PM</p>
                                </a>
                            </div>

                            <div class="inbox-item">
                                <a href="#">
                                    <div class="inbox-item-img"><img
                                            src="{{ url('public/assets/images/users/user-3.jpg') }}"
                                            class="rounded-circle" alt=""></div>
                                    <h5 class="inbox-item-author mt-0 mb-1">Stillnotdavid</h5>
                                    <p class="inbox-item-text">This theme is awesome!</p>
                                    <p class="inbox-item-date">13:17 PM</p>
                                </a>
                            </div>

                            <div class="inbox-item">
                                <a href="#">
                                    <div class="inbox-item-img"><img
                                            src="{{ url('public/assets/images/users/user-4.jpg') }}"
                                            class="rounded-circle" alt=""></div>
                                    <h5 class="inbox-item-author mt-0 mb-1">Kurafire</h5>
                                    <p class="inbox-item-text">Nice to meet you</p>
                                    <p class="inbox-item-date">12:20 PM</p>
                                </a>
                            </div>

                            <div class="inbox-item">
                                <a href="#">
                                    <div class="inbox-item-img"><img
                                            src="{{ url('public/assets/images/users/user-5.jpg') }}"
                                            class="rounded-circle" alt=""></div>
                                    <h5 class="inbox-item-author mt-0 mb-1">Shahedk</h5>
                                    <p class="inbox-item-text">Hey! there I'm available...</p>
                                    <p class="inbox-item-date">10:15 AM</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="dropdown float-end">
                            <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="mdi mdi-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Another action</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Something else</a>
                                <!-- item-->
                                <a href="javascript:void(0);" class="dropdown-item">Separated link</a>
                            </div>
                        </div>

                        <h4 class="header-title mt-0 mb-3">Latest Projects</h4>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Project Name</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Assign</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Adminto Admin v1</td>
                                        <td>01/01/2017</td>
                                        <td>26/04/2017</td>
                                        <td><span class="badge bg-danger">Released</span></td>
                                        <td>Coderthemes</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Adminto Frontend v1</td>
                                        <td>01/01/2017</td>
                                        <td>26/04/2017</td>
                                        <td><span class="badge bg-success">Released</span></td>
                                        <td>Adminto admin</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Adminto Admin v1.1</td>
                                        <td>01/05/2017</td>
                                        <td>10/05/2017</td>
                                        <td><span class="badge bg-pink">Pending</span></td>
                                        <td>Coderthemes</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Adminto Frontend v1.1</td>
                                        <td>01/01/2017</td>
                                        <td>31/05/2017</td>
                                        <td><span class="badge bg-purple">Work in Progress</span>
                                        </td>
                                        <td>Adminto admin</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Adminto Admin v1.3</td>
                                        <td>01/01/2017</td>
                                        <td>31/05/2017</td>
                                        <td><span class="badge bg-warning">Coming soon</span></td>
                                        <td>Coderthemes</td>
                                    </tr>

                                    <tr>
                                        <td>6</td>
                                        <td>Adminto Admin v1.3</td>
                                        <td>01/01/2017</td>
                                        <td>31/05/2017</td>
                                        <td><span class="badge bg-primary">Coming soon</span></td>
                                        <td>Adminto admin</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->

        </div> --}}
            <!-- end row -->
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        function redirectopermanage(link) {
            var url = "{{ admin_url('') }}" + link;
            window.location.href = url;
        }
    </script>
@endpush
