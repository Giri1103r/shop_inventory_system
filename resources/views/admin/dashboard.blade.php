@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
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
            <div id="search" class="collapse card p-2 mt-2">
                <form action="" id="formsearch">
                    <div class="px-2 boder-rounded">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4 mb-3 form-input">
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
                                <div class="col-md-4 form-input">
                                    <label for="fromDate" class="form-label">{{ __('From Date') }}</label>
                                    <div class="input-group date form-input">
                                        <input type="text" required class="form-control todaymaxdatepicker"
                                            id="fromDate" name="fromDate" value="">
                                        <div class="input-group-addon input-group-text">
                                            <span class="fa fa-calendar"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 form-input">
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
                                        class="btn btn-primary mt-2">Search</button>
                                    <button type="reset" id="resetform" class="btn btn-danger mt-2">Reset</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card dashboard_card mt-2 p-2">

                <div class="px-2 boder-rounded">


                    <div class="row gx-0">

                        <div class="col-md-2 gx-0">
                            <div class="card mt-2  h- w-100">
                                <div class="card-header py-2  mb-2">
                                    <h5 class="m-0 text-center text-white">Operating Management</h5>
                                </div>
                                <div class="px-2 mt-1">
                                    <div class="container-fluid p-0">
                                        @foreach ($masterLink as $item)
                                            <div class="row  mb-3 border-bottom">
                                                <div class="col">
                                                    <div
                                                        class=" boder-bottom d-flex card-rounded justify-content-between align-items-center flex-wrap">
                                                        <a href="{{ admin_url($item['link']) }}"
                                                            class="text-dark me-2 text-truncate"
                                                            style="max-width: calc(100% - 50px);">
                                                            {{ $item['name'] ?? '' }}
                                                        </a>
                                                        <span
                                                            class="badge bg-primary text-center  text-black me-2">{{ $item['count'] ?? 0 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 ">
                            {{-- row 1 --}}
                            <div class="row gx-0">
                                {{-- PPE Request --}}
                                <div class="ppe-request col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(to right, #a3f0dc 0%, #83aeee 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">PPE Request</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_pperequest">0</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- PPE Exemption --}}
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="ppe-exemption widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">PPE Exemption</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_ppe_exemption">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Safety Permit --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="safety-permit  widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #d2f8bc 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">Safety Permit</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_safety_permit">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Training --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="training-schedule widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #FFC796 0%, #FF6B95 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">Training</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_training_schedule">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- row 2 --}}

                            <div class="row gx-0">
                                {{-- 6 S Audit Assesment --}}
                                <div class="audit-assessment col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #5ba4f1, #cfeeee);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">6S Audit Assessment</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_audit_assessment">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Audit Analysis --}}
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="audit-analysis widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">Audit Analysis</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_audit_analysis">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Monthly Audit --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="monthly-audit  widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #23aec0 0%, #f3a7bd 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">EHS Audit Calendar</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_monthly_audit">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Inter unit audit --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="inter-unit-audit widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #28dfc6 0%, #5698ee 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">Inter Unit Audit</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_inter_unit_audit">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- row 3 --}}

                            <div class="row gx-0">
                                {{-- No of OPD --}}
                                <div class="no-of-opd col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #ec94cf, #c2daf3);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of OPD</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_opd">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of First Aid --}}
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="first-aid widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f0868c 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of First Aid</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_first_aid">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Minor Accident --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="minor-accident widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #89f7fe, #66a6ff);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Minor Acident</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_minor_acident">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Major Accident --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="major-accident widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #a18cd1, #fbc2eb);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Major Accident</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_major_accident">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- row 4 --}}

                            <div class="row gx-0">
                                {{-- No of Near miss --}}
                                <div class="near-miss col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f6d365, #fda085);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Near Miss</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_near_miss">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Unsafe Act --}}
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="un-safe-act widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #cfd9df, #e2ebf0);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of UnSafe Act</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_un_safe_act">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Unsafe Condition --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="unsafe-condition widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #ffecd2, #fcb69f);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Unsafe Condition</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_unsafe_condition">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of 6 s Observation --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="gemba-walk widget-stat"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f9d29d, #ffd8cb); ">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of 6 s Observation</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_gembawalk">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            {{-- row 5 --}}

                            <div class="row gx-0">
                                {{-- No of Fire Incidence --}}
                                <div class="fire-incidence col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #5ba4f1, #cfeeee);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Fire Incidence</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_fire_incidence">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Fire inspection --}}
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="fire-inspection widget-stat "
                                        style="background-image: linear-gradient(to right, #28dfc6 0%, #5698ee 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Fire Inspection</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_fire_inspection">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of OHC Inspection --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="ohc-inspection widget-stat"
                                        style="background-image: linear-gradient(135deg, #f6d365, #fda085);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of OHC Inspection</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_ohc_inspection">0
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- No of Safety Inspection --}}

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="safety-inspection widget-stat"
                                        style="background-image: linear-gradient(135deg, #a18cd1, #fbc2eb);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <h4 class="fw-bold text-black">No of Safety Inspection</h4>
                                                    <h4 class="my-1 count-value text-black" id="total_safety_inspection">0
                                                    </h4>
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
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="monthwiseptwDiv">

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
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="ptw_type_wise_countDiv">
                                </div>

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
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="unitwiseptwDiv">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card responsive">
                                <div class="card-header">
                                    <h4 class="text-white">PTW – Violation Hold for Compliance</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="LoadPtwHoldViolation_download"></a>
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
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="ptw_open_close_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="ptw_open_close_countDiv">
                                </div>

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
                                <div class="px-2 boder-rounded" id="loadinjurychartDiv"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">Total Incidents (YTD)</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="total_incidents_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="TotalIncidentsCountDiv">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">Heatmap of IMS Data</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="heatmapofImsData_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="heatmapofImsDataDiv">
                                </div>

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
                                <div id="accident_report_unit_wise"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">IIR Type Wise UAUC</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="iirTypewiseUAUC_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="iirTypewiseUAUCCountDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">IIR Type Wise RCPA</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="iirTypewiseRCPA_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="iirTypewiseRCPACountDiv">
                                </div>

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
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="nearMissCountDiv">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card responsive">
                                <div class="card-header">
                                    <h4 class="text-white">Unsafe Act / Unsafe Condition Static Report</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="uaucstaticreport_download"></a>
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
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="inspection_wise_count_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="inspection_wise_countDiv">
                                </div>

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
                                    <h4 class="text-white">PPE Consumption Group Wise</h4>
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
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="TrainingCompletion_download"></a>
                                </div>
                                <div id="training_open_close"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Training Management Topic wise --}}

                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card responsive">
                                <div class="card-header">
                                    <h4 class="text-white">Training Hour of Topic Wise</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="LoadTrainingHourSafetyDepartmentWise_download"></a>
                                </div>
                                <div id="training_topic_wise_count"></div>
                            </div>
                        </div>
                    </div>
                    {{-- Training management Month wise count --}}
                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">MONTH WISE TRAINING COUNT</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="month_wise_training_count_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="month_wise_training_count">
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Training management department wise count --}}

                    <div class="row">
                        <div class="col-xl-12 col-xxl-12">
                            <div class="card dashboard_card">
                                <div class="card-header">
                                    <h4 class="text-white">DEPARTMENT WISE TRAINING COUNT</h4>
                                    <a class="fas fa-arrow-alt-circle-down chartdownload"
                                        id="LoadDepartmentCount_download"></a>
                                </div>
                                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450"
                                    id="LoadDepartmentCountDiv">
                                </div>


                            </div>
                        </div>
                    </div>


                </div>
            </div>
        @endsection


        @push('scripts')
            <script>
                $(document).ready(function() {

                    // row -1
                    PPERequestCard();
                    PPEExemptionCard();
                    getTrainingSchedule();
                    getSafetypermittotal();

                    // row -2
                    getInterUnitTotal();
                    getMonthlyAuditTotal();
                    getAuditAnalysisTotal();
                    getAuditAssessmentTotal();

                    // row - 3
                    getFirstAid();
                    getMajorAccident();
                    getMinorAccident();
                    getOPD();

                    // row 4

                    getNearmiss();
                    getUnsafeact();
                    getUnsafeCondition();
                    getGembawalkTotal();

                    // row -- 5
                    getFireInspection();
                    getOHCInspection();
                    getSafetyInspection();
                    getFireIncidence();
                });
                // card redirection
                function cardRedirectUrl(chart_type = null, id = null, url = null) {
                    let CompanyId = $('#company_id').val();
                    let Fromdate = $('#fromDate').val();
                    let Todate = $('#toDate').val();

                    let form = $('<form>', {
                        method: 'POST',
                        action: url,
                    });

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: "{{ csrf_token() }}"
                    }));

                    if (chart_type && id) {
                        form.append($('<input>', {
                            type: 'hidden',
                            name: chart_type,
                            value: id
                        }));
                    }

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'fromDate',
                        value: Fromdate
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'toDate',
                        value: Todate
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'company_id',
                        value: CompanyId
                    }));

                    $('body').append(form);
                    form.submit();
                }

                // Corrected calls
                $('.ppe-request').on('click', function() {
                    var url = "{{ admin_url('ppe_request/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.ppe-exemption').on('click', function() {
                    var url = "{{ admin_url('ppe_exemption/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.safety-permit').on('click', function() {
                    var url = "{{ admin_url('safetypermit/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.training-schedule').on('click', function() {
                    var url = "{{ admin_url('training_schedule/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.audit-assessment').on('click', function() {
                    var url = "{{ admin_url('audit/assessment/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.audit-analysis').on('click', function() {
                    var url = "{{ admin_url('audit/6s-analysis/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.monthly-audit').on('click', function() {
                    var url = "{{ admin_url('audit/monthly-audit/audit-plan/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.inter-unit-audit').on('click', function() {
                    var url = "{{ admin_url('audit/inter-unit-audit/checklist/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.major-accident').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '11';
                    cardRedirectUrl('major_accident', id, url);
                });

                $('.minor-accident').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '10';
                    cardRedirectUrl('minor_accident', id, url);
                });

                $('.first-aid').on('click', function() {
                    var url = "{{ admin_url('ohc/first-aid/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.no-of-opd').on('click', function() {
                    var url = "{{ admin_url('ohc/prescribe-to-patient/list') }}";
                    cardRedirectUrl(null, null, url);
                });

                $('.near-miss').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '8';
                    cardRedirectUrl('near_miss', id, url);
                });
                $('.un-safe-act').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '23';
                    cardRedirectUrl('near_miss', id, url);
                });
                $('.unsafe-condition ').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '24';
                    cardRedirectUrl('near_miss', id, url);
                });
                $('.gemba-walk ').on('click', function() {
                    var url = "{{ admin_url('inspection/gemba-walk/list') }}";
                    cardRedirectUrl(null, null, url);
                });
                $('.fire-incidence').on('click', function() {
                    var url = "{{ admin_url('incident/initial-incident/list/all/type') }}";
                    const id = '5';
                    cardRedirectUrl('fire_incidence', id, url);
                });

                // $('.fire-inspection').on('click', function() {

                //     cardRedirectUrl(null, null, null);
                // });
                // $('.ohc-inspection').on('click', function() {

                //     cardRedirectUrl(null, null, null);
                // });
                // $('.safety-inspection').on('click', function() {

                //     cardRedirectUrl(null, null, null);
                // });


                function redirectToIms(iirType, unitId, incidentId, injuryType, month, uauc, rcpa) {

                    CompanyId = $("#company_id").val();
                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('incident/initial-incident/list/all/type') }}");

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

                function redirectToIssuance(unit) {

                    Fromdate = $("#fromDate").val();
                    Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('ppe_request/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);



                    var inputunit = document.createElement('input');
                    inputunit.setAttribute('type', 'hidden');
                    inputunit.setAttribute('name', 'unit');
                    inputunit.setAttribute('value', unit);
                    form.appendChild(inputunit);





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



                    var inputopenclose = document.createElement('input');
                    inputopenclose.setAttribute('type', 'hidden');
                    inputopenclose.setAttribute('name', 'openclose');
                    inputopenclose.setAttribute('value', openclose);
                    form.appendChild(inputopenclose);


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

                function redirectTotrainigschedule(department) {

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



                    var inputdepartment = document.createElement('input');
                    inputdepartment.setAttribute('type', 'hidden');
                    inputdepartment.setAttribute('name', 'department');
                    inputdepartment.setAttribute('value', department);
                    form.appendChild(inputdepartment);


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

                // gembaWalk
                function redirectToGembaWalk(unitId) {

                    // CompanyId = $("#company_id").val();
                    // Fromdate = $("#fromDate").val();
                    // Todate = $("#toDate").val();
                    // Create a form element
                    var form = document.createElement('form');
                    form.setAttribute('method', 'post');
                    form.setAttribute('action', "{{ admin_url('inspection/gemba-walk/list') }}");

                    // Add CSRF token field
                    var csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);

                    // Create hidden input fields for each POST data
                    var inputUnitId = document.createElement('input');
                    inputUnitId.setAttribute('type', 'hidden');
                    inputUnitId.setAttribute('name', 'unit_name');
                    inputUnitId.setAttribute('value', unitId);
                    form.appendChild(inputUnitId);

                    // Append the form to the body and submit it
                    document.body.appendChild(form);
                    form.submit();
                }

                function redirectopermanage(link) {
                    var url = "{{ admin_url('') }}" + link;
                    window.location.href = url;
                }


                function filterDashboard() {
                    let CompanyId = $('#company_id').val();
                    let Fromdate = $('#fromDate').val();
                    let Todate = $('#toDate').val();

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

                    // card
                    // row -1
                    PPERequestCard(CompanyId, Fromdate, Todate);
                    PPEExemptionCard(CompanyId, Fromdate, Todate);
                    getSafetypermittotal(CompanyId, Fromdate, Todate);
                    getTrainingSchedule(CompanyId, Fromdate, Todate);

                    // row - 2
                    getAuditAssessmentTotal(CompanyId, Fromdate, Todate);
                    getAuditAnalysisTotal(CompanyId, Fromdate, Todate);
                    getMonthlyAuditTotal(CompanyId, Fromdate, Todate);
                    getInterUnitTotal(CompanyId, Fromdate, Todate);

                    // row -3

                    getMajorAccident(CompanyId, Fromdate, Todate);
                    getMinorAccident(CompanyId, Fromdate, Todate);
                    getOPD(CompanyId, Fromdate, Todate);
                    getFirstAid(CompanyId, Fromdate, Todate);

                    // row 4

                    getNearmiss(CompanyId, Fromdate, Todate);
                    getUnsafeact(CompanyId, Fromdate, Todate);
                    getUnsafeCondition(CompanyId, Fromdate, Todate);
                    getGembawalkTotal(CompanyId, Fromdate, Todate);

                    // row -- 5

                    getSafetyInspection(CompanyId, Fromdate, Todate);
                    getOHCInspection(CompanyId, Fromdate, Todate);
                    getFireInspection(CompanyId, Fromdate, Todate);
                    getFireIncidence(CompanyId, Fromdate, Todate);

                }

                // card totals


                function PPERequestCard(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-pperequest-total') }}';
                    $('#total_pperequest').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_pperequest').html(response.total);
                            } else {
                                $('#total_pperequest').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }


                function PPEExemptionCard(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-ppe-exemption-total') }}';
                    $('#total_ppe_exemption').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_ppe_exemption').html(response.total);
                            } else {
                                $('#total_ppe_exemption').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // safety permit

                function getSafetypermittotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-safety-permit-total') }}';
                    $('#total_safety_permit').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_safety_permit').html(response.total);
                            } else {
                                $('#total_safety_permit').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // Training Managment

                function getTrainingSchedule(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-training-schedule-total') }}';
                    $('#total_training_schedule').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_training_schedule').html(response.total);
                            } else {
                                $('#total_training_schedule').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // audit assessment

                function getAuditAssessmentTotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-audit-assessment-total') }}';
                    $('#total_audit_assessment').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_audit_assessment').html(response.total);
                            } else {
                                $('#total_audit_assessment').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // audit analysis

                function getAuditAnalysisTotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-audit-analysis-total') }}';
                    $('#total_audit_analysis').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_audit_analysis').html(response.total);
                            } else {
                                $('#total_audit_analysis').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // monthly audit

                function getMonthlyAuditTotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-monthly-audit-total') }}';
                    $('#total_monthly_audit').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_monthly_audit').html(response.total);
                            } else {
                                $('#total_monthly_audit').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // inter unit audit

                function getInterUnitTotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-inter-audit-total') }}';
                    $('#total_inter_unit_audit').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_inter_unit_audit').html(response.total);
                            } else {
                                $('#total_inter_unit_audit').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }


                // prescribe to patient - ohc management

                function getOPD(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-opd-total') }}';
                    $('#total_opd').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_opd').html(response.total);
                            } else {
                                $('#total_opd').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // get first aid --> ohc management

                function getFirstAid(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-first-aid-total') }}';
                    $('#total_first_aid').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_first_aid').html(response.total);
                            } else {
                                $('#total_first_aid').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // get minor accident --> IMS

                function getMinorAccident(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-minor-accident-total') }}';
                    $('#total_minor_acident').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_minor_acident').html(response.total);
                            } else {
                                $('#total_minor_acident').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // get major accident--> IMS

                function getMajorAccident(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-major-accident-total') }}';
                    $('#total_major_accident').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_major_accident').html(response.total);
                            } else {
                                $('#total_major_accident').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // row -4 near miss--> IMS

                function getNearmiss(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-near-miss-total') }}';
                    $('#total_near_miss').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_near_miss').html(response.total);
                            } else {
                                $('#total_near_miss').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }
                // unsafe act row -4
                function getUnsafeact(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-unsafe-act-total') }}';
                    $('#total_un_safe_act').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_un_safe_act').html(response.total);
                            } else {
                                $('#total_un_safe_act').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }
                // unsafe condition
                function getUnsafeCondition(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-unsafe-condition-total') }}';
                    $('#total_unsafe_condition').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_unsafe_condition').html(response.total);
                            } else {
                                $('#total_unsafe_condition').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // row - 4 gemba walk
                function getGembawalkTotal(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-gemba-walk-total') }}';
                    $('#total_gembawalk').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_gembawalk').html(response.total);
                            } else {
                                $('#total_gembawalk').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // row - 5 --> fire incidence

                function getFireIncidence(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-fire-incidence-total') }}';
                    $('#total_fire_incidence').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_fire_incidence').html(response.total);
                            } else {
                                $('#total_fire_incidence').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // row  -- 6 fire inspection

                function getFireInspection(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-fire-inspection-total') }}';
                    $('#total_fire_inspection').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_fire_inspection').html(response.total);
                            } else {
                                $('#total_fire_inspection').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }
                // row -- 5 ohc inspection
                function getOHCInspection(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-ohc-inspection-total') }}';
                    $('#total_ohc_inspection').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_ohc_inspection').html(response.total);
                            } else {
                                $('#total_ohc_inspection').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
                }

                // row -- 5 Safety inspection
                function getSafetyInspection(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = '{{ admin_url('dashboard/get-safety-inspection-total') }}';
                    $('#total_safety_inspection').html('0');

                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            CompanyId: CompanyId,
                            Fromdate: Fromdate,
                            Todate: Todate,
                        },
                        cache: false,
                        success: function(response) {
                            console.log(response);
                            if (response && response.total !== undefined) {
                                $('#total_safety_inspection').html(response.total);
                            } else {
                                $('#total_safety_inspection').html('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching data: ", error);
                        }
                    });
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
                    var url = "{{ admin_url('dashboard/month-wise-training-count') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#month_wise_training_count').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#month_wise_training_count').html(dataAjx);
                        }
                    });
                }

                // function loadtraining_count_status(CompanyId = '', Fromdate = '', Todate = '') {
                //     var url = "{{ admin_url('dashboard/trainingStatusCount') }}"
                //     var data = {
                //         CompanyId: CompanyId,
                //         Fromdate: Fromdate,
                //         Todate: Todate,
                //     };
                //     $('#trainingStatusPieChartDiv').html('');
                //     $.ajax({
                //         type: 'get',
                //         url: url,
                //         data: data,
                //         cache: false,
                //         success: function(dataAjx) {

                //             $('#trainingStatusPieChartDiv').html(dataAjx);
                //         }
                //     });
                // }

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
                    var url = "{{ admin_url('dashboard/near-miss-frequency') }}"
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
                    var url = "{{ admin_url('dashboard/accident-report-unit-wise') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#accident_report_unit_wise').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#accident_report_unit_wise').html(dataAjx);
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
                    var url = "{{ admin_url('dashboard/training-open-close-total') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#training_open_close').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#training_open_close').html(dataAjx);
                        }
                    });
                }

                function TrainingHourSafetyDepartmentWise(CompanyId = '', Fromdate = '', Todate = '') {
                    var url = "{{ admin_url('dashboard/training-hour-topic-wise') }}"
                    var data = {
                        CompanyId: CompanyId,
                        Fromdate: Fromdate,
                        Todate: Todate,
                    };
                    $('#training_topic_wise_count').html('');
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: data,
                        cache: false,
                        success: function(dataAjx) {

                            $('#training_topic_wise_count').html(dataAjx);
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



                // ims month wise filter

                function redirectchartIMSurl(chart_type, id, url, month = null) {
                    let CompanyId = $('#company_id').val();
                    let Fromdate = $('#fromDate').val();
                    let Todate = $('#toDate').val();

                    let form = $('<form>', {
                        method: 'POST',
                        action: url,
                    });

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: "{{ csrf_token() }}"
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: chart_type,
                        value: id
                    }));

                  

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'month',
                        value: month
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'fromDate',
                        value: Fromdate
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'toDate',
                        value: Todate
                    }));
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'company_id',
                        value: CompanyId
                    }));


                    $('body').append(form);
                    form.submit();
                }
                // trainig schedule

                function redirectcharturl(chart_type, id, url, unit_id = null) {
                    let CompanyId = $('#company_id').val();
                    let Fromdate = $('#fromDate').val();
                    let Todate = $('#toDate').val();

                    let form = $('<form>', {
                        method: 'POST',
                        action: url,
                    });

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: "{{ csrf_token() }}"
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: chart_type,
                        value: id
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'unit_id',
                        value: unit_id
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'fromDate',
                        value: Fromdate
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'toDate',
                        value: Todate
                    }));
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'company_id',
                        value: CompanyId
                    }));


                    $('body').append(form);
                    form.submit();
                }

                function redirectTrainingcharturl(month, id, url) {
                    let CompanyId = $('#company_id').val();
                    let Fromdate = $('#fromDate').val();
                    let Todate = $('#toDate').val();

                    let form = $('<form>', {
                        method: 'POST',
                        action: url,
                    });

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: "{{ csrf_token() }}"
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'month',
                        value: month
                    }));
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'status',
                        value: id
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'fromDate',
                        value: Fromdate
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'toDate',
                        value: Todate
                    }));
                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'company_id',
                        value: CompanyId
                    }));


                    $('body').append(form);
                    form.submit();
                }
            </script>
        @endpush
