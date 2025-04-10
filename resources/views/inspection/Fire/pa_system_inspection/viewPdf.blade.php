<!DOCTYPE html>
<html>

<head>
    <title>Fire PA System Inspection | KARAM</title>

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
                    Fire PA System Inspection </td>
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
                    Fire PA System Inspection
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>{{ __('inspection.doc_no') }}</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>{{ __('inspection.issue_date') }}</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>{{ __('inspection.rev_date') }}</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
            </td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created By</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ getUsername(isset($forklift_details->created_by) ? $forklift_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($forklift_details->created_at) }}</td>
        </tr>
    </table>

    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Fire PA System Inspection Details
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%; border-collapse: collapse; text-align: center; border: 1px solid black;">
        <thead>
            <tr>
                <td colspan="3" style="border: 1px solid black; padding: 8px; font-weight: bold;"
                    title="DATE OF INSPECTION">DATE OF INSPECTION :-
                    {{ DisplayDateformat($forklift_details->date_of_inspection) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="LOCATION">
                    LOCATION :- {{ getLocationname($forklift_details->location) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="SHIFT">
                    SHIFT :- {{ getShiftName($forklift_details->shift) }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid black; padding: 8px; font-weight: bold;"
                    title="NEXT DUE ON">
                    NEXT DUE ON :- {{ DisplayDateformat($forklift_details->next_due) }}</td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="UNIT">UNIT
                    :- {{ getUnitname($forklift_details->unit) }}
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="FREQUENCY">
                    FREQUENCY :- {{ getFrequencyname($forklift_details->frequency) }}</td>
            </tr>
            <tr>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">SL</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">FIRE POINT NO.</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">LOCATION</th>
                <th colspan="7" style="border: 1px solid black; padding: 8px;">CHECK ITEMS</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">REMARK</th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px;">UNIT</th>
                <th style="border: 1px solid black; padding: 8px;">AUDIO QUALITY</th>
                <th style="border: 1px solid black; padding: 8px;">MIC CONDITION</th>
                <th style="border: 1px solid black; padding: 8px;">MIC QUANTITY</th>
                <th style="border: 1px solid black; padding: 8px;">PHYSICAL CONDITION</th>
                <th style="border: 1px solid black; padding: 8px;">CABLE CONDITION</th>
                <th style="border: 1px solid black; padding: 8px;">OPERATION</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($inspection as $details)
            <tr>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->sr_no }}</td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    {{ getLocationname($details->location) }}
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    {{ getUnitname($details->unit) }}
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    @if ($details->audio_quality == GOOD)
                        GOOD
                    @elseif($details->audio_quality == FAIR)
                        FAIR
                    @elseif($details->audio_quality == POOR)
                        POOR
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    @if ($details->mic_condition == GOOD)
                        GOOD
                    @elseif($details->mic_condition == FAIR)
                        FAIR
                    @elseif($details->mic_condition == POOR)
                        POOR
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    {{ $details->mic_quantity }}
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    @if ($details->physical_condition == GOOD)
                        GOOD
                    @elseif($details->physical_condition == FAIR)
                        FAIR
                    @elseif($details->physical_condition == POOR)
                        POOR
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    @if ($details->cable_condition == GOOD)
                        GOOD
                    @elseif($details->cable_condition == FAIR)
                        FAIR
                    @elseif($details->cable_condition == POOR)
                        POOR
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    @if ($details->operation == FUNCTIONAL)
                        {{ __('inspection.functional') }}
                    @elseif($details->operation == NON_FUNCTIONAL)
                        {{ __('inspection.non_functional') }}
                    @endif
                </td>
                <td style="border: 1px solid black; padding: 8px; text-align: center;">
                    {{ $details->remark }}
                </td>

            </tr>
            @endforeach
            @php
                $prepared_by_signature = GetFireSignature(
                    $forklift_details->created_by,
                    $forklift_details->id,
                    FIRE_PA_SYSTEM_INSPECTION,
                );
                $verified_by_signature = GetFireSignature(
                    $forklift_details->updated_by,
                    $forklift_details->id,
                    FIRE_PA_SYSTEM_INSPECTION,
                );
                $verified_by_signature = GetFireSignature(
                    $forklift_details->approved_by,
                    $forklift_details->id,
                    FIRE_PA_SYSTEM_INSPECTION,
                );
            @endphp
            <tr>
                <td colspan="4"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    <img src="{{ admin_url($prepared_by_signature) }}" alt="Checked By Signature"
                        style="height: 50px;">
                    <div>Checked & Prepared By: {{ getUsername($forklift_details->created_by) }}</div>
                </td>
                <td colspan="4"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->updated_by != null)
                        <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                            style="height: 50px;">
                        <div>Verified By: {{ getUsername($forklift_details->updated_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
                <td colspan="4"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->approved_by != null)
                        <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                            style="height: 50px;">
                        <div>Approved By: {{ getUsername($forklift_details->approved_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    @if ($forklift_details->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
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
            @if (isset($forklift_details->verified_by))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($forklift_details->verified_by) }}</td>
                </tr>
            @endif
            @if (isset($forklift_details->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($forklift_details->created_at) }}
                    </td>
                </tr>
            @endif
            @if (isset($forklift_details->approved_by))
                @if ($forklift_details->verified_by == $forklift_details->approved_by)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUsername($forklift_details->approved_by) }}
                        </td>
                    </tr>
                @endif
            @endif
            @if (isset($forklift_details->capa_recomendation))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_recomendation') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $forklift_details->capa_recomendation }}
                    </td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $forklift_details->remarks }}
                </tr>
            @endif
        </table>
        <br>
    @endif

    @if (isset($forklift_details->capa_remarks))
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
                <td width="48%" style="padding:5px;"> {{ getUserName($forklift_details->created_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($forklift_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_action_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $forklift_details->capa_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if ($forklift_details->capa_ehs_remarks)
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
                <td width="48%" style="padding:5px;"> {{ getUserName($forklift_details->verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($forklift_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $forklift_details->capa_ehs_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if (isset($forklift_details->level_one_manager_remarks))
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
                    {{ getUserName($forklift_details->l1_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($forklift_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $forklift_details->level_one_manager_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif

    @if (isset($forklift_details->level_two_manager_remarks))
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUserName($forklift_details->l2_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($forklift_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername($forklift_details->approved_by) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $forklift_details->level_two_manager_remarks }}
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
