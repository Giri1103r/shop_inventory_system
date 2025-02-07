<html>

<head>
    <title> Training Details | KARAM</title>
    <style>
        .badge {
            padding: 1px 9px 2px;
            font-size: 12.025px;
            font-weight: bold;
            white-space: nowrap;
            color: #ffffff;
            background-color: #999999;
            border-radius: 9px;
        }

        @page {
            size: auto;
            odd-header-name: html_myHeader1;
            even-header-name: html_myHeader1;
            odd-footer-name: html_myFooter1;
            even-footer-name: html_myFooter1;
        }

        @page noheader {
            odd-header-name: _blank;
            even-header-name: _blank;
            odd-footer-name: _blank;
            even-footer-name: _blank;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td,
        .table th {
            border: 1px solid black;
            padding: 5px;
            word-wrap: break-word;
            max-width: 100px;
            /* Adjust as needed */
        }

        .table-striped tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05) !important;
        }

        body {
            font-size: 13px;
        }

        .full-width {
            width: 100%;
            font-size: 11px;
        }

        .tblborder {
            border: 1px solid black;
        }

        .activity,
        .activity th,
        .activity td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .header-cell {
            background-color: #ce0f1f;
            color: #000;
            font-weight: bold;
            padding: 5px;
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
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <htmlpageheader name="myHeader1" style="display:block;">
            <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
                <tr style="">
                    <td border="0" style="width:50%;float:left;text-align:left;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                    </td>
                    <td border="0"
                        style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                        Training Details
                    </td>
                </tr>
            </table>


        </htmlpageheader>


        <htmlpagefooter name="myFooter1" style="display:none">
            <table width="100%"
                style="width:100%;border:0;background-color: #FFF;border-top: 4px solid #000;padding-top:10px;padding-bottom:10px;">
                <tr>
                    <td width="33%">
                        <span style="font-style: italic;">{DATE d-m-Y}</span>
                    </td>
                    <td width="33%" align="center" style="font-weight: bold; font-style: italic;">

                    </td>
                    <td width="33%" style="text-align: right;">
                        {PAGENO}/{nbpg}
                    </td>
                </tr>
            </table>
        </htmlpagefooter>


        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                        Training Schedule
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>From Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ Displaydateformat($training_schedule->from_date) }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>To Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ Displaydateformat($training_schedule->to_date) }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>Start Time</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ Displaytimeformat($training_schedule->start_time) }}</td>
            </tr>
          
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>End Time</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ Displaytimeformat($training_schedule->end_time) }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Training Topic</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Trainer</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->emp_name) ? $training_schedule->emp_name : '' }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Unit</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->unit_name) ? $training_schedule->unit_name : '' }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Department</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->department_name) ? $training_schedule->department_name : '' }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Target Trainees</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->target_trainees) ? $training_schedule->target_trainees : '' }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Venue/Location</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                </td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Training Status</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                @if ($training_schedule->training_status == 1 ||
                $training_schedule->training_status == 2 ||
                $training_schedule->training_status == 4 ||
                $training_schedule->training_status == 5)
                    <td width="58%">
                        Training Pending
                    </td>
                @elseif ($training_schedule->training_status == 8)
                    <td width="58%">
                        Training Completed
                    </td>
                @elseif ($training_schedule->training_status == 6 || $training_schedule->training_status == 7)
                    <td width="58%">
                        Training in Progress
                    </td>
                @elseif ($training_schedule->training_status == 3)
                    <td width="58%">
                        Training Rejected
                    </td>
                @endif
            </tr>

            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Created By</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ getusername($training_schedule->created_by) }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Created Date</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ displayDateformat($training_schedule->created_at) }}</td>
            </tr>
        </table>
        @if (isset($rejectedlog) && $rejectedlog->isNotEmpty())
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                            Training Rejection Log List
                        </td>
                    </tr>
                </table>
            </div>

            <table class="table_card" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th class="form-label">Date</th>
                        <th class="form-label">Remark</th>
                    </tr>
                </thead>
                <tbody id="lesson_learned_block">
                    @foreach ($rejectedlog as $log)
                        <tr class="lesson_learned_row">

                            <td>
                                {{ Displaydateformat($log->created_at) ?? '' }}
                            </td>
                            <td>
                                {{ isset($log->remarks) ? $log->remarks : '' }}
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                        EHS Head Approval
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>Approver Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ $training_schedule->approver_name }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;"><b>Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ Displaydateformat($training_schedule->date) }}</td>
            </tr>
            <tr>
                <td width="40%" style="padding:5px;text-transform: uppercase;">
                    <b>Remark</b>
                </td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="58%" style="padding:5px;">
                    {{ isset($training_schedule->remark) ? $training_schedule->remark : '' }}</td>
            </tr>
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                        Nomination Process
                    </td>
                </tr>
            </table>
        </div>
        <table class="table_card" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th class="form-label required">Employee ID</th>
                    <th class="form-label required">Employee Name</th>
                    <th class="form-label required">Email ID</th>
                    <th class="form-label required">Department</th>
                    <th class="form-label required">Employee Type</th>
                    <th class="form-label required">Last training attended on
                        (Date)
                    </th>
                    <th class="form-label required">Last Training Attended on
                        (Topic)</th>
                </tr>
            </thead>
            <tbody id="lesson_learned_block">
                @php $i = 1; @endphp
                @foreach ($nominationProcessList as $nomination_process)
                    <tr class="lesson_learned_row">

                        <td>
                            {{ isset($nomination_process->emp_id) ? $nomination_process->emp_id : '-' }}
                        </td>
                        <td>
                            {{ isset($nomination_process->emp_name) ? $nomination_process->emp_name : '-' }}
                        </td>
                        <td>
                            {{ isset($nomination_process->email) ? $nomination_process->email : '-' }}
                        </td>
                        <td>
                            {{ isset($nomination_process->department_name) ? $nomination_process->department_name : '-' }}
                        </td>
                        <td>
                            {{ isset($nomination_process->employee_type) ? $nomination_process->employee_type : '-' }}
                        </td>
                        <td>
                            {{ $nomination_process->last_training_attended_on ? displayDateformat($nomination_process->last_training_attended_on) : '-' }}
                        </td>
                        <td>
                            {{ isset($nomination_process->topic_name) ? $nomination_process->topic_name : '-' }}
                        </td>
                    </tr>
                    @php $i++; @endphp
                @endforeach
            </tbody>
        </table>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                        Training Attendance
                    </td>
                </tr>
            </table>
        </div>
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
                        <td>{{ Displaydateformat($training_attendance->attendance_date) ?? '-' }}
                        </td>
                        <td>{{ $training_attendance->emp_name ?? '-' }}</td>
                        <td>
                            @if ($training_attendance->attendance_status == 1)
                                <i class="fa-solid fa-check" style="color: #267709;">✔</i>
                            @else
                                <i class="fa-solid fa-x" style="color: #f72626;">✘</i>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="width: 100%;">
            <table style="width: 100%;">
                <tr>
                    <td
                        style="width: 100%; background-color: #ce0f1f; color: #ffffff; font-weight: bold; padding: 10px;">
                        Training Assessment
                    </td>
                </tr>
            </table>
        </div>

        @if (isset($trainingAssessmentList) && $trainingAssessmentList->isNotEmpty())
            <table class="table_card" style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th class="form-label">Employee Name</th>
                        <th class="form-label">Attendee/Non-Attendee</th>
                        <th class="form-label">Mark</th>
                        <th class="form-label">Assessment</th>
                        <th class="form-label">Feedback</th>
                    </tr>
                </thead>
                <tbody id="lesson_learned_block">
                    @foreach ($trainingAssessmentList as $assessment)
                        <tr>
                            <td>{{ $assessment->emp_name ?? '-' }}</td>
                            <td>
                                @if ($assessment->attended_status == 1)
                                    <i class="fa fa-check" style="color: #267709;">✔</i>
                                @else
                                    <i class="fa fa-times" style="color: #f72626;">✘</i>
                                @endif
                            </td>
                            <td>{{ $assessment->mark ?? '-' }}</td>
                            <td>
                                @if ($assessment->assessment == 1)
                                    Pass
                                @elseif($assessment->assessment == 2)
                                    Fail
                                @elseif($assessment->assessment == 3)
                                    Not Attended
                                @endif
                            </td>
                            <td>{{ strip_tags($assessment->feedback ?? '-') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; margin-top: 20px;">No training assessment data available.</p>
        @endif

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;font-weight:bold;padding: 10px 10px 10px;">
                        Training Feedback By Trainees
                    </td>
                </tr>
            </table>
        </div>
        @if (isset($trainingFeedbackList) && $trainingFeedbackList->isNotEmpty())
            <table class="table_card" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th class="form-label">Employee ID</th>
                        <th class="form-label">Employee Name</th>
                        <th class="form-label">Feedback about the Trainer</th>
                        <th class="form-label">Feedback about the Training</th>
                    </tr>
                </thead>

                <tbody id="lesson_learned_block">
                    @foreach ($trainingFeedbackList as $feedback)
                        <tr>

                            <td>{{ $feedback->emp_id ?? '-' }}</td>

                            <td>{{ $feedback->emp_name ?? '-' }}</td>

                            <td>{{ strip_tags($feedback->trainer_feedback) ?? '-' }}</td>

                            <td>{{ strip_tags($feedback->training_feedback) ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No training feedback data available.</p>
        @endif

</body>

</html>
