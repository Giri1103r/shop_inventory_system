<!DOCTYPE html>
<html>

<head>
    <title>SAFETY GALLERY INSPECTION | KARAM</title>

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
                        style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold;">
                        SAFETY GALLERY INSPECTION
                    </td>
                </tr>
            </table>
        </div>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
            <tr>
                <th colspan="3" style="border:1px solid black; height:50px;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                </th>
                <th colspan="6" style="border:1px solid black; text-align: center;">
                    <h3><b>{{__('title.safety_gallery')}}</b></h3>
                </th>
                <th colspan="3" style="border:1px solid black;">
                    <table style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $details->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ displaydateformat($details->issue_date) }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black; width:70px;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $details->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>
                </th>
            </tr>

            <tr>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    DATE OF INSPECTION: {{ Displaydateformat($details->date_of_inspection) ?? 'N/A' }}
                </th>

                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    RESOURCE CODE: {{ $details->resource_code ?? 'N/A' }}
                </th>
            </tr>

            <tr>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    LOCATION: {{ $details->location_name ?? 'N/A' }}
                </th>
                <th colspan="6"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    UNIT: {{ $details->unit_name ?? 'N/A' }}
                </th>

            </tr>


            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">Sr. No
                </th>
                <th colspan="5"
                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                    CHECK ITEMS</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">Status
                </th>
                <th colspan="5"
                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">Remarks
                </th>
            </tr>

            @php
                $user_response = json_decode($details->responses, true);
                $srNo = 1;
            @endphp

            @foreach ($user_response as $index => $item)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $srNo++ }}</td>
                    <td colspan="5" style="border: 1px solid black; padding: 8px;">
                        {{ GetChecklistTypeDate($index) }}
                    </td>
                    <td
                        style="border: 1px solid black; padding: 8px; text-align: center; color: {{ strtoupper($item['answer']) == 'YES' ? 'green' : 'red' }};">
                        @if (strtoupper($item['answer']) == 'YES')
                            ✔
                        @else
                            ❌
                        @endif
                    </td>
                    <td colspan="5" style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $item['remarks'] }}
                    </td>
                </tr>
            @endforeach
            {{-- @php
                $creator_signature = GetSafetySignature($details->checked_by, $details->inspection_id, SAFETY_GALLERY_INSPECTION);
                $verifier_signature = GetSafetySignature(
                    $details->verified_by,
                    $details->inspection_id,
                    SAFETY_GALLERY_INSPECTION,
                );
                $approver_signature = GetSafetySignature(
                    $details->approved_by,
                    $details->inspection_id,
                    SAFETY_GALLERY_INSPECTION,
                );
            @endphp --}}

            <!-- Signature Section in a Single Row -->
            <tr>
                <td colspan="4"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    {{-- <img src="{{ admin_url($creator_signature) }}" alt="Checked By Signature" style="height: 50px;"> --}}
                    <div>Checked & Prepared By: {{ getUsername($details->checked_by) }}</div>
                </td>
                <td colspan="4"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($details->verified_by != null)
                        {{-- <img src="{{ admin_url($verifier_signature) }}" alt="Verified By Signature"
                            style="height: 50px;"> --}}
                        <div>Verified By: {{ getUsername($details->verified_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
                <td colspan="4"
                    style="border: 1px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($details->approved_by != null)
                        {{-- <img src="{{ admin_url($approver_signature) }}" alt="Approved By Signature"
                            style="height: 50px;"> --}}
                        <div>Approved By: {{ getUsername($details->approved_by) }}</div>
                    @else
                        <p>Inspection has not been Approved Yet</p>
                    @endif
                </td>
            </tr>

        </table>
        <div class="page-break"></div>
    @endforeach

    <br>

</body>

</html>
