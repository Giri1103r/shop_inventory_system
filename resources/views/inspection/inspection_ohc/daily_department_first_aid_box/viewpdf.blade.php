<!DOCTYPE html>
<html>

<head>
    <title>Daily Departmental First Aid Box| KARAM</title>

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
                    Daily Departmental First Aid Box</td>
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
                    Daily Departmental First Aid Box
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUnitname(isset($medicinerequisition->unit) ? $medicinerequisition->unit : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Department</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getDepartment(isset($medicinerequisition->department) ? $medicinerequisition->department : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Shift</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getShift(isset($medicinerequisition->shift) ? $medicinerequisition->shift : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>First Aid Box Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ (isset($medicinerequisition->first_aid_box_no) ? $medicinerequisition->first_aid_box_no : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>First Aider Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getFirstAider(isset($medicinerequisition->first_aider) ? $medicinerequisition->first_aider : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Signature</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                @if (!empty($requestorsignature) && !empty($requestorsignature->file_path))
                    <img src="{{ admin_url($requestorsignature->file_path) }}" alt="Requestor Signature"
                        style="width: 150px; height: auto;" />
                @elseif (!empty($signatureview) && !empty($signatureview->signature_upload))
                    {{-- Fixed typo --}}
                    <img src="{{ admin_url($signatureview->signature_upload) }}" alt="Approver Signature"
                        style="width: 150px; height: auto;" />
                @else
                    <span>No signature available</span>
                @endif
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($medicinerequisition->created_by) ? $medicinerequisition->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($medicinerequisition->created_at) }}</td>
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
                @if (isset($daily_department_first_aid_box) && $daily_department_first_aid_box->isNotEmpty())
                    <table class="table table-bordered table-hover tblborder">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Medicine Name</th>
                                <th>Available Quantity</th>
                                <th>Freeze Quantity</th>
                                <th>Material Expiry</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($daily_department_first_aid_box->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center">No data is available</td>
                                </tr>
                            @else
                                @foreach ($daily_department_first_aid_box as $data)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ getMedicinename($data->medicine_id) }}</td>
                                        <td>{{ $data->available_quantity }}</td>
                                        <td>{{ $data->freeze_quantity }}</td>
                                        <td>{{ Displaydateformat($data->material_expiry) }}</td>

                                        <td>{{ $data->remarks }}</td>


                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="text-dark">{{ __('No status logs available.') }}</p>
                    </div>
                @endif
            </div>
        </div>
        <br>
    </div>

    @if (
        $medicinerequisition->approve_status == MEDICAL_ASSISTANT_APPROVED ||
            $medicinerequisition->approve_status == MEDICAL_ASSISTANT_REJECTED)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        Floor manager/ Medical Assistant Approval
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Approver Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($floormanger->approved_by) ? $floormanger->approved_by : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Approved Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Approved Time</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaytimeformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Signature</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (!empty($floormanagersignature) && !empty($floormanagersignature->file_path))
                        <img src="{{ admin_url($floormanagersignature->file_path) }}" alt="Approver Signature"
                            style="width: 150px; height: auto;" />
                    @elseif(!empty($floorapproversignatureview) && !empty($floorapproversignatureview->signature_upload))
                        <img src="{{ admin_url($floorapproversignatureview->signature_upload) }}" alt="Approver Signature"
                            style="width: 150px; height: auto;" />
                    @else
                        No Signature Available
                    @endif

                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($floormanger->remarks) ? $floormanger->remarks : '' }}
                </td>
            </tr>

        </table>
    @endif
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
                @if (isset($statuslog) && $statuslog->isNotEmpty())
                    <table class="table table-bordered table-hover tblborder">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Remarks</th>
                                <th>Approved By</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($statuslog as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ getohcrequisitionfloorstatus($log->from_status) }}</td>
                                    <td>{{ getohcrequisitionfloorstatus($log->to_status) }}</td>
                                    <td>{{ $log->remarks ?? 'N/A' }}</td>
                                    <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                    </td>
                                    <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}</td>
                                    <td>{{ displaydateformat($log->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="text-dark">{{ __('No status logs available.') }}</p>
                    </div>
                @endif
            </div>
        </div>
        <br>
    </div>
    <br>

</body>

</html>
