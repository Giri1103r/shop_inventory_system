<!DOCTYPE html>
<html>

<head>
    <title>6S AUDIT ASSESSMENT| KARAM</title>

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
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        6S AUDIT ASSESSMENT
                    </td>
                </tr>
            </table>
        </div>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

            <tr>
                <th colspan="10">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="12" style="border:1px solid black;">
                    <h3>
                        <span><b>6S AUDIT ASSESSMENT</b></span>
                        <br>

                    </h3>
                </th>
                <th colspan="2">
                    {{ $document_no->doc_no ?? 'N/A' }}
                </th>

            </tr>
            <tr>
                <th colspan="24"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    Name Of The Shop Floor: {{ $details->floor_name ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th colspan="24"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    Date Of Audit: {{ Displaydateformat($details->audit_date) ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th colspan="24"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    Shift: {{ getShift($details->shift_id) ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th colspan="24"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    Floor Executive On Duty: {{ $details->floor_executive ?? 'N/A' }}
                </th>
            </tr>


            <tr>

                <th colspan="12"
                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    Check Points
                </th>

                <th colspan="12"
                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                    class="require">
                    YES/NO/NA
                </th>
            </tr>
            @php $srNo = 1; @endphp
            @php
                $user_response = json_decode($details->checklist, true);
            @endphp
            @if (!empty($user_response))
                @foreach ($user_response as $subcategory => $questions)
                    @php
                        $rowCount = is_array($questions) ? count($questions) : 1;
                        $firstRow = true;
                    @endphp

                    @if (is_array($questions))
                        @foreach ($questions as $questionId => $answer)
                            <tr>
                                @if ($firstRow)
                                    <td rowspan="{{ $rowCount }}"
                                        style="border: 1px solid black; padding: 8px; font-weight: bold;">
                                        {{ GetSubChecklistTypeName($subcategory) }}
                                    </td>
                                    @php
                                        $srNo++;
                                        $firstRow = false;
                                    @endphp
                                @endif

                                <td colspan="11" style="border: 1px solid black; padding: 8px;">
                                    {{ GetChecklistTypeDate($questionId) }}
                                </td>

                                <td colspan="12" style="border: 1px solid black; padding: 8px; text-align: center;">
                                    @if ($answer == 'YES')
                                        <span style="color: green; font-size: 20px;">✓</span>
                                    @elseif ($answer == 'NO')
                                        <span style="color: red; font-size: 20px;">X</span>
                                    @elseif ($answer == 'N/A')
                                        <span style="color: rgb(191, 212, 4); font-size: 20px;">X</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="border: 1px solid black; padding: 8px; font-weight: bold;">
                                {{ GetSubChecklistTypeName($subcategory) }}
                            </td>
                            <td colspan="11" style="border: 1px solid black; padding: 8px;">
                                {{ GetChecklistTypeDate($subcategory) }}
                            </td>
                            <td colspan="12" style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if ($questions == 'YES')
                                    <span style="color: green; font-size: 20px;">✓</span>
                                @elseif ($questions == 'NO')
                                    <span style="color: red; font-size: 20px;">X</span>
                                @elseif ($questions == 'N/A')
                                    <span style="color: rgb(191, 212, 4); font-size: 20px;">X</span>
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            @else
                <tr>

                    <th colspan="24"
                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        No Data is Available
                    </th>


                </tr>
            @endif




        </table>

        <div class="page-break"></div>
    @endforeach


    <br>

</body>

</html>
