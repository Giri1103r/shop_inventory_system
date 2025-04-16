<!DOCTYPE html>
<html>

<head>
    <title>Fire Equipment Monthly Phsyical Inspection| KARAM</title>

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
                    Fire Equipment Monthly Phsyical Inspection</td>
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
                    Fire Equipment Monthly Phsyical Inspection
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Inspection Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_details->date_of_inspection) ? $inspection_details->date_of_inspection : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Location</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getLocationname(isset($inspection_details->location) ? $inspection_details->location : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUnitname($inspection_details->unit ? $inspection_details->unit : '') }}
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
                    Fire Equipment Monthly Phsyical Inspection
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; padding: 5px; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    {{ __('inspection.sr_no') }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    Equipment Name
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    {{ __('inspection.status') }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    {{ __('inspection.remarks') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspection_data as $medicines)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getMonthlyInspectionEquipmentname($medicines['id']) }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['status'] }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['remarks'] }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    @foreach ($images as $index => $image)
        <div
            style="width: 100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold; padding-top: 10px;">
            {{ getMonthlyInspectionEquipmentname($index) }}
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
            <tr>
                @foreach ($image as $key => $img)
                    <td style="border: 1px solid black; text-align: center; padding: 5px; vertical-align: middle;">
                        <img src="{{ admin_url($img->file_path) }}" alt="Verified By Signature"
                            style="height: 50px; width: auto;">
                    </td>
                @endforeach
            </tr>
            </tr>
        </table>
        <br>
    @endforeach
</body>

</html>
