<!DOCTYPE html>
<html>

<head>
    <title>PA SYSTEM INSPECTION CHECKLIST | KARAM</title>

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

    @foreach ($content as $detail => $group)
        @php
            $first = $group->first();
        @endphp

        <br>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('title.pa_system_inspection') }}
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="2" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </th>
                <th colspan="5" style="border:1px solid black;">
                    <h3>
                        <span><b> {{ __('title.pa_system_inspection') }}</b></span>
                        <br>
                    </h3>
                </th>

                <th colspan="3" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>
                </th>
            </tr>


            <tr style="background-color: #ddd;">
                <th style="border: 1px solid black; padding: 8px; text-align: left;" colspan="4">
                    DATE OF INSPECTION: {{ Displaydateformat($first->date_of_inspection) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; text-align: left;" colspan="3">
                    UNIT: {{ getUnitname($first->unit) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px;text-align: left;" colspan="3">
                    SHIFT: {{ $first->shift ?? 'N/A' }}
                </th>
            </tr>
            <tr style="background-color: #ddd;">
                <th style="border: 1px solid black; padding: 8px; text-align: left;" colspan="4">
                    NEXT DUE: {{ Displaydateformat($first->next_due) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; text-align: left;" colspan="3">
                    LOCATION: {{ getLocationname($first->location) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; text-align: left;" colspan="3">
                    FREQUENCY: {{ getFrequencyname($first->frequency) ?? 'N/A' }}
                </th>

            </tr>

            <tr style="background-color: #ddd;">
                <th style="border: 1px solid black; padding: 8px;" rowspan="2">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px;" rowspan="2">EXACT LOCATION
                </th>
                <th style="border: 1px solid black; padding: 8px;" colspan="7">CHECK ITEMS
                </th>
                <th style="border: 1px solid black; padding: 8px;" rowspan="2">REMARK
                </th>
            </tr>
            <tr style="background-color: #ddd;">
                <th style="border: 1px solid black; padding: 8px;">UNIT
                </th>
                <th style="border: 1px solid black; padding: 8px;">AUDIO QUALITY
                </th>
                <th style="border: 1px solid black; padding: 8px;">MIC CONDITION
                </th>
                <th style="border: 1px solid black; padding: 8px;">MIC QUANTITY
                </th>
                <th style="border: 1px solid black; padding: 8px;">PHYSICAL CONDITION
                </th>
                <th style="border: 1px solid black; padding: 8px;">CABLE CONDITION
                </th>
                <th style="border: 1px solid black; padding: 8px;">OPERATION
                </th>
            </tr>
            @foreach ($group as $details)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $loop->iteration }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ ($details->location) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ getUnitname($details->unit) }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($details->audio_quality == GOOD)
                            GOOD
                        @elseif($details->audio_quality == FAIR)
                            FAIR
                        @elseif($details->audio_quality == POOR)
                            POOR
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($details->mic_condition == GOOD)
                            GOOD
                        @elseif($details->mic_condition == FAIR)
                            FAIR
                        @elseif($details->mic_condition == POOR)
                            POOR
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $details->mic_quantity }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($details->physical_condition == GOOD)
                            GOOD
                        @elseif($details->physical_condition == FAIR)
                            FAIR
                        @elseif($details->physical_condition == POOR)
                            POOR
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($details->cable_condition == GOOD)
                            GOOD
                        @elseif($details->cable_condition == FAIR)
                            FAIR
                        @elseif($details->cable_condition == POOR)
                            POOR
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        @if ($details->operation == FUNCTIONAL)
                            {{ __('inspection.functional') }}
                        @elseif($details->operation == NON_FUNCTIONAL)
                            {{ __('inspection.non_functional') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                        {{ $details->remark }}
                    </td>

                </tr>
            @endforeach

            {{-- <tr>
                @php
                    $createdSignature  = GetFireSignature($details->created_by, $details->id, FIRE_PA_SYSTEM_INSPECTION);
                    $verifiedSignature = GetFireSignature($details->verified_by, $details->id, FIRE_PA_SYSTEM_INSPECTION);
                    $approvedSignature = GetFireSignature($details->approved_by, $details->id, FIRE_PA_SYSTEM_INSPECTION);
                @endphp

                <th style="border: 1px solid black; padding: 8px;" colspan="3">
                    <img src="{{ admin_url( $createdSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -2px;" />
                    <div style="margin-top: 5px;">Checked By</div>
                </th>
                <th style="border: 1px solid black; padding: 8px;" colspan="3">
                    <img src="{{ admin_url($verifiedSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -2px;" />
                    <div style="margin-top: 5px;">Verified By</div>
                </th>
                <th style="border: 1px solid black; padding: 8px;" colspan="4">
                    <img src="{{ admin_url($approvedSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -2px;" />
                    <div style="margin-top: 5px;">Approved By</div>
                </th>
            </tr> --}}

            <tr>

                <th style="border: 1px solid black; padding: 8px;" colspan="4">
                    @if (isset($details->created_by))
                        <div style="margin-top: 5px;">Checked By:{{ getUsername($details->created_by) }}</div>
                    @else
                        <div style="margin-top: 5px;"> Not Yet Checked</div>
                    @endif
                </th>
                <th style="border: 1px solid black; padding: 8px;" colspan="3">

                    @if (isset($details->verified_by))
                        <div style="margin-top: 5px;">Verified By:{{ getUsername($details->verified_by) }}</div>
                    @else
                        <div style="margin-top: 5px;"> Inspection has not been Verified Yet</div>
                    @endif
                </th>
                <th style="border: 1px solid black; padding: 8px;" colspan="4">

                    @if (isset($details->approved_by))
                        <div style="margin-top: 5px;">Approved By:{{ getUsername($details->approved_by) }}</div>
                    @else
                        <div style="margin-top: 5px;"> Inspection has not been Approved Yet</div>
                    @endif
                </th>
            </tr>

        </table>
        <br>

        <div class="page-break"></div>
    @endforeach

</body>

</html>
