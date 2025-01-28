@extends('admin.layouts.admin')
@section('title', ' PPE Management Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>
        body {
            background-color: #f4f6f9;
        }

        .dashboard-header {
            background-color: #ffffff;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        .dashboard-header h4 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }
    </style>
@endpush

@section('content')

    <div class="content-body default-height">
        <div class="container-fluid" style="padding: 30px 20px;">
            <div class="float-end mb-3">
                <x-button-filter dataId="" class="search" href=""></x-button-filter>
            </div>
            <div id="search" class="collapse card mt-5">
                <form action="" id="formsearch">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('Unit') }}</label>
                                    <select name="unit" id="unit" style="width: 100%"
                                        class="form-control single-select">
                                        <option value="">Select the Unit</option>
                                        @foreach ($unitList as $list)
                                            <option value="{{ $list->id }}">
                                                {{ $list->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('From Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required="" class="form-control todaymaxdatepicker"
                                            id="from_date" name="from_date" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('To Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required="" class="form-control todaymaxdatepicker"
                                            id="to_date" name="to_date" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <x-button-search></x-button-search>
                                    <x-button-reset></x-button-reset>

                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mt-5">
                <div class="col-xl-4 col-xxl-4 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">PPE Request STATUS</h4>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height370">
                            <div class="d-flex justify-content-between align-items-center market-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="black" />
                                            <path
                                                d="M54.8196 44.5759L54.2286 43.7693C52.6585 44.9197 46.092 49 32.1429 49C18.1937 49 11.6272 44.9197 10.0571 43.7693L10.0512 43.7649L10.0453 43.7607C10.0413 43.7579 10.0319 43.7502 10.0216 43.7285C10.0105 43.7052 10 43.6681 10 43.6196C10 43.4326 10.1469 43.2857 10.3339 43.2857H53.9518C54.1388 43.2857 54.2857 43.4326 54.2857 43.6196C54.2857 43.6669 54.2757 43.7013 54.2651 43.7234C54.2553 43.744 54.2429 43.7587 54.2264 43.7708L54.8196 44.5759ZM54.8196 44.5759C53.0759 45.8536 46.3018 50 32.1429 50C17.9839 50 11.2098 45.8536 9.46607 44.5759C9.16071 44.3589 9 43.9973 9 43.6196C9 42.8804 9.59464 42.2857 10.3339 42.2857H53.9518C54.6911 42.2857 55.2857 42.8804 55.2857 43.6196C55.2857 43.9973 55.125 44.3509 54.8196 44.5759ZM28 16.5714C28 15.7014 28.7014 15 29.5714 15H34.7143C35.5843 15 36.2857 15.7014 36.2857 16.5714V16.7563V24.7598C36.2857 25.7621 37.095 26.5714 38.0973 26.5714C38.7336 26.5714 39.3455 26.2364 39.674 25.6555C39.6747 25.6543 39.6754 25.6531 39.676 25.6519L43.0497 19.755C48.1708 22.643 51.6476 28.1045 51.7143 34.3838V38.7143H12.5714V34.5714C12.5714 28.2138 16.063 22.6691 21.2358 19.7546L24.6117 25.6555C24.9402 26.2364 25.5521 26.5714 26.1884 26.5714C27.1907 26.5714 28 25.7621 28 24.7598V16.7563V16.5714Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Total Shoe Request</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="TotalShoeRequest">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#FFAB2D" />
                                            <path
                                                d="M19.2734 19.2734L16.8828 16.8828C15.8211 15.8211 14 16.5734 14 18.0711V25.8125C14 26.7477 14.7523 27.5 15.6875 27.5H23.4289C24.9336 27.5 25.6859 25.6789 24.6242 24.6172L22.4586 22.4516C24.8984 20.0117 28.2734 18.5 32 18.5C39.4531 18.5 45.5 24.5469 45.5 32C45.5 39.4531 39.4531 45.5 32 45.5C29.1313 45.5 26.4734 44.607 24.2867 43.0813C23.2672 42.3711 21.868 42.6172 21.1508 43.6367C20.4336 44.6562 20.6867 46.0555 21.7062 46.7727C24.6312 48.8047 28.182 50 32 50C41.9422 50 50 41.9422 50 32C50 22.0578 41.9422 14 32 14C27.0289 14 22.5289 16.018 19.2734 19.2734ZM32 23C31.0648 23 30.3125 23.7523 30.3125 24.6875V32C30.3125 32.45 30.4883 32.8789 30.8047 33.1953L35.8672 38.2578C36.5281 38.9187 37.5969 38.9187 38.2508 38.2578C38.9047 37.5969 38.9117 36.5281 38.2508 35.8742L33.6805 31.3039V24.6875C33.6805 23.7523 32.9281 23 31.993 23H32Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Request Pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="shoeRequestpending">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <span>


                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#3ab67a" />
                                            <path
                                                d="M40.6524 29.4024L41.3595 28.6953L41.2179 28.5537C41.6365 27.573 41.4355 26.3962 40.6492 25.6014C39.6018 24.5428 37.8969 24.5482 36.8476 25.5976L36.8473 25.5979L29.7497 32.7019L27.1562 30.1084C27.1555 30.1077 27.1548 30.107 27.1541 30.1063C26.1019 29.045 24.4013 29.069 23.3584 30.1008C22.2998 31.1482 22.3053 32.8531 23.3546 33.9024L27.8508 38.3986C27.8516 38.3994 27.8523 38.4001 27.853 38.4008C28.9006 39.4572 30.6038 39.451 31.6524 38.4024L40.6524 29.4024ZM44.0208 44.0208C40.8327 47.2089 36.5087 49 32 49C27.4913 49 23.1673 47.2089 19.9792 44.0208C16.7911 40.8327 15 36.5087 15 32C15 27.4913 16.7911 23.1673 19.9792 19.9792C23.1673 16.7911 27.4913 15 32 15C36.5087 15 40.8327 16.7911 44.0208 19.9792C47.2089 23.1673 49 27.4913 49 32C49 36.5087 47.2089 40.8327 44.0208 44.0208Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Request Approved</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ShoeRequestApproved">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#fd5f5f" />
                                            <path
                                                d="M44.0208 44.0208C40.8327 47.2089 36.5087 49 32 49C27.4913 49 23.1673 47.2089 19.9792 44.0208C16.7911 40.8327 15 36.5087 15 32C15 27.4913 16.7911 23.1673 19.9792 19.9792C23.1673 16.7911 27.4913 15 32 15C36.5087 15 40.8327 16.7911 44.0208 19.9792C47.2089 23.1673 49 27.4913 49 32C49 36.5087 47.2089 40.8327 44.0208 44.0208ZM29.3971 25.5993C28.3502 24.5435 26.6485 24.5489 25.5996 25.5955C24.5428 26.643 24.5488 28.3466 25.5976 29.3954L28.1952 31.993L25.6014 34.5868C25.6007 34.5875 25.6 34.5882 25.5993 34.5889C24.538 35.6411 24.562 37.3416 25.5938 38.3846C26.6412 39.4432 28.346 39.4377 29.3954 38.3884L31.993 35.7908L34.5868 38.3846C34.5874 38.3852 34.5881 38.3859 34.5887 38.3865C35.641 39.4479 37.3416 39.424 38.3846 38.3921C39.4432 37.3448 39.4377 35.6399 38.3884 34.5905L35.7908 31.993L38.3846 29.3992C38.3852 29.3985 38.3859 29.3979 38.3865 29.3972C39.4479 28.345 39.424 26.6443 38.3921 25.6014C37.3448 24.5428 35.6399 24.5482 34.5905 25.5976L31.993 28.1952L29.3992 25.6014C29.3985 25.6007 29.3978 25.6 29.3971 25.5993Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>


                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Request Rejected</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ShoeRequestRejected">0</h5>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-xl-8 col-xxl-8 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;"> UNIT WISE PPE Request</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ppeshoerequestDownload"
                                style="color: white;"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="PpeshoerequestData">
                            <!-- This div will display the chart -->
                            <div id="Ppe_Request_Data"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-xl-12 col-xxl-12 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">MONTH WISE PPE REQUEST</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="month_wise_request"
                                style="color: white;"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="month_PpeRequestData">
                            <!-- This div will display the chart -->
                            <div id="month_Ppe_request_Data"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-xl-4 col-xxl-4 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">PPE SHOE EXEMPTION STATUS</h4>

                        </div>

                        <div class="card-body px-0 pt-0 dlab-scroll height370">
                            <div class="d-flex justify-content-between align-items-center market-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="black" />
                                            <path
                                                d="M54.8196 44.5759L54.2286 43.7693C52.6585 44.9197 46.092 49 32.1429 49C18.1937 49 11.6272 44.9197 10.0571 43.7693L10.0512 43.7649L10.0453 43.7607C10.0413 43.7579 10.0319 43.7502 10.0216 43.7285C10.0105 43.7052 10 43.6681 10 43.6196C10 43.4326 10.1469 43.2857 10.3339 43.2857H53.9518C54.1388 43.2857 54.2857 43.4326 54.2857 43.6196C54.2857 43.6669 54.2757 43.7013 54.2651 43.7234C54.2553 43.744 54.2429 43.7587 54.2264 43.7708L54.8196 44.5759ZM54.8196 44.5759C53.0759 45.8536 46.3018 50 32.1429 50C17.9839 50 11.2098 45.8536 9.46607 44.5759C9.16071 44.3589 9 43.9973 9 43.6196C9 42.8804 9.59464 42.2857 10.3339 42.2857H53.9518C54.6911 42.2857 55.2857 42.8804 55.2857 43.6196C55.2857 43.9973 55.125 44.3509 54.8196 44.5759ZM28 16.5714C28 15.7014 28.7014 15 29.5714 15H34.7143C35.5843 15 36.2857 15.7014 36.2857 16.5714V16.7563V24.7598C36.2857 25.7621 37.095 26.5714 38.0973 26.5714C38.7336 26.5714 39.3455 26.2364 39.674 25.6555C39.6747 25.6543 39.6754 25.6531 39.676 25.6519L43.0497 19.755C48.1708 22.643 51.6476 28.1045 51.7143 34.3838V38.7143H12.5714V34.5714C12.5714 28.2138 16.063 22.6691 21.2358 19.7546L24.6117 25.6555C24.9402 26.2364 25.5521 26.5714 26.1884 26.5714C27.1907 26.5714 28 25.7621 28 24.7598V16.7563V16.5714Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Total Shoe Exemption</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="TotalShoeExemption">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 market-preview">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#FFAB2D" />
                                            <path
                                                d="M19.2734 19.2734L16.8828 16.8828C15.8211 15.8211 14 16.5734 14 18.0711V25.8125C14 26.7477 14.7523 27.5 15.6875 27.5H23.4289C24.9336 27.5 25.6859 25.6789 24.6242 24.6172L22.4586 22.4516C24.8984 20.0117 28.2734 18.5 32 18.5C39.4531 18.5 45.5 24.5469 45.5 32C45.5 39.4531 39.4531 45.5 32 45.5C29.1313 45.5 26.4734 44.607 24.2867 43.0813C23.2672 42.3711 21.868 42.6172 21.1508 43.6367C20.4336 44.6562 20.6867 46.0555 21.7062 46.7727C24.6312 48.8047 28.182 50 32 50C41.9422 50 50 41.9422 50 32C50 22.0578 41.9422 14 32 14C27.0289 14 22.5289 16.018 19.2734 19.2734ZM32 23C31.0648 23 30.3125 23.7523 30.3125 24.6875V32C30.3125 32.45 30.4883 32.8789 30.8047 33.1953L35.8672 38.2578C36.5281 38.9187 37.5969 38.9187 38.2508 38.2578C38.9047 37.5969 38.9117 36.5281 38.2508 35.8742L33.6805 31.3039V24.6875C33.6805 23.7523 32.9281 23 31.993 23H32Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Exemption Pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="Exemptionpending">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 align-items-center market-preview">
                                <div class="d-flex align-items-center">
                                    <span>


                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#3ab67a" />
                                            <path
                                                d="M40.6524 29.4024L41.3595 28.6953L41.2179 28.5537C41.6365 27.573 41.4355 26.3962 40.6492 25.6014C39.6018 24.5428 37.8969 24.5482 36.8476 25.5976L36.8473 25.5979L29.7497 32.7019L27.1562 30.1084C27.1555 30.1077 27.1548 30.107 27.1541 30.1063C26.1019 29.045 24.4013 29.069 23.3584 30.1008C22.2998 31.1482 22.3053 32.8531 23.3546 33.9024L27.8508 38.3986C27.8516 38.3994 27.8523 38.4001 27.853 38.4008C28.9006 39.4572 30.6038 39.451 31.6524 38.4024L40.6524 29.4024ZM44.0208 44.0208C40.8327 47.2089 36.5087 49 32 49C27.4913 49 23.1673 47.2089 19.9792 44.0208C16.7911 40.8327 15 36.5087 15 32C15 27.4913 16.7911 23.1673 19.9792 19.9792C23.1673 16.7911 27.4913 15 32 15C36.5087 15 40.8327 16.7911 44.0208 19.9792C47.2089 23.1673 49 27.4913 49 32C49 36.5087 47.2089 40.8327 44.0208 44.0208Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Exemption Approved</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ExemptionApproved">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 align-items-center market-preview">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#fd5f5f" />
                                            <path
                                                d="M44.0208 44.0208C40.8327 47.2089 36.5087 49 32 49C27.4913 49 23.1673 47.2089 19.9792 44.0208C16.7911 40.8327 15 36.5087 15 32C15 27.4913 16.7911 23.1673 19.9792 19.9792C23.1673 16.7911 27.4913 15 32 15C36.5087 15 40.8327 16.7911 44.0208 19.9792C47.2089 23.1673 49 27.4913 49 32C49 36.5087 47.2089 40.8327 44.0208 44.0208ZM29.3971 25.5993C28.3502 24.5435 26.6485 24.5489 25.5996 25.5955C24.5428 26.643 24.5488 28.3466 25.5976 29.3954L28.1952 31.993L25.6014 34.5868C25.6007 34.5875 25.6 34.5882 25.5993 34.5889C24.538 35.6411 24.562 37.3416 25.5938 38.3846C26.6412 39.4432 28.346 39.4377 29.3954 38.3884L31.993 35.7908L34.5868 38.3846C34.5874 38.3852 34.5881 38.3859 34.5887 38.3865C35.641 39.4479 37.3416 39.424 38.3846 38.3921C39.4432 37.3448 39.4377 35.6399 38.3884 34.5905L35.7908 31.993L38.3846 29.3992C38.3852 29.3985 38.3859 29.3979 38.3865 29.3972C39.4479 28.345 39.424 26.6443 38.3921 25.6014C37.3448 24.5428 35.6399 24.5482 34.5905 25.5976L31.993 28.1952L29.3992 25.6014C29.3985 25.6007 29.3978 25.6 29.3971 25.5993Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>


                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">Shoe Exemption Rejected</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ExemptionRejected">0</h5>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="col-xl-8 col-xxl-8 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;"> UNIT WISE PPE EXEMPTION</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ppeExemptionDownload"
                                style="color: white;"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="PpeExemptionData">
                            <!-- This div will display the chart -->
                            <div id="Ppe_Exemption_Data"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row">
                <div class="col-xl-12 col-xxl-12 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-dark d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">Month WISE PPE EXEMPTION</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="month_wise_exemption"
                                style="color: white;"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="month_wise_exemption_data">
                            <!-- This div will display the chart -->
                            <div id="Month_Ppe_Exemption_Data"></div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {


            function redirectToShoeRequest(status, unit) {
                var fromDate = $("#fromDate").val();
                var toDate = $("#toDate").val();
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', "{{ admin_url('ppe_request/list') }}");

                form.appendChild(createHiddenInput('_token', '{{ csrf_token() }}'));
                form.appendChild(createHiddenInput('ppe_status', status));
                form.appendChild(createHiddenInput('unit', unit));
                form.appendChild(createHiddenInput('Fromdate', fromDate));
                form.appendChild(createHiddenInput('Todate', toDate));

                document.body.appendChild(form);
                form.submit();
            }

            function redirectToExemptionRequest(status, unit) {
                var fromDate = $("#fromDate").val();
                var toDate = $("#toDate").val();
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', "{{ admin_url('ppe_exemption/list') }}");

                form.appendChild(createHiddenInput('_token', '{{ csrf_token() }}'));
                form.appendChild(createHiddenInput('exemption_status', status));
                form.appendChild(createHiddenInput('unit', unit));
                form.appendChild(createHiddenInput('Fromdate', fromDate));
                form.appendChild(createHiddenInput('Todate', toDate));

                document.body.appendChild(form);
                form.submit();
            }

            function createHiddenInput(name, value) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                return input;
            }

            function filterDashboard() {
                var unit = $("#unit").val();

                var fromDate = $("#from_date").val();
                var toDate = $("#to_date").val();
                getExemptionDashboard(unit,fromDate, toDate);
                getAllDashMetrics(unit,fromDate, toDate);
                PpeChartExemptionData(unit, fromDate, toDate);
                PpeChartRequestData(unit, fromDate, toDate);
            }

            $('#searchform').click(function() {
                filterDashboard();
            });

            $('#resetform').click(function() {
                window.location.reload();
            });




            // Fetch Exemption Dashboard Metrics
            function getExemptionDashboard(unit,fromDate, toDate) {
                var url = "{{ admin_url('ppe-dashboard/getExemptionstatus') }}";
                var data = {
                    unit: $("#unit").val(),
                    Fromdate: fromDate,
                    Todate: toDate,
                    unit:unit
                };

                resetExemptionMetrics();

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    cache: false,
                    success: function(response) {
                        var metricData = JSON.parse(response);
                        $('#TotalShoeExemption').html(metricData.approve_status[0].count);
                        $('#Exemptionpending').html(metricData.approve_status[1].count);
                        $('#ExemptionApproved').html(metricData.approve_status[2].count);
                        $('#ExemptionRejected').html(metricData.approve_status[3].count);
                    },
                    error: function(xhr) {
                        console.error('Error fetching exemption metrics:', xhr.responseText);
                    }
                });
            }

            // Reset Exemption Metrics
            function resetExemptionMetrics() {
                $('#TotalShoeExemption').html('0');
                $('#Exemptionpending').html('0');
                $('#ExemptionApproved').html('0');
                $('#ExemptionRejected').html('0');
            }

            // Fetch All Dashboard Metrics
            function getAllDashMetrics(unit,fromDate, toDate) {
                var url = "{{ admin_url('ppe-dashboard/getshoerequeststatus') }}";
                var data = {
                    unit:unit,
                    Fromdate: fromDate,
                    Todate: toDate

                };
                console.log(data);

                resetRequestMetrics();

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    cache: false,
                    success: function(response) {
                        var metricData = JSON.parse(response);
                        $('#TotalShoeRequest').html(metricData.approve_status[0].count);
                        $('#shoeRequestpending').html(metricData.approve_status[1].count);
                        $('#ShoeRequestApproved').html(metricData.approve_status[2].count);
                        $('#ShoeRequestRejected').html(metricData.approve_status[3].count);
                    },
                    error: function(xhr) {
                        console.error('Error fetching request metrics:', xhr.responseText);
                    }
                });
            }

            // Reset Request Metrics
            function resetRequestMetrics() {
                $('#TotalShoeRequest').html('0');
                $('#shoeRequestpending').html('0');
                $('#ShoeRequestApproved').html('0');
                $('#ShoeRequestRejected').html('0');
            }

            // Fetch PPE Exemption Chart Data
            function PpeChartExemptionData(unit, from_date, to_date) {
                var url = "{{ admin_url('ppe-dashboard/ppeExemption') }}";
                var data = {
                    unit: unit,
                    Fromdate: from_date,
                    Todate: to_date
                };
                console.log(data);
                $('#PpeExemptionData').html('');

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(response) {
                        $('#PpeExemptionData').html(response);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch PPE Exemption data:', xhr.responseText);
                    }
                });
            }

            // Fetch PPE Request Chart Data
            function PpeChartRequestData(unit, from_date, to_date) {
                var url = "{{ admin_url('ppe-dashboard/ppeRequestList') }}";
                var data = {
                    unit: unit,
                    Fromdate: from_date,
                    Todate: to_date
                };

                $('#PpeshoerequestData').html('');

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(response) {
                        $('#PpeshoerequestData').html(response);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch PPE Request data:', xhr.responseText);
                    }
                });
            }
            var fromDatepicker = flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        var startDate = selectedDates[0];
                        toDatepicker.set('minDate', startDate);
                        toDatepicker.clear();
                    }
                }
            });

            var toDatepicker = flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                minDate: "today"
            });

            filterDashboard();
        });
    </script>
@endpush
