@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>
        .widget-user-header {
            padding: 10px;
            color: #fff;
        }

        .nav ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 8px;
        }

        .nav ul a {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            padding: 6px 10px;
            background: #f5f5f5;
            border-radius: 4px;
            color: #333;
            font-size: 14px;
            transition: background 0.2s ease;
        }

        . nav ul a:hover {
            background: #ddd;
        }

        .badge {
            background-color: #ffc107;
            color: #000;
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 10px;
        }
    </style>
@endpush

@section('content')

    <div class="content-body default-height ">
        <div class="container-fluid" style="padding-top: 30px !important;padding-bottom: 20px !important;">
            <div class="row">
                <div class="col-xl-12">
                    <div class="coin-warpper d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center dz-head-title">
                            <h4 class="m-0 " style="padding-left: 10px;">Welcome {{ Auth::user()->name }}!
                            </h4>
                        </div>
                        <div>
                            <x-button-filter dataId="" class="search" href=""></x-button-filter>

                        </div>
                    </div>
                </div>
            </div>

            <!--Filter -->
            <div id="search" class="collapse card">
                <form action="" id="formsearch">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 mb-3 form-input">
                                    <label for="company_id" class="form-label ">Company Name</label>
                                    <select name="company_id" id="company_id" class=" form-control single-select"
                                        style="width: 100%">
                                        <option value="">Select Company Name</option>
                                        @foreach ($companyList as $company)
                                            <option value="{{ encryptId($company->id) }}">
                                                {{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-input">
                                    <label for="fromDate" class="form-label">{{ __('From Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required class="form-control todaymaxdatepicker"
                                            id="fromDate" name="fromDate" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 form-input">
                                    <label for="toDate" class="form-label">{{ __('To Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required class="form-control todaymaxdatepicker"
                                            id="toDate" name="toDate" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <button type="button" id="searchform" onclick="filterDashboard();"
                                        class="btn btn-primary mt-4">Search</button>
                                    <button type="reset" id="resetform" class="btn btn-danger mt-4">Reset</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card dashboard_card mt-2">

                <div class="card-body">


                    <div class="row gx-0">

                        <div class="col-md-2 gx-0">
                            <div class="card bg-yellow" style="width: 200px; height: 300px;">
                                <div class="card-header mt-2 ">
                                    <h3 class="m-0">Operating Management</h3>
                                </div>

                                <div class="card-body" style="overflow: hidden;">
                                    <div class="container-fluid p-0">
                                        @foreach ($masterLink as $item)
                                            <div class="row g-0 mb-1">
                                                <div class="col">
                                                    <div class="d-flex justify-content-between align-items-center p-0.5">
                                                        <a href="{{ admin_url($item['link']) }}"
                                                            class="text-decoration-none text-dark m-0">
                                                            {{ $item['name'] ?? '' }}
                                                        </a>
                                                        <span class="badge bg-primary">{{ $item['count'] ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-10 gx-0">
                            <div class="row gx-0">
                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[0]['link']) ? $moduleLink[0]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #a3f0dc 0%, #83aeee 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="50"
                                                        viewBox="0 0 64 64" fill="none">

                                                        <rect x="14" y="8" width="36" height="48" rx="4"
                                                            ry="4" fill="#e0e0e0" stroke="#333" stroke-width="2" />
                                                        <rect x="24" y="4" width="16" height="8" rx="2"
                                                            ry="2" fill="#ccc" stroke="#333"
                                                            stroke-width="2" />


                                                        <path d="M32 32c-6.627 0-12 5.373-12 12h24c0-6.627-5.373-12-12-12z"
                                                            fill="#fcd34d" stroke="#333" stroke-width="2" />
                                                        <path d="M26 32v-4c0-3.314 2.686-6 6-6s6 2.686 6 6v4"
                                                            stroke="#333" stroke-width="2" fill="none" />


                                                        <line x1="20" y1="44" x2="44"
                                                            y2="44" stroke="#666" stroke-width="2" />
                                                        <line x1="20" y1="50" x2="40"
                                                            y2="50" stroke="#666" stroke-width="2" />
                                                    </svg>

                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[0]['name']) ? $moduleLink[0]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[0]['count']) ? $moduleLink[0]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[1]['link']) ? $moduleLink[1]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);">
                                        <div class="card-body"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="50"
                                                        viewBox="0 0 64 64" fill="none">
                                                        <!-- Helmet -->
                                                        <path d="M32 28c-6.627 0-12 5.373-12 12h24c0-6.627-5.373-12-12-12z"
                                                            fill="#fcd34d" stroke="#333" stroke-width="2" />
                                                        <path d="M26 28v-4c0-3.314 2.686-6 6-6s6 2.686 6 6v4"
                                                            stroke="#333" stroke-width="2" fill="none" />

                                                        <!-- Prohibition Slash -->
                                                        <circle cx="32" cy="40" r="16" stroke="#e11d48"
                                                            stroke-width="3" fill="none" />
                                                        <line x1="22" y1="30" x2="42"
                                                            y2="50" stroke="#e11d48" stroke-width="3" />

                                                        <!-- Optional text hint -->
                                                        <!-- <text x="14" y="58" font-size="8" fill="#444">Exempt</text> -->
                                                    </svg>

                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[1]['name']) ? $moduleLink[1]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[1]['count']) ? $moduleLink[1]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[2]['link']) ? $moduleLink[2]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #d2f8bc 0%, #c3cfe2 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="100" height="50"
                                                        viewBox="0 0 64 64" fill="none">
                                                        <!-- Clipboard -->
                                                        <rect x="14" y="10" width="36" height="44" rx="4"
                                                            ry="4" fill="#f3f4f6" stroke="#4b5563"
                                                            stroke-width="2" />
                                                        <rect x="24" y="6" width="16" height="8" rx="2"
                                                            ry="2" fill="#d1d5db" stroke="#4b5563"
                                                            stroke-width="2" />

                                                        <!-- Checkmark -->
                                                        <circle cx="32" cy="44" r="10" fill="#10b981"
                                                            stroke="#065f46" stroke-width="2" />
                                                        <path d="M28 44l3 3 5-5" stroke="white" stroke-width="2"
                                                            fill="none" />

                                                        <!-- Form lines -->
                                                        <line x1="20" y1="28" x2="44"
                                                            y2="28" stroke="#9ca3af" stroke-width="2" />
                                                        <line x1="20" y1="34" x2="44"
                                                            y2="34" stroke="#9ca3af" stroke-width="2" />
                                                    </svg>


                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[2]['name']) ? $moduleLink[2]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[2]['count']) ? $moduleLink[2]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[3]['link']) ? $moduleLink[3]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #FFC796 0%, #FF6B95 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"
                                                        viewBox="0 0 64 64" fill="none">
                                                        <!-- Book base -->
                                                        <path d="M10 48V16a4 4 0 0 1 4-4h36a4 4 0 0 1 4 4v32"
                                                            fill="#fef3c7" stroke="#92400e" stroke-width="2" />
                                                        <path d="M10 48h44" stroke="#92400e" stroke-width="2" />

                                                        <!-- Graduation cap -->
                                                        <path d="M32 10L16 16l16 6 16-6-16-6z" fill="#facc15"
                                                            stroke="#92400e" stroke-width="2" />
                                                        <line x1="32" y1="16" x2="32"
                                                            y2="26" stroke="#92400e" stroke-width="2" />
                                                        <circle cx="32" cy="26" r="2" fill="#92400e" />
                                                    </svg>


                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[3]['name']) ? $moduleLink[3]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[3]['count']) ? $moduleLink[3]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[4]['link']) ? $moduleLink[4]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #007BFF, #cfeeee);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg width="50" height="50" viewBox="0 0 64 64"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="64" height="64" rx="8"
                                                            fill="#F4F4F4" />
                                                        <rect x="16" y="8" width="32" height="38" rx="4"
                                                            fill="#FFF" stroke="#555" stroke-width="2" />
                                                        <rect x="20" y="12" width="24" height="6" rx="2"
                                                            fill="#E0E0E0" />
                                                        <circle cx="24" cy="24" r="2" fill="#4CAF50" />
                                                        <rect x="28" y="23" width="16" height="2" rx="1"
                                                            fill="#999" />
                                                        <circle cx="24" cy="30" r="2" fill="#4CAF50" />
                                                        <rect x="28" y="29" width="16" height="2" rx="1"
                                                            fill="#999" />
                                                        <circle cx="24" cy="36" r="2" fill="#F44336" />
                                                        <rect x="28" y="35" width="16" height="2" rx="1"
                                                            fill="#999" />
                                                        <circle cx="48" cy="48" r="6" stroke="#1976D2"
                                                            stroke-width="2" />
                                                        <line x1="52.2" y1="52.2" x2="58"
                                                            y2="58" stroke="#1976D2" stroke-width="2"
                                                            stroke-linecap="round" />
                                                    </svg>



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[4]['name']) ? $moduleLink[4]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[4]['count']) ? $moduleLink[4]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[5]['link']) ? $moduleLink[5]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg width="50" height="60" viewBox="0 0 64 64"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="64" height="64" rx="8"
                                                            fill="#F0F4F8" />

                                                        <!-- Chart Base -->
                                                        <rect x="10" y="18" width="44" height="30" rx="2"
                                                            fill="white" stroke="#B0BEC5" stroke-width="1.5" />

                                                        <!-- Bar Graphs -->
                                                        <rect x="16" y="38" width="4" height="6"
                                                            fill="#42A5F5" />
                                                        <rect x="22" y="34" width="4" height="10"
                                                            fill="#42A5F5" />
                                                        <rect x="28" y="28" width="4" height="16"
                                                            fill="#42A5F5" />
                                                        <rect x="34" y="24" width="4" height="20"
                                                            fill="#42A5F5" />
                                                        <rect x="40" y="30" width="4" height="14"
                                                            fill="#42A5F5" />

                                                        <!-- Magnifying Glass -->
                                                        <circle cx="48" cy="48" r="5" stroke="#1976D2"
                                                            stroke-width="2" fill="none" />
                                                        <line x1="51.5" y1="51.5" x2="58"
                                                            y2="58" stroke="#1976D2" stroke-width="2"
                                                            stroke-linecap="round" />
                                                    </svg>




                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[5]['name']) ? $moduleLink[5]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[5]['count']) ? $moduleLink[5]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[6]['link']) ? $moduleLink[6]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #23aec0 0%, #f3a7bd 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(128, 42, 116, 0.103), 0 1px 3px rgba(61, 26, 156, 0.329);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg width="60" height="50" viewBox="0 0 64 64"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="64" height="64" rx="8"
                                                            fill="#F0F4F8" />

                                                        <!-- Calendar Base -->
                                                        <rect x="12" y="16" width="40" height="36" rx="3"
                                                            fill="white" stroke="#90A4AE" stroke-width="2" />
                                                        <line x1="12" y1="24" x2="52"
                                                            y2="24" stroke="#90A4AE" stroke-width="1.5" />

                                                        <!-- Calendar Rings -->
                                                        <rect x="18" y="20" width="4" height="4" rx="1"
                                                            fill="#42A5F5" />
                                                        <rect x="26" y="20" width="4" height="4" rx="1"
                                                            fill="#42A5F5" />
                                                        <rect x="34" y="20" width="4" height="4" rx="1"
                                                            fill="#42A5F5" />

                                                        <!-- Calendar Dates -->
                                                        <circle cx="20" cy="30" r="1.5" fill="#42A5F5" />
                                                        <circle cx="28" cy="30" r="1.5" fill="#42A5F5" />
                                                        <circle cx="36" cy="30" r="1.5" fill="#42A5F5" />
                                                        <circle cx="44" cy="30" r="1.5" fill="#42A5F5" />

                                                        <circle cx="20" cy="38" r="1.5" fill="#42A5F5" />
                                                        <circle cx="28" cy="38" r="1.5" fill="#42A5F5" />
                                                        <circle cx="36" cy="38" r="1.5" fill="#42A5F5" />
                                                        <circle cx="44" cy="38" r="1.5" fill="#42A5F5" />

                                                        <!-- Magnifying Glass -->
                                                        <circle cx="48" cy="48" r="5" stroke="#1976D2"
                                                            stroke-width="2" fill="none" />
                                                        <line x1="51.5" y1="51.5" x2="58"
                                                            y2="58" stroke="#1976D2" stroke-width="2"
                                                            stroke-linecap="round" />
                                                    </svg>



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        {{ isset($moduleLink[6]['name']) ? $moduleLink[6]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        {{ isset($moduleLink[6]['count']) ? $moduleLink[6]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                                    <div class="widget-stat card card-dashbaord"
                                        onclick="redirectopermanage('{{ isset($moduleLink[7]['link']) ? $moduleLink[7]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #28dfc6 0%, #5698ee 100%);">
                                        <div class="card-body "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                    <svg width="100" height="50" viewBox="0 0 64 64"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="64" height="64" rx="8"
                                                            fill="#F0F4F8" />

                                                        <!-- Unit 1 -->
                                                        <rect x="10" y="16" width="18" height="20" rx="2"
                                                            fill="#FFFFFF" stroke="#90A4AE" stroke-width="1.5" />
                                                        <circle cx="16" cy="22" r="1.5" fill="#4CAF50" />
                                                        <rect x="20" y="21.5" width="6" height="1.5"
                                                            fill="#B0BEC5" />
                                                        <circle cx="16" cy="28" r="1.5" fill="#4CAF50" />
                                                        <rect x="20" y="27.5" width="6" height="1.5"
                                                            fill="#B0BEC5" />

                                                        <!-- Unit 2 -->
                                                        <rect x="36" y="28" width="18" height="20" rx="2"
                                                            fill="#FFFFFF" stroke="#90A4AE" stroke-width="1.5" />
                                                        <circle cx="42" cy="34" r="1.5" fill="#4CAF50" />
                                                        <rect x="46" y="33.5" width="6" height="1.5"
                                                            fill="#B0BEC5" />
                                                        <circle cx="42" cy="40" r="1.5" fill="#4CAF50" />
                                                        <rect x="46" y="39.5" width="6" height="1.5"
                                                            fill="#B0BEC5" />

                                                        <!-- Arrows between units -->
                                                        <path d="M28 26 L36 30 M36 30 L32 32" stroke="#1976D2"
                                                            stroke-width="2" fill="none"
                                                            marker-end="url(#arrowhead)" />
                                                        <path d="M36 36 L28 40 M28 40 L32 42" stroke="#1976D2"
                                                            stroke-width="2" fill="none"
                                                            marker-end="url(#arrowhead)" />

                                                        <!-- Arrowhead definition -->
                                                        <defs>
                                                            <marker id="arrowhead" markerWidth="6" markerHeight="6"
                                                                refX="3" refY="3" orient="auto">
                                                                <polygon points="0 0, 6 3, 0 6" fill="#1976D2" />
                                                            </marker>
                                                        </defs>

                                                        <!-- Magnifying Glass -->
                                                        <circle cx="50" cy="14" r="5" stroke="#1976D2"
                                                            stroke-width="2" fill="none" />
                                                        <line x1="53.5" y1="17.5" x2="58"
                                                            y2="22" stroke="#1976D2" stroke-width="2"
                                                            stroke-linecap="round" />
                                                    </svg>



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1" style="color:black;">
                                                        @if (is_array($moduleLink[7]) && isset($moduleLink[7]['name']))
                                                            {{ $moduleLink[7]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-0 medicine-requisition">
                                                        @if (is_array($moduleLink[7]) && isset($moduleLink[7]['count']))
                                                            {{ $moduleLink[7]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>




                    </div>
                </div>
            </div>

            <div class = "row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white ">Month Wise PTW</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="monthwiseptw_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="monthwiseptwDiv">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">PTW Type Wise Count</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ptw_type_wise_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="ptw_type_wise_countDiv"></div>

                    </div>
                </div>
            </div>
            <div class = "row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white ">Unit Wise PTW</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="unitwiseptw_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="unitwiseptwDiv">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">PTW – Violation Hold for Compliance</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadPtwHoldViolation_download"></a>
                        </div>
                        <div id="LoadPtwHoldViolation_CountDiv"></div>
                    </div>
                </div>
            </div>
            <div class = "row">
                <div class="col-xl-6 col-xxl-6">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">PTW Open Close</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ptw_open_close_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="ptw_open_close_countDiv"> </div>

                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Incident Type</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="IncidentType_download"></a>
                        </div>
                        <div id="IncidentTypeChartDiv"></div>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Injury Report - Based On Body Parts</h4>
                        </div>
                        <div class="card-body" id="loadinjurychartDiv"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Total Incidents (YTD)</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="total_incidents_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="TotalIncidentsCountDiv"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Heatmap of IMS Data</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="heatmapofImsData_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="heatmapofImsDataDiv"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Type of IIR</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="TypeofIIR_download"></a>
                        </div>
                        <div id="TypeofIIRCountDiv"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Accident Report Unit Wise</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload"
                                id="AccidentReportUnitWise_download"></a>
                        </div>
                        <div id="AccidentReportUnitWiseCountDiv"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">IIR Type Wise UAUC</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="iirTypewiseUAUC_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="iirTypewiseUAUCCountDiv"> </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">IIR Type Wise RCPA</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="iirTypewiseRCPA_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="iirTypewiseRCPACountDiv"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Monthly Near Miss Frequency Rate</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="nearMiss_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="nearMissCountDiv"> </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">Unsafe Act / Unsafe Condition Static Report</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="uaucstaticreport_download"></a>
                        </div>
                        <div id="uaucStaticReportDiv"></div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Inspection Type Wise Count</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="inspection_wise_count_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="inspection_wise_countDiv"> </div>

                    </div>
                </div>

            </div>
            <div class = "row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Type of Audit Findings</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="auditFindings_download"></a>
                        </div>
                        <div id="LoadauditFindingsCountDiv"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">Gemba Walk Potential Hazard 6's Observation Report</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="gembaWalkDownload"></a>
                        </div>
                        <div id="gembaWalkDiv"></div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">

                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">PTW Average time between initial to closed</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadPTWAvgTimeChart_download"></a>
                        </div>
                        <div id="LoadPTWAvgTimeChartCount"></div>
                    </div>
                </div>
            </div>  --}}


            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">PPE Issuance Group Wise</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload"
                                id="LoadPPEIssuanceGroupWise_download"></a>
                        </div>
                        <div id="LoadPPEIssuanceGroupWiseCountDiv"></div>
                    </div>
                </div>
            </div>

            <div class="row">



                <div class="col-xl-6 col-xxl-6">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">PPE Availability</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload"
                                id="LoadPPEAvailabilityChart_download"></a>
                        </div>
                        <div id="LoadPPEAvailabilityChartCountDiv"></div>
                    </div>
                </div>

                <div class="col-xl-6 col-xxl-6">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">Training Completion</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="TrainingCompletion_download"></a>
                        </div>
                        <div id="TrainingCompletionCountDiv"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">Training Hour of Safety Department Wise</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload"
                                id="LoadTrainingHourSafetyDepartmentWise_download"></a>
                        </div>
                        <div id="LoadTrainingHourSafetyDepartmentWise_CountDiv"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">MONTH WISE TRAINING COUNT</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="monthwisetraining_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="LoadmonthwisetrainingDiv"> </div>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card dashboard_card">
                        <div class="card-header">
                            <h4 class="text-white">DEPARTMENT WISE TRAINING COUNT</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadDepartmentCount_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="LoadDepartmentCountDiv">
                        </div>


                    </div>
                </div>
            </div>



            {{-- <div class="row">
                    <div class="col-xl-12 col-xxl-12">
                        <div class="card dashboard_card responsive">
                            <div class="card-header">
                                <h4 class="text-white">Daily 6's Observation Report Monthly Static Report</h4>
                                <a class="fas fa-arrow-alt-circle-down chartdownload" id="dailyObservationDownload"></a>
                            </div>
                            <div id="dailyObservation"></div>
                        </div>
                    </div>
                </div> --}}

            <div class="row" style="display: none">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card dashboard_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">TRAINING STATUS COUNT</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="training_count_download"></a>
                        </div>
                        <div id="trainingStatusPieChartDiv"></div>
                    </div>
                </div>
            </div>

        @endsection


        @push('scripts')
            <script>
                function redirectToIms(iirType, unitId, incidentId, injuryType, month, uauc, rcpa) {

                    CompanyId = $("#company_id").val();
                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('incident/initial-incident/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Create hidden input fields for each POST data
                    var inputiirType = document.createElement('input');
                    inputiirType.setAttribute('type', 'hidden');
                    inputiirType.setAttribute('name', 'iir_type');
                    inputiirType.setAttribute('value', iirType);
                    form.appendChild(inputiirType);

                    var inputincidentId = document.createElement('input');
                    inputincidentId.setAttribute('type', 'hidden');
                    inputincidentId.setAttribute('name', 'incident_id');
                    inputincidentId.setAttribute('value', incidentId);
                    form.appendChild(inputincidentId);


                    var inputunitId = document.createElement('input');
                    inputunitId.setAttribute('type', 'hidden');
                    inputunitId.setAttribute('name', 'unit_id');
                    inputunitId.setAttribute('value', unitId);
                    form.appendChild(inputunitId);

                    var inputinjuryType = document.createElement('input');
                    inputinjuryType.setAttribute('type', 'hidden');
                    inputinjuryType.setAttribute('name', 'injury_type');
                    inputinjuryType.setAttribute('value', injuryType);
                    form.appendChild(inputinjuryType);

                    var inputmonth = document.createElement('input');
                    inputmonth.setAttribute('type', 'hidden');
                    inputmonth.setAttribute('name', 'month');
                    inputmonth.setAttribute('value', month);
                    form.appendChild(inputmonth);

                    var inputuauc = document.createElement('input');
                    inputuauc.setAttribute('type', 'hidden');
                    inputuauc.setAttribute('name', 'uauc');
                    inputuauc.setAttribute('value', uauc);
                    form.appendChild(inputuauc);

                    var inputrcpa = document.createElement('input');
                    inputrcpa.setAttribute('type', 'hidden');
                    inputrcpa.setAttribute('name', 'rcpa');
                    inputrcpa.setAttribute('value', rcpa);
                    form.appendChild(inputrcpa);



                    var inputCompanyId = document.createElement('input');
                    inputCompanyId.setAttribute('type', 'hidden');
                    inputCompanyId.setAttribute('name', 'CompanyId');
                    inputCompanyId.setAttribute('value', CompanyId);
                    form.appendChild(inputCompanyId);

                    var inputFromdate = document.createElement('input');
                    inputFromdate.setAttribute('type', 'hidden');
                    inputFromdate.setAttribute('name', 'Fromdate');
                    inputFromdate.setAttribute('value', Fromdate);
                    form.appendChild(inputFromdate);

                    var inputTodate = document.createElement('input');
                    inputTodate.setAttribute('type', 'hidden');
                    inputTodate.setAttribute('name', 'Todate');
                    inputTodate.setAttribute('value', Todate);
                    form.appendChild(inputTodate);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }

                function redirectToImsRCPA(iirType, unitId, incidentId, injuryType, month, uauc, rcpa) {

                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('incident/initial-incident/calist') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Create hidden input fields for each POST data
                    var inputiirType = document.createElement('input');
                    inputiirType.setAttribute('type', 'hidden');
                    inputiirType.setAttribute('name', 'iir_type');
                    inputiirType.setAttribute('value', iirType);
                    form.appendChild(inputiirType);

                    var inputincidentId = document.createElement('input');
                    inputincidentId.setAttribute('type', 'hidden');
                    inputincidentId.setAttribute('name', 'incident_id');
                    inputincidentId.setAttribute('value', incidentId);
                    form.appendChild(inputincidentId);


                    var inputunitId = document.createElement('input');
                    inputunitId.setAttribute('type', 'hidden');
                    inputunitId.setAttribute('name', 'unit_id');
                    inputunitId.setAttribute('value', unitId);
                    form.appendChild(inputunitId);

                    var inputinjuryType = document.createElement('input');
                    inputinjuryType.setAttribute('type', 'hidden');
                    inputinjuryType.setAttribute('name', 'injury_type');
                    inputinjuryType.setAttribute('value', injuryType);
                    form.appendChild(inputinjuryType);

                    var inputmonth = document.createElement('input');
                    inputmonth.setAttribute('type', 'hidden');
                    inputmonth.setAttribute('name', 'month');
                    inputmonth.setAttribute('value', month);
                    form.appendChild(inputmonth);

                    var inputuauc = document.createElement('input');
                    inputuauc.setAttribute('type', 'hidden');
                    inputuauc.setAttribute('name', 'uauc');
                    inputuauc.setAttribute('value', uauc);
                    form.appendChild(inputuauc);

                    var inputrcpa = document.createElement('input');
                    inputrcpa.setAttribute('type', 'hidden');
                    inputrcpa.setAttribute('name', 'rcpa');
                    inputrcpa.setAttribute('value', rcpa);
                    form.appendChild(inputrcpa);



                    var inputFromdate = document.createElement('input');
                    inputFromdate.setAttribute('type', 'hidden');
                    inputFromdate.setAttribute('name', 'Fromdate');
                    inputFromdate.setAttribute('value', Fromdate);
                    form.appendChild(inputFromdate);

                    var inputTodate = document.createElement('input');
                    inputTodate.setAttribute('type', 'hidden');
                    inputTodate.setAttribute('name', 'Todate');
                    inputTodate.setAttribute('value', Todate);
                    form.appendChild(inputTodate);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }

                function redirectToPTW(PTWID, unitId, month, openclose, typeOfWork, permitStatus) {

                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('safetypermit/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    var inputPermitId = document.createElement('input');
                    inputPermitId.setAttribute('type', 'hidden');
                    inputPermitId.setAttribute('name', 'permit_id');
                    inputPermitId.setAttribute('value', PTWID);
                    form.appendChild(inputPermitId);


                    var inputunitId = document.createElement('input');
                    inputunitId.setAttribute('type', 'hidden');
                    inputunitId.setAttribute('name', 'unit_id');
                    inputunitId.setAttribute('value', unitId);
                    form.appendChild(inputunitId);


                    var inputmonth = document.createElement('input');
                    inputmonth.setAttribute('type', 'hidden');
                    inputmonth.setAttribute('name', 'month');
                    inputmonth.setAttribute('value', month);
                    form.appendChild(inputmonth);

                    var inputopenclose = document.createElement('input');
                    inputopenclose.setAttribute('type', 'hidden');
                    inputopenclose.setAttribute('name', 'openclose');
                    inputopenclose.setAttribute('value', openclose);
                    form.appendChild(inputopenclose);

                    var inputtypeOfWork = document.createElement('input');
                    inputtypeOfWork.setAttribute('type', 'hidden');
                    inputtypeOfWork.setAttribute('name', 'typeOfWork');
                    inputtypeOfWork.setAttribute('value', typeOfWork);
                    form.appendChild(inputtypeOfWork);

                    var inputpermitStatus = document.createElement('input');
                    inputpermitStatus.setAttribute('type', 'hidden');
                    inputpermitStatus.setAttribute('name', 'permitStatus');
                    inputpermitStatus.setAttribute('value', permitStatus);
                    form.appendChild(inputpermitStatus);

                    var inputFromdate = document.createElement('input');
                    inputFromdate.setAttribute('type', 'hidden');
                    inputFromdate.setAttribute('name', 'Fromdate');
                    inputFromdate.setAttribute('value', Fromdate);
                    form.appendChild(inputFromdate);

                    var inputTodate = document.createElement('input');
                    inputTodate.setAttribute('type', 'hidden');
                    inputTodate.setAttribute('name', 'Todate');
                    inputTodate.setAttribute('value', Todate);
                    form.appendChild(inputTodate);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }


                function redirectToPPE(sub, month) {

                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('ppe_stock_inventory/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // var inputPermitId = document.createElement('input');
                    // inputPermitId.setAttribute('type', 'hidden');
                    // inputPermitId.setAttribute('name', 'ppe_id');
                    // inputPermitId.setAttribute('value', PTWID);
                    // form.appendChild(inputPermitId);


                    var inputsub = document.createElement('input');
                    inputsub.setAttribute('type', 'hidden');
                    inputsub.setAttribute('name', 'sub');
                    inputsub.setAttribute('value', sub);
                    form.appendChild(inputsub);


                    var inputmonth = document.createElement('input');
                    inputmonth.setAttribute('type', 'hidden');
                    inputmonth.setAttribute('name', 'month');
                    inputmonth.setAttribute('value', month);
                    form.appendChild(inputmonth);

                    // var inputopenclose = document.createElement('input');
                    // inputopenclose.setAttribute('type', 'hidden');
                    // inputopenclose.setAttribute('name', 'openclose');
                    // inputopenclose.setAttribute('value', openclose);
                    // form.appendChild(inputopenclose);

                    // var inputtypeOfWork = document.createElement('input');
                    // inputtypeOfWork.setAttribute('type', 'hidden');
                    // inputtypeOfWork.setAttribute('name', 'typeOfWork');
                    // inputtypeOfWork.setAttribute('value', typeOfWork);
                    // form.appendChild(inputtypeOfWork);

                    // var inputpermitStatus = document.createElement('input');
                    // inputpermitStatus.setAttribute('type', 'hidden');
                    // inputpermitStatus.setAttribute('name', 'permitStatus');
                    // inputpermitStatus.setAttribute('value', permitStatus);
                    // form.appendChild(inputpermitStatus);

                    var inputFromdate = document.createElement('input');
                    inputFromdate.setAttribute('type', 'hidden');
                    inputFromdate.setAttribute('name', 'Fromdate');
                    inputFromdate.setAttribute('value', Fromdate);
                    form.appendChild(inputFromdate);

                    var inputTodate = document.createElement('input');
                    inputTodate.setAttribute('type', 'hidden');
                    inputTodate.setAttribute('name', 'Todate');
                    inputTodate.setAttribute('value', Todate);
                    form.appendChild(inputTodate);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }

                function redirectToTraining(openclose) {

                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('training_schedule/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // var inputPermitId = document.createElement('input');
                    // inputPermitId.setAttribute('type', 'hidden');
                    // inputPermitId.setAttribute('name', 'ppe_id');
                    // inputPermitId.setAttribute('value', PTWID);
                    // form.appendChild(inputPermitId);


                    // var inputsub = document.createElement('input');
                    // inputsub.setAttribute('type', 'hidden');
                    // inputsub.setAttribute('name', 'sub');
                    // inputsub.setAttribute('value', sub);
                    // form.appendChild(inputsub);


                    // var inputmonth = document.createElement('input');
                    // inputmonth.setAttribute('type', 'hidden');
                    // inputmonth.setAttribute('name', 'month');
                    // inputmonth.setAttribute('value', month);
                    // form.appendChild(inputmonth);

                    var inputopenclose = document.createElement('input');
                    inputopenclose.setAttribute('type', 'hidden');
                    inputopenclose.setAttribute('name', 'openclose');
                    inputopenclose.setAttribute('value', openclose);
                    form.appendChild(inputopenclose);

                    // var inputtypeOfWork = document.createElement('input');
                    // inputtypeOfWork.setAttribute('type', 'hidden');
                    // inputtypeOfWork.setAttribute('name', 'typeOfWork');
                    // inputtypeOfWork.setAttribute('value', typeOfWork);
                    // form.appendChild(inputtypeOfWork);

                    // var inputpermitStatus = document.createElement('input');
                    // inputpermitStatus.setAttribute('type', 'hidden');
                    // inputpermitStatus.setAttribute('name', 'permitStatus');
                    // inputpermitStatus.setAttribute('value', permitStatus);
                    // form.appendChild(inputpermitStatus);

                    var inputFromdate = document.createElement('input');
                    inputFromdate.setAttribute('type', 'hidden');
                    inputFromdate.setAttribute('name', 'Fromdate');
                    inputFromdate.setAttribute('value', Fromdate);
                    form.appendChild(inputFromdate);

                    var inputTodate = document.createElement('input');
                    inputTodate.setAttribute('type', 'hidden');
                    inputTodate.setAttribute('name', 'Todate');
                    inputTodate.setAttribute('value', Todate);
                    form.appendChild(inputTodate);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }

                function redirectopermanage(link) {
                    var url = "{{ admin_url('') }}" + link;
                    window.location.href = url;
                }

                function filterDashboard() {
                    CompanyId = $("#company_id").val();
                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();

                    TrainingHourSafetyDepartmentWise(CompanyId, Fromdate, Todate);
                    PTWViolationHoldCompliance(CompanyId, Fromdate, Todate);
                    IncidentsCount(CompanyId, Fromdate, Todate);
                    heatmapofIms(CompanyId, Fromdate, Todate);
                    IncidentType(CompanyId, Fromdate, Todate);
                    TrainingCompletionCount(CompanyId, Fromdate, Todate);
                    TypeofIIRCount(CompanyId, Fromdate, Todate);
                    AccidentReportUnitWiseCount(CompanyId, Fromdate, Todate);
                    LoadiirTypewiseUAUCCount(CompanyId, Fromdate, Todate);
                    LoadnearMissCount(CompanyId, Fromdate, Todate);
                    inspectionWiseCount(CompanyId, Fromdate, Todate);
                    ptw_open_close_count(CompanyId, Fromdate, Todate);
                    ptw_type_wise_count(CompanyId, Fromdate, Todate);
                    LoadPPEAvailabilityChartCount(CompanyId, Fromdate, Todate);
                    LoadPTWAvgTimeChartCount(CompanyId, Fromdate, Todate);
                    LoadPPEIssuanceGroupWiseCount(CompanyId, Fromdate, Todate);
                    loadinjurychart(CompanyId, Fromdate, Todate);
                    LoadauditFindingsCount(CompanyId, Fromdate, Todate);
                    LoadiirTypewiseRCPACount(CompanyId, Fromdate, Todate);
                    GembaWalkObservationReport(CompanyId, Fromdate, Todate);
                    uaucStaticReportData(CompanyId, Fromdate, Todate);
                    LoadDepartmentCount(CompanyId, Fromdate, Todate);
                    loadfmonthwisetraining(CompanyId, Fromdate, Todate);
                    LoadmonthewisePTWData(CompanyId, Fromdate, Todate);
                    loadunitwisecount(CompanyId, Fromdate, Todate);
                    loadtraining_count_status(CompanyId, Fromdate, Todate);
                    // dailyObservation(Fromdate, Todate);
                }

                function LoadDepartmentCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/department') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadDepartmentCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {
                            $('#LoadDepartmentCountDiv').html(dataAjx);
                        }
                    });
                }


                function loadfmonthwisetraining(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwisetraining') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadmonthwisetrainingDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadmonthwisetrainingDiv').html(dataAjx);
                        }
                    });
                }

                function loadtraining_count_status(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/trainingStatusCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#trainingStatusPieChartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#trainingStatusPieChartDiv').html(dataAjx);
                        }
                    });
                }

                function LoadiirTypewiseRCPACount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/IIRTypeWiseRCPA') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#iirTypewiseRCPACountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#iirTypewiseRCPACountDiv').html(dataAjx);
                        }
                    });
                }

                function loadmonthewisecount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#monthwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#monthwiseptwDiv').html(dataAjx);
                        }
                    });
                }


                function LoadauditFindingsCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/auditFindings') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadauditFindingsCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadauditFindingsCountDiv').html(dataAjx);
                        }
                    });
                }

                function IncidentsCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/total-incident') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TotalIncidentsCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {
                            $('#TotalIncidentsCountDiv').html(dataAjx);
                        }
                    });
                }

                function GembaWalkObservationReport(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/gemba-walk-observation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#gembaWalkDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#gembaWalkDiv').html(dataAjx);
                        }
                    });
                }

                function dailyObservation(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/dailyObservation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#dailyObservation').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#dailyObservation').html(dataAjx);
                        }
                    });
                }

                function heatmapofIms(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/heatmap-of-imsData') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#heatmapofImsDataDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#heatmapofImsDataDiv').html(dataAjx);
                        }
                    });
                }

                function IncidentType(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/incident-type-chart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#IncidentTypeChartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#IncidentTypeChartDiv').html(dataAjx);
                        }
                    });
                }

                function loadinjurychart(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/injurypart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#loadinjurychartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#loadinjurychartDiv').html(dataAjx);
                        }
                    });
                }


                function LoadPTWAvgTimeChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PTWAvgTimeChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPTWAvgTimeChartCount').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPTWAvgTimeChartCount').html(dataAjx);
                        }
                    });
                }

                function LoadPPEAvailabilityChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PPEAvailabilityChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPPEAvailabilityChartCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPPEAvailabilityChartCountDiv').html(dataAjx);
                        }
                    });
                }

                function ptw_type_wise_count(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-type-wise-count') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#ptw_type_wise_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#ptw_type_wise_countDiv').html(dataAjx);
                        }
                    });
                }

                function ptw_open_close_count(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-open-close') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#ptw_open_close_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#ptw_open_close_countDiv').html(dataAjx);
                        }
                    });
                }

                function inspectionWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/inspection-count') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#inspection_wise_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#inspection_wise_countDiv').html(dataAjx);
                        }
                    });
                }

                function LoadnearMissCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/nearMissFrequency') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#nearMissCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#nearMissCountDiv').html(dataAjx);
                        }
                    });
                }

                function LoadiirTypewiseUAUCCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/IIRTypeWiseUAUC') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#iirTypewiseUAUCCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#iirTypewiseUAUCCountDiv').html(dataAjx);
                        }
                    });
                }

                function AccidentReportUnitWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/AccidentReportUnitWiseCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#AccidentReportUnitWiseCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#AccidentReportUnitWiseCountDiv').html(dataAjx);
                        }
                    });
                }

                function TypeofIIRCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/TypeofIIRCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TypeofIIRCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#TypeofIIRCountDiv').html(dataAjx);
                        }
                    });
                }

                function TrainingCompletionCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/TrainingCompletionCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TrainingCompletionCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#TrainingCompletionCountDiv').html(dataAjx);
                        }
                    });
                }

                function TrainingHourSafetyDepartmentWise(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/training-hour-safety-department') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadTrainingHourSafetyDepartmentWise_CountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadTrainingHourSafetyDepartmentWise_CountDiv').html(dataAjx);
                        }
                    });
                }

                function PTWViolationHoldCompliance(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-hold-violation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPtwHoldViolation_CountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPtwHoldViolation_CountDiv').html(dataAjx);
                        }
                    });
                }

                function uaucStaticReportData(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/uauc-static-report') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#uaucStaticReportDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#uaucStaticReportDiv').html(dataAjx);
                        }
                    });
                }

                function LoadmonthewisePTWData(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#monthwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#monthwiseptwDiv').html(dataAjx);
                        }
                    });
                }


                function loadunitwisecount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/unitwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#unitwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#unitwiseptwDiv').html(dataAjx);
                        }
                    });
                }




                function LoadPPEIssuanceGroupWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PPEIssuanceGroupWise') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPPEIssuanceGroupWiseCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPPEIssuanceGroupWiseCountDiv').html(dataAjx);
                        }
                    });
                }

                function LoadPTWAvgTimeChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PTWAvgTimeChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPTWAvgTimeChartCount').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPTWAvgTimeChartCount').html(dataAjx);
                        }
                    });
                }



                $(document).ready(function() {
                    const toDatePicker = flatpickr("#toDate", {
                        dateFormat: "d-m-Y",
                        minDate: "today",
                    });

                    flatpickr("#fromDate", {
                        dateFormat: "d-m-Y",
                        onChange: function(selectedDates, dateStr) {
                            if (selectedDates.length > 0 && toDatePicker) {
                                toDatePicker.set("minDate", dateStr);
                            }
                        }
                    });

                    $('#resetform').on('click', function(e) {
                        e.preventDefault();
                        location.reload();
                    });

                    // Initial load
                    filterDashboard();
                });

                function loadfmonthwisetraining(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwisetraining') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadmonthwisetrainingDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadmonthwisetrainingDiv').html(dataAjx);
                        }
                    });
                }

                function loadtraining_count_status(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/trainingStatusCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#trainingStatusPieChartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#trainingStatusPieChartDiv').html(dataAjx);
                        }
                    });
                }

                function LoadiirTypewiseRCPACount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/IIRTypeWiseRCPA') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#iirTypewiseRCPACountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#iirTypewiseRCPACountDiv').html(dataAjx);
                        }
                    });
                }

                function loadmonthewisecount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#monthwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#monthwiseptwDiv').html(dataAjx);
                        }
                    });
                }


                function LoadauditFindingsCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/auditFindings') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadauditFindingsCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadauditFindingsCountDiv').html(dataAjx);
                        }
                    });
                }

                function IncidentsCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/total-incident') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TotalIncidentsCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {
                            $('#TotalIncidentsCountDiv').html(dataAjx);
                        }
                    });
                }

                function GembaWalkObservationReport(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/gemba-walk-observation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#gembaWalkDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#gembaWalkDiv').html(dataAjx);
                        }
                    });
                }

                function dailyObservation(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/dailyObservation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#dailyObservation').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#dailyObservation').html(dataAjx);
                        }
                    });
                }

                function heatmapofIms(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/heatmap-of-imsData') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#heatmapofImsDataDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#heatmapofImsDataDiv').html(dataAjx);
                        }
                    });
                }

                function IncidentType(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/incident-type-chart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#IncidentTypeChartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#IncidentTypeChartDiv').html(dataAjx);
                        }
                    });
                }

                function loadinjurychart(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/injurypart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#loadinjurychartDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#loadinjurychartDiv').html(dataAjx);
                        }
                    });
                }


                function LoadPTWAvgTimeChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PTWAvgTimeChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPTWAvgTimeChartCount').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPTWAvgTimeChartCount').html(dataAjx);
                        }
                    });
                }

                function LoadPPEAvailabilityChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PPEAvailabilityChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPPEAvailabilityChartCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPPEAvailabilityChartCountDiv').html(dataAjx);
                        }
                    });
                }

                function ptw_type_wise_count(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-type-wise-count') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#ptw_type_wise_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#ptw_type_wise_countDiv').html(dataAjx);
                        }
                    });
                }

                function ptw_open_close_count(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-open-close') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#ptw_open_close_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#ptw_open_close_countDiv').html(dataAjx);
                        }
                    });
                }

                function inspectionWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/inspection-count') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#inspection_wise_countDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#inspection_wise_countDiv').html(dataAjx);
                        }
                    });
                }

                function LoadnearMissCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/nearMissFrequency') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#nearMissCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#nearMissCountDiv').html(dataAjx);
                        }
                    });
                }

                function LoadiirTypewiseUAUCCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/IIRTypeWiseUAUC') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#iirTypewiseUAUCCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#iirTypewiseUAUCCountDiv').html(dataAjx);
                        }
                    });
                }

                function AccidentReportUnitWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/AccidentReportUnitWiseCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#AccidentReportUnitWiseCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#AccidentReportUnitWiseCountDiv').html(dataAjx);
                        }
                    });
                }

                function TypeofIIRCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/TypeofIIRCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TypeofIIRCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#TypeofIIRCountDiv').html(dataAjx);
                        }
                    });
                }

                function TrainingCompletionCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/TrainingCompletionCount') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#TrainingCompletionCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#TrainingCompletionCountDiv').html(dataAjx);
                        }
                    });
                }

                function TrainingHourSafetyDepartmentWise(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/training-hour-safety-department') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadTrainingHourSafetyDepartmentWise_CountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadTrainingHourSafetyDepartmentWise_CountDiv').html(dataAjx);
                        }
                    });
                }

                function PTWViolationHoldCompliance(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/ptw-hold-violation') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPtwHoldViolation_CountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPtwHoldViolation_CountDiv').html(dataAjx);
                        }
                    });
                }

                function uaucStaticReportData(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/uauc-static-report') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#uaucStaticReportDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#uaucStaticReportDiv').html(dataAjx);
                        }
                    });
                }

                function LoadmonthewisePTWData(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/monthwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#monthwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#monthwiseptwDiv').html(dataAjx);
                        }
                    });
                }


                function loadunitwisecount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/unitwiseptw') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#unitwiseptwDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#unitwiseptwDiv').html(dataAjx);
                        }
                    });
                }




                function LoadPPEIssuanceGroupWiseCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PPEIssuanceGroupWise') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPPEIssuanceGroupWiseCountDiv').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPPEIssuanceGroupWiseCountDiv').html(dataAjx);
                        }
                    });
                }

                function LoadPTWAvgTimeChartCount(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/PTWAvgTimeChart') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#LoadPTWAvgTimeChartCount').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#LoadPTWAvgTimeChartCount').html(dataAjx);
                        }
                    });
                }



                $(document).ready(function() {
                    const toDatePicker = flatpickr("#toDate", {
                        dateFormat: "d-m-Y",
                        minDate: "today",
                    });

                    flatpickr("#fromDate", {
                        dateFormat: "d-m-Y",
                        onChange: function(selectedDates, dateStr) {
                            if (selectedDates.length > 0 && toDatePicker) {
                                toDatePicker.set("minDate", dateStr);
                            }
                        }
                    });

                    $('#resetform').on('click', function(e) {
                        e.preventDefault();
                        location.reload();
                    });

                    // Initial load
                    filterDashboard();
                });
            </script>
        @endpush
