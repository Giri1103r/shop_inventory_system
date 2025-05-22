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
            <div id="search" class="collapse card">
                <form action="" id="formsearch">
                    <div class="px-2 boder-rounded">
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

                <div class="px-2 boder-rounded">


                    <div class="row gx-0">

                        <div class="col-md-2 gx-0">
                            <div class="card  h- w-100">
                                <div class="card-header py-2 mb-2">
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
                            <div class="row gx-0">
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[0]['link']) ? $moduleLink[0]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #a3f0dc 0%, #83aeee 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[0]['name']) ? $moduleLink[0]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[0]['count']) ? $moduleLink[0]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[1]['link']) ? $moduleLink[1]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);">
                                        <div class="px-2 boder-rounded"
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[1]['name']) ? $moduleLink[1]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[1]['count']) ? $moduleLink[1]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[2]['link']) ? $moduleLink[2]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #d2f8bc 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[2]['name']) ? $moduleLink[2]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[2]['count']) ? $moduleLink[2]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[3]['link']) ? $moduleLink[3]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #FFC796 0%, #FF6B95 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">

                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[3]['name']) ? $moduleLink[3]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[3]['count']) ? $moduleLink[3]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-0">
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat"
                                        onclick="redirectopermanage('{{ isset($moduleLink[4]['link']) ? $moduleLink[4]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #5ba4f1, #cfeeee);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[4]['name']) ? $moduleLink[4]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[4]['count']) ? $moduleLink[4]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[5]['link']) ? $moduleLink[5]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[5]['name']) ? $moduleLink[5]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[5]['count']) ? $moduleLink[5]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[6]['link']) ? $moduleLink[6]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #23aec0 0%, #f3a7bd 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(128, 42, 116, 0.103), 0 1px 3px rgba(61, 26, 156, 0.329);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[6]['name']) ? $moduleLink[6]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[6]['count']) ? $moduleLink[6]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[7]['link']) ? $moduleLink[7]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #28dfc6 0%, #5698ee 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[7]) && isset($moduleLink[7]['name']))
                                                            {{ $moduleLink[7]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
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

                            <div class="row gx-0">
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat"
                                        onclick="redirectopermanage('{{ isset($moduleLink[8]['link']) ? $moduleLink[8]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #ec94cf, #c2daf3);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[8]['name']) ? $moduleLink[8]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[8]['count']) ? $moduleLink[8]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[9]['link']) ? $moduleLink[9]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f0868c 0%, #c3cfe2 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[9]['name']) ? $moduleLink[9]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[9]['count']) ? $moduleLink[9]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[10]['link']) ? $moduleLink[10]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #89f7fe, #66a6ff);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(128, 42, 116, 0.103), 0 1px 3px rgba(61, 26, 156, 0.329);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        {{ isset($moduleLink[10]['name']) ? $moduleLink[10]['name'] : '' }}
                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        {{ isset($moduleLink[10]['count']) ? $moduleLink[10]['count'] : 0 }}
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[11]['link']) ? $moduleLink[11]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #a18cd1, #fbc2eb);
">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[11]) && isset($moduleLink[11]['name']))
                                                            {{ $moduleLink[11]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[11]) && isset($moduleLink[11]['count']))
                                                            {{ $moduleLink[11]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-0">
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[12]['link']) ? $moduleLink[12]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f6d365, #fda085);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[12]) && isset($moduleLink[12]['name']))
                                                            {{ $moduleLink[12]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[12]) && isset($moduleLink[12]['count']))
                                                            {{ $moduleLink[12]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[13]['link']) ? $moduleLink[13]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #cfd9df, #e2ebf0);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[13]) && isset($moduleLink[13]['name']))
                                                            {{ $moduleLink[13]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[13]) && isset($moduleLink[13]['count']))
                                                            {{ $moduleLink[13]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[14]['link']) ? $moduleLink[14]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #ffecd2, #fcb69f);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[14]) && isset($moduleLink[14]['name']))
                                                            {{ $moduleLink[14]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[14]) && isset($moduleLink[14]['count']))
                                                            {{ $moduleLink[14]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[15]['link']) ? $moduleLink[15]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f9d29d, #ffd8cb); ">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[15]) && isset($moduleLink[15]['name']))
                                                            {{ $moduleLink[15]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[15]) && isset($moduleLink[15]['count']))
                                                            {{ $moduleLink[15]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row gx-0">
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[16]['link']) ? $moduleLink[16]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #5ba4f1, #cfeeee);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[16]) && isset($moduleLink[16]['name']))
                                                            {{ $moduleLink[16]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[16]) && isset($moduleLink[16]['count']))
                                                            {{ $moduleLink[16]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[17]['link']) ? $moduleLink[17]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(to right, #28dfc6 0%, #5698ee 100%);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[17]) && isset($moduleLink[17]['name']))
                                                            {{ $moduleLink[17]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[17]) && isset($moduleLink[17]['count']))
                                                            {{ $moduleLink[17]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[18]['link']) ? $moduleLink[18]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #f6d365, #fda085);">
                                        <div class="px-2 boder-rounded "
                                            style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                            <div class="media ai-icon" style="display: flex; align-items: center;">
                                                <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                </span>
                                                <div class="media-body" style="display: block;">
                                                    <p class="mb-1 mt-1" style="color:black;">
                                                        @if (is_array($moduleLink[18]) && isset($moduleLink[18]['name']))
                                                            {{ $moduleLink[18]['name'] }}
                                                        @endif

                                                    </p>
                                                    <h4 class="mb-1 medicine-requisition">
                                                        @if (is_array($moduleLink[18]) && isset($moduleLink[18]['count']))
                                                            {{ $moduleLink[18]['count'] }}
                                                        @endif
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-xl-3 col-lg-3 col-sm-3 p-1 boder-rounded">
                                    <div class="widget-stat "
                                        onclick="redirectopermanage('{{ isset($moduleLink[19]['link']) ? $moduleLink[19]['link'] : '' }}')"
                                        style="cursor: pointer;background-image: linear-gradient(135deg, #a18cd1, #fbc2eb);
                                        <div class="px-2
                                        boder-rounded "
                                                style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                                <div class="media ai-icon" style="display: flex; align-items: center;">
                                                    <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">



                                                    </span>
                                                    <div class="media-body" style="display: block;">
                                                        <p class="mb-1 mt-1" style="color:black;">
                                                             @if (is_array($moduleLink[19]) && isset($moduleLink[19]['name']))
                                        {{ $moduleLink[19]['name'] }}
                                        @endif

                                        </p>
                                        <h4 class="mb-1 medicine-requisition">
                                            @if (is_array($moduleLink[19]) && isset($moduleLink[19]['count']))
                                                {{ $moduleLink[19]['count'] }}
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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="ptw_type_wise_countDiv"></div>

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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="ptw_open_close_countDiv"> </div>

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
                    <a class="fas fa-arrow-alt-circle-down chartdownload" id="total_incidents_download"></a>
                </div>
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="TotalIncidentsCountDiv"> </div>

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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="heatmapofImsDataDiv"> </div>

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
                    <a class="fas fa-arrow-alt-circle-down chartdownload" id="AccidentReportUnitWise_download"></a>
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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="iirTypewiseUAUCCountDiv"> </div>
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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="iirTypewiseRCPACountDiv"> </div>

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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="nearMissCountDiv"> </div>
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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="inspection_wise_countDiv"> </div>

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
                    <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadPPEIssuanceGroupWise_download"></a>
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
                    <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadPPEAvailabilityChart_download"></a>
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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="LoadmonthwisetrainingDiv"> </div>

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
                <div class="px-2 boder-rounded px-0 pt-0 dlab-scroll height450" id="LoadDepartmentCountDiv">
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
