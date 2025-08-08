<!DOCTYPE html>
<html>

<head>
    <title>FIRST AID RECORD | KARAM</title>

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

    @foreach ($content as $detail)
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('title.first_aid_record') }}
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="3" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </th>
                <th colspan="6" style="border:1px solid black;">
                    <h3>
                        <span><b> {{ __('title.first_aid_record') }}</b></span>
                        <br>
                    </h3>
                </th>

                <th colspan="6" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="1">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">MONTH
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2"> DEPARTMENT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">UNIT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">FIRST AID
                    STATION NUMBER
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">FIRST AID
                    BOX NUMBER
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">TOTAL
                    NUMBER OF FIRST AID
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">REMARK
                </th>
            </tr>
            @foreach ($detail as $details)
                <tr>
                    <td colspan="1"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}</td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ isset($details->month) ? $details->month : '' }}</td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ getDepartment($details->department) }}
                    </td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ getUnitname($details->unit) }}
                    </td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ isset($details->first_aid_station_number) ? $details->first_aid_station_number : '' }}
                    </td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ isset($details->first_aid_box_number) ? $details->first_aid_box_number : '' }}
                    </td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ isset($details->total_number_of_first_aid) ? $details->total_number_of_first_aid : '' }}
                    </td>
                    <td colspan="2"
                        style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ isset($details->remark) ? $details->remark : '' }}
                    </td>

                </tr>
            @endforeach

            <tr>
                <th colspan="7" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">Overall
                    Total Number of First Aid</th>

                <th colspan="8" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                    <div>
                        {{ isset($details->overall_total_number_of_first_aid) ? $details->overall_total_number_of_first_aid : '' }}
                    </div>
                </th>
            </tr>

        </table>
        <br>

        <div class="page-break"></div>
    @endforeach

</body>

</html>
