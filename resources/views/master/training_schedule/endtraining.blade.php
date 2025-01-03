@extends('admin.layouts.admin')
@section('title', 'Training Assessment/Feedback')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')

    <style>
        .required {
            color: red;
            font-weight: bold;
        }

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

                            <div class="tab-content">
                                <div class="card-body" id="training_attendance_details">

                                    <form method="POST" id="training_details"
                                        action="{{ admin_url('training_schedule/training_end/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="training_schedule_id" id="training_schedule_id"
                                            value="{{ encryptId($training_schedule->id) }}">

                                        <div class="basic-form">
                                            <div class="row">


                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">From Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydatetimeformat($training_schedule->from_date) }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">To Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydatetimeformat($training_schedule->to_date) }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Training Topic</label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Trainer </label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->emp_name) ? $training_schedule->emp_name : '' }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Unit</label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->unit_name) ? $training_schedule->unit_name : '' }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Department</label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->department_name) ? $training_schedule->department_name : '' }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Venue</label>
                                                    <div class="view_data">
                                                        {{ $training_schedule->name_of_the_conference_hall ?? '' }}
                                                    </div>
                                                </div>


                                                <table class="table_card" style="margin-top: 20px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="form-label">Employee Name</th>
                                                            <th class="form-label required">Attendee/Non-Attendee</th>
                                                            <th><span class="form-label">Mark</span> <span
                                                                    class="required">*</span></th>
                                                            <th><span class="form-label">Assessment</span> <span
                                                                    class="required">*</span></th>
                                                            <th class="form-label">Feedback</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            // Get unique employee names
                                                            $uniqueAttendanceList = $trainingAttendanceList->unique(
                                                                'emp_name',
                                                            );
                                                        @endphp

                                                        @foreach ($uniqueAttendanceList as $index => $training_attendance)
                                                            <tr>
                                                                <input type="hidden" name="attendance_id[]"
                                                                    value="{{ $training_attendance->id }}">
                                                                <input type="hidden" name="emp_name[]"
                                                                    value="{{ $training_attendance->emp_name }}">
                                                                <input type="hidden" name="email[]"
                                                                    value="{{ $training_attendance->email }}">

                                                                <td>
                                                                    @php
                                                                        $isAttended = $attendedEmployees->contains(
                                                                            strtolower(
                                                                                trim($training_attendance->emp_name),
                                                                            ),
                                                                        );
                                                                    @endphp

                                                                    @if ($isAttended)
                                                                        {{ $training_attendance->emp_name }}
                                                                    @else
                                                                        {{ $training_attendance->emp_name }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @php
                                                                        $isAttended = $attendedEmployees->contains(
                                                                            strtolower(
                                                                                trim($training_attendance->emp_name),
                                                                            ),
                                                                        );

                                                                    @endphp

                                                                    @if ($isAttended)
                                                                        <i class="fa fa-check"
                                                                            style="font-size:24px;color: green;"></i>
                                                                        <input type="hidden" name="attended_status[]"
                                                                            value="1">
                                                                    @else
                                                                        <i class="fa fa-close"
                                                                            style="font-size:24px;color:red"></i>
                                                                        <input type="hidden" name="attended_status[]"
                                                                            value="0">
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    @if (!$isAttended)
                                                                   
                                                                        <input type="hidden"
                                                                            name="assessment[{{ $index }}]"
                                                                            value="2">
                                                                    @else
                                                                        <input type="text"
                                                                            name ="mark[{{ $index }}]"
                                                                            class="form-control validate-range-required"
                                                                            placeholder="Mark">
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (!$isAttended)
                                                                        <select name="assessment[{{ $index }}]"
                                                                            class="form-control single-select validate-select-required"
                                                                            disabled>
                                                                            <option value="3" selected>Not Attended
                                                                            </option>
                                                                        </select>
                                                                        <input type="hidden"
                                                                            name="assessment[{{ $index }}]"
                                                                            value="3">
                                                                    @else
                                                                        <select name="assessment[{{ $index }}]"
                                                                            class="form-control single-select validate-select-required"
                                                                            style="width: 80%">
                                                                            <option value="">Select Assessment
                                                                            </option>
                                                                            <option value="1">Pass</option>
                                                                            <option value="2">Fail</option>
                                                                            <option value="3">Not Attended</option>
                                                                        </select>
                                                                    @endif
                                                                </td>


                                                                <td>
                                                                    <textarea class="form-control maxTextareaLength" name="feedback[{{ $index }}]"></textarea>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>

                                            </div>
                                        </div>

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

            $('#training_details').validate({
                rules: {
                    'mark[]': {
                        required: true,
                        iRange: true
                    },
                    'assessment[]': {
                        required: true,
                    },
                    'feedback[]': {
                        maxlength: 1000
                    },
                },
                messages: {
                    'mark[]': {
                        required: "Please enter a mark.",
                        iRange: "Mark must be between 0 and 100."
                    },
                    'assessment[]': {
                        required: "Please select an assessment."
                    },
                    'feedback[]': {
                        maxlength: "Feedback cannot exceed 1000 characters."
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');

                    if (element.hasClass('single-select')) {
                        element.next('.select2-container').append(error);
                    } else {
                        element.closest('td').append(error);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush
