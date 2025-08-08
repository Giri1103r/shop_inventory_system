<!DOCTYPE html>
<html>

<head>
    <title>Health Instrument Calibration Details| KARAM</title>

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
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Health Instrument Calibration

                </td>
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
                     Health Instrument Calibration

                </td>
            </tr>
        </table>
    </div>

    @if ($health_instrument_calibration_details->isNotEmpty())
            @php
            $health_details = $health_instrument_calibration_details->first();
            @endphp 

        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Health Instrument ID</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ isset($health_details->health_auto_id) ? $health_details->health_auto_id : '' }}</td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Unit</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ getUnitname(isset($health_details->unit_id) ? $health_details->unit_id : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Document Number</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}

                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}

                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername($health_details->created_by ?? '') }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ displayDateformat($health_details->created_at ?? '') }}
                </td>
            </tr>
        </table>
    @else
        <p style="text-align:center; color:red; font-weight:bold;">No Health Instrument Calibration Details Available</p>
    @endif
    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                    Health Instrument Calibration Details
                </td>
            </tr>
        </table>
    </div>

    <div class=" mt-2 table-responsive">
        <div class="col-md-12">
            @if ($health_instrument_calibration_details)

            <table class="table table-bordered tblborder">
                <thead>
                    <tr>
                        <th>Sr.No</th>
                        
                        <th>Instrument Name</th>
                        <th>Resource Code</th>
                        <th>Exact Location</th>
                        <th>Instrument Serial No</th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Instrument Range</th>
                        <th>Calibration Frequency</th>
                        <th>Date of Calibration</th>
                        <th>Next Due Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($health_instrument_calibration_details as $index => $health)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $health->instrument_name ?? 'N/A' }}</td>
                            <td>{{ $health->resource_code ?? 'N/A' }}</td>
                            <td>{{ $health->exact_location ?? 'N/A' }}</td>
                            <td>{{ $health->instrument_serial_no ?? 'N/A' }}</td>
                            <td>{{ $health->make ?? 'N/A' }}</td>
                            <td>{{ $health->model ?? 'N/A' }}</td>
                            <td>{{ $health->instrument_range ?? 'N/A' }}</td>
                            <td>{{ getFrequencyname($health->calibration_frequency ?? 'N/A') }}</td>
                            <td>{{ displaydateformat($health->date_of_calibration ?? 'N/A') }}</td>
                            <td>{{ displaydateformat($health->due_date_of_calibration ?? 'N/A') }}</td>
                            <td>{{ $health->remarks ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @else
                <div class="card-body">
                    <p class="text-dark">{{ __('No status logs available.') }}</p>
                </div>
            @endif
        </div>
    </div>
    <br>
</body>

</html>
