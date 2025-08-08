<!DOCTYPE html>
<html>

<head>
    <title>Hydrant And Riser Inspection | KARAM</title>

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

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <htmlpageheader name="myHeader1" style="display:block;">
        <table border="0" style="width:100%;border:0;border-bottom: 4px solid #000;background-color: #FFF;">
            <tr style="">
                <td border="0" style="width:50%;float:left;text-align:left;">
                    <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                </td>
                <td border="0"
                    style="width:50%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                    Hydrant And Riser Inspection </td>
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
                    Hydrant And Riser Inspection
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
                {{ getUsername(isset($hydrant_details->created_by) ? $hydrant_details->created_by : '') }}</td>
        </tr>
        <tr>
            <td width="50%" style="padding:5px;"><b>Created Date</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;"> {{ displayDateformat($hydrant_details->created_at) }}</td>
        </tr>
    </table>

    <br>
    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Hydrant And Riser Inspection Observation
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" style="width:100%;">

        <tr>
            <td width="50%" style="padding:5px;"><b>Observation</b></td>
            <td width="2%" style="padding:5px;">:</td>
            <td width="48%" style="padding:5px;">
                {{ $hydrant_details->observation_needed == '1' ? 'YES' : 'NO' }}</td>
        </tr>

    </table>
    <br>
    <div style="width:100%;">
        <table style="width:100%;">
            <tr>
                <td
                    style="width:100%;background-color: #ce0f1f;color:#ffffff;padding: 10px 10px 10px;font-weight:bold;">
                    Hydrant And Riser Inspection Details
                </td>
            </tr>
        </table>
    </div>

    <table
        style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; text-align: center; border: 1px solid black;">
        <!-- Header with Date, Location, etc. -->
        <tr>
            <th colspan="4"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                DATE OF INSPECTION: {{ Displaydateformat($hydrant_details->date_of_inspection) ?? 'N/A' }}
            </th>
            <th colspan="3" style="border: 1px solid black; text-align: left; padding: 6px;">
                LOCATION: {{ getLocationName($hydrant_details->location) }}
            </th>
            <th colspan="3" style="border: 1px solid black; text-align: left; padding: 6px;">
                Exaction LOCATION: {{ $hydrant_details->exact_location }}
            </th>
            <th colspan="4"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                SHIFT: {{ getShift($hydrant_details->shift_id) ?? 'N/A' }}
            </th>
        </tr>
        <tr>
            <th colspan="5"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                NEXT DUE: {{ Displaydateformat($hydrant_details->next_due) ?? 'N/A' }}
            </th>
            <th colspan="5"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                UNIT: {{ getUnitname($hydrant_details->unit) ?? 'N/A' }}
            </th>
            <th colspan="4"
                style="border: 1px solid black; padding: 8px; background-color: #f2f2f2; text-align: left;">
                FREQUENCY: {{ getFrequencyname($hydrant_details->frequency) ?? 'N/A' }}
            </th>
        </tr>


        <!-- Main Table Header -->
        <tr>
            <th rowspan="3" style="border: 1px solid black; padding: 8px;">SL</th>
            <th rowspan="3" style="border: 1px solid black; padding: 8px;">EXACT LOCATION</th>
            <th rowspan="3" style="border: 1px solid black; padding: 8px;">HYDRANT NO.</th>
            <th colspan="8" style="border: 1px solid black; padding: 8px;">CHECK ITEMS</th>
            <th rowspan="3" style="border: 1px solid black; padding: 8px;">APPROACH</th>
            <th rowspan="3" style="border: 1px solid black; padding: 8px;">REMARKS</th>
        </tr>
        <tr>
            <th colspan="6" style="border: 1px solid black; padding: 8px;">CONDITION OF LANDING VALVE</th>
            <th colspan="2" style="border: 1px solid black; padding: 8px;">CONDITION OF ISV</th>
        </tr>
        <tr>
            <th style="border: 1px solid black; padding: 8px;">LUGS</th>
            <th style="border: 1px solid black; padding: 8px;">RUBBER WASHER</th>
            <th style="border: 1px solid black; padding: 8px;">CHECK NUT</th>
            <th style="border: 1px solid black; padding: 8px;">SPINDLE WHEEL</th>
            <th style="border: 1px solid black; padding: 8px;">BLANK CAP</th>
            <th style="border: 1px solid black; padding: 8px;">FEMALE COUPLING</th>
            <th style="border: 1px solid black; padding: 8px;">LEVER</th>
            <th style="border: 1px solid black; padding: 8px;">FLOW TEST</th>
        </tr>


        <!-- Loop through inspection data -->
        @foreach ($inspection as $details)
            <tr>
                <td style="border: 1px solid black; padding: 8px;">{{ $loop->iteration }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ ($details->location_check_id) }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->hydrant_no }}</td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->lugs_id == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Present</span>
                    @elseif ($details->lugs_id == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Missing</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->rubber_washer == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Intact</span>
                    @elseif ($details->rubber_washer == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Damaged</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->check_nut == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Present</span>
                    @elseif ($details->check_nut == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Missing</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->spindle_wheel == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Functional</span>
                    @elseif ($details->spindle_wheel == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Non-Functional</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->blank_cap == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Present</span>
                    @elseif ($details->blank_cap == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Missing</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->female_coupling == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Present</span>
                    @elseif ($details->female_coupling == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Missing</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">
                    @if ($details->lever == 1)
                        <span style="color: green; font-weight: bold;">&#10004; Functional</span>
                    @elseif ($details->lever == 0)
                        <span style="color: red; font-weight: bold;">&#10060; Non-Functional</span>
                    @else
                        <span style="color: gray; font-weight: bold;">N/A</span>
                    @endif
                </td>

                <td style="border: 1px solid black; padding: 8px;">{{ $details->flow_test == '1' ? 'Good' : 'Bad' }}
                </td>
                <td style="border: 1px solid black; padding: 8px;">{{ getOkNotokStatus($details->approach) }}</td>
                <td style="border: 1px solid black; padding: 8px;">{{ $details->remarks }}</td>
            </tr>
        @endforeach


        {{-- @php
            $prepared_by_signature = GetFireSignature(
                $hydrant_details->created_by,
                $hydrant_details->id,
                HYDRANT_RISER,
            );
            $verified_by_signature = GetFireSignature(
                $hydrant_details->verified_by,
                $hydrant_details->id,
                HYDRANT_RISER,
            );
            $verified_by_signature = GetFireSignature(
                $hydrant_details->approved_by,
                $hydrant_details->id,
                HYDRANT_RISER,
            );
        @endphp
        <tr>
            <td colspan="5"
                style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                <img src="{{ admin_url($prepared_by_signature) }}" alt="Checked By Signature" style="height: 50px;">
                <div>Checked & Prepared By: {{ getUsername($hydrant_details->created_by) }}</div>
            </td>
            <td colspan="5"
                style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                    style="height: 50px;">
                <div>Verified By: {{ getUsername($hydrant_details->verified_by) }}</div>

            </td>
            <td colspan="5"
                style="border: 2px solid black; text-align: center; font-weight: bold; vertical-align: middle;">
                <img src="{{ admin_url($verified_by_signature) }}" alt="Verified By Signature"
                    style="height: 50px;">
                <div>Approved By: {{ getUsername($hydrant_details->approved_by) }}</div>

            </td>
        </tr> --}}

        <tr>
            <td colspan="5" style="border: 1px solid black; padding: 6px; text-align: center;">
                <div class="view_data">
                    @if (!empty($hydrant_details->checked_by))
                        <p style="margin: 0;">Checked By:- {{ getUsername($hydrant_details->checked_by) }}</p>
                    @else
                        <p style="margin: 0;">Checked By:- Not yet checked</p>
                    @endif
                </div>
            </td>
            <td colspan="4" style="border: 1px solid black; padding: 6px; text-align: center;">
                <div class="view_data">
                    @if (!empty($hydrant_details->verified_by))
                        <p style="margin: 0;">Verified By:- {{ getUsername($hydrant_details->verified_by) }}</p>
                    @else
                        <p style="margin: 0;">Verified By:- Not yet verified</p>
                    @endif
                </div>
            </td>
            <td colspan="5" style="border: 1px solid black; padding: 6px; text-align: center;">
                <div class="view_data">
                    @if (!empty($hydrant_details->approved_by))
                        <p style="margin: 0;">Approved By:- {{ getUsername($hydrant_details->approved_by) }}</p>
                    @else
                        <p style="margin: 0;">Approved By:- Not yet approved</p>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>
    <br>

    @if ($hydrant_details->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
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
            @if (isset($hydrant_details->verified_by))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.verified_by') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ getUserName($hydrant_details->verified_by) }}</td>
                </tr>
            @endif
            @if (isset($hydrant_details->is_passed))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('ohc_management.capa') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ isset($hydrant_details->is_passed) && $hydrant_details->is_passed == 1 ? 'Yes' : 'NO' }}
                    </td>
                </tr>
            @endif
            @if (isset($hydrant_details->created_at))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;"> {{ Displaydateformat($hydrant_details->created_at) }}
                    </td>
                </tr>
            @endif
            @if (isset($hydrant_details->approved_by))
                @if ($hydrant_details->verified_by == $hydrant_details->approved_by)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUsername($hydrant_details->approved_by) }}
                        </td>
                    </tr>
                @endif
            @endif
            @if (isset($hydrant_details->capa_recomendation))
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_recomendation') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $hydrant_details->capa_recomendation }}
                    </td>
                </tr>
            @else
                <tr>
                    <td width="50%" style="padding:5px;"><b>{{ __('inspection.remarks') }}</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $hydrant_details->remarks }}
                </tr>
            @endif
        </table>
        <br>
    @endif

    @if (isset($hydrant_details->capa_remarks))
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
                <td width="48%" style="padding:5px;"> {{ getUserName($hydrant_details->created_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($hydrant_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_action_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $hydrant_details->capa_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if ($hydrant_details->capa_ehs_remarks)
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
                <td width="48%" style="padding:5px;"> {{ getUserName($hydrant_details->verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($hydrant_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.capa_reverifcation_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $hydrant_details->capa_ehs_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif


    @if (isset($hydrant_details->level_one_manager_remarks))
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
                    {{ getUserName($hydrant_details->l1_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($hydrant_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_one_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $hydrant_details->level_one_manager_remarks }}
                </td>
            </tr>
        </table>
        <br>
    @endif

    @if (isset($hydrant_details->level_two_manager_remarks))
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
                    {{ getUserName($hydrant_details->l2_manager_verified_by) }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.date') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;"> {{ Displaydateformat($hydrant_details->created_at) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.approved_by') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUsername($hydrant_details->approved_by) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>{{ __('inspection.level_two_manager_remarks') }}</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $hydrant_details->level_two_manager_remarks }}
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
    <div class="page-break"></div>

</body>

</html>
