<html>

<head>
    <title>Incident | KARAM</title>
    <meta charset="UTF-8">
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
                    <td border="0" style="width:35%;float:left;text-align:left;">
                        <img src="{{ url('public/assets/images/logo-dark.png') }}" style="width:100px;height:70px;">
                    </td>

                    <td border="0"
                        style="width:30%;float:right;text-align:center;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                        {{ $incident_report->sr_no }}
                    </td>
                    <td border="0"
                        style="width:35%;float:right;text-align:right;font-size: 24px;font-weight:bold;font-family: Georgia, serif;">
                        {{ $incident_report->incident_type_name }}
                    </td>
                </tr>
            </table>
        </htmlpageheader>


        <htmlpagefooter name="myFooter1" style="display:none">
            <table width="100%"
                style="width:100%;border:0;background-color: #FFF;border-top: 4px solid #070707;padding-top:0px;padding-bottom:10px;">
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
                        style="width:100%;background-color: #ce0f1f;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                        Incident
                    </td>
                </tr>
            </table>
        </div>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                        Incident Reported By
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Name</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->reported_name }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Designation</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->designation }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Department</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->reported_department }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Employee Code</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->employee_code }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Time of reporting</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->time_of_reporting }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Reporting Media</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ implode(', ', $displayMedia) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Brief Description</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->brief_description }}
                </td>
            </tr>
            <tr>
                <td style="padding:5px;"><b>Existing Evidence</b></td>
                <td style="padding:5px;">:</td>
                <td style="padding:5px;">
                    @if (!$initialincidentevidence->isEmpty())
                        @foreach ($initialincidentevidence as $key => $evidence)
                            <a href="{{ asset($evidence->file_path) }}" target="_blank">
                                <img src="{{ asset($evidence->file_path) }}" alt="Signature" style="max-width: 10%;">
                            </a>
                        @endforeach
                    @else
                        <p>No files available</p>
                    @endif
                </td>
            </tr>
        </table>
        <div style="width:100%;">
            <table style="width:100%;">
                <tr>
                    <td
                        style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                        Incident Reported By
                    </td>
                </tr>
            </table>
        </div>
        <table width="100%" style="width:100%;">
            <tr>
                <td width="50%" style="padding:5px;"><b>Sr. No</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->sr_no }}</td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Date and Time</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ Displaydatetimeformat($incident_report->incident_date_time) }}
                </td>
            </tr>
             <tr>
                <td width="50%" style="padding:5px;"><b>Company</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getCompanyname($incident_report->company_id) }}
                </td>
            </tr>
             <tr>
                <td width="50%" style="padding:5px;"><b>Location</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->location_name }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Unit</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ getUnitname($incident_report->unit_id) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>Shift</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->shift }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>IIR Type</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->incident_type_name }}
                </td>
            </tr>

            <tr>
                <td width="50%" style="padding:5px;"><b>Immediate Action Taken</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    {{ $incident_report->immediate_action_taken }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding:5px;"><b>If any person has injured?</b></td>
                <td width="2%" style="padding:5px;">:</td>
                <td width="48%" style="padding:5px;">
                    @if ($incident_report->anyone_injured == 1)
                        <span style="color: #28a745; font-size: 1.2em;">✓</span> Yes
                    @else
                        <span style="color: #dc3545; font-size: 1.2em;">✗</span> No
                    @endif
                </td>
            </tr>
        </table>
        @if ($incident_report->anyone_injured == 1)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Injured Person Details
                        </td>
                    </tr>
                </table>
            </div>
            <table class="table_card" style="margin-top: 20px;">
                <thead>
                    <tr>
                        <th class="form-label required">Injury Person Type</th>
                        <th class="form-label required">Injury Person Name</th>
                        <th class="form-label required">Injury Person Employee ID</th>
                        <th class="form-label required">Injury Person Designation</th>
                        <th class="form-label required">Injury Person Department</th>
                        <th class="form-label required">Nature of Injury</th>
                        <th class="form-label required">Injury Body Parts</th>
                        <th class="form-label required">Description</th>
                    </tr>
                </thead>
                <tbody id="lesson_learned_block">
                    @foreach ($injury_details as $injury)
                        <tr class="lesson_learned_row">

                            <td>
                                {{ $injury->injury_person_type == 1 ? 'Employee' : ($injury->injury_person_type == 2 ? 'Worker' : 'Others') }}
                            </td>
                            <td>
                                @if ($injury->injury_person_type == 1 || $injury->injury_person_type == 2)
                                    {{ $injury->emp_name }}
                                @else
                                    {{ $injury->injury_person_name }}
                                @endif
                            </td>
                            <td>
                                {{ $injury->emp_id }} </td>
                            <td>{{ $injury->injury_person_designation }}</td>
                            <td>
                                {{-- @if ($injury->injury_person_type == 1 || $injury->injury_person_type == 2)
                                    {{ $injury->department_name }}
                                @else --}}
                                {{ $injury->injury_person_department_id }}
                                {{-- @endif --}}
                            </td>
                            <td>
                                {{ $injury->nature_of_injury == 1 ? 'Major' : ($injury->injury_person_type == 2 ? 'Minor' : 'Fatal') }}
                            </td>
                            <td>
                                @if ($injury->body_part_image)
                                    <a href="{{ admin_url('storage/app/public/uploads/' . $injury->body_part_image) }}"
                                        target="_blank">
                                        <img src="{{ admin_url('storage/app/public/uploads/' . $injury->body_part_image) }}"
                                            alt="Body Parts Image"
                                            style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                    </a>
                                @endif
                            </td>


                            <td>

                                @php
                                    $imgMapDataDecoded = json_decode(
                                        $injury->imgMapdata,
                                        true,
                                    );
                                @endphp
                                @if (isset($imgMapDataDecoded['map']['total']) && is_array($imgMapDataDecoded['map']['total']))
                                    <ul>
                                        @foreach ($imgMapDataDecoded['map']['total'] as $key => $value)
                                            <li>{{ ucfirst($key) }}: {{ $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No data available</p>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if ($incident_report->incident_status >= STATUS_INVESTIGATION_PENDING)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            EHS Head Review
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>Reviewer Name</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getEHSReview->reviewer_name }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Date</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ Displaydateformat($getEHSReview->date) }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>I.M Team members</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getEHSReview->team_member_names }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Remark</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getEHSReview->remark }}
                    </td>
                </tr>
            </table>
        @endif
        @if ($incident_report->incident_status > STATUS_INVESTIGATION_PENDING)
            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Investigation
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>Name of the Witness</b>
                    </td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->witness_name ?? "-"}}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Was anything
                            damaged?</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @php

                            $damageTypes = [
                                1 => 'Man',
                                2 => 'Machine',
                                3 => 'Materials',
                            ];

                            $damagedItems = explode(',', $getInvestigation->anything_damaged);
                            $damagedLabels = array_map(function ($item) use ($damageTypes) {
                                return $damageTypes[$item] ?? 'NA';
                            }, $damagedItems);
                        @endphp

                        {{ implode(', ', $damagedLabels) }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Possible Root
                            Cause
                            Analysis (PRCA)</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @if ($getInvestigation->root_cause_analysis == 1)
                            Why Why Analysis
                        @elseif($getInvestigation->root_cause_analysis == 2)
                            Fish Bone Analysis
                        @else
                            NA
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Remarks (If Any)</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->remark }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Recommended Corrective & Preventive
                            Action</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->corrective_preventive_action }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Investigation Submission Date</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ Displaydateformat($getInvestigation->investigation_date) }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Investigation Submission Time</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->investigation_time }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Risk Analysis</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->risk_analysis == 1 ? 'Yes' : 'No' }}
                    </td>
                </tr>
                @if ($getInvestigation->risk_analysis == 2)
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Risk Analysis Remark</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $getInvestigation->risk_analysis_remark }}
                        </td>
                    </tr>
                @endif
            </table>
            @if ($getInvestigation->root_cause_analysis == 1 && !empty($getwhywhy))
                <h4 style="margin-top: 20px; border-bottom: 2px solid black; padding-bottom: 5px;">Why Why Analysis
                </h4>
                <table border="1" width="100%" cellspacing="0" cellpadding="5" style="text-align: center;">
                    <thead style="background-color: #d3d3d3;">
                        <tr>
                            <th>Why 1</th>
                            <th></th>
                            <th>Why 2</th>
                            <th></th>
                            <th>Why 3</th>
                            <th></th>
                            <th>Why 4</th>
                            <th></th>
                            <th>Why 5</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($getwhywhy as $item)
                            <tr>
                                <td>{{ $item->why_1 }}</td>
                                <td>&#8594;</td> <!-- Unicode right arrow -->
                                <td>{{ $item->why_2 }}</td>
                                <td>&#8594;</td>
                                <td>{{ $item->why_3 }}</td>
                                <td>&#8594;</td>
                                <td>{{ $item->why_4 }}</td>
                                <td>&#8594;</td>
                                <td>{{ $item->why_5 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            @if ($getInvestigation->root_cause_analysis == 2)
                <h4 style="margin-top: 20px; border-bottom: 2px solid black; padding-bottom: 5px;">
                    Fishbone</h4>
                <table width="100%">
                    <tr>
                        <td style="text-align: center">
                            @if (!$getfishbone->isEmpty())
                                @foreach ($getfishbone as $key => $fishbone)
                                    <a href="{{ asset($fishbone->fishbone_image) }}" target="_blank">
                                        <img src="{{ asset($fishbone->fishbone_image) }}" alt="Fishbone Image"
                                            style="width: 80%; max-width: 1000px; height: auto;">
                                    </a>
                                @endforeach
                            @else
                                <p>No files available</p>
                            @endif
                        </td>
                    </tr>
                </table>
            @endif
            <table width="100%">
                <tr>
                    <td width="50%" style="padding:5px;"><b>Recommended Corrective & Preventive Action</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->corrective_preventive_action }}
                    </td>
                </tr>
            </table>

            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            RCPA
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>RCPA ID</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $rcpa->rcpa_id }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Recommended Corrective & Preventive
                            Action</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $rcpa->rcpa }}</td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Responsibility</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ getUsername($rcpa->responsibility)}}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Timeline</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ DisplayDateformat($rcpa->timeline) }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Status</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @if ($rcpa->capa_status == 1)
                            <span class="">Open</span>
                        @elseif($rcpa->capa_status == 2)
                            <span class="">In Progress</span>
                        @elseif($rcpa->capa_status == 3)
                            <span class="">Closed</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Remark if any</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $rcpa->remark }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Main Root Cause</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        {{ $getInvestigation->main_root_cause }}
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>Leading Factors</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @if ($getInvestigation->leading_factors == 1)
                            <span style="color: #28a745; font-size: 1.2em;">✓</span> Human Factor
                            <span style="color: #dc3545; font-size: 1.2em;">✗</span> System Factor
                        @else
                            <span style="color: #dc3545; font-size: 1.2em;">✓</span> Human Factor
                            <span style="color: #28a745; font-size: 1.2em;">✗</span> System Factor
                        @endif
                    </td>
                </tr>
            </table>

            <div style="width:100%;">
                <table style="width:100%;">
                    <tr>
                        <td
                            style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                            Recommended Causes
                        </td>
                    </tr>
                </table>
            </div>
            <table width="100%" style="width:100%;">
                <tr>
                    <td width="50%" style="padding:5px;"><b>UAUC</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @if ($incident_report->ua_uc_yes_no == 1)
                            Yes
                        @else
                            No
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:5px;"><b>UA/UC</b></td>
                    <td width="2%" style="padding:5px;">:</td>
                    <td width="48%" style="padding:5px;">
                        @php
                            $ua_uc_values = explode(',', $incident_report->ua_or_uc);
                        @endphp

                        <span>Unsafe Act: {!! in_array('1', $ua_uc_values) ? '&#10004;' : '&#10008;' !!}</span>
                        <br>
                        <span>Unsafe Condition: {!! in_array('2', $ua_uc_values) ? '&#10004;' : '&#10008;' !!}</span>
                        <br>
                        <span>Natural Causes: {!! in_array('3', $ua_uc_values) ? '&#10004;' : '&#10008;' !!}</span>
                    </td>
                </tr>
            </table>
            @if ($getInvestigation->risk_analysis != 2)
                <div style="width:100%;">
                    <table style="width:100%;">
                        <tr>
                            <td
                                style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                                Risk Level
                            </td>
                        </tr>
                    </table>
                </div>
                <table width="100%" style="width:100%;">
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Risk Level</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            @if ($getrisklevel->risk_level == 1)
                                Low
                            @elseif($getrisklevel->risk_level == 2)
                                Medium
                            @else
                                High
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Description of Corrective Action & Preventive Action</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $getrisklevel->description_ca }}
                        </td>
                    </tr>
                </table>
            @endif
        @endif

        @if ($getInvestigation->root_cause_analysis != 3)

            @if ($rcpa->incident_status >= STATUS_EHSAPPROVAL_PENDING)
                <div style="width:100%;">
                    <table style="width:100%;">
                        <tr>
                            <td
                                style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                                Action submission
                            </td>
                        </tr>
                    </table>
                </div>
                <table width="100%" style="width:100%;">
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Submission By</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ getUsername($rcpa->action_submission_by) }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Date</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($rcpa->action_submission_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:5px;"><b>Evidence</b></td>
                        <td style="padding:5px;">:</td>
                        <td style="padding:5px;">
                            @if (!$capaEvidence->isEmpty())
                                @foreach ($capaEvidence as $key => $capaEvidence)
                                    <a href="{{ asset($capaEvidence->file_path) }}" target="_blank">
                                        <img src="{{ asset($capaEvidence->file_path) }}" alt="Signature" style="max-width: 10%;">
                                    </a>
                                @endforeach
                            @else
                                <p>No files available</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Action Taken</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $rcpa->action_submission_description }}
                        </td>
                    </tr>
                </table>
            @endif

            @if($rcpa->incident_status == STATUS_INCIDENT_CLOSED || $rcpa->incident_status == STATUS_EHSAPPROVAL_REJECTED )
                <div style="width:100%;">
                    <table style="width:100%;">
                        <tr>
                            <td
                                style="width:100%;background-color: #6c757d;color:#FFF;font-weight:bold;padding: 10px 10px 10px;">
                                EHS Approval
                            </td>
                        </tr>
                    </table>
                </div>
                <table width="100%" style="width:100%;">
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Approval Name</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $getEHSApprovalincident->reviewer_name }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Date</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ Displaydateformat($getEHSApprovalincident->date) }}
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding:5px;"><b>Remark</b></td>
                        <td width="2%" style="padding:5px;">:</td>
                        <td width="48%" style="padding:5px;">
                            {{ $getEHSApprovalincident->remark }}
                        </td>
                    </tr>
                </table>
            @endif
        @endif
</body>

</html>
