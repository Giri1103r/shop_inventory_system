<!DOCTYPE html>
<html>

<head>
    <title>Monthly Eyewash Inspection | KARAM</title>

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
                    Monthly EyeWash Inspection </td>
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
                    Monthly EyeWash Inspection
                </td>
            </tr>
        </table>
        <br>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Document Number</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                </td>
            </tr>
        </table>
    </div>
    <br>
    @foreach ($content as $details)
        <div style="width:100%; margin-bottom: 20px;">
            <table style="width:100%;">
                <tr>
                    <td style="background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        Monthly EyeWash Inspection Details
                    </td>
                </tr>
            </table>

            <br>
            <table
                style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 13px;">
                {{-- Inspection Info Rows --}}
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>DATE OF
                            INSPECTION:</strong> {{ Displaydateformat($details->date_of_inspection) ?? '' }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>LOCATION:</strong>
                        {{ $details->location_name ?? '' }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>NEXT DUE ON:</strong>
                        {{ Displaydateformat($details->next_due) ?? '' }}</td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>UNIT:</strong>
                        {{ $details->unit_name ?? '' }}</td>
                </tr>
                <tr>
                    <td colspan="6" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>SHIFT:</strong>
                        {{ $details->shift ?? '' }}</td>
                    <td colspan="6" style="border: 1px solid black; padding: 6px; background-color: #ddd;"><strong>FREQUENCY:</strong>
                        {{ $details->frequency_name ?? '' }}</td>
                </tr>

                {{-- Header Row 1 --}}
                <tr>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">SR. NO.
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">LOCATION
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">RESOURCE
                        CODE</th>
                    <th colspan="4" style="border: 2px solid black; padding: 6px; background-color: #ccc;">CONDITION
                    </th>
                    <th colspan="4" style="border: 2px solid black; padding: 6px; background-color: #ddd;">WATER
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">REMARK
                    </th>
                </tr>

                {{-- Header Row 2 --}}
                <tr>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">VALVE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">HANDS-FREE STAY OPEN
                        VALVE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">FOOT PEDAL VALVE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">EYEWASH HEADS</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">RECEPTACLE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">QUALITY</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">PRESSURE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">TEMPERATURE (15–35°C)
                    </th>
                </tr>

                {{-- Data Row --}}
                <tr>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->sr_no }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ getLocationName($details->location) }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->resource_code }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->hand_free_stay_open_value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->foot_pedal_value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->eyewash_heads_value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->receptacle }}</td>
                    <td style="border: 2px solid black; padding: 6px;">
                        @if ($details->quality == 'GOOD')
                            Good
                        @elseif($details->quality == 'FAIR')
                            Fair
                        @elseif($details->quality == 'POOR')
                            Poor
                        @else
                            Unknown
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->pressure }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->temperature }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->remarks }}</td>
                </tr>
            </table>

        </div>
    @endforeach


</body>

</html>
