<!DOCTYPE html>
<html>

<head>
    <title>PPE Request | KARAM</title>

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
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">
                <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    PPE Request
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
                <td class="header-cell">PPE Request</td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Employee Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">{{ isset($pperequest->emp_name) ? $pperequest->emp_name : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Employee Id</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ isset($pperequest->emp_id) ? $pperequest->emp_id : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Department</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getDepartment(isset($pperequest->department) ? $pperequest->department : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Reason</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($pperequest->employee_reason) ? $pperequest->employee_reason : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($pperequest->created_by) ? $pperequest->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($pperequest->created_at) }}</td>
        </tr>
    </table>

    <br>

    @if ($pperequest->approve_status != STATUS_HOD_APPROVAL_PENDING)
        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td class="header-cell">Status Logs</td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>

                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Remarks</th>
                                <th>Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            @if ($ppestatuslog->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No data is available</td>
                                </tr>
                            @else
                                @foreach ($ppestatuslog as $log)
                                    <tr class="hover-row">

                                        <td>
                                            @if ($log['to_status'] == STATUS_HOD_APPROVAL_PENDING)
                                                HOD Approval Pending
                                            @elseif ($log['to_status'] == STATUS_HOD_APPROVED)
                                                HOD Approved
                                            @elseif ($log['to_status'] == STATUS_USER_APPLIED)
                                                User Applied
                                            @elseif ($log['to_status'] == STATUS_HOD_REJECTED)
                                                HOD Rejected
                                            @elseif ($log['to_status'] == STATUS_EHS_APPROVAL_PENDING)
                                                EHS Officer Approval Pending
                                            @elseif ($log['to_status'] == STATUS_EHS_APPROVED)
                                                EHS Officer Approved
                                            @elseif ($log['to_status'] == STATUS_EHS_REJECTED)
                                                EHS Officer Rejected
                                            @endif
                                        </td>

                                        <td>{{ getUsername($log['created_by']) }}</td>
                                        <td>{{ $log['remarks'] }}</td>
                                        <td>{{ displaydateformat($log['created_at']) }}</td>

                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>
            <br>
        </div>
    @endif
    <br>

    @if ($pperequest->approve_status != STATUS_HOD_APPROVAL_PENDING)
        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td class="header-cell">Previous History</td>
                    </tr>
                </table>
            </div>
            <br>
            <table class="table table-bordered table-hover tblborder ">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Employee Id</th>
                        <th>Previous applied Date</th>
                        <th>Approval Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($userdata->isEmpty())
                        <tr>
                            <td class="text-center" colspan="6">No data is available</td>
                        </tr>
                    @else
                        @foreach ($userdata as $data)
                            <tr class="hover-row">
                                <td>{{ $data['emp_name'] }}</td>
                                <td>{{ $data['emp_id'] }}</td>
                                <td>{{ displaydateformat($data['created_at']) }}</td>
                                <td>{{ removeUnderScore(getStatus($data['approve_status'])) }}</td>
                                <td>{{ $data['remarks'] }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    @endif
    <br>

</body>

</html>
