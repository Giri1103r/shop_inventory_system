<!DOCTYPE html>
<html>

<head>
    <title>Medicine Receiving Stock Details| KARAM</title>

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
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Medicine Receiving Stock Details
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
                    Medicine Details
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Medicine Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">{{ getMedicinename(isset($medicine_receiving->medicine_id) ? $medicine_receiving->medicine_id : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>HSN Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ (isset($medicine_receiving->hsn_id) ? $medicine_receiving->hsn_id : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Pack Details</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($medicine->pack) ? $medicine->pack : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Quantity</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($medicine_receiving->quantity) ? $medicine_receiving->quantity : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Batch Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($medicine_receiving->batch_number) ? $medicine_receiving->batch_number : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Rate</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($medicine_receiving->rate) ? $medicine_receiving->rate : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Expire Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($medicine_receiving->expire_date) ? $medicine_receiving->expire_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Vendor Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($vendor->vendor_name) ? $vendor->vendor_name : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($medicine_receiving->created_by) ? $medicine_receiving->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Stock Entry Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($medicine_receiving->created_at) }}</td>
        </tr>
    </table>

    <br>




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
                            <th>Approved By</th>
                            <th>Remarks</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($medicineReceivingStockData as $status_log)
                            <tr>
                            <tr>
                                <!-- From Status Column -->
                                <td>
                                    @if( ($status_log['from_status']) == STATUS_OHC_STOCK_REQUEST)
                                        <p  style='font-size: 1.0em;'>Stock Requested</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                        <p  style='font-size: 1.0em;'>EHS Officer Verification Pending</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_VERIFIED)
                                        <p  style='font-size: 1.0em;'>EHS Officer Verified</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_L1_EHS_VERIFIED)
                                        <p  style='font-size: 1.0em;'> L1 EHS Officer Verified</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_REJECTED)
                                        <p  style='font-size: 1.0em;'>EHS Officer Rejected</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_AGM_APPROVED)
                                        <p  style='font-size: 1.0em;'>EHS Head Approved</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                        <p  style='font-size: 1.0em;'>L1 EHS Officer Verification Pending</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_AGM_APPROVAL_PENDING)
                                        <p  style='font-size: 1.0em;'>EHS Head Approval Pending</p>
                                    @elseif( ($status_log['from_status']) == STATUS_OHC_OPEN)
                                        <p  style='font-size: 1.0em;'>Open</p>
                                    @endif
                                </td>
                                <td>
                                    @if(($status_log['to_status']) == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                        <p  style='font-size: 1.0em;'>EHS Officer Verification Pending</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_EHS_VERIFIED)
                                        <p  style='font-size: 1.0em;'>EHS Officer Verified</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_EHS_REJECTED)
                                        <p  style='font-size: 1.0em;'>EHS Officer Rejected</p>
                                    @elseif( ($status_log['to_status']) == STATUS_OHC_L1_EHS_VERIFIED)
                                        <p  style='font-size: 1.0em;'> L1 EHS Officer Verified</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_AGM_APPROVED)
                                        <p  style='font-size: 1.0em;'>EHS Head Approved</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_OPEN)
                                        <p  style='font-size: 1.0em;'>Open</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_AGM_REJECTED)
                                        <p  style='font-size: 1.0em;'>EHS Head Rejected</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                        <p  style='font-size: 1.0em;'>L1 EHS Officer Verification Pending</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_AGM_APPROVAL_PENDING)
                                        <p  style='font-size: 1.0em;'>EHS Head Approval Pending</p>
                                    @elseif(($status_log['to_status']) == STATUS_OHC_CLOSE)
                                        <p  style='font-size: 1.0em;'>Closed</p>
                                    @endif
                                </td>

                                <td>{{ isset($status_log['created_by']) ? getUsername($status_log['created_by']) : '-' }}
                                </td>
                                <td>{{ isset($status_log['remarks']) ? $status_log['remarks'] : '-' }}
                                </td>
                                <td>{{ null !== Displaydateformat($status_log['created_at']) ? Displaydateformat($status_log['created_at']) : '-' }}
                                </td>
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
