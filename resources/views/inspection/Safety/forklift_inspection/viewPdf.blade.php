=
<!DOCTYPE html>
<html>

<head>
    <title>Safety Walk Observation | KARAM</title>

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
                    Safety Walk Observation </td>
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
                    Safety Walk Observation
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($inspection_details->doc_no) ? $inspection_details->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($inspection_details->revision_data) ? $inspection_details->revision_data : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_details->date) ? $inspection_details->date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Shift Id</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getShiftname(isset($inspection_details->shift_id) ? $inspection_details->shift_id : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Month</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($inspection_details->month) ? $inspection_details->month : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Safety Walk Taken By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($inspection_details->safety_walk_taken_by) ? $inspection_details->safety_walk_taken_by : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($inspection_details->created_by) ? $inspection_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($inspection_details->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Safety Walk Observation
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">SR. NO.</th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">LOCATION</th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">DATE OF
                    OBSERVATION
                </th>
                <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">OBSERVATION
                </th>
            </tr>
            <tr>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">PICTURE
                </th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    RECOMENDED ACTION</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    RESPONSIBILITY</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">DATE OF
                    COMPLIANCE</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                    STATUS</th>
                <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">REMARKS
                </th>
            </tr>
        </thead>
        <tbody>

            @if ($last_month_observation_details !== false)
                <tr>
                    <td colspan="12" style="text-align: center; font-weight: bold; background-color: #d3d3d3;">
                        Previous
                        Month Observation</td>
                </tr>
                @php
                    $i = 1;
                @endphp
                @foreach ($last_month_observation_details as $details)
                    @foreach ($details as $details)
                        <tr>
                            <td style="border: 2px solid black; padding: 8px;">{{ $i }}</td>
                            <td style="border: 2px solid black; padding: 8px;">
                                {{ getLocationName($details->location) }}
                            </td>
                            <td style="border: 2px solid black; padding: 8px;">
                                {{ Displaydateformat($details->observation_date) }}</td>
                            <td style="border: 2px solid black; padding: 8px;">{{ $details->observation }}</td>

                            <td style="border: 2px solid black; padding: 8px;"><img
                                    src="{{ admin_url(GetSafetyWalkImage($details->id)) }}" alt="" />
                            </td>
                            <td style="border: 2px solid black; padding: 8px;">{{ $details->recomended_action }}
                            </td>
                            <td style="border: 2px solid black; padding: 8px;">{{ $details->responsibility }}</td>
                            <td style="border: 2px solid black; padding: 8px;">{{ $details->date_of_compliance }}</td>
                            <td style="border: 2px solid black; padding: 8px;">
                                @if ($details->observation_status == 1)
                                    Active
                                @elseif($details->observation_status == 0)
                                    Inactive
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td style="border: 2px solid black; padding: 8px;">{{ $details->remarks }}</td>
                        </tr>
                        @php

                            $i++;
                        @endphp
                    @endforeach
                @endforeach
            @endif

            <tr>
                <td colspan="12" style="text-align: center; font-weight: bold; background-color: #d3d3d3;">Current
                    Month Observation</td>
            </tr>
            @foreach ($inspection as $detail)
                <tr>
                    <td style="border: 2px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ getLocationName($detail->location) }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ Displaydateformat($detail->observation_date) }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->observation }}</td>
                    <td style="border: 2px solid black; padding: 8px;"><img
                            src="{{ GetSafetyWalkImage($detail->id) }}" alt="">
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->recomended_action }}</td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->responsibility }}
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">
                        {{ Displaydateformat($detail->date_of_compliance) }}</td>

                    <td style="border: 2px solid black; padding: 8px;">
                        @if ($detail->observation_status == 1)
                            Active
                        @elseif($detail->observation_status == 0)
                            Inactive
                        @else
                            Unknown
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 8px;">{{ $detail->remarks }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>


    <br>

</body>

</html>
