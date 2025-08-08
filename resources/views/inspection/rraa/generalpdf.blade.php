<!DOCTYPE html>
<html>

<head>
    <title>RRAA Inspection | KARAM</title>

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
                    RRAA Inspection </td>
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
                    RRAA Inspection
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
                {{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($rraa_details->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    RRAA Inspection Checklist
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">SERIAL NUMBER</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">CATEGORY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd;">OHS Compliance Index(Role)</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">FREQUENCY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">SCOPE</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">RESPONSIBILITY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">AUTHORITY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">ACCOUNTABILITY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">REMARK</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">CREATED BY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">CREATED DATE</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->serial_number) ? $rraa_details->serial_number : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ getCategoryname($rraa_details->category) }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->ohs_compliance_index) ? $rraa_details->ohs_compliance_index : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ getFrequencyname($rraa_details->frequency) }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->scope) ? $rraa_details->scope : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ getUsername($rraa_details->responsibility) }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->authority) ? $rraa_details->authority : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->accountability) ? $rraa_details->accountability : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ isset($rraa_details->remark) ? $rraa_details->remark : '' }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}</td>
                <td style="border: 2px solid black; padding: 8px;">{{ displayDateformat($rraa_details->created_at) }}</td>
            </tr>
        </tbody>
    </table>

    <br>

</body>

</html>
