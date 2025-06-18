<!DOCTYPE html>
<html>

<head>
    <title>Inter Unit Monthly Audit | KARAM</title>

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
                    Inter Unit Monthly Audit</td>
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
                    Inter Unit Monthly Audit
                </td>
            </tr>
        </table>
    </div>

    @php
        $user_response = json_decode($inter_unit_audit->checklist, true);
    @endphp
    <div class="table-responsive">
        <div class="col-md-12">
            <table class="table table-bordered table-hover tblborder">
                <tr>
                    <th colspan="8" style="border:1px solid black;height:50;width:40">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                    </th>
                    <th colspan="8" style="border:1px solid black;">
                        <h3>
                            <span><b>Inter Unit Monthly Audit Checklist</b></span>
                            </h3>
                    </th>

                    <th colspan="8" style="border:1px solid black;">
                        <table class="table table-bordered scrolldown">
                            <thead>
                                <tr>
                                    <td style="border: 1px solid black;width:70;">Doc.No</td>
                                    <td style="border: 1px solid black;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                    <td style="border: 1px solid black;"></td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                    <td style="border: 1px solid black;"></td>
                                </tr>
                            </thead>
                        </table>

                    </th>
                </tr>
                <tr>
                    <th colspan="8"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Name of Safety Officer: {{ ($inter_unit_audit->safety_officer) ?? 'N/A' }}
                    </th>
                    <th colspan="8"
                        style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                        Date of Audit: {{ Displaydateformat($inter_unit_audit->audit_date) ?? 'N/A' }}
                    </th>
                    <th colspan="8"
                    style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                    Unit: {{ getUnitname($inter_unit_audit->unit_id) ?? 'N/A' }}
                </th>
                </tr>
                <tr>
                    <th colspan="4" style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Sr. No</th>
                    <th colspan="10"
                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Check Points</th>

                    <th  colspan="4" style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        OK/NOT-OK</th>
                    <th colspan="6" style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                        Remarks</th>
                </tr>

                <tbody>
                    @php
                        $srNo = 1;
                        $displayedSections = [];
                    @endphp
                    @foreach ($user_response as $checklistId => $data)
                        @php
                            $sectionName = GetSubChecklistTypeName($data['sub_type_id']);
                        @endphp

                        @if (!in_array($sectionName, $displayedSections))
                            <tr>
                                <td colspan="24"
                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                    {{ $sectionName }}
                                </td>
                            </tr>
                            @php $displayedSections[] = $sectionName; @endphp
                        @endif

                        <tr>
                            <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                {{ $srNo }}
                            </td>
                            <td colspan="10" style="border: 1px solid black; padding: 8px;">
                                {{ GetChecklistTypeDate($checklistId) }}
                            </td>
                            <td colspan="4" style="border: 1px solid black; padding: 8px; text-align: center;">
                                @if (($data['response'] ?? '') == 'Ok')
                                    <span style="color: green; font-size: 20px;">Ok</span>
                                @elseif (($data['response'] ?? '') == 'Not Ok')
                                    <span style="color: red; font-size: 20px;">Not Ok</span>
                                @else
                                    <span style="color: red; font-size: 20px;">N/A</span>
                                @endif
                            </td>
                            <td colspan="6" style="border: 1px solid black; padding: 8px; text-align: center;">
                                {{ $data['remarks'] ? $data['remarks'] : '-' }}
                            </td>
                        </tr>

                        @php $srNo++; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
