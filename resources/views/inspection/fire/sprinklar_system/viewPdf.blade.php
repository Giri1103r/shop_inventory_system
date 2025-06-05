<!DOCTYPE html>
<html>

<head>
    <title>Sprinklar System Inspection | KARAM</title>

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
                    Sprinklar System Inspection </td>
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
                    Sprinklar System Inspection
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
                {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}</td>
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
                    Sprinklar System Inspection Details
                </td>
            </tr>
        </table>
    </div>
    <br>
    <table
        style="width: 100%; border-collapse: collapse; background-color: white; font-family: Arial, sans-serif; font-size: 14px;">
        <tr style="background-color: #ddd;">
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">DATE OF INSPECTION :-
                {{ Displaydateformat($forklift_details->date_of_inspection) }}
            </th>
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">LOCATION :-
                {{ getLocationName($forklift_details->location) }}</th>
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">SHIFT :-
                {{ GetShiftName($forklift_details->shift) }}</th>
        </tr>
        <tr style="background-color: #ddd;">
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">NEXT DUE ON :-
                {{ Displaydateformat($forklift_details->next_due) }}</th>
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">UNIT :-
                {{ getUnitName($forklift_details->unit) }} </th>
            <th colspan="4" style="border: 1px solid black; text-align: left; padding: 8px; background-color: #bbb;">FREQUENCY :-
                {{ getFrequencyName($forklift_details->frequency) }}</th>
        </tr>
        <thead>
            <tr>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">SR. NO
                </th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">
                    DEPARTMENT/ LOCATION</th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">QUANTITY
                </th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">RESOURCE
                    CODE</th>
                <th colspan="6"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">CHECK
                    ITEMS (OK / NOT OK)</th>
                <th rowspan="2"
                    style="border: 2px solid black; padding: 10px; text-align: center; background-color: #bbb;">REMARK
                </th>
            </tr>
            <tr>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">WATER
                    LEAKAGE IN PIPE</th>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">PAINTING
                </th>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">QBD
                    CONDITION</th>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">
                    CONDITION OF FLOW METER</th>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">MAIN
                    ISOLATION VALVE CONDITION</th>
                <th style="border: 2px solid black; padding: 10px; text-align: center; background-color: #ddd;">DRAIN
                    VALVE CONDITION</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inspection as $details)
                <tr style="background-color: {{ $loop->even ? '#f9f9f9' : 'white' }};">
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">{{ $loop->iteration }}</td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        {{ GetDeptName($details->department) }}</td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">{{ $details->quantity }}
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        {{ $details->resource_code }}</td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->water_leakage == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->painting == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->qbd == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->condition_of_flow_meter == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->main_isolation == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">
                        @if ($details->drain_condition == OK)
                            {{ __('inspection.ok') }}
                        @else
                            {{ __('inspection.not_ok') }}
                        @endif
                    </td>
                    <td style="border: 2px solid black; padding: 10px; text-align: center;">{{ $details->remarks }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($forklift_details->created_by))
                            {{-- <img src="{{ admin_url($checked_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                            <p style="margin: 0;">Checked By:- {{ getUsername($forklift_details->created_by) }}</p>
                        @else
                            <p style="margin: 0;">Checked By:- Not yet checked</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($forklift_details->verified_by))
                            {{-- <img src="{{ admin_url($verified_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                            <p style="margin: 0;">Verified By:- {{ getUsername($forklift_details->verified_by) }}</p>
                        @else
                            <p style="margin: 0;">Verified By:- Not yet verified</p>
                        @endif
                    </div>
                </td>
                <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                    <div class="view_data">
                        @if (!empty($forklift_details->approved_by))
                            {{-- <img src="{{ admin_url($approved_by) }}" alt=""
                                style="max-height: 60px; display: block; margin: 0 auto 5px;"> --}}
                            <p style="margin: 0;">Approved By:- {{ getUsername($forklift_details->approved_by) }}</p>
                        @else
                            <p style="margin: 0;">Approved By:- Not yet approved</p>
                        @endif
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <br>



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
                    <td width="48%" style="padding:5px;">
                        {{ Displaydateformat($forklift_details->created_at) }}
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b>
                </td>
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b>
                </td>
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
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b>
                </td>
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
                                    <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}
                                    </td>
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
