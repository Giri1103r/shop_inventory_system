<!DOCTYPE html>
<html>

<head>
    <title>Weekly Ambulance Inspection Checklist | KARAM</title>

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


    @foreach ($content as $groupedCollection)
        @php $firstItem = $groupedCollection->first(); @endphp
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        Gemba Walk Details
                    </td>
                </tr>
            </table>
        </div>

        <table style="width:100%; border-collapse: collapse;" border="1">
            <tr>
                <th colspan="4" style="height:50px; width:40%;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="6">
                    <h3 style="margin: 0;"><b>{{ __('title.gemba_walk') }}</b></h3>
                </th>
                <th colspan="6">
                    <table style="width:100%; border-collapse: collapse;" border="1">
                        <tr>
                            <td style="width: 70px;"><b>Doc.No</b></td>
                            <td>{{ $firstItem->doc_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><b>Issue Dt.</b></td>
                            <td>{{ $firstItem->issue_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><b>Rev.& Dt.</b></td>
                            <td>{{ $firstItem->rev_dt ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="8">
                    <span style="font-size: 12px;"><b>Date:</b>
                        {{ displaydateformat($firstItem->date ?? 'N/A') }}</span>
                </th>
                <th colspan="8">
                    <span style="font-size: 12px;"><b>Shift:</b> {{ getShift($firstItem->shift_id ?? 'N/A') }}</span>
                </th>
            </tr>

        </table>


        <table class="table table-bordered table-hover tblborder" style="width:100%; border-collapse: collapse;"
            border="1">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Location</th>
                    <th>Unit</th>
                    <th>Date of Observation</th>
                    <th>Observation Type</th>
                    <th>Description</th>
                    <th>Hazard</th>
                    <th>Image</th>
                    <th>CAPA</th>
                    <th>Date of Compliance</th>
                    <th>Responsibility</th>
                    <th>Status</th>
                    <th>Remark</th>
                    <th>Observation</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedCollection as $index => $gembaWalk)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ getLocationname($gembaWalk->location_id ?? 'N/A') }}</td>
                        <td>{{ getUnitname($gembaWalk->unit_id ?? 'N/A') }}</td>
                        <td>{{ displaydateformat($gembaWalk->date_of_observation ?? 'N/A') }}</td>
                        <td>{{ getObservationType($gembaWalk->observation_type_id ?? 'N/A') }}</td>
                        <td>{{ $gembaWalk->description ?? 'N/A' }}</td>
                        <td>{{ $gembaWalk->hazard ?? 'N/A' }}</td>
                        <td>
                            @if (!empty($gembaWalk->file_path))
                                <img src="{{ public_path($gembaWalk->file_path) }}"
                                    style="width: 100px; height: auto;">
                            @else
                                N/A
                            @endif
                        </td>

                        <td>{{ $gembaWalk->capa ?? 'N/A' }}</td>
                        <td>{{ displaydateformat($gembaWalk->date_of_compliance ?? 'N/A') }}</td>
                        <td>{{ getEmployeename($gembaWalk->responsibility_id ?? 'N/A') }}</td>
                        <td>{{ getGembaWalkStatus($gembaWalk->gemba_walk_checklist_status ?? 'N/A') }}</td>
                        <td>{{ $gembaWalk->remark ?? 'N/A' }}</td>
                        <td>{{ $gembaWalk->observation_needed == '1' ? 'YES' : 'NO' }}</td>

                        {{-- <td>
                            @if (!empty($gembaWalk->observation))
                                @php $observations = json_decode($gembaWalk->observation, true); @endphp
                                @if (is_array($observations))
                                    @foreach ($observations as $key => $obs)
                                        {{ $key + 1 }}. {{ $obs }}<br>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            @else
                                N/A
                            @endif
                        </td> --}}
                    </tr>
                @endforeach
            </tbody>
            <tr>
                @php
                    $firstItem = $groupedCollection->first();
                    $createdSignature = GetSignature(
                        $firstItem->inspection_created_by,
                        $firstItem->inspection_id,
                        GEMBA_WALK,
                    );
                    $verifiedSignature = GetSignature($firstItem->verified_by, $firstItem->inspection_id, GEMBA_WALK);
                @endphp

                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="8">
                    <img src="{{ admin_url($createdSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Checked By</div>
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="8">
                    <img src="{{ admin_url($verifiedSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Verified By</div>
                </th>

            </tr>
        </table>
        <div class="page-break"></div>
    @endforeach


    <br>

</body>

</html>
