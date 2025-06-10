<!DOCTYPE html>
<html>

<head>
    <title>Physical Health Examination Check-up | KARAM</title>

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
                    Physical Health Examination Check-up </td>
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
                    Physical Health Examination Check-up
                </td>
            </tr>
        </table>
    </div>

    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
        <!-- First Row -->
        <tr>
            <th colspan="6" style="border:1px solid black;height:50px;width:40px;">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:50px;height:50px;">
            </th>
            <th colspan="6" style="border:1px solid black;">
                <h3>
                    <span><b>Physical Health Examination Check-up</b></span>
                </h3>
            </th>
            <th colspan="6" style="border:1px solid black; padding: 0;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="border: 1px solid black; width: 70px;">Doc.No</td>
                        <td style="border: 1px solid black;">{{ $document_no->doc_no }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">Issue Dt.</td>
                        <td style="border: 1px solid black;">{{ Displaydateformat($document_no->issue_date) }}</td>
                    </tr>
                    <tr>
                        <td style="border: 1px solid black;">Rev.& Dt.</td>
                        <td style="border: 1px solid black;">{{ $document_no->rev_dt }}</td>
                    </tr>
                </table>
            </th>
        </tr>

        <!-- Second Row: Full width for Form Name or Number -->
        <tr>
            <th colspan="18" style="border:1px solid black; text-align: left; padding: 10px;">
                <b>Form Number : </b> {{ $physicalHealth->form_number }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>EMPLOYEE CODE:</b> {{ $physicalHealth->emp_id ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>EMPLOYEE NAME:</b> {{ $physicalHealth->emp_name ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>CONTACT NUMBER:</b> {{ $physicalHealth->mobile_no ?? 'N/A' }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>GENDER:</b> {{ $physicalHealth->gender ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>DATE OF BIRTH:</b> {{ Displaydateformat($physicalHealth->dob) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>AGE:</b> {{ $physicalHealth->age ?? 'N/A' }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>BLOOD GROUP:</b> {{ $physicalHealth->blood_group ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>DATE:</b> {{ Displaydateformat($physicalHealth->date) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>UNIT:</b> {{ getUnitname($physicalHealth->unit_id ?? 'N/A') }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>DEPARTMENT:</b> {{ getDepartment($physicalHealth->department_id) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>HEIGHT:</b> {{ $physicalHealth->height ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>WEIGHT:</b> {{ $physicalHealth->weight ?? 'N/A' }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="6">
                <b>BMI:</b> {{ $physicalHealth->bmi ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="12">
                <b>ADDRESS:</b> {{ $physicalHealth->address ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;"
                colspan="18">
                <b>Clinical details</b>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                {{ __('common.sno') }}</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Details Of Personal Habits</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Status</th>
        </tr>
        @php
            $clinicalDetails = json_decode($physicalHealth->personal_details, true);
            $statusArray = $clinicalDetails['status'] ?? [];
        @endphp
        @foreach ($statusArray as $key => $value)
            <tr>
                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                    colspan="6">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"colspan="6">
                    {{ getClinicalDetails($key) }}</td>
                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"colspan="6">
                    @if ($value == '1')
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">✗</span>
                    @endif
                </td>
            </tr>
        @endforeach

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="18">
                <b>Present Complaints : </b> {{ $physicalHealth->present_complaint ?? 'N/A' }}
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="18">
                <b>Past History : </b> {{ $physicalHealth->past_history ?? 'N/A' }}
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;"
                colspan="18">
                <b>Family History</b>
        </tr>

        @php
            $FamilyHistory = json_decode($physicalHealth->family_history, true);
            $statusArray = $FamilyHistory['status'] ?? [];
            $remarksArray = $FamilyHistory['remarks'] ?? [];
        @endphp

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">
                {{ __('common.sno') }}</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Details Of Personal Habits</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                Status</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Remarks</th>
        </tr>
        @foreach ($familyHistory as $item)
            <tr>
                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                    colspan="2">{{ $loop->iteration }}</td>

                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                    colspan="6">
                    {{ $item->family_history }}

                </td>

                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                    colspan="4">
                    @php
                        $status = $statusArray[$item->id] ?? null;
                    @endphp

                    @if ($status === '1')
                        <span style="color: green; font-size: 20px;">✓</span>
                    @elseif ($status === '0')
                        <span style="color: red; font-size: 20px;">✗</span>
                    @else
                        <span style="color: gray; font-size: 16px;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                    colspan="6">
                    {{ $remarksArray[$item->id] ?? 'No remarks' }}
                </td>
            </tr>
        @endforeach
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;"
                colspan="18">
                <b>Vital Check Points</b>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                {{ __('common.sno') }}</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Check Points</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Reading Value</th>
        </tr>

        @php
            $vital_checkpoints = json_decode($physicalHealth->vital_checkpoints, true);
            $reading_value = $vital_checkpoints['reading_value'] ?? [];
            $serial = 1;
        @endphp

        @foreach ($check_points as $label => $items)
            @foreach ($items as $point)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                        colspan="6">{{ $loop->iteration }}</td>

                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                        colspan="6">
                        {{ $point->name }}
                        <input type="hidden" name="id[{{ $point->id }}]" value="{{ encryptId($point->id) }}">
                    </td>

                    <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;"
                        colspan="6">
                        {{ $reading_value[$point->id] ?? 'No Value' }}
                    </td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: center;"
                colspan="18">
                <b>EYE Check Up</b>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                Vision</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Without Glasses(Right)</th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">With
                Glasses(Left)</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">Color
                Blindness</th>
        </tr>
        <tr>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="4">
                Distance</td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="6">
                {{ isset($physicalHealth->distance_without_glass) ? $physicalHealth->distance_without_glass : '' }}
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="6">
                {{ isset($physicalHealth->distance_with_glass) ? $physicalHealth->distance_with_glass : '' }}</td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="2">

                @if ($physicalHealth->distance_without_glass_yes === '1')
                    <span style="color: green; font-size: 20px;">✓</span>
                @elseif ($physicalHealth->distance_without_glass_yes === '0')
                    <span style="color: red; font-size: 20px;">✗</span>
                @else
                    <span style="color: gray; font-size: 16px;">N/A</span>
                @endif
            </td>
        </tr>
        <tr>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="4">
                Near</td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="6">
                {{ isset($physicalHealth->near_without_glass_yes) ? $physicalHealth->near_without_glass_yes : '' }}
            </td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="6">
                {{ isset($physicalHealth->near_with_glass) ? $physicalHealth->near_with_glass : '' }}</td>
            <td style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;" colspan="2">

                @if ($physicalHealth->near_without_glass_yes === '1')
                    <span style="color: green; font-size: 20px;">✓</span>
                @elseif ($physicalHealth->near_without_glass_yes === '0')
                    <span style="color: red; font-size: 20px;">✗</span>
                @else
                    <span style="color: gray; font-size: 16px;">N/A</span>
                @endif
            </td>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="18">
                <b>Remarks By Medical Officer : </b> {{ isset($physicalHealth->remarks) ? $physicalHealth->remarks : '' }}
        </tr>
    </table>




    <br>

</body>

</html>
