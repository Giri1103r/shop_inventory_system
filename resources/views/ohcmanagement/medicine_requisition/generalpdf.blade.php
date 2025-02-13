<!DOCTYPE html>
<html>

<head>
    <title>Medicine Requisition Details| KARAM</title>

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
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">
                <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Medicine Requisition Details
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
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                  Medicine Requisition
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Requisition ID</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">{{ isset($user_medicine_requisition->req_id) ? $user_medicine_requisition->req_id : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">{{ getUnitname(isset($user_medicine_requisition->unit_id) ? $user_medicine_requisition->unit_id : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Department</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ getDepartment(isset($user_medicine_requisition->department_id) ? $user_medicine_requisition->department_id : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Request Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ displaydateformat(isset($user_medicine_requisition->request_date) ? $user_medicine_requisition->request_date : '') }}</td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($user_medicine_requisition->created_by) ? $user_medicine_requisition->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($user_medicine_requisition->created_at) }}</td>
        </tr>
    </table>

    <br>


    <div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                      Medicine Details
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <div class="col-md-12">
                <table class="table table-bordered table-hover tblborder">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Medicine Name</th>
                            <th>Available Quantity</th>
                            <th>Quantity</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($medicine_requisition->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">No data available</td>
                            </tr>
                        @else
                            @foreach ($medicine_requisition as $data)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ getMedicinename($data->medicine_id) }}</td>
                                    <td>{{ $data->available_quantity }}</td>
                                    <td>{{ $data->quantity }}</td>
                                    <td>{{ $data->remarks }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>

    <div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                        Status Logs
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <div class="col-md-12">
                <table class="table table-bordered table-hover tblborder">
                    <thead>
                        <tr>
                            <th>From Status</th>
                            <th>To Status</th>
                            <th>Remarks</th>
                            <th>Approved By</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logdata as $log)
                        <tr>
                            <td>
                                @if($log['from_status'] == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                                    <p>Paramedics Approval Pending</p>
                                    @elseif($log['from_status'] == STATUS_OHC_STOCK_REQUEST)
                                    <p>Stock Requested</p>
                                @elseif($log['from_status'] == STATUS_OHC_PARAMEDICS_APPROVED)
                                    <p>Paramedics Approved</p>
                                @elseif($log['from_status'] == STATUS_OHC_PARAMEDICS_REJECTED)
                                    <p>Paramedics Approval Pending</p>
                                @elseif($log['from_status'] == STATUS_OHC_CLOSE)
                                    <p>Paramedics Approved</p>
                                @endif
                            </td>

                            <td>
                                @if($log['to_status'] == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                                    <p>Paramedics Approval Pending</p>
                                @elseif($log['to_status'] == STATUS_OHC_PARAMEDICS_APPROVED)
                                    <p>Paramedics Approved</p>
                                @elseif($log['to_status'] == STATUS_OHC_PARAMEDICS_REJECTED)
                                    <p>Paramedics Rejected</p>
                                @elseif($log['to_status'] == STATUS_OHC_CLOSE)
                                    <p>Close</p>
                                @endif
                            </td>

                            <td>{{ isset($log['remarks']) ? $log['remarks'] : '-' }}</td>
                            <td>{{ isset($log['created_by']) ? getUsername($log['created_by']) : '-' }}</td>
                            <td>{{ isset($log['created_at']) ? Displaydateformat($log['created_at']) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <br>
    </div>


    <br>

</body>

</html>
