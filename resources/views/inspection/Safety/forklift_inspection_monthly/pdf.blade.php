<!DOCTYPE html>
<html>

<head>
    <title>Monthly ForkLift Inspection | KARAM</title>

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
                    Monthly ForkLift Inspection </td>
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
                    Monthly ForkLift Inspection
                </td>
            </tr>
        </table>
    </div>

    @foreach ($content as $forklift_details)
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Document Number</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($forklift_details->doc_no) ? $forklift_details->doc_no : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat(isset($forklift_details->issue_date) ? $forklift_details->issue_date : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($forklift_details->rev_dt) ? $forklift_details->rev_dt : '' }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($forklift_details->created_by) ? $forklift_details->created_by : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ displayDateformat($forklift_details->created_at) }}</td>
            </tr>
        </table>

        <br>
        @php
            $user_response = json_decode($forklift_details->responses, true);
        @endphp


        <table style="width:100%;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        DATE OF INSPECTION: {{ Displaydateformat($forklift_details->date_of_inspection) ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        LOCATION: {{ $forklift_details->location_name ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        SHIFT: {{ $forklift_details->shift ?? 'N/A' }}
                    </th>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        DATE OF INSPECTION: {{ Displaydateformat($forklift_details->date_of_inspection) ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        LOCATION: {{ $forklift_details->location_name ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        SHIFT: {{ $forklift_details->shift ?? 'N/A' }}
                    </th>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        NEXT DUE ON: {{ Displaydateformat($forklift_details->next_due) ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        UNIT: {{ $forklift_details->unit_name ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        FREQUENCY: {{ $forklift_details->frequency_name ?? 'N/A' }}
                    </th>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        IDENTIFICATION NO: {{ Displaydateformat($forklift_details->identification_no) ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        TYPE: {{ $forklift_details->forklift ?? 'N/A' }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                        colspan="3">
                        CAPACITY: {{ $forklift_details->capacity ?? 'N/A' }}
                    </th>
                </tr>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;" colspan="2">
                        Sr. No</th>
                    <th colspan="4"
                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Check Points</th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;" colspan="3">
                        Reports</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($user_response as $subcategory => $questions)
                    @php
                        $rowCount = count($questions);
                        $firstRow = true;
                        $srNo = 1;
                    @endphp
                    @foreach ($questions as $questionId => $answer)
                        <tr>
                            @if ($firstRow)
                                <td rowspan="{{ $rowCount }}"
                                    style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="2">
                                    {{ $srNo }}</td>
                                <td rowspan="{{ $rowCount }}"
                                    style="border: 1px solid black; padding: 8px; font-weight: bold;">
                                    {{ GetSubChecklistTypeName($subcategory) }}
                                </td>
                                @php
                                    $srNo++;
                                    $firstRow = false;
                                @endphp
                            @endif
                            <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                {{ GetChecklistTypeDate($questionId) }}
                            </td>
                            <td style="border: 1px solid black; padding: 8px; text-align: center;" colspan="3">
                                @if ($answer == 'YES')
                                    <span style="color: green; font-size: 20px;">✓</span>
                                @elseif ($answer == 'NO' || $answer == 'N/A')
                                    <span style="color: red; font-size: 20px;">X</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        <br>
    @endforeach

</body>

</html>
