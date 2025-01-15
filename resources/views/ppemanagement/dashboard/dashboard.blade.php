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
                        <div class="card-header border-0 pb-1 bg-danger d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">PPE SHOE REQUEST STATUS</h4>
                        </div>
                        <div class="card-body  px-0 pt-0 dlab-scroll height450">
                            <label for="">PPE Shoe Request Status</label>
                        </div>

                    </div>
                </div>
                <div class="col-xl-8 col-xxl-8 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-danger d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;"> UNIT WISE PPE SHOE REQUEST</h4>
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
            <div class="row">
                <div class="col-xl-4 col-xxl-4 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-danger d-flex justify-content-between align-items-center">
                            <h4 class="card-title" style="color: white;">PPE SHOE EXEMPTION STATUS</h4>

                        </div>

                    </div>
                </div>
                <div class="col-xl-8 col-xxl-8 mt-2">
                    <div class="card view_card">
                        <div class="card-header border-0 pb-1 bg-danger d-flex justify-content-between align-items-center">
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
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

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

            var defaultUnit = $('#unit').val();
            var defaultFromDate = $("#from_date").val();
            var defaultToDate = $("#to_date").val();

            PpeChartExemptiondata(defaultUnit, defaultFromDate, defaultToDate);
            PpeChartRequestdata(defaultUnit, defaultFromDate, defaultToDate);


            $('#searchform').on('click', function(e) {
                e.preventDefault();

                var unit = $('#unit').val();
                var from_date = $("#from_date").val();
                var to_date = $("#to_date").val();

                PpeChartExemptiondata(unit, from_date, to_date);
                PpeChartRequestdata(unit, from_date, to_date);
            });

            function PpeChartExemptiondata(unit, from_date, to_date) {
                var url = "{{ admin_url('ppe-dashboard/ppeExemption') }}";
                var data = {
                    unit: unit,
                    Fromdate: from_date,
                    Todate: to_date,
                };


                $('#PpeExemptionData').html('');

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(dataAjx) {
                        $('#PpeExemptionData').html(dataAjx);
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch PPE Exemption data:', xhr
                        .responseText);
                    }
                });
            }


            function PpeChartRequestdata(unit, from_date, to_date) {
                var url = "{{ admin_url('ppe-dashboard/ppeRequestList') }}";
                var data = {
                    unit: unit,
                    Fromdate: from_date,
                    Todate: to_date,
                };



                $('#PpeshoerequestData').html(''); // Clear previous data

                $.ajax({
                    type: 'get',
                    url: url,
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    cache: false,
                    success: function(dataAjx) {

                        $('#PpeshoerequestData').html(dataAjx); // Update the div with new data
                    },
                    error: function(xhr) {
                        console.error('Failed to fetch PPE Request data:', xhr
                        .responseText); // Error logging
                    }
                });
            }
        });
    </script>
@endpush
