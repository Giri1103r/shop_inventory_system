<!DOCTYPE html>
<html>

<head>
    <title>OHC DAILY VITAL EQUIPMENT | KARAM</title>

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
                   OHC DAILY VITAL EQUIPMENT </td>
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
                    DAILY VITAL EQUIPMENT
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Data</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($daily_vital->created_by) ? $daily_vital->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($daily_vital->created_at) }}</td>
        </tr>
    </table>

    <br>
    @php
        $user_response = json_decode($daily_vital->responses, true);
        $srNo = 1;
    @endphp

    <table border="1" cellspacing="0" cellpadding="8" width="100%">
        <thead>
            <tr>
                <td colspan="1" style="border: 1px solid black; padding: 8px; font-weight: bold;"
                    title="DATE OF INSPECTION">DATE OF INSPECTION :-
                    {{ DisplayDateformat($daily_vital->date_of_inspection) }}</td>
                <td colspan="2" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="UNIT">
                    UNIT :- {{ getUnitname($daily_vital->unit) }}
                </td>
                <td colspan="2" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="SHIFT">
                    SHIFT :- {{ getShiftName($daily_vital->shift) }}
                </td>
            </tr>

            <tr>
                <th style="background-color: #ccc; text-align: center;">Sr. No</th>
                <th style="background-color: #ccc; text-align: center;">Check Points</th>
                <th style="background-color: #ccc; text-align: center;">Response</th>
                <th style="background-color: #ccc; text-align: center;">Quantity</th>
                <th style="background-color: #ccc; text-align: center;">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user_response as $subcategory => $questions)
                @foreach ($questions as $questionId => $answer)
                    <tr>
                        <td style="text-align: center; font-weight: bold;">{{ $srNo }}</td>
                        <td>{{ GetChecklistTypeDate($questionId) }}</td>
                        <td style="text-align: center;">
                            @php
                                $responseText = $answer['response'] ?? '-';
                            @endphp
                            @if ($responseText == 'YES')
                                <span style="color: green; font-size: 20px;">✓</span>
                            @elseif ($responseText == 'NO' || $responseText == 'N/A')
                                <span style="color: red; font-size: 20px;">X</span>
                            @else
                                {{ $responseText }}
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $answer['quantity'] ?? '-' }}</td>
                        <td style="text-align: center;">{{ $answer['remark'] ?? '-' }}</td>
                    </tr>
                    @php $srNo++; @endphp
                @endforeach
            @endforeach
            @php
                $signature = GetOHCSignature(
                    $daily_vital->created_by,
                    $daily_vital->id,
                    OHC_DAILY_VITAL_EQUIPMENT_CHECKLIST,
                );
            @endphp
            <tr>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    <img src="{{ admin_url($signature) }}" alt="Checked By Signature"
                        style="height: 50px;">
                    <div>Checked & Prepared By: {{ getUsername($daily_vital->created_by) }}</div>
                </td>
            </tr>

        </tbody>
    </table>

</body>

</html>
