
<!DOCTYPE html>
<html>

<head>
    <title>Hose Reel Hose Inspection| KARAM</title>

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


    @foreach ($content as $inspection_id => $group)
        @php
            $first = $group->first();
            $infoCells = [
                ['Doc.No', $first->doc_no],
                ['Issue Dt.', Displaydateformat($first->issue_date)],
                ['Rev.& Dt.', $first->rev_dt],
            ];
        @endphp

        <div style="width:100%; margin-bottom: 20px;">
            <table style="width:100%;">
                <tr>
                    <td style="background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        Hose Reel Hose InspectionDetails
                    </td>
                </tr>
            </table>

            <br>

            <!-- Header Table -->
            <table style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 13px;">
                <tr>
                    <th colspan="3" rowspan="3"
                        style="border: 1px solid black; text-align: center; vertical-align: middle;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                    </th>

                    <th colspan="6" rowspan="3"
                        style="border: 1px solid black; text-align: center; vertical-align: middle;">
                        <h3 style="margin: 0;"><b>{{ __('title.hose_reel') }}</b></h3>
                    </th>

                    @foreach ($infoCells as $index => [$label, $value])
                        @if ($index == 0)
                            <th colspan="4" style="border: 1px solid black; font-size: 12px; padding: 4px;">
                                <strong>{{ $label }}</strong>: {{ $value }}
                            </th>
                        @else
                </tr>
                <tr>
                    <th colspan="4" style="border: 1px solid black; font-size: 12px; padding: 4px;">
                        <strong>{{ $label }}</strong>: {{ $value }}
                    </th>
    @endif
    @endforeach
    </tr>

    <tr style="background-color: #ddd;">
        <th colspan="4" style="border: 1px solid black; text-align: left; padding: 6px;">
            DATE OF INSPECTION: {{ Displaydateformat($first->date_of_inspection) }}
        </th>
        <th colspan="4" style="border: 1px solid black; text-align: left; padding: 6px;">
            LOCATION: {{ getLocationName($first->location) }}
        </th>
        <th colspan="5" style="border: 1px solid black; text-align: left; padding: 6px;">
            SHIFT: {{ $first->shift }}
        </th>
    </tr>

    <tr style="background-color: #ddd;">
        <th colspan="4" style="border: 1px solid black; text-align: left; padding: 6px;">
            NEXT DUE ON: {{ Displaydateformat($first->next_due) }}
        </th>
        <th colspan="4" style="border: 1px solid black; text-align: left; padding: 6px;">
            UNIT: {{ getUnitName($first->unit) }}
        </th>
        <th colspan="5" style="border: 1px solid black; text-align: left; padding: 6px;">
            FREQUENCY: {{ getFrequencyName($first->frequency) }}
        </th>
    </tr>

    <tr>
        <th rowspan="2" title="sr_no" style="border: 1px solid black; padding: 5px;">SR. NO.</th>
        <th rowspan="2" title="department" style="border: 1px solid black; padding: 5px;">DEPARTMENT</th>
        <th rowspan="2" title="location" style="border: 1px solid black; padding: 5px;">LOCATION</th>
        {{-- <th rowspan="2" title="length" style="border: 1px solid black; padding: 5px;">LENGTH</th> --}}
        <th colspan="5" style="border: 1px solid black; padding: 5px;">CHECK ITEMS</th>
        <th rowspan="2" title="approach" style="border: 1px solid black; padding: 5px;">APPROACH</th>
        <th rowspan="2" title="remarks" style="border: 1px solid black; padding: 5px;">REMARKS</th>
    </tr>
    <tr>
        <th title="length" style="border: 1px solid black; padding: 5px;">LENGTH</th>
        <th title="nozzle" style="border: 1px solid black; padding: 5px;">NOZZLE CONDITION</th>
        <th title="hose" style="border: 1px solid black; padding: 5px;">HOSE CONDITION</th>
        <th title="flow" style="border: 1px solid black; padding: 5px;">FLOW TEST</th>
        <th title="test_status" style="border: 1px solid black; padding: 5px;">STATUS</th>
    </tr>

    @foreach ($group as $detail)
        <tr>
            <td style="border: 1px solid black; padding: 5px;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ getDeptName($detail->department) }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ getLocationName($detail->location) }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail->length }}</td>
            <td style="border: 1px solid black; padding: 5px;">
                @if ($detail->nozzle == GOOD)
                    <p>Present</p>
                @elseif ($detail->nozzle == FAIR)
                    <p>Fair</p>
                @else
                    <p>Poor</p>
                @endif
            </td>
            <td style="border: 1px solid black; padding: 5px;">
                @if ($detail->hose == GOOD)
                    <p>Present</p>
                @elseif ($detail->hose == FAIR)
                    <p>Fair</p>
                @else
                    <p>Poor</p>
                @endif
            </td>
            <td style="border: 1px solid black; padding: 5px;">
                @if ($detail->flow == PASS)
                    <p>Pass</p>
                @else
                    <p>Fail</p>
                @endif
            </td>
            <td style="border: 1px solid black; padding: 5px;">
                @if ($detail->status_of_hose == OPERATIONAL)
                    <p>Operational</p>
                @else
                    <p>Non Operational</p>
                @endif
            </td>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail->approach }}</td>
            <td style="border: 1px solid black; padding: 5px;">{{ $detail->remarks }}</td>
        </tr>
    @endforeach

    @php
        $approved_by = GetFireSignature($first->approved_by, $first->fire_id, $inspection_type);
        $verified_by = GetFireSignature($first->verified_by, $first->fire_id, $inspection_type);
        $checked_by = GetFireSignature($first->checked_by, $first->fire_id, $inspection_type);
    @endphp

    <tr>
        <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
            <div class="view_data">
                @if (!empty($first->checked_by))
                    {{-- <img src="{{ admin_url($checked_by) }}" alt=""
                        style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                    <p style="margin: 0;">Checked By:- {{ getUsername($first->created_by) }}</p>
                @else
                    <p style="margin: 0;">Checked By:- Not yet checked</p>
                @endif
            </div>
        </td>
        <td colspan="3" style="border: 1px solid black; padding: 6px; text-align: center;">
            <div class="view_data">
                @if (!empty($first->verified_by))
                    {{-- <img src="{{ admin_url($verified_by) }}" alt=""
                        style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                    <p style="margin: 0;">Verified By:- {{ getUsername($first->verified_by) }}</p>
                @else
                    <p style="margin: 0;">Verified By:- Not yet verified</p>
                @endif
            </div>
        </td>
        <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
            <div class="view_data">
                @if (!empty($first->approved_by))
                    {{-- <img src="{{ admin_url($approved_by) }}" alt=""
                        style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                    <p style="margin: 0;">Approved By:- {{ getUsername($first->approved_by) }}</p>
                @else
                    <p style="margin: 0;">Approved By:- Not yet approved</p>
                @endif
            </div>
        </td>
    </tr>
    </table>
    </div>
    <div class="page-break"></div>
    @endforeach










    <br>

</body>

</html>
