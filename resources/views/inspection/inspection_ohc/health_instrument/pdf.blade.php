<!DOCTYPE html>
<html>

<head>
    <title>Health Instrument Calibration | KARAM</title>

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

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                    Health Instrument Calibration
                </td>
            </tr>
        </table>
    </div>
    
    @php
    $flattenedContent = collect($content)->flatten(1);
    $firstItem = $flattenedContent->first();
    @endphp


    <table width="100%" style="width:100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td colspan="4" style="border:1px solid black; height:50px; text-align: center;">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
            </td>
            <td colspan="6" style="border:1px solid black; text-align: center;">
                <h3 style="margin:0;"><b>{{ __('title.health_instrument') }}</b></h3>
            </td>
            <td colspan="3" style="border:1px solid black; padding:0;">
                <table style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td style="border:1px solid black;"><b>Doc.No</b></td>
                        <td style="border:1px solid black;">{{ $firstItem->doc_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;"><b>Issue Dt.</b></td>
                        <td style="border:1px solid black;">{{ $firstItem->issue_date ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="border:1px solid black;"><b>Rev.& Dt.</b></td>
                        <td style="border:1px solid black;">{{ $firstItem->rev_dt ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="table table-bordered table-hover tblborder" style="width:100%; border-collapse: collapse;"
    border="1">
        <thead>
            <tr>
                <th>Sr.</th>
                <th>Instrument Name</th>
                <th>Resource Code</th>
                <th>Exact Location</th>
                <th>Unit</th>
                <th>Instrument Serial Number</th>
                <th>Make</th>
                <th>Mod.</th>
                <th>Instrument Range</th>
                <th>Calibration Frequency</th>
                <th>Date of Calibration</th>
                <th>Next Due Date</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($content as $unitName => $details)
            <tr style="background-color: #ffb9bf; color: #fff; font-weight: bold;">
                <td colspan="13" style="text-align: center;">{{ strtoupper($unitName) }}</td>
            </tr>
            
                @foreach ($details as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->instrument_name }}</td>
                        <td>{{ $item->resource_code }}</td>
                        <td>{{ $item->exact_location }}</td>
                        <td>{{ $item->unit_name }}</td>
                        <td>{{ $item->instrument_serial_no }}</td>
                        <td>{{ $item->make }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->instrument_range }}</td>
                        <td>{{ getFrequencyname($item->calibration_frequency) }}</td>
                        <td>{{ displaydateformat($item->date_of_calibration)}}</td>
                        <td>{{ displaydateformat($item->due_date_of_calibration)}}</td>
                        <td>{{ $item->remarks }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>


    {{-- <div class="page-break"></div> --}}


</body>

</html>
