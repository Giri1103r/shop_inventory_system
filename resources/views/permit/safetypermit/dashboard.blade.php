@extends('admin.layouts.admin')
@section('title', 'Dashboard')
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
            <div>
                <x-button-filter dataId="" class="search" href=""></x-button-filter>
            </div>
            <div id="search" class="collapse card">
                <form action="" id="formsearch">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('Location') }}</label>
                                    <select name="location" id="location" style="width: 100%" class="form-control select2">
                                        <option value="">Select Location</option>
                                        {{-- @foreach ($locationlist as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->location_type_name }}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('From Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required="" class="form-control todaymaxdatepicker"
                                            id="fromDate" name="fromDate" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-3 form-input">
                                    <label for="status" class="form-label ">{{ __('To Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required="" class="form-control todaymaxdatepicker"
                                            id="toDate" name="toDate" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" id="searchform" onclick="filterDashboard();"
                                        class="btn btn-primary mt-4">Search</button>
                                    <button type="reset" id="resetform" onclick="resetForm()"
                                        class="btn btn-danger mt-4">Reset</button>

                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-xl-3 col-xxl-4 col-sm-6 my-order-ile">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">SAFETY PERMIT STATUS</h4>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height370">
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToUAUClist()">
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
                                            <h5 class="fs-14 font-w600 mb-0">Total Safety Permit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">

                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="total_permit">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(1) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">EHS Verification Pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_verification_pending">0</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(1) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">EHS Approval Pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_approval_pending">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(1) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">EHS Hold the Permit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_hold">0</h5>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(3) }}')">
                                <div class="d-flex align-items-center">
                                    <span>
                                        <svg width="38" height="38" viewBox="0 0 64 64" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="32" cy="32" r="32" fill="#374C98" />
                                            <path
                                                d="M32.5 14C29.5609 14 27.0578 15.8773 26.1367 18.5H23.5C21.018 18.5 19 20.518 19 23V45.5C19 47.982 21.018 50 23.5 50H41.5C43.982 50 46 47.982 46 45.5V23C46 20.518 43.982 18.5 41.5 18.5H38.8633C37.9422 15.8773 35.4391 14 32.5 14ZM32.5 18.5C33.0967 18.5 33.669 18.7371 34.091 19.159C34.5129 19.581 34.75 20.1533 34.75 20.75C34.75 21.3467 34.5129 21.919 34.091 22.341C33.669 22.7629 33.0967 23 32.5 23C31.9033 23 31.331 22.7629 30.909 22.341C30.4871 21.919 30.25 21.3467 30.25 20.75C30.25 20.1533 30.4871 19.581 30.909 19.159C31.331 18.7371 31.9033 18.5 32.5 18.5ZM24.0625 33.125C24.0625 32.6774 24.2403 32.2482 24.5568 31.9318C24.8732 31.6153 25.3024 31.4375 25.75 31.4375C26.1976 31.4375 26.6268 31.6153 26.9432 31.9318C27.2597 32.2482 27.4375 32.6774 27.4375 33.125C27.4375 33.5726 27.2597 34.0018 26.9432 34.3182C26.6268 34.6347 26.1976 34.8125 25.75 34.8125C25.3024 34.8125 24.8732 34.6347 24.5568 34.3182C24.2403 34.0018 24.0625 33.5726 24.0625 33.125ZM31.375 32H40.375C40.9938 32 41.5 32.5062 41.5 33.125C41.5 33.7438 40.9938 34.25 40.375 34.25H31.375C30.7562 34.25 30.25 33.7438 30.25 33.125C30.25 32.5062 30.7562 32 31.375 32ZM24.0625 39.875C24.0625 39.4274 24.2403 38.9982 24.5568 38.6818C24.8732 38.3653 25.3024 38.1875 25.75 38.1875C26.1976 38.1875 26.6268 38.3653 26.9432 38.6818C27.2597 38.9982 27.4375 39.4274 27.4375 39.875C27.4375 40.3226 27.2597 40.7518 26.9432 41.0682C26.6268 41.3847 26.1976 41.5625 25.75 41.5625C25.3024 41.5625 24.8732 41.3847 24.5568 41.0682C24.2403 40.7518 24.0625 40.3226 24.0625 39.875ZM30.25 39.875C30.25 39.2562 30.7562 38.75 31.375 38.75H40.375C40.9938 38.75 41.5 39.2562 41.5 39.875C41.5 40.4938 40.9938 41 40.375 41H31.375C30.7562 41 30.25 40.4938 30.25 39.875Z"
                                                stroke="white" stroke-width="2" />
                                        </svg>

                                    </span>
                                    <div class="ms-3">
                                        <a href="javascript:void(0);">
                                            <h5 class="fs-14 font-w600 mb-0">EHS Declined the Permit-Rework the permit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_declined">0</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">EHS Re-assigned the Permit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_reassigned">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Plant Head Approval Pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="planthead_approval">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Plant Head Approved</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="planthead_approved">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">EHS Resumed the permit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="ehs_resumed">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Permit Expired</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="permit_expired">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Permit Extended - EHS extension approval
                                                pending</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="permitextend_approval">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Permit Extended - EHS extension approved</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="permitextend_approved">0</h5>
                                    </div>
                                </div>
                            </div>
                
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Permit extension rejected - Resubmit extension
                                            </h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="permitextend_rejected">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Plant Head Rejected - EHS Verification
                                                resubmit</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="planthead_rejected">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                                onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                            <h5 class="fs-14 font-w600 mb-0">Permit Cancelled</h5>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="ms-3">
                                        <h5 class="fs-14 font-w600 mb-0 status-count" id="cancelled">0</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center market-preview"
                            onclick="redirectToTraininglist('{{ encryptId(4) }}')">
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
                                        <h5 class="fs-14 font-w600 mb-0">Closed</h5>
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="ms-3">
                                    <h5 class="fs-14 font-w600 mb-0 status-count" id="closed">0</h5>
                                </div>
                            </div>
                        </div>
                        </div>

                    </div>
                </div>
                <div class="col-xl-9 col-xxl-8">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Month Wise PTW</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="monthwiseptw_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="monthwiseptw"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-3">
                            <h4 class="card-title">Unit Wise PTW </h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="unitwiseptw_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="unitwiseptw"> </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $('#location').select2({

            allowClear: true,
            closeOnSelect: true,
        });

        function redirectopermanage(link) {
            var url = "{{ admin_url('') }}" + link;
            window.location.href = url;
        }


        function resetForm() {
            location.reload();
        }

        function filterDashboard() {
            Location = $("#location").val()
            Fromdate = $("#fromDate").val();
            Todate = $("#toDate").val();
            loadunitwisecount();
            loadmonthewisecount();
            getalldashmetric(Fromdate, Todate);

        }

        function loadunitwisecount() {
            var url = "{{ admin_url('safetypermit/dashboard/unitwiseptw') }}"
            var data = {
                // Location: Location,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#unitwiseptw').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#unitwiseptw').html(dataAjx);
                }
            });
        }
        function loadmonthewisecount() {
            var url = "{{ admin_url('safetypermit/dashboard/monthwiseptw') }}"
            var data = {
                // Location: Location,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#unitwiseptw').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#unitwiseptw').html(dataAjx);
                }
            });
        }



        function getalldashmetric(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('safetypermit/dashboard/getpermitstatus') }}"
            var data = {
                // Factory: Factory,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#total_permit').html('0');
            $('#ehs_verification_pending').html('0');
            $('#ehs_approval_pending').html('0');
            $('#ehs_hold').html('0');
            $('#ehs_declined').html('0');
            $('#ehs_reassigned').html('0');
            $('#planthead_approval').html('0');
            $('#planthead_approved').html('0');
            $('#ehs_resumed').html('0');
            $('#permit_expired').html('0');
            $('#permitextend_approval').html('0');
            $('#permitextend_approved').html('0');
            $('#permitextend_rejected').html('0');
            $('#planthead_rejected').html('0');
            $('#cancelled').html('0');
            $('#closed').html('0');

            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {
                    var metric_data = JSON.parse(dataAjx);
                    var permit_status = metric_data['permit_status'];
                    $('#total_permit').html(permit_status[0].count);
                    $('#ehs_verification_pending').html(permit_status[1].count);
                    $('#ehs_approval_pending').html(permit_status[2].count);
                    $('#ehs_hold').html(permit_status[2].count);
                    $('#ehs_declined').html(permit_status[3].count);
                    $('#ehs_reassigned').html(permit_status[4].count);
                    $('#planthead_approval').html(permit_status[5].count);
                    $('#planthead_approved').html(permit_status[6].count);
                    $('#ehs_resumed').html(permit_status[7].count);
                    $('#permit_expired').html(permit_status[8].count);
                    $('#permitextend_approval').html(permit_status[9].count);
                    $('#permitextend_approved').html(permit_status[10].count);
                    $('#permitextend_rejected').html(permit_status[11].count);
                    $('#planthead_rejected').html(permit_status[12].count);
                    $('#cancelled').html(permit_status[13].count);
                    $('#closed').html(permit_status[14].count);

                }
            });
        }
        
    </script>
    <script>
        $(document).ready(function() {

            $('#fromDate').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                endDate: new Date()
            }).on('changeDate', function(selected) {
                var startDate = new Date(selected.date.valueOf());
                $('#toDate').datepicker('setStartDate', startDate);
                if ($('#toDate').val() !== '') {
                    var endDate = new Date($('#toDate').val());
                    if (startDate > endDate) {
                        $('#toDate').datepicker('setDate', startDate);
                    }
                }
            });

            $('#toDate').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                endDate: new Date()
            }).on('changeDate', function(selected) {
                var endDate = new Date(selected.date.valueOf());
                $('#fromDate').datepicker('setEndDate', endDate);
                if ($('#fromDate').val() !== '') {
                    var startDate = new Date($('#fromDate').val());
                    if (startDate > endDate) {
                        $('#fromDate').datepicker('setDate', endDate);
                    }
                }
            });

            filterDashboard();
        });
    </script>
@endpush
