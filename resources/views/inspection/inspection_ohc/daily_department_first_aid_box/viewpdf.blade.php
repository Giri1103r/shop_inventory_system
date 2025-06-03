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
    <div>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

            <tr>
                <th colspan="6" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black;">
                    <h3>
                        <span><b>DAILY DEPARTMENTAL FIRST-AID BOX INSPECTION CHECKLIST</b></span>
                        <br>

                    </h3>
                </th>

                <th colspan="6" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>

                </th>
            </tr>
            <tr>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    DATE OF INSPECTION: {{ Displaydateformat($medicinerequisition->date) ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    FIRST AID BOX NO: {{ $medicinerequisition->first_aid_box_no ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    SHIFT: {{ getShift($medicinerequisition->shift) ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    DEPARTMENT: {{ getDepartment($medicinerequisition->department) ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    UNIT: {{ getUnitname($medicinerequisition->unit) ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    NAME OF THE FIRST AIDER: {{ getFirstAider($medicinerequisition->first_aider) ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">SR. NO
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">NAME OF
                    THE
                    MEDICINE</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">FREEZE
                    QUANTITY</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">AVAILABLE
                    QUANTITY
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">MATERIAL
                    EXPIRY
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">REMARK
                </th>

            </tr>
            @php
                $inspection_data = json_decode($medicinerequisition->checklist, true);
            @endphp
            @foreach ($inspection_data as $medicines)
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}
                    </td>
                    <td  colspan="3" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getMedicinename($medicines['medicine_id']) }}
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['freeze_quantity'] }}
                    </td>

                    <td  colspan="3" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['available_quantity'] }}
                    </td>
                    <td  colspan="3" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ Displaydateformat($medicines['expired_date']) }}
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['remarks'] }}
                    </td>
                </tr>
            @endforeach

            @php
                $createdSignature = GetOHCSignature(
                    $medicinerequisition->created_by,
                    $medicinerequisition->id,
                    OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                );
                $FloorManagerSignature = GetOHCSignature(
                    $medicinerequisition->verified_by,
                    $medicinerequisition->id,
                    OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                );

            @endphp

            {{-- <tr>
                <th colspan="9" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                    <img src="{{ admin_url($createdSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">First Aider Signature</div>
                </th>
                <th colspan="9" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                    <img src="{{ admin_url($FloorManagerSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Floor Manager Signature</div>
                </th>

            </tr> --}}

            <tr>
                <th colspan="9" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                    @if ($medicinerequisition->created_by != null)
                        <div style="margin-top: 5px;">First Aider Name :-
                            {{ getUsername($medicinerequisition->created_by) }}</div>
                    @endif
                </th>
                <th colspan="9" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                    @if ($medicinerequisition->verified_by != null)
                        <div style="margin-top: 5px;">Floor Manager Name :-
                            {{ getUsername($medicinerequisition->verified_by) }}</div>
                    @else
                        <div style="margin-top: 5px;">Has Not Yet Been Verified</div>
                    @endif
                </th>

            </tr>
        </table>
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
            {{-- <tr>
                <td width="50%" style="padding:5px;"><b>Signature</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if (!empty($floormanagersignature) && !empty($floormanagersignature->file_path))
                        <img src="{{ admin_url($floormanagersignature->file_path) }}" alt="Approver Signature"
                            style="width: 150px; height: auto;" />
                    @elseif(!empty($floorapproversignatureview) && !empty($floorapproversignatureview->signature_upload))
                        <img src="{{ admin_url($floorapproversignatureview->signature_upload) }}"
                            alt="Approver Signature" style="width: 150px; height: auto;" />
                    @else
                        No Signature Available
                    @endif

                </td>
            </tr> --}}
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
