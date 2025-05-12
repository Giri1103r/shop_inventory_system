<!DOCTYPE html>
<html>

<head>
    <title> SAFETY EQUIPMENT | KARAM</title>

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


    <br>

    <div style="width:100%;">
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        {{ __('Fire Safety Equipment Details') }}
                    </td>
                </tr>
            </table>
        </div>
        <br>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="3" style="border:1px solid black; height:50px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black; text-align: center;">
                    <h3><b>{{ __('title.safety_equipment') }}</b></h3>
                </th>
                <th colspan="3" style="border:1px solid black;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>
                </th>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">SR. NO.
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    {{ __('inspection.equipment_name') }}
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    {{ __('inspection.item_code') }}
                </th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('inspection.standard_norms') }}
                </th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('inspection.equipment_category') }}
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                    {{ __('inspection.unit_of_measurement') }}
                </th>
            </tr>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('inspection.minimum_order_value') }}
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('inspection.economic_order_quantity') }}
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('Status') }}
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #ddd; font-weight: bold;">
                    {{ __('inspection.remarks') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($content as $details)
                <tr>
                    <td style="border: 2px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ getEquipmentName($details->equipment_id) }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->item_code }}</td>
                    <td style="border: 2px solid black; padding: 8px;">
                        @if ($details->standard_norms == STANDARD)
                            Standard
                        @elseif ($details->standard_norms == NORMS)
                            Norms
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->equipment_category }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->measurement_unit }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->minimum_order_level }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->economic_order_quantity }}</td>
                    <td style="border: 2px solid black; padding: 8px;">
                        @if ($details->observation_status == 1)
                            Active
                        @elseif($details->observation_status == 0)
                            Deactive
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $details->remark }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="page-break"></div>


    <br>

</body>

</html>
