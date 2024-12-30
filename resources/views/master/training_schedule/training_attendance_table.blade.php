<table class="table_card" style="margin-top: 20px;">
    <thead>
        <tr>
            <th class="form-label">Attendance Date</th>
            <th class="form-label">Employee Name</th>
            <th class="form-label required">Attendance (Present / Absent)</th>
        </tr>
    </thead>
    <tbody>
        @if ($trainingAttendanceList->isNotEmpty())
            @foreach ($trainingAttendanceList as $training_attendance)
                <tr>
                    <td>{{ Displaydateformat($training_attendance->attendance_date) ?? '' }}</td>
                    <td>{{ $training_attendance->emp_name ?? '' }}</td>
                    <td>
                        @if ($training_attendance->attendance_status == 1)
                            <i class="fa fa-check" style="font-size:24px; color: green;"></i>
                        @else
                            <i class="fa fa-close" style="font-size:24px; color: red;"></i>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3" class="text-center">No attendance records available for the selected date.</td>
            </tr>
        @endif
    </tbody>
</table>
