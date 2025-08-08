<!DOCTYPE html>
<html>

<head>
    <title>Emergency Light Inspection | KARAM</title>

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
        .page-break {
            page-break-before: always;
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
                {{-- <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Emergency Light Inspection </td> --}}
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


    @foreach ($content as $details)

        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        Emergency Light Inspection
                    </td>
                </tr>
            </table>
        </div>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="4" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </th>
                <th colspan="4" style="border:1px solid black;">
                    <h3>
                        <span><b>EMERGENCY LIGHT INSPECTION CHECKLIST</b></span>

                    </h3>
                </th>

                <th colspan="4" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->issue_date }}</td>
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
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    DATE OF INSPECTION: {{ Displaydateformat($details->date_of_inspection) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    LOCATION: {{ getLocationname($details->location) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    SHIFT: {{ $details->shift ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    NEXT DUE: {{ Displaydateformat($details->next_due) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    UNIT: {{ getUnitname($details->unit) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    FREQUENCY: {{ getFrequencyname($details->frequency) ?? 'N/A' }}
                </th>
            </tr>

            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">DEPARTMENT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">LOCATION
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">EMERGENCY
                    LIGHT
                    NUMBER</th>
                <th colspan="7" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CHECK ITEMS
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">REMARK</th>
            </tr>
            <tr>

                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">DESCRIPTION
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CONDITION
                    OF
                    LIGHT</th>

            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">TYPE OF LIGHT</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CAPACITY</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">QUANTITY</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LIGHT CONDITION</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">SWITCH CONDITION</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">POWER SUPPLY</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">STATUS</th>
            </tr>
            @php
                $inspection = GetEmeregencyLightInspection($details->id);
            @endphp

            @foreach ($inspection as $detail)
                <tr>
                    <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ getDepartment($detail->department) }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $detail->location }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $detail->emergency_of_light }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ GetTypeofLight($detail->type_of_light) }}
                    </td>

                    <td style="border: 1px solid black; padding: 8px;">
                        {{ $detail->capacity }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px;">
                        {{ $detail->quantity }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px;">
                        {{ getLightCondition($detail->light_condition) }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px;">
                        {{ getLightCondition($detail->switc_condition) }}

                    </td>
                    <td style="border: 1px solid black; padding: 8px;">{{ GetPowerSuply($detail->power_supply) }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px;">
                        {{ getFireLightInspectionStatus($detail->fire_status) }}</td>
                    <td style="border: 1px solid black; padding: 8px;">{{ $detail->remarks }}</td>


                </tr>
            @endforeach
            {{-- @php
                $checked_by = GetSignature($details->created_by, $details->id, EMERGENCY_LIGHT_INSPECTION);
                $approved_by = GetSignature($details->approved_by, $details->id, EMERGENCY_LIGHT_INSPECTION);
                $verified_by = GetSignature($details->verified_by, $details->id, EMERGENCY_LIGHT_INSPECTION);
            @endphp
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->created_by))
                            <img src="{{ admin_url($checked_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;">
                            <p style="margin: 0;">Checked By:- {{ getUsername($details->created_by) }}</p>
                        @else
                            <p style="margin: 0;">Checked By:- Not yet checked</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->verified_by))
                            <img src="{{ admin_url($verified_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;">
                            <p style="margin: 0;">Verified By:- {{ getUsername($details->verified_by) }}</p>
                        @else
                            <p style="margin: 0;">Verified By:- Not yet verified</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->approved_by))
                            <img src="{{ admin_url($approved_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;">
                            <p style="margin: 0;">Approved By:- {{ getUsername($details->approved_by) }}</p>
                        @else
                            <p style="margin: 0;">Approved By:- Not yet approved</p>
                        @endif
                    </div>
                </td>
            </tr> --}}

            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->created_by))
                            <p style="margin: 0;">Checked By:- {{ getUsername($details->created_by) }}</p>
                        @else
                            <p style="margin: 0;">Checked By:- Not yet checked</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->verified_by))
                            <p style="margin: 0;">Verified By:- {{ getUsername($details->verified_by) }}</p>
                        @else
                            <p style="margin: 0;">Verified By:- Not yet verified</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($details->approved_by))
                            <p style="margin: 0;">Approved By:- {{ getUsername($details->approved_by) }}</p>
                        @else
                            <p style="margin: 0;">Approved By:- Not yet approved</p>
                        @endif
                    </div>
                </td>
            </tr>


        </table>
        <div class="page-break"></div>
    @endforeach
    <br>
    </div>
    <br>

</body>

</html>
