<!DOCTYPE html>
<html>

<head>
    <title>Cartridge Type Fire Extinguisher Inspection | KARAM</title>

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
                    Cartridge Type Fire Extinguisher Inspection </td>
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
                    Cartridge Type Fire Extinguisher Inspection
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">
        <tr>
            <td width="50%" style="padding:5px;"><b>Document Number</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
            </td>
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
                    Cartridge Type Fire Extinguisher Observation
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Observation</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $forklift_details->observation_needed == '1' ? 'YES' : 'NO' }}</td>
        </tr>

    </table>
    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Cartridge Type Fire Extinguisher Inspection Details
                </td>
            </tr>
        </table>
    </div>
    <table style="width: 100%; border-collapse: collapse; text-align: center; border: 1px solid black;">
        <thead>
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;"
                    title="DATE OF INSPECTION">DATE OF INSPECTION :-
                    {{ DisplayDateformat($forklift_details->inspection_date) }}</td>
                <td colspan="5" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="LOCATION">
                    LOCATION :- {{ getLocationname($forklift_details->location) }}</td>
                <td colspan="5" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="SHIFT">
                    SHIFT :- {{ getShiftName($forklift_details->shift) }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 8px; font-weight: bold;"
                    title="NEXT DUE ON">
                    NEXT DUE ON :- {{ DisplayDateformat($forklift_details->next_due) }}</td>
                <td colspan="5" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="UNIT">UNIT
                    :- {{ getUnitname($forklift_details->unit) }}
                </td>
                <td colspan="5" style="border: 1px solid black; padding: 8px; font-weight: bold;" title="FREQUENCY">
                    FREQUENCY :- {{ getFrequencyname($forklift_details->frequency) }}</td>
            </tr>
            <tr>
                <th rowspan="3" style="border: 1px solid black; padding: 8px;">SL</th>
                <th rowspan="3" style="border: 1px solid black; padding: 8px;">FIRE POINT NO.</th>
                <th rowspan="3" style="border: 1px solid black; padding: 8px;">DEPARTMENT</th>
                <th rowspan="3" style="border: 1px solid black; padding: 8px;">LOCATION</th>
                <th colspan="9" style="border: 1px solid black; padding: 8px;">CHECK ITEMS</th>
                <th rowspan="3" style="border: 1px solid black; padding: 8px;">REMARKS</th>
            </tr>
            <tr>
                <th colspan="3" style="border: 1px solid black; padding: 8px;">DESCRIPTION</th>
                <th colspan="3" style="border: 1px solid black; padding: 8px;">CONDITION</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">WEIGHT OF CARTRIDGE</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">SAFETY PIN</th>
                <th rowspan="2" style="border: 1px solid black; padding: 8px;">APPROACH</th>
            </tr>
            <tr>
                <th style="border: 1px solid black; padding: 8px;">TYPE</th>
                <th style="border: 1px solid black; padding: 8px;">CAPACITY</th>
                <th style="border: 1px solid black; padding: 8px;">QUANTITY</th>
                <th style="border: 1px solid black; padding: 8px;">DISCHARGE TUBE</th>
                <th style="border: 1px solid black; padding: 8px;">HANDLE</th>
                <th style="border: 1px solid black; padding: 8px;">WHEEL</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($inspection as $details)
                <tr>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;"> {{ $details->sr_no }}</td>

                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $details->fire_point_no }}</td>

                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ GetDeptName($details->department) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getLocationname($details->location) }}</td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ getExtinguisherTypeName($details->type) }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->capacity }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->quantity }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        @if ($details->discharge_tube == FUNCTIONAL)
                            {{ __('inspection.functional') }}
                        @elseif($details->discharge_tube == NON_FUNCTIONAL)
                            {{ __('inspection.non_functional') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        @if ($details->handle == GOOD)
                            GOOD
                        @elseif($details->handle == DAMAGED)
                            DAMAGED
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        @if ($details->wheel == FUNCTIONAL)
                            {{ __('inspection.functional') }}
                        @elseif($details->wheel == NON_FUNCTIONAL)
                            {{ __('inspection.non_functional') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        {{ $details->weight_of_cartidge }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                        @if ($details->safety_pin == PRESENT)
                            {{ __('inspection.present') }}
                        @elseif($details->safety_pin == MISSING)
                            {{ __('inspection.missing') }}
                        @endif
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->approach }}
                    </td>
                    <td style="border: 1px solid black; padding: 8px; text-align: center;">{{ $details->remarks }}
                    </td>
                </tr>
            @endforeach
            {{-- @php
                $prepared_by_signature = GetFireSignature(
                    $forklift_details->created_by,
                    $forklift_details->id,
                    CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                );
                $verified_by_signature = GetFireSignature(
                    $forklift_details->updated_by,
                    $forklift_details->id,
                    CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                );
                $verified_by_signature = GetFireSignature(
                    $forklift_details->approved_by,
                    $forklift_details->id,
                    CARTRIDGE_TYPE_FIRE_EXTINGUISHER_INSPECTION,
                );
            @endphp
            <tr>
                <td colspan="4"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    <img src="{{ admin_url($prepared_by_signature) }}" alt="Checked By Signature"
                        style="height: 50px;">
                    <div>Checked & Prepared By: {{ getUsername($forklift_details->created_by) }}</div>
                </td>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->updated_by != null)
                        <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                            style="height: 50px;">
                        <div>Verified By: {{ getUsername($forklift_details->updated_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->approved_by != null)
                        <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                            style="height: 50px;">
                        <div>Approved By: {{ getUsername($forklift_details->approved_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
            </tr> --}}

            <tr>
                <td colspan="4"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->created_by != null)
                        <div>Checked & Prepared By: {{ getUsername($forklift_details->created_by) }}</div>
                    @else
                        <div>
                            Checked & Prepared By: Inspection Not Yet Started
                        </div>
                    @endif
                </td>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->verified_by != null)
                        <div>Verified By: {{ getUsername($forklift_details->verified_by) }}</div>
                    @else
                        <p>Inspection has not been Verified Yet</p>
                    @endif
                </td>
                <td colspan="5"
                    style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                    @if ($forklift_details->approved_by != null)
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
            @if (isset($forklift_details->is_passed))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ohc_management.capa') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($forklift_details->is_passed) && $forklift_details->is_passed == 1 ? 'Yes' : 'NO' }}
                    </td>
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
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat($forklift_details->fire_associate_updated_at) }}
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
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat($forklift_details->ehs_officer_verified_at) }}
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
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat($forklift_details->l1_manager_updated_at) }}
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
                <td width="48%" style="padding:5px;">
                    {{ Displaydateformat($forklift_details->l2_manager_updated_at) }}
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
                                <th>{{ __('common.sno') }}</th>
                                <th>{{ __('common.from_status') }}</th>
                                <th>{{ __('common.to_status') }}</th>
                                <th>{{ __('common.remarks') }}</th>
                                <th>{{ __('common.approve_or_reject') }}</th>
                                <th>{{ __('common.created_by') }}</th>
                                <th>{{ __('common.created_date') }}</th>
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
