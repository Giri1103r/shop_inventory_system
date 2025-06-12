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


    @foreach ($content as $details)
        <br>

        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                        {{ __('ohc_management.weekly_ambulance_inspection_checklist') }}
                    </td>
                </tr>
            </table>
        </div>
        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="4" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black;">
                    <h3>
                        <span><b>WEEKLY AMBULANCE INSPECTION CHECKLIST</b></span>
                        <br>

                    </h3>
                </th>

                <th colspan="6" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->issue_date }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>

                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="4">
                    DATE OF INSPECTION: {{ Displaydateformat($details->date_of_inspection) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="6">
                    UNIT: {{ getUnitname($details->unit) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="6">
                    SHIFT: {{ $details->shift ?? 'N/A' }}
                </th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="8">
                    NEXT DUE: {{ Displaydateformat($details->next_due) ?? 'N/A' }}
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                    colspan="8">
                    LOCATION: {{ getLocationname($details->location) ?? 'N/A' }}
                </th>

            </tr>

            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CHECK ITEMS
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">OK/NOT-OK
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">REMARKS
                </th>

            </tr>

            @php
                $checklist = json_decode($details->checklist, true);
                $index = 1;
            @endphp

            @foreach ($checklist['check_item'] as $groupId => $items)
                @foreach ($items as $itemId)
                    <tr>
                        <td colspan="2"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            {{ $index }}</td>
                        <td colspan="4"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            {{ getSubcategoryDataname($itemId) }}</td>
                        <td colspan="5"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            @php
                                $status = strtolower(trim($checklist['status'][$itemId] ?? ''));
                            @endphp

                            @if ($status === 'ok')
                                <span style="color: green; font-size: 20px;">Ok</span>
                            @elseif ($status === 'not ok')
                                <span style="color: red; font-size: 20px;">Not Ok</span>
                            @else
                                <span style="color: red; font-size: 20px;">N/A</span>
                            @endif
                        </td>
                        <td colspan="5"
                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                            {{ $checklist['remarks'][$itemId] ?? '' }}</td>
                    </tr>
                    @php
                        $index++;
                    @endphp
                @endforeach
            @endforeach


            <tr>
                {{-- @php
                    $createdSignature = GetOHCSignature(
                        $details->inspection_created_by,
                        $details->inspection_id,
                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                    );
                    $verifiedSignature = GetOHCSignature(
                        $details->verified_by,
                        $details->inspection_id,
                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                    );
                    $approvedSignature = GetOHCSignature(
                        $details->approved_by,
                        $details->inspection_id,
                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                    );
                @endphp --}}

                {{-- <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    <img src="{{ admin_url($createdSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Checked By</div>
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    <img src="{{ admin_url($verifiedSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Verified By</div>
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                    <img src="{{ admin_url($approvedSignature) }}" alt="Signature Upload"
                        style="width: 150px; margin-top: -10px;" />
                    <div style="margin-top: 5px;">Approved By</div>
                </th> --}}

                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    @if ($details->inspection_created_by != null)
                        <div style="margin-top: 5px;">Checked By : {{ getUsername($details->inspection_created_by) }}
                        </div>
                    @else
                        <div style="margin-top: 5px;">Checked By : Not yet Checked</div>
                    @endif
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="5">
                    @if ($details->verified_by != null)
                        <div style="margin-top: 5px;">Verified By : {{ getUsername($details->verified_by) }}
                        </div>
                    @else
                        <div style="margin-top: 5px;">Verified By : Has Not yet been Verified</div>
                    @endif
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                    @if ($details->approved_by != null)
                        <div style="margin-top: 5px;">Approved By : {{ getUsername($details->approved_by) }}
                        </div>
                    @else
                        <div style="margin-top: 5px;">Approved By : Has Not yet been Approved</div>
                    @endif
                    <div style="margin-top: 5px;">Approved By</div>
                </th>
            </tr>


        </table>
        <div class="page-break"></div>
    @endforeach



    <br>

</body>

</html>
