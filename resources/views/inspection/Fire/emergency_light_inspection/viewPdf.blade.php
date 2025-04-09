<!DOCTYPE html>
<html>

<head>
    <title>Emergency Light Inspection | KARAM</title>

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
                    Emergency Light Inspection </td>
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
                    Emergency Light Inspection
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Issue Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Revision Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($emergency_light->created_by) ? $emergency_light->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($emergency_light->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Emergency Light Inspection
                </td>
            </tr>
        </table>
    </div>
    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                DATE OF INSPECTION: {{ Displaydateformat($emergency_light->date_of_inspection) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                LOCATION: {{ getLocationname($emergency_light->location) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                SHIFT: {{ getShift($emergency_light->shift) ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                NEXT DUE: {{ Displaydateformat($emergency_light->next_due) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                UNIT: {{ getUnitname($emergency_light->unit) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                FREQUENCY: {{ getFrequencyname($emergency_light->frequency) ?? 'N/A' }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">SR. NO</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">DEPARTMENT</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">LOCATION</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">EMERGENCY LIGHT
                NUMBER</th>
            <th colspan="7" style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CHECK ITEMS
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">REMARK</th>
        </tr>
        <tr>

            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">DESCRIPTION
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="4">CONDITION OF
                LIGHT</th>

        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">TYPE OF LIGHT</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CAPACITY</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">QUANTITY</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">LIGHT CONDITION</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">SWITCH CONDITION</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">POWER SUPPLY</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">STATUS</th>
        </tr>

        @foreach ($inspection as $details)
            <tr>
                <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ getDepartment($details->department) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->location }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->emergency_of_light }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ GetTypeofLight($details->type_of_light) }}</td>

                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->capacity }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->quantity }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ getLightCondition($details->light_condition) }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ getLightCondition($details->switc_condition) }}

                </td>
                <td style="border: 1px solid black; padding: 8px;">{{ GetPowerSuply($details->power_supply) }}</td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ getFireLightInspectionStatus($details->fire_status) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->remarks }}</td>


            </tr>
        @endforeach
    </table>



    @if ($emergency_light->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
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
            @if (isset($emergency_light->verified_by))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($emergency_light->verified_by) }}</td>
                </tr>
                @php
                    $signature = GetSignature(
                        $emergency_light->verified_by,
                        $emergency_light->id,
                        EMERGENCY_LIGHT_INSPECTION,
                    );
                @endphp
            @endif
            @if (isset($emergency_light->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($emergency_light->created_at) }}
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
            @if (isset($emergency_light->approved_by))
                @if ($emergency_light->verified_by == $emergency_light->approved_by)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUsername($emergency_light->approved_by) }}
                        </td>
                    </tr>
                @endif

                @php
                    $signature = GetSignature(
                        $emergency_light->approved_by,
                        $emergency_light->id,
                        EMERGENCY_LIGHT_INSPECTION,
                    );
                @endphp
            @endif
            @if (isset($signature))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.signature') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>


                    <td> <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                            style="width: 150px; margin-top: -10px;" /></td>

                </tr>
            @endif
            @if (isset($emergency_light->capa_recomendation))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_recomendation') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $emergency_light->capa_recomendation }}
                    </td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $emergency_light->remarks }}
                </tr>
            @endif
        </table>
        <br>
    @endif

    @if (isset($emergency_light->capa_remarks))
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
                <td width="48%" style="padding:5px;"> {{ getUserName($emergency_light->created_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($emergency_light->created_at) }}
                </td>
            </tr>
            @php
                $signature = GetFireSignature($emergency_light->created_by, $emergency_light->id, EMERGENCY_LIGHT_INSPECTION);
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
                    {{ $emergency_light->capa_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if ($emergency_light->capa_ehs_remarks)
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
                <td width="48%" style="padding:5px;"> {{ getUserName($emergency_light->verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($emergency_light->created_at) }}
                </td>
            </tr>
            @php
            $signature = GetFireSignature($emergency_light->verified_by, $emergency_light->id, EMERGENCY_LIGHT_INSPECTION);
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $emergency_light->capa_ehs_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if (isset($emergency_light->level_one_manager_remarks))
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
                    {{ getUserName($emergency_light->l1_manager_verification) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($emergency_light->created_at) }}
                </td>
            </tr>
            @php
            $signature = GetFireSignature($emergency_light->l1_manager_verification, $emergency_light->id, EMERGENCY_LIGHT_INSPECTION);
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $emergency_light->level_one_manager_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif

    @if (isset($emergency_light->level_two_manager_remarks))
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
                    {{ getUserName($emergency_light->l2_manager_verification) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($emergency_light->created_at) }}
                </td>
            </tr>
            @php
            $signature = GetFireSignature($emergency_light->l2_manager_verification, $emergency_light->id, EMERGENCY_LIGHT_INSPECTION);
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $emergency_light->level_two_manager_remarks }}
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
                                    <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}</td>
                                    <td>{{ displaydateformat($log->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="card-body">
                        <p class="text-dark">No status logs available.</p>
                    </div>
                @endif
            </div>
        </div>
        <br>
    </div>
    <br>

</body>

</html>
