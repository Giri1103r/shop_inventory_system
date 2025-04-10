<!DOCTYPE html>
<html>

<head>
    <title>MONTHLY FORKLIFT INSPECTION Checklist | KARAM</title>

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
                        style="width:100%; background-color: #ce0f1f; color: #ffffff; padding: 10px; font-weight: bold; text-align: center;">
                        MONTHLY FORKLIFT INSPECTION CHECKLIST
                    </td>
                </tr>
            </table>
        </div>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; border: 1px solid black; table-layout: fixed;">
            <tr>
                <td colspan="3" style="border:1px solid black; height:50px; text-align:center;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px; height:50px;">
                </td>
                <td colspan="6" style="border:1px solid black; text-align:center;">
                    <div style="font-size: 16px; font-weight: bold;">MONTHLY FORKLIFT INSPECTION<br>CHECKLIST</div>
                </td>
                <td colspan="3" style="border:1px solid black; padding:0;">
                    <table style="width:100%; border-collapse: collapse; table-layout: fixed;">
                        <tr>
                            <td style="border: 1px solid black; width: 40%; font-weight:bold;">Doc.No</td>
                            <td style="border: 1px solid black;">{{ $details->doc_no }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; font-weight:bold;">Issue Dt.</td>
                            <td style="border: 1px solid black;">{{ $details->issue_date }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid black; font-weight:bold;">Rev.& Dt.</td>
                            <td style="border: 1px solid black;">{{ $details->rev_dt }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="4"
                    style="border:1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    <strong>DATE OF INSPECTION:</strong> {{ Displaydateformat($details->date_of_inspection) ?? 'N/A' }}
                </td>
                <td colspan="4"
                    style="border:1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    <strong>LOCATION:</strong> {{ $details->location_name ?? 'N/A' }}
                </td>
                <td colspan="4"
                    style="border:1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    <strong>SHIFT:</strong> {{ $details->shift ?? 'N/A' }}
                </td>
            </tr>

            <tr>
                <th colspan="3" style="border: 1px solid black; background-color: #f2f2f2; padding: 8px;">Sr. No</th>
                <th colspan="3" style="border: 1px solid black; background-color: #f2f2f2; padding: 8px;">Check
                    Points</th>
                <th colspan="3" style="border: 1px solid black; background-color: #f2f2f2; padding: 8px;">Response
                </th>
                <th colspan="3" style="border: 1px solid black; background-color: #f2f2f2; padding: 8px;">Remarks
                </th>
            </tr>

            @php
                $user_response = json_decode($details->responses, true);
                $srNo = 1;
            @endphp

            @if (is_array($user_response))
                @foreach ($user_response as $subcategory => $questions)
                    <tr>
                        <td colspan="3" style="border: 1px solid black; text-align: center;">{{ $srNo }}</td>
                        <td colspan="3" style="border: 1px solid black; text-align: left;">
                            {{ GetChecklistTypeDate($subcategory) }}</td>
                        <td colspan="3" style="border: 1px solid black; text-align: center;">
                            @php $responseText = $questions['response'] ?? '-'; @endphp
                            @if ($responseText == 'YES')
                                <span style="color: green; font-size: 20px;">✓</span>
                            @elseif ($responseText == 'NO' || $responseText == 'N/A')
                                <span style="color: red; font-size: 20px;">X</span>
                            @else
                                {{ $responseText }}
                            @endif
                        </td>
                        <td colspan="3" style="border: 1px solid black; text-align: left;">
                            {{ $questions['remark'] ?? '-' }}</td>
                    </tr>
                    @php $srNo++; @endphp
                @endforeach
            @else
                <tr>
                    <td colspan="12" style="border: 1px solid black; text-align: center; color: red;">
                        No inspection responses available or response data is invalid.
                    </td>
                </tr>
            @endif

            @php
                $creator_signature = GetFireSignature($details->checked_by, $details->inspection_id, MONTHLY_FIRE_PUMP);
                $verifier_signature = GetFireSignature(
                    $details->verified_by,
                    $details->inspection_id,
                    MONTHLY_FIRE_PUMP,
                );
                $approver_signature = GetFireSignature(
                    $details->approved_by,
                    $details->inspection_id,
                    MONTHLY_FIRE_PUMP,
                );
            @endphp

            <tr>
                <td colspan="4" style="border: 1px solid black; text-align: center; padding: 10px;">
                    <img src="{{ admin_url($creator_signature) }}" alt="Checked By Signature"
                        style="height: 50px;"><br>
                    <strong>Checked & Prepared By:</strong><br>{{ getUsername($details->checked_by) }}
                </td>
                <td colspan="4" style="border: 1px solid black; text-align: center; padding: 10px;">
                    @if ($details->verified_by != null)
                        <img src="{{ admin_url($verifier_signature) }}" alt="Verified By Signature"
                            style="height: 50px;"><br>
                        <strong>Verified By:</strong><br>{{ getUsername($details->verified_by) }}
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
                <td colspan="4" style="border: 1px solid black; text-align: center; padding: 10px;">
                    @if ($details->approved_by != null)
                        <img src="{{ admin_url($approver_signature) }}" alt="Approved By Signature"
                            style="height: 50px;"><br>
                        <strong>Approved By:</strong><br>{{ getUsername($details->approved_by) }}
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
