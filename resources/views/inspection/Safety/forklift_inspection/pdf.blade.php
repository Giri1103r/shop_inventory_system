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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">

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
        <table style="width: 100%; border-collapse: collapse; border: 3px double black;">
            <tr>
                <th colspan="3" style="border: 2px double black;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="4" style="border: 2px double black; text-align: center;">
                    <h3 style="margin: 0;">
                        <span><b>FORKLIFT INSPECTION</b></span>
                    </h3>
                </th>
                <th colspan="3" style="border: 2px double black; padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 2px double black; width: 30%;">Doc.No</td>
                            <td style="border: 2px double black;">{{ $details['0']->doc_no }}</td>
                        </tr>
                        <tr>
                            <td style="border: 2px double black;">Issue Dt.</td>
                            <td style="border: 2px double black;">{{ displaydateformat($details['0']->issue_date) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 2px double black;">Rev.& Dt.</td>
                            <td style="border: 2px double black;">{{ $details['0']->rev_dt }}</td>
                        </tr>
                    </table>
                </th>
            </tr>
        </table>
        <br>
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr>
                    <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">SR.
                        NO.
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                        DEPARTMENT
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">UNIT
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">EXACT LOCATION
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                        IDENTIFICATION
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
                    <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                        DATE
                        OF
                        COMPLIANCE</th>
                    <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                        STATUS</th>
                    <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                        REMARKS
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($details as $detail)
                    <tr>
                        <td style="border: 2px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                        <td style="border: 2px solid black; padding: 8px;">
                            {{ getDepartment($detail->department_id) }}
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">
                            {{ getUnitname($detail->unit_id) }}</td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->exact_location }}</td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->identification_no }}</td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->observation }}</td>

                        <td style="border: 2px solid black; padding: 8px;">
                            {{ $detail->correction_preventive_action }}
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">{{ getUserName($detail->responsibility) }}
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
                        $detail->checked_by,
                        $detail->safety_id,
                        FORKLIFT_INSPECTION,
                    );
                    $verified_by_signature = GetSafetySignature(
                        $detail->verified_by,
                        $detail->safety_id,
                        FORKLIFT_INSPECTION,
                    );
                @endphp --}}
                <tr>
                    <td colspan="6"
                        style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                        {{-- <img src="{{ admin_url($prepared_by_signature) }}" alt="Checked By Signature"
                            style="height: 50px;"> --}}
                        <div>Checked & Prepared By: {{ getUsername($detail->checked_by) }}</div>
                    </td>
                    <td colspan="6"
                        style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                        @if ($detail->verified_by != null)
                            {{-- <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                                style="height: 50px;"> --}}
                            <div>Verified By: {{ getUsername($detail->verified_by) }}</div>
                        @else
                            <p>Inspection has not been Verified Yet</p>
                        @endif
                    </td>
                </tr>

            </tbody>
        </table>
        <div class="page-break"></div>
    @endforeach
</body>

</html>
