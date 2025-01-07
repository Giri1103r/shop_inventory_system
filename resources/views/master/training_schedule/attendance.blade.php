@extends('admin.layouts.admin')
@section('title', 'Training Attendance')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')

    <style>
        .table_card {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        .table_card th,
        .table_card td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table_card th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .table_card tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table_card tr:hover {
            background-color: #f1f1f1;
        }

        .table_card td {
            text-align: center;
        }

        .table-container {
            padding: 20px;
        }
    </style>
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Training Attendance</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">From Date</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($training_schedule->from_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">To Date</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($training_schedule->to_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Start Time</label>
                                        <div class="view_data">
                                            {{ Displaytimeformat($training_schedule->start_time) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">End Time</label>
                                        <div class="view_data">
                                            {{ Displaytimeformat($training_schedule->end_time) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Topic</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Venue</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                        </div>
                                    </div>
                                    <form method="POST" id="attendance"
                                        action="{{ admin_url('training_schedule/attendance/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="training_schedule_id" id="training_schedule_id"
                                            value="{{ encryptId($training_schedule->id) }}">
                                        <input type="hidden" name="from_date" value="{{ $training_schedule->from_date }}">
                                        <input type="hidden" name="to_date" value="{{ $training_schedule->to_date }}">
                                        <input type="hidden" name="topic_id" value="{{ $training_schedule->topic_id }}">


                                        <div class="col-md-4">
                                            <div class="form-group form-input">
                                                <label for="attendance_date" class="form-label require">Date</label>
                                                <input type="text" name ="attendance_date" id="date_datepicker"
                                                    class="form-control" placeholder="Date">
                                            </div>
                                        </div>
                                        <table class="table_card" style="margin-top: 20px;">
                                            <thead>
                                                <tr>
                                                    <th class="form-label">Employee Name</th>
                                                    <th class="form-label required">Attendance(present /absent)</th>
                                                </tr>
                                            </thead>

                                            <tbody id="lesson_learned_block">
                                                @foreach ($nominationProcessList as $nomination_process)
                                                    <input type="hidden" name="nomination_id[]"
                                                        value="{{ $nomination_process->id }}">
                                                    <input type="hidden" name="emp_id[]"
                                                        value="{{ $nomination_process->emp_id }}">
                                                    <input type="hidden" name="emp_name[]"
                                                        value="{{ $nomination_process->emp_name }}">
                                                    <input type="hidden" name="email[]"
                                                        value="{{ $nomination_process->email }}">
                                                    <tr class="lesson_learned_row">

                                                        <td>
                                                            {{ isset($nomination_process->emp_name) ? $nomination_process->emp_name : '' }}
                                                        </td>
                                                        <td>
                                                            <input type="hidden"
                                                                name="attendance_status[{{ $loop->index }}]"
                                                                value="0">
                                                            <input type="checkbox" id="attendance_status"
                                                                name="attendance_status[{{ $loop->index }}]"
                                                                value="1">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('training_schedule/list') }}"></x-button-cancel>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });


            var fromDate = '{{ \Carbon\Carbon::parse($training_schedule->from_date)->format('d-m-Y') }}';
            var toDate = '{{ \Carbon\Carbon::parse($training_schedule->to_date)->format('d-m-Y') }}';

            flatpickr("#date_datepicker", {
                dateFormat: "d-m-Y",
                minDate: fromDate,
                maxDate: toDate
            });
            $(function() {
                $('#attendance').validate({
                    rules: {
                        attendance_date: {
                            required: true,
                            remote: {
                                url: '{{ admin_url('training_schedule/attendance/unique') }}',
                                type: 'post',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    attendance_date: function() {
                                        return $('#date_datepicker').val();
                                    },
                                    training_schedule_id: function() {
                                        return $('#training_schedule_id').val();
                                    }
                                }
                            }
                        },
                    },
                    messages: {
                        attendance_date: {
                            required: "Select a Date.",
                            remote: "This attendance date already exists."
                        },

                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element, errorClass, validClass) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element, errorClass, validClass) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        console.log('test');
                        form.submit();

                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " +
                                error
                                .message);
                        });
                    }
                });
            });
        });
    </script>
@endpush
