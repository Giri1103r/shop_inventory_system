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
                <div class="col-md-3">
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

        </div>
    </div>

@endsection


@push('script')
    <script>
        function filterDashboard() {
            Fromdate = $("#fromDate").val();
            Todate = $("#toDate").val();
            leadingStackedChart(Fromdate, Todate);
            LoadChart2Count(Fromdate, Todate);
            LoadChart3Count(Fromdate, Todate);
            LoadChart4Count(Fromdate, Todate);
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

        function LoadChart2Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart2') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart2Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart2Count').html(dataAjx);
                }
            });
        }

        function LoadChart3Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart3') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart3Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart3Count').html(dataAjx);
                }
            });
        }

        function LoadChart4Count(Fromdate = '', Todate = '') {
            var url = "{{ admin_url('kpi/dashboard/chart4') }}"
            var data = {
                Fromdate: Fromdate,
                Todate: Todate,
            };
            $('#LoadChart4Count').html('');
            $.ajax({
                type: 'get',
                url: url,
                data: data,
                cache: false,
                success: function(dataAjx) {

                    $('#LoadChart4Count').html(dataAjx);
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
