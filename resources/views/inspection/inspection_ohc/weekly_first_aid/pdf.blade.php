<!DOCTYPE html>
<html>

<head>
    <title>Weekly First-Aid Box Inspection | KARAM</title>

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
                        style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        WEEKLY FIRST-AID BOX INSPECTION CHECKLIST
                    </td>
                </tr>
            </table>
        </div>



        <table width="100%" style="width:100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <!-- Logo -->
                <td colspan="3" style="border:1px solid black; height:50px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                </td>

                <!-- Title -->
                <td colspan="6" style="border:1px solid black; text-align: center;">
                    <h3 style="margin:0;"><b>{{ __('title.weekly_first_aid') }}</b></h3>
                </td>

                <!-- Doc Info -->
                <td colspan="3" style=" padding:0;">
                    <table style="width:100%; border-collapse: collapse;">
                        <tr>
                            <td style="border:1px solid black;"><b>Doc.No</b></td>
                            <td style="border:1px solid black;">{{ $details->doc_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;"><b>Issue Dt.</b></td>
                            <td style="border:1px solid black;">{{ $details->issue_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black;"><b>Rev.& Dt.</b></td>
                            <td style="border:1px solid black;">{{ $details->rev_dt ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- Row: Date, Box No, Shift -->
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>DATE OF INSPECTION :-</strong>
                    {{ Displaydateformat($details->date_of_inspection) }}
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>FIRST-AID BOX No :-</strong>
                    {{ $details->first_aid_box_no }}
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>SHIFT :-</strong>
                    {{ $details->shift }}
                </td>
            </tr>

            <!-- Row: Location, Unit, First Aider -->
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>LOCATION :-</strong>
                    {{ $details->location_name }}
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>UNIT :-</strong>
                    {{ $details->unit_name }}
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 5px;">
                    <strong>NAME OF FIRST-AIDER :-</strong>
                    {{ getUsername($details->first_aider) }}
                </td>
            </tr>
        </table>

        {{-- <br> --}}
        @php
            $inspection_data = json_decode($details->inspection_data, true);
        @endphp


        <table style="width: 100%; border-collapse: collapse; padding: 5px;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        {{ __('inspection.sr_no') }}
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Name Of Medicine
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Available Quantity
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Frezee Quantity
                    </th>
                    <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Expiry Date
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
                            {{ getMedicinename($medicines['medicine_id']) }}
                        </td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">
                            {{ $medicines['available_quantity'] }}
                        </td>

                        <td style="border: 1px solid black; padding: 8px; text-align: center;">
                            {{ $medicines['freeze_quantity'] }}
                        </td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">
                            {{ Displaydateformat($medicines['expired_date']) }}
                        </td>
                        <td style="border: 1px solid black; padding: 8px; text-align: center;">
                            {{ $medicines['remarks'] }}
                        </td>
                    </tr>
                @endforeach

                {{-- @php
                    $inspection_created_by = GetOHCSignature(
                        $details->created_by,
                        $details->checklist_id,
                        OHC_TYPE_WEEEKLY_FIRST_AID_MEDICINE_STORE,
                    );
                @endphp--}}

                <tr>
                    <td colspan="6" style="border: 1px solid black; text-align: center; font-weight: bold;">
                        {{-- <img src="{{ admin_url($inspection_created_by) }}" alt="Checked By Signature"
                            style="height: 50px; margin-top:2px;"><br> --}}
                        Checked & Prepared By: {{ getUsername($details->created_by) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="page-break"></div>
    @endforeach
    <br>

</body>

</html>
