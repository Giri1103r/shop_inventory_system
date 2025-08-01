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

        .page-break {
            page-break-before: always;
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

    @foreach ($content as $detail => $group)
        @php
            $first = $group->first();
        @endphp
        @foreach ($group as $detail)
            <br>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                            Safety Walk Observation Sheet
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
                        <h3><b>Safety Walk Observation Sheet </b></h3>
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
                                    <td style="border: 1px solid black;">
                                        {{ displaydateformat($document_no->issue_date) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black; width:70px;">Rev.& Dt.</td>
                                    <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                                </tr>
                            </thead>
                        </table>
                    </th>
                </tr>

                <tr>
                    <th colspan="4"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Date:- {{ Displaydateformat($detail->date) ?? 'N/A' }}
                    </th>

                    <th colspan="4"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Shift :- {{ getShiftname($detail->shift_id) ?? 'N/A' }}
                    </th>
                    <th colspan="4" rowspan="3"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Safety Walk Taken By (Name):- {{ getUsername($detail->safety_walk_taken_by) ?? 'N/A' }}
                    </th>
                </tr>

                <tr>
                    <th colspan="4"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Month:- {{ $detail->month ?? 'N/A' }}
                    </th>
                    <th colspan="4"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Unit:- {{ getUnitname($detail->unit) ?? 'N/A' }}
                    </th>
                </tr>


            </table>
            <table style="width: 100%; border-collapse: collapse; text-align: center;">
                <thead>
                    <tr>

                        <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                            LOCATION
                        </th>
                        <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">Exact
                            Location
                        </th>
                        <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">DATE
                            OF
                            OBSERVATION
                        </th>
                        <th rowspan="2" style="border: 2px solid black; padding: 8px; background-color: #ddd;">
                            OBSERVATION
                        </th>
                    </tr>
                    <tr>
                        <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                            PICTURE
                        </th>
                        <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                            RECOMENDED ACTION</th>
                        <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                            RESPONSIBILITY</th>

                        <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                            STATUS</th>
                        <th style="border: 2px solid black; padding: 8px; background-color: #eee; font-weight: bold;">
                            REMARKS
                        </th>
                    </tr>
                </thead>





                <tbody>

                    <tr>

                        <td style="border: 2px solid black; padding: 8px;">
                            {{ getLocationName($detail->location_id) }}
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->excat_location }}
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">
                            {{ Displaydateformat($detail->observation_date) }}</td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->observation }}</td>
                        <td style="border: 2px solid black; padding: 8px;"><img
                                src="{{ GetSafetyWalkImage($detail->id) }}" alt=""
                                style="width:80px; height:80px">
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->recomended_action }}</td>
                        <td style="border: 2px solid black; padding: 8px;">
                            {{ getUsername($detail->responsible_persion) }}
                        </td>


                        <td style="border: 2px solid black; padding: 8px;">
                            @if ($detail->observing_status == 1)
                                Active
                            @elseif($detail->observing_status == 0)
                                Deactive
                            @else
                                Unknown
                            @endif
                        </td>
                        <td style="border: 2px solid black; padding: 8px;">{{ $detail->remarks }}</td>
                    </tr>


                    <tr>
                        <td colspan="2"
                            style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">

                            <div>Checked & Prepared By: {{ getUsername($detail->safety_walk_taken_by) }}</div>
                        </td>
                        <td colspan="4"
                            style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                            @if ($detail->observer_person != null)

                                <div>Responsible Person: {{ getUsername($detail->observer_person) }}</div>
                            @else
                                <p>Inspection has not been Verified Yet</p>
                            @endif
                        </td>
                        <td colspan="4"
                            style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                            @if ($detail->approver_id != null)

                                <div>Approved By: {{ getUsername($detail->approver_id) }}</div>
                            @else
                                <p>Inspection has not been Approved Yet</p>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="page-break"></div>
        @endforeach
    @endforeach
    <br>
</body>

</html>
