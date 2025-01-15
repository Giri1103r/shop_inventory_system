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

        // function redirectToUAUCCAlist(status, work_status, hall, location) {

        //     console.log(status)
        //     Fromdate = $("#fromDate").val();
        //     Todate = $("#toDate").val();
        //     // Create a form element
        //     var form = document.createElement('form');
        //     form.setAttribute('method', 'post');
        //     form.setAttribute('action', "{{ admin_url('ptw/hotwork_permit/list') }}");

        //     // Add CSRF token field
        //     var csrfToken = document.createElement('input');
        //     csrfToken.type = 'hidden';
        //     csrfToken.name = '_token';
        //     csrfToken.value = '{{ csrf_token() }}'; // Use Blade directive to get CSRF token
        //     form.appendChild(csrfToken);

        //     // Create hidden input fields for each POST data
        //     var inputStatus = document.createElement('input');
        //     inputStatus.setAttribute('type', 'hidden');
        //     inputStatus.setAttribute('name', 'hot_status');
        //     inputStatus.setAttribute('value', status);
        //     form.appendChild(inputStatus);

        //     var inputStatus = document.createElement('input');
        //     inputStatus.setAttribute('type', 'hidden');
        //     inputStatus.setAttribute('name', 'work_status');
        //     inputStatus.setAttribute('value', work_status);
        //     form.appendChild(inputStatus);

        //     var inputHall = document.createElement('input');
        //     inputHall.setAttribute('type', 'hidden');
        //     inputHall.setAttribute('name', 'hall_id');
        //     inputHall.setAttribute('value', hall);
        //     form.appendChild(inputHall);

        //     var inputLocation = document.createElement('input');
        //     inputLocation.setAttribute('type', 'hidden');
        //     inputLocation.setAttribute('name', 'location');
        //     inputLocation.setAttribute('value', location);
        //     form.appendChild(inputLocation);

        //     var inputFromdate = document.createElement('input');
        //     inputFromdate.setAttribute('type', 'hidden');
        //     inputFromdate.setAttribute('name', 'Fromdate');
        //     inputFromdate.setAttribute('value', Fromdate);
        //     form.appendChild(inputFromdate);

        //     var inputTodate = document.createElement('input');
        //     inputTodate.setAttribute('type', 'hidden');
        //     inputTodate.setAttribute('name', 'Todate');
        //     inputTodate.setAttribute('value', Todate);
        //     form.appendChild(inputTodate);

        //     // Append the form to the body and submit it
        //     document.body.appendChild(form);
        //     form.submit();
        // }

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

        }

        function loadunitwisecount() {
            var url = "{{ admin_url('safetypermit/dashboard/unitwiseptw') }}"
            var data = {
                Location: Location,
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

