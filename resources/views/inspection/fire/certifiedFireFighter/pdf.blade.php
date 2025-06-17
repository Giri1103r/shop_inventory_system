<!DOCTYPE html>
<html>

<head>
    <title>Certified Fire Fighter List | KARAM</title>

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

    @foreach ($content as $inspection_id => $group)
        @php
            $first = $group->first();
            $infoCells = [
                ['Doc.No', $first->doc_no],
                ['Issue Dt.', Displaydateformat($first->issue_date)],
                ['Rev.& Dt.', $first->rev_dt],
            ];

        @endphp

        <div style="width: 100%; margin-bottom: 20px;">
            <table style="width: 100%;">
                <tr>
                    <td style="background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold; text-align: center;">
                        Certified Fire Fighter List
                    </td>
                </tr>
            </table>
            <br>
            <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
                <tr>
                    <th style="border: 1px solid black; text-align: center; vertical-align: middle; padding: 10px; width: 15%;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width: 125px; height: 50px;">
                    </th>

                    <th colspan="5" style="border: 1px solid black; text-align: center; vertical-align: middle;">
                        <h3 style="margin: 0;"><b>Certified Fire Fighter List</b></h3>
                    </th>

                    <th style="border: 1px solid black; padding: 0; width: 30%;">
                        <table style="width: 100%; border-collapse: collapse;">
                            @foreach ($infoCells as [$label, $value])
                                <tr>
                                    <td style="border: 1px solid black; font-size: 12px; padding: 4px; width: 40%;"><strong>{{ $label }}</strong></td>
                                    <td style="border: 1px solid black; font-size: 12px; padding: 4px;">{{ $value }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </th>
                </tr>

                <tr>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">SL</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">UNIT</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">DEPARTMENT</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;"> EMPLOYEE NAME</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">EMP CODE</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">CONTACT NUMBER</th>
                    <th style="border: 1px solid black; padding: 8px; text-align: center;">STATUS</th>
                </tr>

                @foreach ($group as $details)
                    <tr>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $loop->iteration }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ getUnitname($details->unit_id) }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ GetDeptName($details->department_id) }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ getEmployeename($details->emp_name) }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->emp_code }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->emp_phone }}</td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">
                            @if ($details->emp_status == 1)
                                <span style="color: green;">Active</span>
                            @else
                                <span style="color: red;">Not-Active</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

        </div>

        <div class="page-break"></div>

    @endforeach

    <br>

</body>

</html>
