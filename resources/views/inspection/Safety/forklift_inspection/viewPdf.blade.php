=
<!DOCTYPE html>
<html>

<head>
    <title>Forklift Inspection | KARAM</title>

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
                    Forklift Inspection </td>
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
                    Forklift Inspection
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
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($inspection_details->created_by) ? $inspection_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($inspection_details->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Forklift Inspection
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">SR. NO.</th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">DEPARTMENT
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">UNIT
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">IDENTIFICATION
                    NUMBER/SERIAL NUMBER
                </th>
            </tr>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    OBSERVATION
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    CORRECTIVE AND PREVENTIVE ACTION</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    RESPONSIBILITY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">DATE OF
                    COMPLIANCE</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    STATUS</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">REMARKS
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspection as $detail)
                <tr>
                    <td style="border: 2px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getDepartment($detail->department_id) }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ getUnitname($detail->unit_id) }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->identification_no }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->observation }}</td>

                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->correction_preventive_action }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getUsername($detail->responsibility) }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ Displaydateformat($detail->date_of_compliance) }}</td>

                    <td style="border: 2px solid black; padding: 8px;">
                        @if ($detail->observation_status == 1)
                            Open
                        @elseif($detail->observation_status == 0)
                            Closed
                        @else
                            Unknown
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->remarks }}</td>
                </tr>
            @endforeach
            {{-- @php
                $prepared_by_signature = GetSafetySignature(
                    $inspection_details->created_by,
                    $inspection_details->id,
                    FORKLIFT_INSPECTION,
                );
                $verified_by_signature = GetSafetySignature(
                    $inspection_details->updated_by,
                    $inspection_details->id,
                    FORKLIFT_INSPECTION,
                );
            @endphp --}}
            <tr>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">

                    <div>Checked & Prepared By: {{ getUsername($inspection_details->created_by) }}</div>
                </td>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($inspection_details->updated_by != null)
                        <div>Verified By: {{ getUsername($inspection_details->updated_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
            </tr>

        </tbody>
    </table>

    <br>


    @if ($inspection_details->observation_status != OBSERVATION_PENDING)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('inspection.ehs_head_approval') }}
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUserName($inspection_details->updated_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($inspection_details->updated_at) }}
                </td>
            </tr>
            @if ($inspection_details->observation_status)
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $inspection_details->observation_status == 3 ? 'Approved' : 'Rejected' }}
                    </td>
                </tr>
            @endif
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $inspection_details->approval_remarks }}
                </td>
            </tr>

        </table>
        <br>
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

        <div class="card-body ">

            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Approved By / Rejected By</th>
                                <th>Created By</th>
                                <th>Remarks</th>
                                <th>Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            @if ($status_log->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No data is available</td>
                                </tr>
                            @else
                                @foreach ($status_log as $status)
                                    <tr>
                                        <td style="text-align: center;">
                                            {{ getForkLiftInspectionStatus($status['from_status'] ?? null) }}</td>
                                        <td style="text-align: center;">
                                            {{ getForkLiftInspectionStatus($status['to_status'] ?? null) }}</td>
                                        <td style="text-align: center;">
                                            {{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ isset($status['created_by']) ? getUsername($status['created_by']) : '-' }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ trim($status['remarks'] ?? '') !== '' ? $status['remarks'] : '-' }}
                                        </td>

                                        <td style="text-align: center;">
                                            {{ null !== Displaydateformat($status['created_at']) ? Displaydateformat($status['created_at']) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                            @endif
                        </tbody>
                    </table>

                </div>
            </div>


        </div>
        <br>
        <div class="page-break"></div>
    </div>
    <br>

</body>

</html>
