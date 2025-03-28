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

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_first_aid->doc_no) ? $monthly_first_aid->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($monthly_first_aid->issue_date) ? $monthly_first_aid->issue_date : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_first_aid->revision_date) ? $monthly_first_aid->revision_date : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Shift</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getShift(isset($monthly_first_aid->shift) ? $monthly_first_aid->shift : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Date of Inspection</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($monthly_first_aid->date_of_inspection) ? $monthly_first_aid->date_of_inspection : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Frequency</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getfrequencyname(isset($monthly_first_aid->frequency) ? $monthly_first_aid->frequency : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($monthly_first_aid->created_by) ? $monthly_first_aid->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($monthly_first_aid->created_at) }}</td>
        </tr>
    </table>

    <div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                        Monthly First Aid Box Audit Checklist
                    </td>
                </tr>
            </table>
        </div>
        <div class="table-responsive">
            <div class="col-md-12">
                @if (isset($monthly_first_aid_audit_checklist) && $monthly_first_aid_audit_checklist->isNotEmpty())
                    <table class="table table-bordered table-hover tblborder">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Unit</th>
                                <th>Department</th>
                                <th>First Aid Box Number</th>
                                <th>Does the first-aid register is being properly maintened as &
                                    when require.</th>
                                <th>Does the first-aid box is bieng inspect as per periodicity.</th>
                                <th>Does the First- aid box inspection Checklist is being filled as per periodicity.
                                </th>
                                <th>Does the First-aid box is being maintained as per the freeze
                                    quantity.</th>
                                <th>Does the medical requisition slip record is being
                                    maintained.</th>
                                <th>Does the first-aid box is clean.</th>
                                <th>Does the first-aid box sticker available.</th>
                                <th>Does the First aid material index is available.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($monthly_first_aid_audit_checklist) || $monthly_first_aid_audit_checklist->isEmpty())
                                <tr>
                                    <td colspan="12" class="text-center">No data is available</td>
                                </tr>
                            @else
                                @foreach ($monthly_first_aid_audit_checklist as $index => $log)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ getUnitname($log->unit_id) }}</td>
                                        <td>{{getDepartment( $log->department_id) }}</td>
                                        <td>{{ $log->first_aid_box_no }}</td>
                                        <td>
                                            @if ($log->first_aid_register_maintained == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_box_inspect_periodicity == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_box_checklist_periodicity == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_box_freeze_quantity == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->medicine_requisition_slip_record == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_box_clean == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_box_sticker == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($log->first_aid_material_index == 1)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @else
                                                 <span style="color: red; font-size: 20px;">X</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="text-dark">{{ __('No status logs available.') }}</p>
                    </div>
                @endif
            </div>
        </div>
        <br>
    </div>
    <br>

</body>

</html>
