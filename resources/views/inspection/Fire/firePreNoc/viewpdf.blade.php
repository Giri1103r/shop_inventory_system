<!DOCTYPE html>
<html>

<head>
    <title>Fire Pre Noc Checklist | KARAM</title>

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
                    Fire Pre Noc Checklist</td>
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
                    Fire Pre Noc Checklist
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Inspection ID</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($fireNoc->inspection_id) ? $fireNoc->inspection_id : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Doc. No</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($fireNoc->document_no) ? $fireNoc->document_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Dt.</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ displayDateformat($fireNoc->issuedate) }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Rev. & Dt.</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $fireNoc->rev_date }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>ब्लाक आधारित विवरण (Block based
                    statement)</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $fireNoc->block_based_statement }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>ब्लाब्लाक(Block)</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $fireNoc->block }}</td>
        </tr>
    </table>

    <br>
    @php
        $user_response = json_decode($fireNoc->checklist, true);
    @endphp
    <div class="table-responsive">
        <div class="col-md-12">
            <table class="table table-bordered table-hover tblborder">
                <thead>
                    <tr>

                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            क्रमांक (Serial Number)
                        </th>

                        <th colspan="2"
                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            जाँच बिंदु (Check Point)
                        </th>

                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                            विवरण (Detail)
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $srNo = 1;
                        $displayedSections = [];
                    @endphp

                    @foreach ($user_response as $checklistId => $data)
                        @php
                            $sectionName = GetSubChecklistTypeName($data['sub_type_id']);
                        @endphp

                        <tr>
                            @if (!in_array($sectionName, $displayedSections))
                                <td colspan="5"
                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                    {{ $sectionName }}
                                </td>
                                @php $displayedSections[] = $sectionName; @endphp
                            @endif
                        </tr>

                        <tr>
                            <td style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                {{ $srNo }}
                            </td>

                            <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                {{ GetChecklistTypeDate($checklistId) }}
                            </td>

                            <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                {{ $data['remarks'] ?? '-' }}
                            </td>
                        </tr>

                        @php
                            $srNo++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
