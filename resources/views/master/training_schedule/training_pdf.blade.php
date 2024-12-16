<html>

<head>
    <style>
        .badge {
            padding: 1px 9px 2px;
            font-size: 12px;
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

        body {
            font-size: 13px;
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        th {
            background-color: #469A9A;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
        }

        .table-header {
            background-color: #469A9A;
            color: #fff;
            font-weight: bold;
            padding: 5px;
            text-transform: uppercase;
            text-align: left;
        }

        .striped tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer-table {
            width: 100%;
            border: none;
            margin-top: 20px;
            background-color: #469A9A;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        .footer-table td {
            border: none;
            color: #fff;
        }

        .no-border {
            border: none;
        }

        .highlight {
            background-color: #D6F6F6;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1">
        <table class="highlight">
            <tr>
                <td style="width:20%;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px; height:100px;">
                </td>
                <td style="width:80%; font-size: 24px; font-weight:bold; font-family: Georgia, serif;">
                    <div>{{ config('app.name') }}</div>
                    <div>Training</div>
                    {{-- <div>{{ $pagetitle }}</div> --}}
                </td>
            </tr>
        </table>
        <hr style="border-top: 4px double #469A9A;">
    </htmlpageheader>

    <htmlpagefooter name="myFooter1">
        <table class="footer-table">
            <tr>
                <td width="33%">{DATE d-m-Y}</td>
                <td width="33%" style="text-align: center; font-style: italic;">Page {PAGENO} of {nbpg}</td>
            </tr>
        </table>
    </htmlpagefooter>

    <div>
        <h4 class="table-header">Training Schedule</h4>
        <table class="striped">
            <tr>
                <td><b>From Date</b></td>
                <td>{{ Displaydatetimeformat($training_schedule->from_date) }}</td>
            </tr>
            <tr>
                <td><b>To Date</b></td>
                <td>{{ Displaydatetimeformat($training_schedule->to_date) }}</td>
            </tr>
            <tr>
                <td><b>Training Topic</b></td>
                <td>{{ $training_schedule->topic_name ?? '' }}</td>
            </tr>
            <tr>
                <td><b>Trainer</b></td>
                <td>{{ $training_schedule->emp_name ?? '' }}</td>
            </tr>
            <tr>
                <td><b>Unit</b></td>
                <td>{{ $training_schedule->unit_name ?? '' }}</td>
            </tr>
            <tr>
                <td><b>Department</b></td>
                <td>{{ $training_schedule->department_name ?? '' }}</td>
            </tr>
            <tr>
                <td><b>Target Trainees</b></td>
                <td>{{ $training_schedule->target_trainees ?? '' }}</td>
            </tr>
            <tr>
                <td><b>Venue/Location</b></td>
                <td>{{ $training_schedule->name_of_the_conference_hall ?? '' }}</td>
            </tr>
            @if ($training_schedule->training_status == 3)
                <tr>
                    <td><b>Training Status</b></td>
                    <td>Training Started</td>
                </tr>
            @elseif($training_schedule->training_status == 4)
                <tr>
                    <td><b>Training Status</b></td>
                    <td>Training Ended</td>
                </tr>
            @endif
            <tr>
                <td><b>Created By</b></td>
                <td>{{ getusername($training_schedule->created_by) }}</td>
            </tr>
            <tr>
                <td><b>Created Date</b></td>
                <td>{{ displayDateformat($training_schedule->created_at) }}</td>
            </tr>
        </table>

        <h4 class="table-header">Nomination Process</h4>
        <table class="striped">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Email ID</th>
                    <th>Department</th>
                    <th>Employee Type</th>
                    <th>Last Training Attended (Date)</th>
                    <th>Last Training Attended (Topic)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nominationProcessList as $nomination_process)
                    <tr>
                        <td>{{ $nomination_process->emp_id ?? '' }}</td>
                        <td>{{ $nomination_process->emp_name ?? '' }}</td>
                        <td>{{ $nomination_process->email ?? '' }}</td>
                        <td>{{ $nomination_process->department_name ?? '' }}</td>
                        <td>{{ $nomination_process->employee_type ?? '' }}</td>
                        <td>{{ displayDateformat($nomination_process->last_training_attended_on) }}</td>
                        <td>{{ $nomination_process->topic_name ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h4 class="table-header">Training Attendance</h4>
        <table class="striped">
            <thead>
                <tr>
                    <th>Attendance Date</th>
                    <th>Employee Name</th>
                    <th>Attendance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trainingAttendanceList as $training_attendance)
                    <tr>
                        <td>{{ Displaydateformat($training_attendance->attendance_date) ?? '' }}</td>
                        <td>{{ $training_attendance->emp_name ?? '' }}</td>
                        <td>
                            @if ($training_attendance->attendance_status == 1)
                                <span class="text-success">✔️</span>
                            @else
                                <span class="text-danger">❌</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
