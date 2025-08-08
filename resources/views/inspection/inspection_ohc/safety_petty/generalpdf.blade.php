<!DOCTYPE html>
<html>

<head>
    <title>OHC Safety Petty Logbook | KARAM</title>

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
                    OHC Safety Petty Logbook </td>
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
                    OHC Safety Petty Logbook
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
            <td width="50%" style="padding:5px;"><b>Revision & Data</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($sfty_petty_details->created_by) ? $sfty_petty_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($sfty_petty_details->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    OHC Safety Petty Logbook Checklist
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">SR. NO.</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">SERIAL NUMBER</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">EMPLOYEE NAME</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">EMPLOYEE CODE</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">DEPARTMEN</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">UNIT</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">DATE</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">AMOUNT</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">DESCRIPTION</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">AMOUNT GIVEN BY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">AMOUNT RECEIVED BY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">REMARK</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">CREATED BY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">CREATED DATE</th>
            </tr>
        </thead>
        @php
            $i = 1;
        @endphp
        <tbody>
                <tr>
                    <td style="border: 2px solid black; padding: 8px;">{{ $i++ }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ isset($sfty_petty_details->serial_number) ? $sfty_petty_details->serial_number : '' }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ ($sfty_petty_details->employee_name) }}</td>
                    <td style="border: 2px solid black; padding: 8px;"> {{ isset($sfty_petty_details->employee_code) ? $sfty_petty_details->employee_code : '' }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getDepartment($sfty_petty_details->department) }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getUnitname($sfty_petty_details->unit) }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ isset($sfty_petty_details->date) ? $sfty_petty_details->date : '' }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ isset($sfty_petty_details->amount) ? $sfty_petty_details->amount : '' }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ isset($sfty_petty_details->description) ? $sfty_petty_details->description : '' }}</td>
                    <td colspan="1" style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                        {{-- @if(isset($signature_amount_givenby && $signature_amount_givenby->file_path))
                            <img src="{{ admin_url($signature_amount_givenby->file_path) }}" alt="Checked By Signature" style="height: 50px;"> --}}
                            {{ getUsername($sfty_petty_details->amount_given_by) }}
                        {{-- @else
                            <div>N/A</div>
                        @endif --}}
                    </td>
                    <td colspan="1" style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                        {{-- @if(isset($signature_amount_receivedby && $signature_amount_receivedby->file_path))
                            <img src="{{ admin_url($signature_amount_receivedby->file_path) }}" alt="Checked By Signature" style="height: 50px;"> --}}
                            {{ getUsername($sfty_petty_details->amount_received_by) }}
                        {{-- @else
                            <div>N/A</div>
                        @endif --}}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ isset($sfty_petty_details->remark) ? $sfty_petty_details->remark : '' }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getUsername(isset($sfty_petty_details->created_by) ? $sfty_petty_details->created_by : '') }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ displayDateformat($sfty_petty_details->created_at) }}</td>
                </tr>
        </tbody>
    </table>

    <br>

</body>

</html>
