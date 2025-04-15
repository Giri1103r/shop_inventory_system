<!DOCTYPE html>
<html>

<head>
    <title>MONTHLY FORKLIFT INSPECTION Checklist | KARAM</title>

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
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        OHS PLANT SUMMARY REPORT
                    </td>
                </tr>
            </table>
        </div>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="3" style="border:1px solid black; height:50px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black; text-align: center;">
                    <h3><b>{{ __('title.ohs_summary_report') }}</b></h3>
                </th>
                <th colspan="3" style="border:1px solid black;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $details->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($details->issue_date) }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $details->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    DATE OF INSPECTION: {{ Displaydateformat($details->inspection_date) ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    UPDATED FREQUENCY: {{ $details->updated_frequency ?? 'N/A' }}
                </th>

            </tr>
        </table>

        @php
            $rowcount = count($units);
            $quantity_details = json_decode($details->quantity_details, true);
            $fire_water_pump_details = json_decode($details->fire_water_pump_details, true);
        @endphp

        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr>
                    <th rowspan="2"
                        style="border: 1px solid black; padding: 8px; background-color: #ddd; width: 80px;">
                        SR. NO.</th>
                    <th rowspan="2"
                        style="border: 1px solid black; padding: 8px; background-color: #ddd; width: 150px;">
                        Description</th>
                    <th colspan="{{ $rowcount }}"
                        style="border: 1px solid black; padding: 8px; background-color: #ddd; text-align:center;">
                        Quantity (in Nos/m²)
                    </th>
                    <th rowspan="2"
                        style="border: 1px solid black; padding: 8px; background-color: #ddd; width: 150px;">
                        Total Quantity (in Nos/m²)</th>
                </tr>
                <tr>
                    @foreach ($units as $unit)
                        <th style="border: 1px solid #000; vertical-align:middle; text-align:center; width: 100px;">
                            {{ $unit->unit_name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($quantity_details as $index => $quantity_detail)
                    <tr>
                        <td style="border: 1px solid #000;">{{ $index }}</td>
                        <td style="border: 1px solid #000;">
                            {{ $quantity_detail['description'] }}</td>
                        @foreach ($units as $index => $unit)
                            <td style="border: 1px solid #000;">
                                {{ isset($quantity_detail['unit - ' . $index + 1]) ? $quantity_detail['unit - ' . $index + 1] : '' }}
                            </td>
                        @endforeach

                        <td style="border: 1px solid #000;">
                            {{ $quantity_detail['total_quantity'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        Fire Water Pump House Details
                    </td>
                </tr>
            </table>
        </div>

        <br>


        <div class="table-responsive">
            <table id="firewaterpump" class="table table-bordered text-center" style="border-collapse: collapse;">
                <thead>
                    <tr>
                        <th rowspan="2" style="border: 1px solid #000;  background-color: #ddd; vertical-align:middle; text-align:center">Sr.
                            No.
                        </th>
                        <th rowspan="2" style="border: 1px solid #000;  background-color: #ddd; vertical-align:middle; text-align:center">Name
                            of
                            Water
                            Pump & Water Storage Tank
                        </th>
                        <th colspan="{{ $rowcount }}"
                            style="border: 1px solid #000; text-align: center;  background-color: #ddd; vertical-align:middle; text-align:center">
                            Capacity
                        </th>
                    </tr>
                    <tr>
                        @foreach ($units as $unit)
                            <th style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                {{ $unit->unit_name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fire_water_pump_details as $index => $quantity_detail)
                        <tr>
                            <td style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                {{ $index + 1 }}</td>
                            <td style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                {{ $quantity_detail['fire_pump_details'] }}
                            </td>
                            @foreach ($units as $unit_index => $unit)
                                <td style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                    {{ isset($quantity_detail['fire_pump_details_unit_' . ($unit_index + 1)]) ? $quantity_detail['fire_pump_details_unit_' . ($unit_index + 1)] : '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    @php
                        $total_columns = 2 + count($units); // 2 static columns + dynamic columns from $units
                    @endphp

                    <tr>
                        <td colspan="{{ $total_columns }}"
                            style="border: 1px solid #000; vertical-align:middle; text-align:left">
                            Prepared By: {{ getUsername($details->checked_by) }}
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>



        <div class="page-break"></div>
    @endforeach

    <br>

</body>

</html>
