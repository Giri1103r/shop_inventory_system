<!DOCTYPE html>
<html>

<head>
    <title>Monthly OHC First-Aid Medicine Inspection Checklist| KARAM</title>

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
                    Monthly OHC First-Aid Medicine Inspection Checklist</td>
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
                    Monthly OHC First-Aid Medicine Inspection Checklist
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Inspection Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_detail->inspection_date) ? $inspection_detail->inspection_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Next Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_detail->next_due) ? $inspection_detail->next_due : '') }}
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($inspection_detail->created_by) ? $inspection_detail->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($inspection_detail->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Monthly OHC First-Aid Medicine Inspection Checklist
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; padding: 5px;">
        <thead>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    {{ __('inspection.sr_no') }}</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; ">
                    Name Of Inspection</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    Available Quantity</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    Expiry Date</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    Inspected By</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    {{ __('inspection.remarks') }}</th>
            </tr>

        </thead>
        <tbody>
            @foreach ($inspection_data as $medicines)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getMedicinename($medicines['medicine_id']) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['available_quantity'] }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ Displaydateformat($medicines['expired_date']) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getUsername($medicines['emp_id']) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $medicines['remarks'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    <img src="{{ admin_url($inspection_created_by) }}" alt="Checked By Signature"
                        style="height: 50px; margin-top:2px;">
                    <div>Checked & Prepared By: {{ getUsername($inspection_detail->created_by) }}</div>
                </td>
                <td colspan="3"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($inspection_detail->updated_by != null)
                        <img src="{{ admin_url($inspection_updated_by) }}" alt="Verified By Signature"
                            style="height: 50px;">
                        <div>Verified By: {{ getUsername($inspection_detail->updated_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
            </tr>

        </tbody>
    </table>
    <br>

    @if ($inspection_detail->approval_remarks)
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Approvals
                </td>
            </tr>
        </table>
        <table>
            <tr>
                <td width="50%" style="padding:5px;"><b>Approved By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($inspection_detail->updated_by) ? $inspection_detail->updated_by : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Approved Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ displayDateformat($inspection_detail->updated_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ $inspection_detail->approval_remarks }}
                </td>
            </tr>
        </table>
    @endif

</body>

</html>
