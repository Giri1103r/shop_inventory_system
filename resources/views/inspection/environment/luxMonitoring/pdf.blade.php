<!DOCTYPE html>
<html>

<head>
    <title>WORK PLACE LUX MONITORING| KARAM</title>

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
    @foreach ($exportedData as $data)
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        WORK PLACE LUX MONITORING
                    </td>
                </tr>
            </table>
        </div>
        @php
            $environmentData = $data['environmentData'];
            $luxDataList = $data['luxDataList'];
            $document_no = $data['document_no'];
        @endphp
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

            {{-- Header Row with Logo and Doc Info --}}
            <tr>
                <th colspan="12" style="border:1px solid black;height:50px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="14" style="border:1px solid black;">
                    <h3>
                        <b>WORK PLACE LUX MONITORING </b><br>

                    </h3>
                </th>
                <th colspan="14" style="border:1px solid black; padding:0;">
                    <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: 1px solid black; width:70px;">Doc.No</td>
                            <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;">Issue Dt.</td>
                            <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black;">Rev.& Dt.</td>
                            <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                        </tr>
                    </table>
                </th>
            </tr>

            {{-- Column Headers --}}
            <tr>
                <th colspan="2" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">SR. NO</th>
                <th colspan="4" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LOCATION
                </th>
                <th colspan="2" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">UNIT</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">DEPARTMENT
                </th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LUX LEVEL
                </th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">DATE OF
                    MONITORING</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">NEXT DUE
                    DATE OF MONITORING</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LUX LEVEL
                </th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">DATE OF
                    MONITORING</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">NEXT DUE
                    DATE OF MONITORING</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LAST DUE
                    DATE OF MONITORING</th>
                <th colspan="4" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">ACT/RULE
                </th>
                <th colspan="4" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">REMARKS
                </th>
            </tr>

            {{-- Table Body --}}
            @foreach ($luxDataList as $list)
                <tr>
                    <td colspan="2" style="border: 1px solid black; padding: 8px;">{{ $list->sr_no }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $list->location_name }}</td>
                    <td colspan="2" style="border: 1px solid black; padding: 8px;">{{ $list->unit_name }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">{{ $list->department_name }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">{{ $list->lux_level1 }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                        {{ Displaydateformat($list->date_of_monitoring) }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                        {{ Displaydateformat($list->next_due_date_of_monitoring) }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">{{ $list->lux_level2 }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                        {{ Displaydateformat($list->date_of_monitoring2) }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                        {{ Displaydateformat($list->next_due_date_of_monitoring2) }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                        {{ Displaydateformat($list->last_due_date_of_monitoring) }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $list->act_rule }}</td>
                    <td colspan="4" style="border: 1px solid black; padding: 8px;">{{ $list->remark }}</td>
                </tr>
            @endforeach

        </table>

        <div class="page-break"></div>
    @endforeach

</body>

</html>
