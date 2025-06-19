<!DOCTYPE html>
<html>

<head>
    <title>OHC Hygiene Inspection Checklist | KARAM</title>

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
                    OHC Hygiene Inspection Checklist </td>
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
                    OHC Hygiene Inspection Checklist
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Shift</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getShiftname($inspection_details->shift_id) ? getShiftname($inspection_details->shift_id) : '' }}
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
                    OHC Hygiene Inspection Checklist Checklist
                </td>
            </tr>
        </table>
    </div>


    <table style="width: 100%; border-collapse: collapse; text-align: center;">
        <thead>
            <tr>
                <th rowspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Date
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Shift
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Description/Equipment
                </th>
                <th colspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Cleaning and Sanitization
                </th>

                <th rowspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Name Of Cleaner
                </th>
                <th rowspan="2"
                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                    Remarks
                </th>

            </tr>
            <tr>
                <td style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                    YES
                </td>
                <td style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                    NO
                </td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td style="border: 1px solid black; text-align: center; padding: 12px;">
                    {{ Displaydateformat($inspection_details->issue_date) }}
                </td>
                <td style="border: 1px solid black; padding: 12px;">
                    {{ getShiftname($inspection_details->shift_id) }}
                </td>
                <td style="border: 1px solid black; padding: 12px;">
                    {{ $inspection_details->inspection_question }}
                </td>
                @if ($inspection_details->inspection_value == 1)
                    <td style="border: 1px solid black; text-align: center; padding: 12px;">
                        <span style="color: green;">✓</span>
                    </td>
                    <td style="border: 1px solid black; text-align: center; padding: 12px;">
                    </td>
                @else
                    <td style="border: 1px solid black; text-align: center; padding: 12px;">
                    </td>
                    <td style="border: 1px solid black; text-align: center; padding: 12px;">
                        <span style="color: red;">X</span>
                    </td>
                @endif
                <td colspan="1"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">

                    <p>{{ getUsername($inspection_details->created_by) }}</p>

                </td>


                <td style="border: 1px solid black; text-align: center; padding: 12px;">
                    {{ $inspection_details->cleaner_remarks }}
                </td>

            </tr>

        </tbody>
    </table>

    @if (isset($inspection_details->updated_by))
        <div style="width:100%; margin-top:10px;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('ohc_management.nursing_officer_approval') }}
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">

            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUserName($inspection_details->updated_by) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat($inspection_details->updated_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $inspection_details->nursing_officer_remarks }}
                </td>
            </tr>

        </table>
    @endif




    <div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                        Status Logs
                    </td>
                </tr>
            </table>
        </div>

        <div class="card-body ">
            <div class="row">
                <div class="card-header-inner">
                    <h4 class="text-white">Status logs</h4>
                </div>
            </div>

            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Approved By / Rejected By</th>
                                <th>Created By</th>

                                <th>Remarks</th>
                                <th>Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            @if ($status_log->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="5">No data is available</td>
                                </tr>
                            @else
                                @foreach ($status_log as $status)   
                                    <tr>
                                        <td style="text-align: center;">
                                            {{ getOhcHygieneCleaningStatus($status['from_status'] ?? null) }}</td>
                                        <td style="text-align: center;">
                                            {{ getOhcHygieneCleaningStatus($status['to_status'] ?? null) }}</td>
                                        <td style="text-align: center;">
                                            {{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ isset($status['created_by']) ? getUsername($status['created_by']) : '-' }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ trim($status['remarks'] ?? '') !== '' ? $status['remarks'] : '-' }}
                                        </td>

                                        <td style="text-align: center;">
                                            {{ null !== Displaydateformat($status['created_at']) ? Displaydateformat($status['created_at']) : '-' }}
                                        </td>
                                    </tr>
                                @endforeach

                            @endif
                        </tbody>
                    </table>

                </div>
            </div>


        </div>
        <br>
        <div class="page-break"></div>
    </div>



    <br>

</body>

</html>
