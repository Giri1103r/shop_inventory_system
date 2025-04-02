<!DOCTYPE html>
<html>

<head>
    <title>RRAA Inspection | KARAM</title>

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
                    Monthly Audit Plan</td>
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
                    Monthly Audit plan
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
       

        <tr>
            <td width="50%" style="padding:5px;"><b>Auditee Name</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_audit_plan->auditee_name) ? $monthly_audit_plan->auditee_name : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Unit</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUnitname(isset($monthly_audit_plan->unit_id) ? $monthly_audit_plan->unit_id : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Task</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getTaskName(isset($monthly_audit_plan->task_id) ? $monthly_audit_plan->task_id : '') }}
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Category</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getCategoryType(isset($monthly_audit_plan->compliance_category_id) ? $monthly_audit_plan->compliance_category_id : '') }}
            </td>
        </tr>
       
        <tr>
            <td width="50%" style="padding:5px;"><b>Reference Doc No</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_audit_plan->reference_doc_no) ? $monthly_audit_plan->reference_doc_no : '' }}
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Frequency</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getFrequencyname(isset($monthly_audit_plan->frequency_id) ? $monthly_audit_plan->frequency_id : '') }}
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Audit Plan Status</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $monthly_audit_plan->audit_plan_status == 1 ? 'Yes' : 'No' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Direct / In-Direct</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $monthly_audit_plan->direct_in_direct == 1 ? 'Direct' : 'In-Direct' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Points</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_audit_plan->points) ? $monthly_audit_plan->points : '' }}

            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Remarks</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($monthly_audit_plan->remarks) ? $monthly_audit_plan->remarks : '' }}
            </td>
        </tr>

        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($monthly_audit_plan->created_by) ? $monthly_audit_plan->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($monthly_audit_plan->created_at) }}</td>
        </tr>
    </table>

   

 


    

   


</body>

</html>
