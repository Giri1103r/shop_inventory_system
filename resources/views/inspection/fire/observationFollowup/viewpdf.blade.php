<!DOCTYPE html>
<html>

<head>
    <title>CHECKLIST OBSERVATION FOLLOW UP SHEET| KARAM</title>

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
                    CHECKLIST OBSERVATION FOLLOW UP SHEET</td>
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
                    CHECKLIST OBSERVATION FOLLOW UP SHEET
                </td>
            </tr>
        </table>
    </div>
    <div>

        <table
            style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">

            <tr>
                <th colspan="25" style="border:1px solid black;height:50;width:40">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:125px;height:50px;">
                </th>
                <th colspan="25" style="border:1px solid black;">
                    <h3>
                        <span><b>CHECKLIST OBSERVATION FOLLOW UP SHEET</b></span>
                        <br>
                        <span><b>PN INTERNATIONAL PVT. LTD.</b></span>
                    </h3>
                </th>

                <th colspan="30" style="border:1px solid black;">
                    <table class="table table-bordered scrolldown">
                        <thead>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Doc.No</td>
                                <td style="border: 1px solid black;">{{ $observation->doc_no }}</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Issue Dt.</td>
                                <td style="border: 1px solid black;">{{ Displaydateformat($observation->issue_date) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid black;width:70;">Rev.& Dt.</td>
                                <td style="border: 1px solid black;">{{ $observation->rev_dt }}</td>
                            </tr>
                        </thead>
                    </table>

                </th>
            </tr>

            <tr>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">SR. NO
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">UNIT</th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">DEPARTMENT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">NAME OF
                    EQUIPMENT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">RESOURCE
                    CODE OF EQUIPMENT
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">OBSERVATION
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">DATE OF
                    OBSERVATION / INSPECTION
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">OBSERVATION
                    OF THE MONTH
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">WHY-01
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">WHY-02
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">WHY-03
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">WHY-04
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">WHY-05
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">MAIN ROOT
                    CAUSE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CORRECTIVE
                    & PREVENTIVE ACTION TAKEN
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">DATE OF
                    CORRECTIVE & PREVENTIVE ACTION TAKEN
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    RESPONSIBILITY
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">TIMELINE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">DATE OF
                    CLOSURE
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">STATUS
                </th>
                <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">REMARK
                </th>
            </tr>


            <tr>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="2">1
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ getUnitname($observation->unit_id) }}</td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">
                    {{ getDepartment($observation->department_id) }}</td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">
                    {{ $observation->equipment_name }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->equipment_code }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->observation }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->date }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->month }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->why_1 }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->why_2 }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->why_3 }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->why_4 }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->why_5 }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->main_root_cause }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->corrective_action }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ Displaydateformat($observation->ehs_verified_date) }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ getUsername($observation->responsible_person_id) }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ Displaydateformat($observation->target_date) }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->closed_date }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->status == 1 ? 'Active' : 'In-active' }}
                </td>
                <td style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">
                    {{ $observation->capa_remarks }}
                </td>
            </tr>
        </table>
        @if ($observation->approve_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            {{ __('inspection.ehs_officer_verify') }}
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                @if (isset($observation->verified_by))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;"> {{ getUserName($observation->verified_by) }}</td>
                    </tr>
                @endif
                @php
                    $signature = GetSignature($observation->verified_by, $observation->id, OBSERVATION_FOLLOWUP);
                @endphp
                @if (isset($observation->created_at))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;"> {{ Displaydateformat($observation->created_at) }}
                        </td>
                    </tr>
                @endif
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif
                @php
                    $signature = GetSignature($observation->approved_by, $observation->id, OBSERVATION_FOLLOWUP);
                @endphp
                @if (isset($observation->approved_by))
                    @if ($observation->verified_by == $observation->approved_by)
                        <tr>
                            <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                            <td width="2%" style="padding:5px;">:</td>
                            <td width="48%" style="padding:5px;">
                                {{ getUsername($observation->approved_by) }}
                            </td>
                        </tr>
                    @endif
                @endif
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif
                @if (isset($observation->capa_recomendation))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_recomendation') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $observation->capa_recomendation }}
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $observation->capa_remarks }}
                    </tr>
                @endif
            </table>
            <br>
        @endif

        @if (isset($observation->capa_remarks))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            {{ __('inspection.fire_associate_action') }}
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.name') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($observation->created_by) }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($observation->created_at) }}
                    </td>
                </tr>
                @php
                    $signature = GetSignature($observation->approved_by, $observation->id, OBSERVATION_FOLLOWUP);
                @endphp
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_action_remarks') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $observation->capa_remarks }}
                    </td>
                </tr>
            </table>
            <br>
        @endif


        @if ($observation->capa_ehs_remarks)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            {{ __('inspection.ehs_officer_reverification') }}
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($observation->verified_by) }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($observation->created_at) }}
                    </td>
                </tr>
                @php
                    $signature = GetSignature($observation->verified_by, $observation->id, OBSERVATION_FOLLOWUP);
                @endphp
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b>
                    </td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $observation->capa_ehs_remarks }}
                    </td>
                </tr>
            </table>
            <br>
        @endif


        @if (isset($observation->level_one_manager_remarks))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            {{ __('inspection.level_one_manager_action') }}
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ getUserName($observation->l1_manager_verified_by) }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($observation->created_at) }}
                    </td>
                </tr>
                @php
                    $signature = GetSignature(
                        $observation->l1_manager_verified_by,
                        $observation->id,
                        OBSERVATION_FOLLOWUP,
                    );
                @endphp
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b>
                    </td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $observation->level_one_manager_remarks }}
                    </td>
                </tr>
            </table>
            <br>
        @endif

        @if (isset($observation->level_two_manager_remarks))
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                            {{ __('inspection.level_two_manager_action') }}
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ getUserName($observation->l2_manager_verified_by) }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($observation->created_at) }}
                    </td>
                </tr>
                @php
                    $signature = GetSignature(
                        $observation->l2_manager_verified_by,
                        $observation->id,
                        OBSERVATION_FOLLOWUP,
                    );
                @endphp
                @if (isset($signature))
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>


                        <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                style="width: 150px; margin-top: -10px;" /></td>

                    </tr>
                @endif

                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b>
                    </td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $observation->level_two_manager_remarks }}
                    </td>
                </tr>
            </table>
            <br>
        @endif


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
                    @if (isset($status_log) && $status_log->isNotEmpty())
                        <table class="table table-bordered table-hover tblborder">
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>From Status</th>
                                    <th>To Status</th>
                                    <th>Remarks</th>
                                    <th>Approved By</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($status_log as $log)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ getInspectionStatus($log->from_status) }}</td>
                                        <td>{{ getInspectionStatus($log->to_status) }}</td>
                                        <td>{{ $log->remarks ?? 'N/A' }}</td>
                                        <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                        </td>
                                        <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}
                                        </td>
                                        <td>{{ displaydateformat($log->created_at) }}</td>
                                    </tr>
                                @endforeach
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



</body>

</html>
