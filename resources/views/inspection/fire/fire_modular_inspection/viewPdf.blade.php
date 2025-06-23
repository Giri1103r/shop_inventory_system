<!DOCTYPE html>
<html>

<head>
    <title>Fire Modular Inspection | KARAM</title>

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

        .page-break{
            page-break-before: always;
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
                    Fire Modular Inspection </td>
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
                    Fire Modular Inspection
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
                    Fire Modular Inspection Observation
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Observation</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $forklift_details->observation == '1' ? 'YES' : 'NO' }}</td>
        </tr>

    </table>
    <br>

    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Fire Modular Inspection Details
                </td>
            </tr>
        </table>
    </div>
    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                DATE OF INSPECTION: {{ Displaydateformat($forklift_details->date_of_inspection) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                LOCATION: {{ getLocationname($forklift_details->location) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="3">
                SHIFT: {{ getShift($forklift_details->shift) ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                NEXT DUE: {{ Displaydateformat($forklift_details->next_due) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="4">
                UNIT: {{ getUnitname($forklift_details->unit) ?? 'N/A' }}
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;"
                colspan="3">
                FREQUENCY: {{ getFrequencyname($forklift_details->frequency) ?? 'N/A' }}
            </th>
        </tr>

        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">SR. NO</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">DEPARTMENT</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">RESOURCE CODE
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">LOCATION</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="2" colspan="3">
                DESCRIPTON
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="1" colspan="3">
                CHECK ITEMS
            </th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" rowspan="3">REMARK</th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;" colspan="3">CONDITION
            </th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">TYPE</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CAPACITY</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">WORKING TEMPERATURE</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">SPRINKLER HEAD</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">NECK RING</th>
            <th style="border: 1px solid black; padding: 8px; background-color: #f2f2f2;">CYLINDER PRESSURE</th>
        </tr>

        @foreach ($inspection as $details)
            <tr>
                <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ GetDeptName($details->department) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->resource_code }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ getLocationname($details->location) }}</td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->types_of_equipment }}
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->capacity_of_equipment }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->working_temperature }} </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->sprinkler_head }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->neck_ring }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">
                    {{ $details->cylinder_pressure }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->remarks }}</td>


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
    </table>
    <br>

    <div class="page-break"></div>



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
            @php
                $updated_time = GetFireUpdatedTime(
                    $forklift_details->verified_by,
                    $forklift_details->id,
                    FIRE_MODULAR_INSPECTION,
                    WAITING_FOR_EHS_OFFICER_VERIFICATION,
                );
            @endphp
            @if (isset($updated_time->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($updated_time->created_at) }}
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
            @if (isset($forklift_details->is_passed))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ohc_management.capa') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $forklift_details->is_passed == 1 ? 'Yes' : 'No' }}
                    </td>
                </tr>
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
            @php
                $updated_time = GetFireUpdatedTime(
                    $forklift_details->created_by,
                    $forklift_details->id,
                    FIRE_MODULAR_INSPECTION,
                    WAITING_FOR_CAPA_ACTION,
                );
            @endphp
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($updated_time->created_at) }}
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
            @php
                $updated_time = GetFireUpdatedTime(
                    $forklift_details->verified_by,
                    $forklift_details->id,
                    FIRE_MODULAR_INSPECTION,
                    WAITING_FOR_CAPA_VERIFICATION,
                );
            @endphp
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($updated_time->created_at) }}
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
            @php
                $updated_time = GetFireUpdatedTime(
                    $forklift_details->l1_manager_verified_by,
                    $forklift_details->id,
                    FIRE_MODULAR_INSPECTION,
                    WAITING_FOR_L1_VERIFICATION,
                );
            @endphp
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($updated_time->created_at) }}
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

            @php
                $updated_time = GetFireUpdatedTime(
                    $forklift_details->l2_manager_verified_by,
                    $forklift_details->id,
                    FIRE_MODULAR_INSPECTION,
                    WAITING_FOR_L2_VERIFICATION,
                );
            @endphp
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($updated_time->created_at) }}
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

    <div class="page-break"></div>

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
