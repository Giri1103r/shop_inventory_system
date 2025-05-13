@extends('admin.layouts.admin')
@section('title', 'KPI Dashboard')
@section('header', 'KPI Dashboard')

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

            <div class="row">
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Inspection Type Wise Count</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="inspection_wise_count_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="inspection_wise_count"> </div>

                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">PTW Open Close</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ptw_open_close_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="ptw_open_close_count"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">PTW Type Wise Count</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="ptw_type_wise_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="ptw_type_wise_count"></div>

                    </div>
                </div>
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">IIR Type Wise RCPA</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="iirTypewiseRCPA_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="iirTypewiseRCPACount"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART5</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart5_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="LoadChart5Count"> </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART6</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart6_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="LoadChart6Count"> </div>

                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART7</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart7_download"></a>
                        </div>
                        <div id="LoadChart7Count"></div>
                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART8</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart8_download"></a>
                        </div>
                        <div id="LoadChart8Count"></div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART9</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart9_download"></a>
                        </div>
                        <div id="LoadChart9Count"></div>
                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART10</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart10_download"></a>
                        </div>
                        <div id="LoadChart10Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART11</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart11_download"></a>
                        </div>
                        <div id="LoadChart11Count"></div>
                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART12</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart12_download"></a>
                        </div>
                        <div id="LoadChart12Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART13</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart13_download"></a>
                        </div>
                        <div id="LoadChart13Count"></div>
                    </div>
                </div>
                <div class="col-xl-6 col-xxl-6">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART14</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart18_download"></a>
                        </div>
                        <div id="LoadChart18Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART15</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart14_download"></a>
                        </div>
                        <div id="LoadChart14Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART16</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart15_download"></a>
                        </div>
                        <div id="LoadChart15Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART17</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart16_download"></a>
                        </div>
                        <div id="LoadChart16Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART18</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart17_download"></a>
                        </div>
                        <div id="LoadChart17Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART19</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart19_download"></a>
                        </div>
                        <div id="LoadChart19Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART20</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart20_download"></a>
                        </div>
                        <div id="LoadChart20Count"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">CHART21</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="LoadChart21_download"></a>
                        </div>
                        <div id="LoadChart21Count"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Injury Report - Based On Body Parts</h4>
                        </div>
                        <div class="card-body" id="loadinjurychart"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Training Completion</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="TrainingCompletion_download"></a>
                        </div>
                        <div id="TrainingCompletionCount"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Type of IIR</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="TypeofIIR_download"></a>
                        </div>
                        <div id="TypeofIIRCount"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">Accident Report Unit Wise</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload"
                                id="AccidentReportUnitWise_download"></a>
                        </div>
                        <div id="AccidentReportUnitWiseCount"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">IIR Type Wise UAUC</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="iirTypewiseUAUC_download"></a>
                        </div>
                        <div class="card-body px-0 pt-0 dlab-scroll height450" id="iirTypewiseUAUCCount"> </div>

                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-xl-12 col-xxl-12">
                    <div class="card view_card">
                        <div class="card-header">
                            <h4 class="text-white">RCA Distribution</h4>
                            <a class="fas fa-arrow-alt-circle-down chartdownload" id="RCADistribution_download"></a>
                        </div>
                        <div id="RCADistributionCount"></div>
                    </div>
                </div>
            </div> --}}



        </div>
    </div>

@endsection


@push('script')
    <script>
        function redirectToTraininglist(status, department) {
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
            csrfToken.value = '{{ csrf_token() }}'; // Use Blade directive to get CSRF token
            form.appendChild(csrfToken);

            // Create hidden input fields for each POST data
            var inputStatus = document.createElement('input');
            inputStatus.setAttribute('type', 'hidden');
            inputStatus.setAttribute('name', 'training_status');
            inputStatus.setAttribute('value', status);
            form.appendChild(inputStatus);

            var inputDepartment = document.createElement('input');
            inputDepartment.setAttribute('type', 'hidden');
            inputDepartment.setAttribute('name', 'department_id');
            inputDepartment.setAttribute('value', department);
            form.appendChild(inputDepartment);

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


        function filterDashboard() {
            Factory = $("#factory").val()
            Fromdate = $("#fromDate").val();
            Todate = $("#toDate").val();
            InspectionWiseCount(Fromdate, Todate);
            PTWOpenClose(Fromdate, Todate);
            PTWTypeWiseCount(Fromdate, Todate);
            LoadChart4Count(Fromdate, Todate);
            LoadChart1Count(Fromdate, Todate);
            LoadChart2Count(Fromdate, Todate);
            LoadChart3Count(Fromdate, Todate);
            LoadiirTypewiseRCPACount(Fromdate, Todate);
            LoadChart5Count(Fromdate, Todate);
            LoadChart6Count(Fromdate, Todate);
            LoadChart7Count(Fromdate, Todate);
            LoadChart8Count(Fromdate, Todate);
            LoadChart9Count(Fromdate, Todate);
            LoadChart10Count(Fromdate, Todate);
            LoadChart11Count(Fromdate, Todate);
            LoadChart12Count(Fromdate, Todate);
            LoadChart13Count(Fromdate, Todate);
            LoadChart14Count(Fromdate, Todate);
            LoadChart15Count(Fromdate, Todate);
            LoadChart16Count(Fromdate, Todate);
            LoadChart17Count(Fromdate, Todate);
            LoadChart18Count(Fromdate, Todate);
            LoadChart19Count(Fromdate, Todate);
            LoadChart20Count(Fromdate, Todate);
            LoadChart21Count(Fromdate, Todate);
            RCADistributionCount(Fromdate, Todate);
            TrainingCompletionCount(Fromdate, Todate);
            TypeofIIRCount(Fromdate, Todate);
            AccidentReportUnitWiseCount(Fromdate, Todate);
            LoadDepartmentCount(Fromdate, Todate);
            loadfmonthwisetraining(Fromdate, Todate);
            loadtraining_count_status(Fromdate, Todate);
            loadinjurychart(Fromdate, Todate);
            LoadiirTypewiseUAUCCount(Fromdate, Todate);
        }

        function InspectionWiseCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/inspection-count') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#inspection_wise_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#inspection_wise_count').html(dataAjx);
                }
            });
        }

        function PTWOpenClose(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/ptw-open-close') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#ptw_open_close_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#ptw_open_close_count').html(dataAjx);
                }
            });
        }

        function PTWTypeWiseCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/ptw-type-wise-count') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#ptw_type_wise_count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#ptw_type_wise_count').html(dataAjx);
                }
            });
        }

        function LoadiirTypewiseRCPACount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/IIRTypeWiseRCPA') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#iirTypewiseRCPACount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#iirTypewiseRCPACount').html(dataAjx);
                }
            });
        }

        function LoadChart5Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart5') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart5Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart5Count').html(dataAjx);
                }
            });
        }

        function LoadChart6Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart6') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart6Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart6Count').html(dataAjx);
                }
            });
        }

        function LoadChart7Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart7') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart7Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart7Count').html(dataAjx);
                }
            });
        }

        function LoadChart8Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart8') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart8Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart8Count').html(dataAjx);
                }
            });
        }

        function LoadChart9Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart9') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart9Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart9Count').html(dataAjx);
                }
            });
        }

        function LoadChart10Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart10') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart10Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart10Count').html(dataAjx);
                }
            });
        }

        function LoadChart11Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart11') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart11Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart11Count').html(dataAjx);
                }
            });
        }

        function LoadChart12Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart12') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart12Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart12Count').html(dataAjx);
                }
            });
        }

        function LoadChart13Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart13') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart13Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart13Count').html(dataAjx);
                }
            });
        }

        function LoadChart14Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart14') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart14Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart14Count').html(dataAjx);
                }
            });
        }

        function LoadChart15Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart15') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart15Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart15Count').html(dataAjx);
                }
            });
        }

        function LoadChart16Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart16') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart16Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart16Count').html(dataAjx);
                }
            });
        }

        function LoadChart17Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart17') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart17Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart17Count').html(dataAjx);
                }
            });
        }

        function LoadChart18Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart18') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart18Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart18Count').html(dataAjx);
                }
            });
        }

        function LoadChart19Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart19') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart19Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart19Count').html(dataAjx);
                }
            });
        }

        function LoadChart20Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart20') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart20Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart20Count').html(dataAjx);
                }
            });
        }

        function LoadChart21Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart21') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart21Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart21Count').html(dataAjx);
                }
            });
        }

        function RCADistributionCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/RCADistributionCount') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#RCADistributionCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#RCADistributionCount').html(dataAjx);
                }
            });
        }

        function TrainingCompletionCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/TrainingCompletionCount') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#TrainingCompletionCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#TrainingCompletionCount').html(dataAjx);
                }
            });
        }

        function TypeofIIRCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/TypeofIIRCount') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#TypeofIIRCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#TypeofIIRCount').html(dataAjx);
                }
            });
        }

        function AccidentReportUnitWiseCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/AccidentReportUnitWiseCount') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#AccidentReportUnitWiseCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#AccidentReportUnitWiseCount').html(dataAjx);
                }
            });
        }

        function LoadDepartmentCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('training/dashboard/department') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadDepartmentCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadDepartmentCount').html(dataAjx);
                }
            });
        }


        function loadfmonthwisetraining(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('training/dashboard/monthwisetraining') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#Loadmonthwisetraining').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#Loadmonthwisetraining').html(dataAjx);
                }
            });
        }

        function loadtraining_count_status(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('training/dashboard/trainingStatusCount') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#trainingStatusPieChart').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#trainingStatusPieChart').html(dataAjx);
                }
            });
        }

        function loadinjurychart(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/injurypart') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#loadinjurychart').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#loadinjurychart').html(dataAjx);
                }
            });
        }

        function LoadiirTypewiseUAUCCount(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/IIRTypeWiseUAUC') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#iirTypewiseUAUCCount').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#iirTypewiseUAUCCount').html(dataAjx);
                }
            });
        }

        $(document).ready(function() {
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
