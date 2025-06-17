<!DOCTYPE html>
<html>

<head>
    <title>Checklist Of Floor Stretcher Inspection | KARAM</title>

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


    @foreach ($content as $inspection_details)
        <div style="width:100%; margin-bottom: 20px;">
            <table style="width:100%;">
                <tr>
                    <td style="background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                     Checklist Of Floor Stretcher Inspection
                    </td>
                </tr>
            </table>

            <br>
            @php
                $details = json_decode($inspection_details->responses, true);
                $srNo = 1;
                $checkpoint_keys = ['fs_first', 'fs_second', 'fs_third', 'fs_fourth', 'fs_fifth', 'fs_sixth'];
            @endphp

            <table style="width: 100%; border-collapse: collapse; padding: 5px;">
                  <tr>
                <th colspan="4" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="8" style="border:1px solid black;">
                    <h3>
                        <span><b>  Monthly Floor Patient Stretcher Checklist</b></span>
                        <br>
                    </h3>
                </th>



                </th>
            </tr>
                <thead>
                    <tr>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: left;"
                            colspan="6">Date:- {{ Displaydateformat($inspection_details->issue_date) }}</th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: left;"
                            colspan="6">Shift :- {{ getShiftName($inspection_details->shift) }}</th>
                    </tr>
                    <tr>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: left;"
                            colspan="6">Frequency :- {{ getFrequencyName($inspection_details->frequency) }} </th>
                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: left;"
                            colspan="6">Unit :- {{ getUnitName($inspection_details->unit) }} </th>
                    </tr>
                    <tr>
                        <th rowspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 8%;">
                            {{ __('inspection.sr_no') }}</th>
                        <th rowspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 12%;">
                            {{ __('inspection.resource_code') }}</th>
                        <th rowspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 12%;">
                            {{ __('inspection.dept/location') }}</th>
                        <th colspan="6"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 48%;">
                            {{ __('inspection.checkpoints') }}</th>
                        <th rowspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 20%;">
                            {{ __('inspection.remarks') }}</th>
                    </tr>
                    <tr>
                        @foreach ($checkpoint_keys as $key)
                            <th
                                style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width: 8%;">
                                {{ __('inspection.' . $key) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($details['resource_code'] as $subTypeId => $resource)
                        @foreach ($resource as $checklistId => $resourceCode)
                            <tr>
                                <td
                                    style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                    {{ $srNo }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                    {{ $resourceCode }}</td>
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                    {{ GetChecklistTypeDate($checklistId) }}</td>
                                @php
                                    $responses = $details['response'][$subTypeId][$checklistId] ?? [];
                                @endphp
                                @foreach ($checkpoint_keys as $key)
                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                        @php $responseText = $responses[$key] ?? '-'; @endphp
                                        @if ($responseText == 'YES')
                                            <span style="color: green; font-size: 20px;">✔</span>
                                        @elseif ($responseText == 'NO')
                                            <span style="color: red; font-size: 20px;">✖</span>
                                        @else
                                            {{ $responseText }}
                                        @endif
                                    </td>
                                @endforeach
                                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                    {{ $details['remarks'][$subTypeId][$checklistId] ?? '-' }}
                                </td>
                            </tr>
                            @php $srNo++; @endphp
                        @endforeach
                    @endforeach
                    {{-- @php
                        $signature = GetFSSignature(
                            $inspection_details->created_by,
                            $inspection_details->id,
                            OHC_TYPE_FLOOR_STRETCHER,
                        );
                    @endphp --}}
                    <tr>
                        <td colspan="12"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            Auditor Name: {{ getUsername(isset($inspection_details->created_by)) }}
                        </td>
                        {{-- <td colspan="6"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            {{ __('inspection.signature') }}:
                             <img src="{{ admin_url($signature) }}" alt="Signature"
                                style="width:70px; vertical-align: middle;">
                        </td> --}}
                    </tr>

                </tbody>
            </table>


        </div>
        <div class="page-break"></div>
    @endforeach






    <br>

</body>

</html>
