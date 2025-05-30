<!DOCTYPE html>
<html>

<head>
    <title>Gemba Walk Inspection (Safety Observation) | KARAM</title>

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

        <table style="width: 100%; border-collapse: collapse;" border="1">
            <tr>
                {{-- Logo --}}
                <th colspan="2" style="text-align: center; vertical-align: middle; padding: 5px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" alt="Logo" style="height: 50px;">
                </th>

                {{-- Title --}}
                <th colspan="9" style="text-align: center; font-size: 18px;">
                    <strong>DAILY GEMBA WALK INSPECTION</strong>
                </th>

                {{-- Doc Details --}}
                <th colspan="2" style="padding: 0;">
                    <table style="width: 100%; border-collapse: collapse;" border="1">
                        <tr>
                            <td style="font-weight: bold;">Doc.No</td>
                            <td>{{ $firstItem->doc_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Issue Dt.</td>
                            <td>{{ $firstItem->issue_date ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Rev.& Dt.</td>
                            <td>{{ $firstItem->rev_dt ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </th>
            </tr>

            {{-- Date & Shift --}}
            <tr>
                <th colspan="6" style="text-align: center; vertical-align: middle; font-size: 12px; padding: 5px;">
                    <strong>Date:</strong> {{ displaydateformat($firstItem->date ?? 'N/A') }}
                </th>
                <th colspan="7" style="text-align: center; vertical-align: middle; font-size: 12px; padding: 5px;">
                    <strong>Shift:</strong> {{ getShift($firstItem->shift_id ?? 'N/A') }}
                </th>
            </tr>

        </table>

        <table class="table table-bordered table-hover tblborder" style="width:100%; border-collapse: collapse;"
            border="1">
            <thead>
                <tr>
                    <th>S No</th>
                    <th colspan="1">Location</th>
                    <th>Unit</th>
                    <th>Department</th>
                    <th>Exact Location</th>
                    <th>Date of Observation</th>
                    <th>Type (Unsafe Act / Unsafe Condition)</th>
                    <th>Description</th>
                    <th>{{ __('inspection.risk_category') }}</th>
                    <th>Hazard</th>
                    <th>Image</th>
                    <th>Recommended Corrective & Preventive</th>
                    <th>Status</th>
                    <th>Remark</th>
                    <th>{{ __('inspection.observer_person') }}</th>
                    <th>Name of the Observer</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groupedCollection as $index => $gembaWalk)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ getLocationname($gembaWalk->location_id ?? 'N/A') }}</td>
                        <td>{{ getUnitname($gembaWalk->unit_id ?? 'N/A') }}</td>
                        <td>{{ getDepartment($gembaWalk->department_id ?? 'N/A') }}</td>
                        <td>{{ $gembaWalk->exact_location ?? 'N/A' }}</td>
                        <td>{{ displaydateformat($gembaWalk->date_of_observation ?? 'N/A') }}</td>
                        <td>{{ getObservationType($gembaWalk->observation_type_id ?? 'N/A') }}</td>
                        <td>{{ $gembaWalk->description ?? 'N/A' }}</td>
                        <td>{{ getRiskCategory($gembaWalk->risk_category ?? 'N/A') }}</td>
                        <td>{{ getGembaWalkHazardName($gembaWalk->hazard ?? 'N/A') }}</td>
                        <td>
                            @if (!empty($gembaWalk->file_path))
                                <img src="{{ public_path($gembaWalk->file_path) }}"
                                    style="width: 100px; height: auto;">
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $gembaWalk->capa ?? 'N/A' }}</td>
                        <td>{{ getGembaWalkStatus($gembaWalk->gemba_walk_checklist_status ?? 'N/A') }}</td>
                        <td>{{ $gembaWalk->remark ?? 'N/A' }}</td>
                        <td>{{ getUsername($gembaWalk->responsibility_id ?? 'N/A') }}</td>
                        <td>{{ getUsername($gembaWalk->created_by ?? 'N/A') }}</td>
                    </tr>
                @endforeach
            </tbody>

            {{-- Footer Row for Signatures --}}
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

                <th colspan="7" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">

                    <div style="margin-top: 5px;">Prepared By : {{ getUsername($firstItem->created_by) }} </div>

                </th>
                <th colspan="10" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">

                    @if (!empty($firstItem->verified_by))
                        <p style="margin: 0;">Verified By:- {{ getUsername($firstItem->verified_by) }}</p>
                    @else
                        <p style="margin: 0;">Verified By:- Not yet Verified</p>
                    @endif
                </th>
            </tr>
        </table>


        <div class="page-break"></div>
    @endforeach


    <br>

</body>

</html>
