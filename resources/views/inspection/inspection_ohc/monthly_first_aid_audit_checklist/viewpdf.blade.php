<!DOCTYPE html>
<html>

<head>
    <title>Monthly First Aid Box Audit Checklist | KARAM</title>

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
                    Monthly First Aid Box Audit Checklist </td>
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
                    Monthly First Aid Box Audit Checklist
                </td>
            </tr>
        </table>
    </div>
    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

        <tr>
            <th colspan="20" style="border:1px solid black;height:50;width:40">
                <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
            </th>
            <th colspan="20" style="border:1px solid black;">
                <h3>
                    <span><b>Monthly First Aid Box Audit Checklist</b></span>
                    <br>
                  
                </h3>
            </th>

            <th colspan="18" style="border:1px solid black;">
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
        <tr>
            <th colspan="29"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                DATE: {{ Displaydateformat($monthly_first_aid->date_of_inspection) ?? 'N/A' }}
            </th>

            <th colspan="29"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                SHIFT: {{ getShift($monthly_first_aid->shift) ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th colspan="29"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                FREQUENCY : {{ getFrequencyname($monthly_first_aid->frequency) ?? 'N/A' }}
            </th>
            <th colspan="29"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                UNIT: {{ getUnitname($monthly_first_aid->unit) ?? 'N/A' }}
            </th>

        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="8">First-Aid Box
                Number</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">
                Department/Location</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">Does the
                first-aid register is being properly maintened as & when require.
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the
                first-aid box is bieng inspect as per periodicity.
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the First-
                aid box inspection Checklist is being filled as per periodicity.
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the
                First-aid box is being maintained as per the freeze quantity.
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the
                medical requisition slip record is being maintained.
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the
                first-aid box is clean.
            </th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the
                first-aid box sticker available.
            </th>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="6">Does the First
                aid material index is available.
            </th>
        </tr>
        @php
            $medicineRequisitionDetails = GetMonthlyAuditChecklist($monthly_first_aid->id);
        @endphp
        @foreach ($medicineRequisitionDetails as $log)
            <tr>


                <td style="border: 1px solid black; padding: 8px;" colspan="2">{{ getUnitname($log->unit_id) }}</td>
                <td style="border: 1px solid black; padding: 8px;" colspan="8">
                    {{ getDepartment($log->department_id) }}</td>
                <td style="border: 1px solid black; padding: 8px;"colspan="6">{{ $log->first_aid_box_no }}</td>
                <td style="border: 1px solid black; padding: 8px;" colspan="4">
                    @if ($log->first_aid_register_maintained == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_box_inspect_periodicity == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_box_checklist_periodicity == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_box_freeze_quantity == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->medicine_requisition_slip_record == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_box_clean == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_box_sticker == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px;" colspan="6">
                    @if ($log->first_aid_material_index == 1)
                        <span style="color: green; font-size: 20px;">✓</span>
                    @else
                        <span style="color: red; font-size: 20px;">X</span>
                    @endif
                </td>

            </tr>
        @endforeach

        @php
            $createdSignature = GetOHCSignature(
                $monthly_first_aid->created_by,
                $monthly_first_aid->id,
                OHC_TYPE_MONTHLY_FIRST_AID_BOX_AUDIT_INSPECTION_CHECKLIST,
            );

        @endphp

        <tr>
            <th colspan="58" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">
                <img src="{{ admin_url($createdSignature) }}" alt="Signature Upload"
                    style="width: 150px; margin-top: -10px;" />
                <div style="margin-top: 5px;">Creator Signature: {{ getUsername($monthly_first_aid->created_by) }}
                </div>
            </th>


        </tr>
    </table>
    <br>

</body>

</html>
