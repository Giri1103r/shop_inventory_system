@extends('admin.layouts.admin')
@section('title', 'Training Schedule Show')
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
                            @if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_TRAINER)
                                <nav class="nav nav-pills nav-fill">
                                    <a class="nav-link active" aria-current="page"
                                        href="#training_schedule_details">Training
                                        Schedule</a>

                                    <a class="nav-link" href="#nomination_process">Nomination Process</a>
                                    <a class="nav-link" href="#training_attendance_details">Training Attendance</a>
                                    <a class="nav-link" href="#training_assessment_details">Training Assessment</a>
                                    <a class="nav-link" href="#training_feedback_details">Training Feedback By
                                        Trainees</a>
                                </nav>
                            @endif
                            <div class="tab-content">
                                @if (Auth::user()->role == ROLE_SUPERADMIN || Auth::user()->role == ROLE_TRAINER)
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Schedule</h4>
                                            </div>
                                        </div>
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
                                                <label class="form-label view_label">Target Trainees</label>
                                                <div class="view_data">
                                                    {{ isset($training_schedule->target_trainees) ? $training_schedule->target_trainees : '' }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Venue/Location</label>
                                                <div class="view_data">
                                                    {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Training Man Hours</label>
                                                <div class="view_data">
                                                    {{ $totalTrainingHours ?? 'N/A' }}
                                                </div>
                                            </div>
                                            @if ($training_schedule->training_status == 3)
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Training Status</label>
                                                    <div class="view_data"> Training Started
                                                    </div>
                                                </div>
                                            @elseif($training_schedule->training_status == 4)
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Training Status</label>
                                                    <div class="view_data">
                                                        Training Ended
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($training_schedule->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('common.created_date') }}</label>
                                                <div class="view_data">
                                                    {{ displayDateformat($training_schedule->created_at) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.status') }}</label>
                                                <div class="view_data">
                                                    @if ($training_schedule->status == 1)
                                                        {{ __('common.active') }}
                                                    @else
                                                        {{ __('common.inactive') }}
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body" id="nomination_process">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Nomination Process</h4>
                                            </div>
                                        </div>
                                        @if (isset($nominationProcessList) && $nominationProcessList->isNotEmpty())
                                            <div class="basic-form">
                                                <form method="POST" id="nomination_processadd"
                                                    action="{{ admin_url('nomination_process/add/submit') }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <table class="table_card" style="margin-top: 20px;">
                                                            <thead>
                                                                <tr>
                                                                    <th class="form-label required">Employee ID</th>
                                                                    <th class="form-label required">Employee Name</th>
                                                                    <th class="form-label required">Email ID</th>
                                                                    <th class="form-label required">Department</th>
                                                                    <th class="form-label required">Employee Type</th>
                                                                    <th class="form-label required">Last training attended
                                                                        on
                                                                        (Date)
                                                                    </th>
                                                                    <th class="form-label required">Last Training Attended
                                                                        on
                                                                        (Topic)</th>
                                                                </tr>
                                                            </thead>
                                                            <input type="hidden" class="form-control"
                                                                name="training_schedule_id" id="training_schedule_id"
                                                                value="{{ encryptId($training_schedule->id) }}">
                                                            <tbody id="lesson_learned_block">
                                                                @php $i = 1; @endphp
                                                                @foreach ($nominationProcessList as $nomination_process)
                                                                    <tr class="lesson_learned_row">
                                                                        <input type="hidden"
                                                                            id="employee[{{ $i }}][id]"
                                                                            name="employee[{{ $i }}][id]"
                                                                            value="{{ $nomination_process->id }}">
                                                                        <td>
                                                                            {{ isset($nomination_process->emp_id) ? $nomination_process->emp_id : '' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ isset($nomination_process->emp_name) ? $nomination_process->emp_name : '' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ isset($nomination_process->email) ? $nomination_process->email : '' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ isset($nomination_process->department_name) ? $nomination_process->department_name : '' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ isset($nomination_process->employee_type) ? $nomination_process->employee_type : '' }}
                                                                        </td>
                                                                        <td>
                                                                            {{ displayDateformat($nomination_process->last_training_attended_on) }}
                                                                        </td>
                                                                        <td>
                                                                            {{ isset($nomination_process->topic_name) ? $nomination_process->topic_name : '' }}
                                                                        </td>
                                                                    </tr>
                                                                    @php $i++; @endphp
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <hr>

                                                </form>
                                            </div>
                                        @else
                                            <p>No nomination process data available.</p>
                                        @endif
                                    </div>


                                    <div class="card-body" id="training_attendance_details">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Attendance</h4>
                                            </div>
                                        </div>
                                        @if (isset($trainingAttendanceList) && $trainingAttendanceList->isNotEmpty())
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
                                                        <label class="form-label view_label">Venue</label>
                                                        <div class="view_data">
                                                            {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                                        </div>
                                                    </div>
                                                    <form method="GET" action="">
                                                        <div class="card-body">
                                                            <div class="col-md-12">
                                                                <div class="row">
                                                                    <div class="col-md-3 mb-3 form-input">
                                                                        <label for="attendance_date"
                                                                            class="form-label">Attendance Date</label>
                                                                        <input type="date" name="attendance_date"
                                                                            id="date_datepicker" class="form-control"
                                                                            value="{{ request()->attendance_date }}"
                                                                            placeholder="Attendance Date">
                                                                    </div>
                                                                    <div
                                                                        class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                                                        <input type="hidden" name="active_tab"
                                                                            id="active_tab"
                                                                            value="#training_attendance_details">
                                                                        <button type="submit" id="searchform"
                                                                            class="btn btn-warning">{{ __('common.search') }}</button>
                                                                        <button type="reset" id="resetform"
                                                                            class="btn btn-primary">Reset</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>

                                                    <table class="table_card" style="margin-top: 20px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="form-label">Attendance Date</th>
                                                                <th class="form-label">Employee Name</th>
                                                                <th class="form-label required">Attendance(present /absent)
                                                                </th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="lesson_learned_block">
                                                            @foreach ($trainingAttendanceList as $training_attendance)
                                                                <tr>
                                                                    <td>{{ Displaydateformat($training_attendance->attendance_date) ?? '' }}
                                                                    </td>
                                                                    <td>{{ $training_attendance->emp_name ?? '' }}</td>
                                                                    <td>
                                                                        @if ($training_attendance->attendance_status == 1)
                                                                            <i class="fa fa-check"
                                                                                style="font-size:24px;color: green;"></i>
                                                                        @else
                                                                            <i class="fa fa-close"
                                                                                style="font-size:24px;color:red"></i>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <hr>
                                                </div>
                                            </div>
                                        @else
                                            <p>No training attendance data available.</p>
                                        @endif
                                    </div>


                                    <div class="card-body" id="training_assessment_details">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Assessment</h4>
                                            </div>
                                        </div>
                                        @if (isset($trainingAssessmentList) && $trainingAssessmentList->isNotEmpty())
                                            <div class="basic-form">
                                                <div class="row">
                                                    <div class="mb-3 col-md-4 form-input">
                                                        <label class="form-label view_label">Department</label>
                                                        <div class="view_data">
                                                            {{ isset($training_schedule->department_name) ? $training_schedule->department_name : '' }}
                                                        </div>
                                                    </div>
                                                    <table class="table_card" style="margin-top: 20px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="form-label">Employee Name</th>
                                                                <th class="form-label">Attendee/Non-Attendee</th>
                                                                <th class="form-label">Assessment</th>
                                                                <th class="form-label">Feedback</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="lesson_learned_block">
                                                            @foreach ($trainingAssessmentList as $assessment)
                                                                <tr>

                                                                    <td>{{ $assessment->emp_name ?? '' }}</td>
                                                                    <td>
                                                                        @if ($assessment->attended_status == 1)
                                                                            <i class="fa fa-check"
                                                                                style="font-size:24px;color: green;"></i>
                                                                        @else
                                                                            <i class="fa fa-close"
                                                                                style="font-size:24px;color:red"></i>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($assessment->assessment == 1)
                                                                            Pass
                                                                        @else
                                                                            Fail
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $assessment->feedback ?? '' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <hr>
                                                </div>
                                            </div>
                                        @else
                                            <p>No training assessment data available.</p>
                                        @endif
                                    </div>

                                    <div class="card-body" id="training_feedback_details">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Feedback</h4>
                                            </div>
                                        </div>
                                        @if (isset($trainingFeedbackList) && $trainingFeedbackList->isNotEmpty())
                                            <div class="basic-form">
                                                <div class="row">
                                                    <table class="table_card" style="margin-top: 20px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="form-label">Employee ID</th>
                                                                <th class="form-label">Feedback about the Trainer</th>
                                                                <th class="form-label">Feedback about the Training</th>
                                                            </tr>
                                                        </thead>

                                                        <tbody id="lesson_learned_block">
                                                            @foreach ($trainingFeedbackList as $feedback)
                                                                <tr>

                                                                    <td>{{ $feedback->emp_id?? '' }}</td>
                                                                    
                                                                    <td>{{ $feedback->trainer_feedback ?? '' }}</td>

                                                                    <td>{{ $feedback->training_feedback ?? '' }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                    <hr>
                                                </div>
                                            </div>
                                        @else
                                            <p>No training feedback data available.</p>
                                        @endif
                                    </div>
                                @endif



                                @if (Auth::user()->role != ROLE_SUPERADMIN && Auth::user()->role != ROLE_TRAINER)
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Schedule</h4>
                                            </div>
                                        </div>
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
                                                <label class="form-label view_label">Venue/Location</label>
                                                <div class="view_data">
                                                    {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
            document.getElementById('resetform').addEventListener('click', function() {
                window.location.href =
                    "{{ url()->current() }}";
            });

            // function resetAttendanceDate() {
            //     document.getElementById("date_datepicker").value = "";
            // }
            flatpickr("#date_datepicker", {
                dateFormat: "d-m-Y",
            });


        });
    </script>
@endpush
