<!DOCTYPE html>
<html>

<head>
    <title>MSDS Inspection | KARAM</title>

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
                    MSDS Inspection </td>
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
                    MSDS Inspection
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
            <td width="50%" style="padding:5px;"><b>Location</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($msds->location_id) ? getLocationname($msds->location_id) : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($msds->unit_id) ? getUnitname($msds->unit_id) : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Department</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($msds->department_id) ? getDepartment($msds->department_id) : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($msds->created_by) ? $msds->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($msds->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    MSDS Inspection Checklist
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    SERIAL NUMBER
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    ITEM CODE
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    NAME OF CHEMICAL
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    STORAGE CAPACITY
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    NPFA RATING TYPE
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    NPFA RATING
                </th>
            </tr>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    MSDS AVAILABILITY STATUS
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    REMARK
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspection_details as $msdsDetails)
                <tr>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->serial_number) ? $msdsDetails->serial_number : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->item_code) ? $msdsDetails->item_code : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->name_of_chemical) ? getChemicalName($msdsDetails->name_of_chemical) : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->storage_capacity) ? ($msdsDetails->storage_capacity) : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->nfa_rating) ? getNFARating($msdsDetails->nfa_rating) : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->nfa_rating_value) ? ($msdsDetails->nfa_rating_value) : '' }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px; text-align: center;">
                        @if ($msdsDetails->msds_availability_status == YES)
                            <span style="color: green; font-size: 20px;">✓</span>
                        @elseif ($msdsDetails->msds_availability_status == NO)
                            <span style="color: red; font-size: 20px;">X</span>
                        @elseif ($msdsDetails->msds_availability_status == 'N/A')
                            <span style="color: gray; font-size: 20px;">N/A</span>
                        @else
                            <span style="color: gray; font-size: 20px;">-</span>
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ isset($msdsDetails->remark) ? $msdsDetails->remark : '' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>

</body>

</html>
