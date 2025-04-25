<!DOCTYPE html>
<html>

<head>
    <title>Daily Fire Pump House Inspection | KARAM</title>

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
                    Daily Fire Pump House Inspection</td>
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
                    Daily Fire Pump House Inspection
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Inspection ID</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($dailyFire->inspection_id) ? $dailyFire->inspection_id : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Doc. No</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($dailyFire->document_no) ? $dailyFire->document_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Dt.</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ displayDateformat($dailyFire->issuedate) }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Rev. & Dt.</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $dailyFire->rev_date }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Date of Inspection</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ displayDateformat($dailyFire->date_of_inspection) }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUnitname(isset($dailyFire->unit_id) ? $dailyFire->unit_id : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Shift</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getShiftname(isset($dailyFire->shift_id) ? $dailyFire->shift_id : '') }}</td>
        </tr>

    </table>

    <br>
    @php
        $user_response = json_decode($dailyFire->checklist, true);
    @endphp
    <div class="table-responsive">
        <div class="col-md-12">
            <table class="table table-bordered table-hover tblborder">
                <thead>
                    <tr>

                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Sr. No</th>
                        <th colspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Check Points</th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Pump No</th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            YES/NO</th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @php $srNo = 1; @endphp
                    @foreach ($user_response as $checklistId => $data)
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                {{ $srNo }}
                            </td>
                            <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                {{ GetChecklistTypeDate($checklistId) }}
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                {{ $data['pump_no'] ?? 'N/A' }}
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if ($data['response'] == 'YES')
                                    <span style="color: green; font-size: 20px;">✓</span>
                                @elseif ($data['response'] == 'NO')
                                    <span style="color: red; font-size: 20px;">X</span>
                                @endif
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                {{ $data['remarks'] ?? '-' }}
                            </td>
                        </tr>
                        @php $srNo++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Signature</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                <a href="{{ asset($dailyFire->file_path) }}" target="_blank">
                    <img src="{{ asset($dailyFire->file_path) }}" alt="Signature" style="max-width: 10%;">
                </a>
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ displayDateformat($dailyFire->date) }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Note</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $dailyFire->note }}
            </td>
        </tr>
    </table>
</body>

</html>
