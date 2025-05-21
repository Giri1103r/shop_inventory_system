@extends('admin.layouts.admin')
@section('title', 'Leading and Lagging Dashboard')
@section('header', 'Leading and Lagging Dashboard')

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
                            {{-- <h4 class="m-0 " style="padding-left: 10px;">Welcome Back {{ Auth::user()->name }}!
                            </h4> --}}
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
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label">{{ __('common.company') }}</label>
                                        <select name="company_id" id="company_id" class=" form-control single-select"
                                            style="width: 100%">
                                            <option value="">Select Company Name</option>
                                            @foreach ($companies as $company)
                                                <option value="{{ encryptId($company->id) }}">
                                                    {{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label">{{ __('common.location') }}</label>
                                        <select name="location_id" id="location_id" class=" form-control single-select"
                                            style="width: 100%">
                                            <option value="">Select Location</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label">{{ __('common.unit') }}</label>
                                        <select name="unit_id" id="unit_id" class=" form-control single-select"
                                            style="width: 100%">
                                            <option value="">Select Unit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="form-group form-input">
                                        <label class="form-label">{{ __('common.department') }}</label>
                                        <select name="department_id" id="department_id" class=" form-control single-select"
                                            style="width: 100%">
                                            <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-input">
                                        <label class="form-label">{{ __('common.year') }}</label>
                                        <input type="text" name="year" id="year" class="form-control"
                                            placeholder="Enter Year">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-input">
                                        <label class="form-label ">{{ __('common.month') }}</label>
                                        <input type="text" name="month" id="month" class="form-control"
                                            placeholder="Enter Month">
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

            {{-- <div class="row">
                <div class="col-md-6">
                    <div class="row h-50">
                        <div class="card view_card">
                            <div class="card-header">
                                <h4 class="text-white">{{ getLeadingName(LEADING_CATEGORY_1) }}</h4>
                                <a class="fas fa-arrow-alt-circle-down chartdownload" id="stakcedLeadingChartDownload"></a>
                            </div>
                            <div class="card-body px-0 pt-0 dlab-scroll height450" id="stakcedLeadingChart">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">Lagging Indicator Line</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="lagging_line_download"></a>
                        </div>
                        <div id="lagging_line_count"></div>
                    </div>
                </div>

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card responsive">
                        <div class="card-header">
                            <h4 class="text-white">Lagging Indicator Doughnut</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="lagging_doughnut_download"></a>
                        </div>
                        <div id="doughnut_count"></div>
                    </div>
                </div>
            </div> --}}

            <div class="card container dashboard_card mt-3 p-2">
                <div class="card-header">
                    <h4 class="text-white">{{ __('common.leading') }}</h4>
                </div>
                <div class="row" id="leading_container">
                    <div class="card-body">

                    </div>
                </div>

                <div class="card-header">
                    <h4 class="text-white">{{ __('common.lagging') }}</h4>
                </div>
                <div class="row" id="lagging_container">

                </div>
            </div>
        </div>
    </div>

@endsection


@push('script')
    <script>
        function filterDashboard() {
            Fromdate = $("#fromDate").val();
            Todate = $("#toDate").val();
            company_id = $("#company_id").val();
            location_id = $("#location_id").val();
            unit_id = $("#unit_id").val();
            department_id = $("#department_id").val();
            year = $("#year").val();
            month = $("#month").val();
            leadingLaggingFilter(Fromdate, Todate, company_id, location_id, unit_id, department_id, year, month);
            // leadingStackedChart(Fromdate, Todate);
            // lagging_line(Fromdate, Todate);
            // lagging_doughnut(Fromdate, Todate);

        }

        // CompanyId = '', CompanyId = '',  CompanyId = '',

        function lagging_line(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/leading-lagging/lagging-line') }}"
            var data = {
                // CompanyId: CompanyId,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#lagging_line_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {
                    $('#lagging_line_count').html(dataAjx);
                }
            });
        }

        function lagging_doughnut(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/leading-lagging/lagging-indicator') }}"
            var data = {
                // CompanyId: CompanyId,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#doughnut_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {
                    $('#doughnut_count').html(dataAjx);
                }
            });
        }

        function leadingStackedChart(CompanyId = '', Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/leading-lagging/leading/chart1') }}"
            var data = {
                CompanyId: CompanyId,
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#stakcedLeadingChart').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {
                    $('#stakcedLeadingChart').html(dataAjx);
                }
            });
        }

        function leadingLaggingFilter(Fromdate = '', Todate = '', CompanyId = '', location_id = '', unit_id = '',
            department_id = '', year = '', month = '') {
            var url = "{{ admin_url('kpi/dashboard/leading-lagging') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
                company_id: CompanyId,
                location_id: location_id,
                unit_id: unit_id,
                department_id: department_id,
                year: year,
                month: month,
            };
            $('#lagging_line_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {
                    updateLeadLagUI(dataAjx.leadings, dataAjx.laggings);
                }
            });
        }

        function updateLeadLagUI(leadings, laggings) {
            const leadingContainer = $('#leading_container');
            const laggingContainer = $('#lagging_container');

            leadingContainer.html('');
            laggingContainer.html('');

            leadings.forEach(item => {
                leadingContainer.append(`
                        <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to right, #a3f0dc 0%, #83aeee 100%);">
                                <div class="card-body"
                                    style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <div class="media-body" style="display: block;">
                                            <p class="mb-1" style="color:black;">${item.name}</p>
                                            <h4 class="mb-0 ">${item.value}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
            });

            laggings.forEach(item => {
                laggingContainer.append(`
                        <div class="col-xl-3 col-lg-3 col-sm-3 p-2">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to right, #d44f4a 0%, #ee8383 100%);">
                                <div class="card-body"
                                    style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <div class="media-body" style="display: block;">
                                            <p class="mb-1 text-white fw-bold" >${item.name}</p>
                                            <h4 class="mb-0 text-white fw-bold">${item.value}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
            });
        }

        $(document).ready(function() {

            $('#year').datepicker({
                format: 'yyyy',
                minViewMode: 2,
                autoclose: true
            });

            $('#month').datepicker({
                format: 'mm',
                minViewMode: 1,
                autoclose: true
            });

            $(document).on('change', '#company_id', function() {
                var companyId = $(this).val();
                if (companyId) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append(
                                '<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                $('#location_id').append('<option value="' + value.id +
                                    '">' + value
                                    .name + '</option>');
                            });
                            $('#location_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching locations. Please try again.');
                        }
                    });
                } else {
                    $('#location_id').empty().append('<option value="">Select Location</option>');
                    $('#location_id').trigger('change.');
                }
            });

            $(document).on('change', '#location_id', function() {
                var locationId = $(this).val();
                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append(
                                '<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' + value.id +
                                    '">' + value
                                    .name + '</option>');
                            });
                            $('#unit_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                    $('#unit_id').trigger('change.');
                }
            });

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            $('#department_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                    $('#department_id').trigger('change.');
                }
            });


            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
            const toDatePicker = flatpickr("#toDate", {
                dateFormat: "d-m-Y",
                minDate: "today",
            });

            flatpickr("#fromDate", {
                dateFormat: "d-m-Y",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const fromDate = selectedDates[0];
                        if (toDatePicker) {
                            toDatePicker.set("minDate",
                                dateStr);
                        }
                    }
                },
            });

            filterDashboard();
        });
    </script>
@endpush
