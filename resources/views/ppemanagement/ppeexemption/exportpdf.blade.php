<html>


<head>
    <title>PPE Shoe Exemption Request| KARAM</title>
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
                        PPE Exemption
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
                        style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px; font-weight:bold;"  >
                       PPE Exemption
                    </td>
                </tr>
            </table>
        </div>

        <table width="100%" style="width:100%;">



            <tr>
                <td width="50%" style="padding:5px;"><b>Employee Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($ppeexemption->emp_name) ? $ppeexemption->emp_name : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Employee Id</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ isset($ppeexemption->emp_id) ? $ppeexemption->emp_id : '' }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>From Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($ppeexemption->from_date) ? $ppeexemption->from_date : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>To Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ isset($ppeexemption->to_date) ? $ppeexemption->to_date : '' }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Department</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getDepartment(isset($ppeexemption->department) ? $ppeexemption->department : '') }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Company</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getCompanyname(isset($ppeexemption->company) ? $ppeexemption->company : '') }}
                </td>

            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Unit</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUnitname(isset($ppeexemption->unit) ? $ppeexemption->unit : '') }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Reason</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Created By</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername(isset($ppeexemption->created_by) ? $ppeexemption->created_by : '') }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Created Date</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ displayDateformat($ppeexemption->created_at) }}
                </td>
            </tr>
        </table>

        <div>
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px; font-weight:bold;">
                            Status Logs
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="table-responsive">
                <div class="col-md-12">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th> From Status</th>
                                <th> To Status</th>

                                <th> Approved By</th>
                                <th>Remarks</th>
                                <th>Date</th>

                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> User
                                        Applied</td>
                                <td>EHS Head
                                        Approval
                                        Pending</td>
                                <td> {{ getUsername(isset($ppeexemption->created_by) ? $ppeexemption->created_by : '') }}
                                </td>
                                <td> {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
                                </td>
                                <td> {{ displaydateformat(isset($ppeexemption->created_at) ? $ppeexemption->created_at : '') }}
                                </td>
                            </tr>
                            <tr>
                                <td> EHS Head Approval
                                    Pending</td>
                                    <td>
                                        @if (isset($ehsheadstatus['to_status']) && $ehsheadstatus['to_status'] == STATUS_EHS_APPROVED)
                                           EHS Head Approved
                                        @elseif (isset($ehsheadstatus['to_status']) && $ehsheadstatus['to_status'] == STATUS_EHS_REJECTED)
                                           EHS Head Rejected
                                        @else
                                            <p>-</p>
                                        @endif
                                    </td>


                            <td> {{ isset($ehsheadstatus->created_by) && $ehsheadstatus->created_by != '' ? getUsername($ehsheadstatus->created_by) : '-' }}
                            </td>
                            <td> {{ isset($ehsheadstatus->remarks) ? $ehsheadstatus->remarks : '-' }}
                            </td>
                            <td>  {{ isset($ehsheadstatus->created_at) && $ehsheadstatus->created_at != '' ? displaydateformat($ehsheadstatus->created_at) : '-' }}
                            </td>
                            </tr>

                        </tbody>
                    </table>

                </div>
            </div>
            <br>

        </div>

</body>

</html>
