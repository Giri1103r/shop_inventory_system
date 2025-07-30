<!DOCTYPE html>
<html>

<head>
    <title>Monthly Eye Wash Inspection | KARAM</title>

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
                style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 13px; border: 1px solid black;">

                {{-- Header Row with Logo, Title, and Document Info --}}
                <tr>
                    <th colspan="3" style="border: 1px solid black; height: 50px; text-align: center;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                    </th>
                    <th colspan="6" style="border: 1px solid black; text-align: center;">
                        <h3 style="margin: 0;">
                            <b>MONTHLY SAFETY SHOWER CUM EYE WASH INSPECTION CHECKLIST
                            </b>
                        </h3>
                    </th>
                    <th colspan="3" style="border: 1px solid black; padding: 0;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                            <tr>
                                <td style="border: 1px solid black; padding: 4px; width: 50%;">Doc.No</td>
                                <td style="border: 1px solid black; padding: 4px;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 4px;">Issue Dt.</td>
                                <td style="border: 1px solid black; padding: 4px;">{{ $document_no->issue_date }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; padding: 4px;">Rev.& Dt.</td>
                                <td style="border: 1px solid black; padding: 4px;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </table>
                    </th>
                </tr>

                {{-- Inspection Info Rows --}}
                <tr>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>DATE OF INSPECTION:</strong> {{ Displaydateformat($details->date_of_inspection) ?? '' }}
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>LOCATION:</strong> {{ $details->location_name ?? '' }}
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>NEXT DUE ON:</strong> {{ Displaydateformat($details->next_due) ?? '' }}
                    </td>
                    <td colspan="3" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>UNIT:</strong> {{ $details->unit_name ?? '' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>SHIFT:</strong> {{ $details->shift ?? '' }}
                    </td>
                    <td colspan="6" style="border: 1px solid black; padding: 6px; background-color: #ddd;">
                        <strong>FREQUENCY:</strong> {{ $details->frequency_name ?? '' }}
                    </td>
                </tr>

                {{-- Header Row 1 --}}
                <tr>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">SR. NO.
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">Exact LOCATION
                    </th>
                    <th rowspan="2" style="border: 2px solid black; padding: 6px; background-color: #ddd;">RESOURCE
                        CODE</th>
                    <th colspan="4" style="border: 2px solid black; padding: 6px; background-color: #ccc;">CONDITION
                    </th>
                    <th colspan="4" style="border: 2px solid black; padding: 6px; background-color: #ddd;">WATER</th>
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
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">WATER QUALITY</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">PRESSURE</th>
                    <th style="border: 2px solid black; padding: 6px; background-color: #eee;">TEMPERATURE (15–35°C)
                    </th>
                </tr>

                {{-- Data Row --}}
                <tr>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->sr_no }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->location }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->resource_code }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->hand_free_stay_open_value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->foot_pedal_value }}</td>
                    <td style="border: 2px solid black; padding: 6px;">
                        {{ $details->eyewash_heads_value == 1 ? 'OK' : 'NOT Ok' }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->receptacle }}</td>
                    <td style="border: 2px solid black; padding: 6px;">
                        @if ($details->water == GOOD)
                            Good
                        @elseif ($details->water == FAIR)
                            Fair
                        @elseif ($details->water == POOR)
                            Poor
                        @else
                            Not specified
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->pressure }}</td>
                    <td style="border: 2px solid black; padding: 6px;">
                        {{ $details->temperature == '1' ? 'ABNORMAL' : 'NORMAL' }}</td>
                    <td style="border: 2px solid black; padding: 6px;">{{ $details->remarks }}</td>
                </tr>
                @php
                    $approved_by = GetSafetySignature($details->approved_by, $details->id, EYE_WASH_INSPECTION);
                    $verified_by = GetSafetySignature($details->verified_by, $details->id, EYE_WASH_INSPECTION);
                    $checked_by = GetSafetySignature($details->checked_by, $details->id, EYE_WASH_INSPECTION);
                @endphp
                <tr>
                    <td colspan="4" style="border: 2px solid black; padding: 6px; text-align: center;">
                        <div class="view_data">
                            @if (!empty($details->created_by))
                                {{-- <img src="{{ admin_url($checked_by) }}" alt=""
                                    style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                                <p style="margin: 0;">Checked By:- {{ getUsername($details->created_by) }}</p>
                            @else
                                <p style="margin: 0;">Checked By:- Not yet checked</p>
                            @endif
                        </div>
                    </td>
                    <td colspan="4" style="border: 2px solid black; padding: 6px; text-align: center;">
                        <div class="view_data">
                            @if (!empty($details->verified_by))
                                {{-- <img src="{{ admin_url($verified_by) }}" alt=""
                                    style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                                <p style="margin: 0;">Verified By:- {{ getUsername($details->verified_by) }}
                                </p>
                            @else
                                <p style="margin: 0;">Verified By:- Not yet verified</p>
                            @endif
                        </div>
                    </td>
                    <td colspan="4" style="border: 2px solid black; padding: 6px; text-align: center;">
                        <div class="view_data">
                            @if (!empty($details->approved_by))
                                {{-- <img src="{{ admin_url($approved_by) }}" alt=""
                                    style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                                <p style="margin: 0;">Approved By:- {{ getUsername($details->approved_by) }}
                                </p>
                            @else
                                <p style="margin: 0;">Approved By:- Not yet approved</p>
                            @endif
                        </div>
                    </td>
                </tr>

            </table>


        </div>
        <div class="page-break"></div>
    @endforeach






    <br>

</body>

</html>
