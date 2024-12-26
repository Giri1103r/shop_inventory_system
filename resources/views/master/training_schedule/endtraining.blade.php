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
                                                    <label class="form-label view_label">Training Topic</label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}
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

                                                {{-- <table class="table_card" style="margin-top: 20px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="form-label">Employee Name</th>
                                                            <th class="form-label required">Attendee/non-Attendee
                                                            </th>
                                                            <th><span class="form-label">Assessment</span> <span
                                                                    class="required">*</span></th>
                                                            <th class="form-label">Feedback</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($trainingAttendanceList as $index => $training_attendance)
                                                            <tr>
                                                                <input type="hidden" name="attendance_id[]"
                                                                    value="{{ $training_attendance->id }}">
                                                                <input type="hidden" name="emp_name[]"
                                                                    value="{{ $training_attendance->emp_name }}">
                                                                <td>{{ $training_attendance->emp_name ?? '' }}</td>
                                                                <td>
                                                                    @if ($attendedEmployees->isNotEmpty())
                                                                        @foreach ($attendedEmployees as $employee)
                                                                            {{ $employee }}
                                                                        @endforeach
                                                                    @elseif($nonAttendedEmployees->isNotEmpty())
                                                                        @foreach ($nonAttendedEmployees as $employee)
                                                                            <li>{{ $employee }}</li>
                                                                        @endforeach
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($training_attendance->attendance_status == 1)
                                                                        <i class="fa fa-check"
                                                                            style="font-size:24px;color: green;"></i>
                                                                    @else
                                                                        <i class="fa fa-close"
                                                                            style="font-size:24px;color:red"></i>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <select name="assessment[{{ $index }}]"
                                                                        class="form-control single-select validate-select-required">
                                                                        <option value="">Select Assessment
                                                                        </option>
                                                                        <option value="1">Pass</option>
                                                                        <option value="2">Fail</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <textarea class="form-control maxTextareaLength" name="feedback[{{ $index }}]"></textarea>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table> --}}
                                                <table class="table_card" style="margin-top: 20px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="form-label">Employee Name</th>
                                                            <th class="form-label required">Attendee/Non-Attendee</th>
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

                                                                <!-- Display the employee name with attendance status -->
                                                                <td>
                                                                    @php
                                                                        $isAttended = $attendedEmployees->contains(
                                                                            strtolower(
                                                                                trim($training_attendance->emp_name),
                                                                            ),
                                                                        );
                                                                    @endphp

                                                                    @if ($isAttended)
                                                                   
                                                                        {{ $training_attendance->emp_name }} (Attended)
                                                                    @else
                                                                      
                                                                        {{ $training_attendance->emp_name }} (Non-Attended)
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
                                                                    @else
                                                                        <i class="fa fa-close"
                                                                            style="font-size:24px;color:red"></i>
                                                                    @endif
                                                                </td>

                                                                <!-- Assessment Dropdown -->
                                                                <td>
                                                                    <select name="assessment[{{ $index }}]"
                                                                        class="form-control single-select validate-select-required">
                                                                        <option value="">Select Assessment</option>
                                                                        <option value="1">Pass</option>
                                                                        <option value="2">Fail</option>
                                                                    </select>
                                                                </td>

                                                                <!-- Feedback Field -->
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
            $('#training_details').validate({
                rules: {
                    'assessment[]': {
                        required: true,
                    },
                    'feedback[]': {
                        maxlength: 1000
                    },
                },
                messages: {
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

                    // Place the error message appropriately
                    if (element.hasClass('single-select')) {
                        // For select2 dropdowns
                        element.next('.select2-container').append(error);
                    } else {
                        // Place in the closest <td>
                        element.closest('td').append(error);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid'); // Add error class
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid'); // Remove error class
                }
            });
        });
    </script>
@endpush
