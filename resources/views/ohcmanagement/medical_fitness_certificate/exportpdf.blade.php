<html>


<head>
    <title>Medical Fitness Certificate | KARAM</title>
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
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <htmlpageheader name="myHeader1" style="display:block;">
            <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
                <tr style="">
                    <td border="0" style="width:50%;float:left;text-align:left;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                    </td>
                    <td border="0"
                        style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                        Medical Fitness Certificate
                    </td>
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
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px; font-weight:bold;">
                        Medical Fitness Certificate
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">



            <tr>
                <td width="50%" style="padding:5px;"><b>Employee Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($medicinefitness->emp_name) ? $medicinefitness->emp_name : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Employee Id</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($medicinefitness->emp_id) ? $medicinefitness->emp_id : '' }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($medicinefitness->date) ? $medicinefitness->date : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Remarks</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($medicinefitness->remarks) ? $medicinefitness->remarks : '' }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($medicinefitness->created_by) ? $medicinefitness->created_by : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ displayDateformat($medicinefitness->created_at) }}
                </td>
            </tr>
        </table>
        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%; background-color: #ce0f1f; color:#ffffff; padding: 10px 10px 10px; font-weight:bold;">
                            Status Logs
                        </td>
                    </tr>
                </table>
            </div>
            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover tblborder">
                        <thead>
                            <tr>
                                <th>From Status</th>
                                <th>To Status</th>
                                <th>Approved By</th>
                                <th>Remarks</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($medicalfitnesslog as $status_log)
                                <tr>
                                <tr>
                                    <!-- From Status Column -->
                                    <td>
                                        <p>
                                            <span>
                                                @if ($status_log['from_status'] == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                    Paramedics request the fitness Approval
                                                @elseif ($status_log['from_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                    Doctor Approval Pending
                                                @elseif ($status_log['from_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                    Doctor Approved
                                                @elseif ($status_log['from_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                    EHS Head Approval Pending
                                                @elseif ($status_log['from_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                    EHS Head Approved
                                                @endif
                                            </span>
                                        </p>
                                    </td>

                                    <td>
                                        <p>
                                            <span>
                                                @if ($status_log['to_status'] == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                    Paramedics request the fitness Approval
                                                @elseif ($status_log['to_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                    Doctor Approval Pending
                                                @elseif ($status_log['to_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                    Doctor Approved
                                                @elseif ($status_log['to_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                    EHS Head Approval Pending
                                                @elseif ($status_log['to_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                    EHS Head Approved
                                                @endif
                                            </span>
                                        </p>
                                    </td>


                                    <td>{{ isset($status_log['created_by']) ? getUsername($status_log['created_by']) : '-' }}
                                    </td>
                                    <td>{{ isset($status_log['remarks']) ? $status_log['remarks'] : '-' }}
                                    </td>
                                    <td>{{ null !== Displaydateformat($status_log['created_at']) ? Displaydateformat($status_log['created_at']) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
        </div>

</body>

</html>
