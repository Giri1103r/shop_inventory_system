@extends('admin.layouts.admin')
@section('title', 'Initial Incident/Accident Report')
@section('pageurl', admin_url('incident/initial-incident/list'))


@section('content')
    <style>
        /* Main container */
        .fishbone-container {
            display: inline-grid;
            grid-template-columns: repeat(4, auto);
            grid-template-rows: auto .2em auto;
            padding-left: 2em;
            font-family: Arial;
            --bone-color: #85A0B2;
            --yellow: #FDBE22;
            --green: #69E982;
            --blue: #5CB2FB;
        }

        .cause {
            display: flex;
            flex-direction: column;
            transform: skew(20deg);
            transform-origin: bottom;
            margin-left: .8em;
        }

        .rootcause {
            text-align: center;
            position: relative;
            left: 100%;
            transform: translateX(-50%) skewX(-20deg);
            font-size: 1.5em;
            color: #fff;
            padding: .2em;
            border-radius: .2em;

            &.yellow {
                background-color: var(--yellow);
            }

            &.green {
                background-color: var(--green);
            }

            &.blue {
                background-color: var(--blue);
            }
        }

        .subcause {
            flex-grow: 1;
            border-right: .2em solid var(--bone-color);
            padding-bottom: .75em;
            padding-top: .75em
        }

        .stat {
            text-align: right;
            padding-right: 3em;
            position: relative;
            transform: skewX(-20deg);
            line-height: 1.5em;
            font-size: 1em;
        }

        .stat:before {
            content: '';
            display: block;
            background-color: var(--bone-color);
            position: absolute;
            width: 3em;
            height: .2em;
            right: 0;
            top: 50%;
            transform: translate(.2em, -50%);
        }

        .line {
            grid-column-start: 1;
            grid-column-end: 4;
            background-color: var(--bone-color);

            ~.cause {
                transform: skewX(-20deg);
                transform-origin: top;
            }

            ~.cause .rootcause {
                transform: translateX(-50%) skewX(20deg);
            }

            ~.cause .stat {
                transform: skewX(20deg);
            }
        }

        .defect-spacer-top {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 1;
            grid-row-end: 2;
        }

        .defect {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 2;
            grid-row-end: 3;
        }

        .defect-spacer-bottom {
            grid-column-start: 4;
            grid-column-end: 4;
            grid-row-start: 3;
            grid-row-end: 4;
        }

        .defect-text {
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            padding: 1em;
            margin-left: .5em;
            background-color: var(--bone-color);
            border-radius: .5em;
            color: #fff;
            text-align: center;
        }

        .subcause .stat {
            margin-bottom: 15px;
            /* Adjust the spacing between input fields */
        }

        .subcause {
            margin-bottom: 20px;
            /* Add spacing between rows of input fields */
        }
    </style>
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('incident/initial-incident/list') }}"></x-button-back>

                                </div>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Incident Reported By</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $incident_report->employee_code }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Name</label>
                                        <div class="view_data">
                                            {{ $incident_report->reported_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Designation</label>
                                        <div class="view_data">
                                            {{ $incident_report->designation }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ $incident_report->reported_department }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Time of reporting</label>
                                        <div class="view_data">
                                            {{ $incident_report->time_of_reporting }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Reporting Media</label>
                                        <div class="view_data">
                                            {{ implode(', ', $displayMedia) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Incident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $incident_report->sr_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($incident_report->incident_date_time) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label ">Company</label>
                                        <div class="view_data">
                                            {{ getCompanyname($incident_report->company_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label ">Location</label>
                                        <div class="view_data">
                                            {{ $incident_report->location_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname($incident_report->unit_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label require">Shift</label>
                                        <div class="view_data">
                                            {{ $incident_report->shift }}
                                        </div>
                                    </div>
                                  
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Exact Location</label>
                                        <div class="view_data">
                                            {{ $incident_report->exact_location }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">IIR Type</label>
                                        <div class="view_data">
                                            {{ $incident_report->incident_type_name }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label require">Brief Description</label>
                                        <div class="view_data">
                                            {{ $incident_report->brief_description }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Existing Evidence</label>
                                        @if (!$initialincidentevidence->isEmpty())
                                            <div class="row">
                                                @foreach ($initialincidentevidence as $key => $evidence)
                                                    <div class="col-md-3 col-sm-6 mb-2">
                                                        <div class="existing-evidence text-center">
                                                            <a href="{{ asset($evidence->file_path) }}" target="_blank">
                                                                <img src="{{ asset($evidence->file_path) }}" alt="Evidence"
                                                                    class="img-fluid rounded shadow"
                                                                    style="max-width: 20%; height: auto;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">Immediate Action Taken</label>
                                        <div class="view_data">
                                            {{ $incident_report->immediate_action_taken }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">If any person has injured?</label>
                                        <div class="view_data">
                                            @if ($incident_report->anyone_injured == 1)
                                                <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                                                Yes
                                                <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                                                No
                                            @else
                                                <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                                                Yes
                                                <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                                                No
                                            @endif
                                        </div>
                                    </div>
                                    @if ($incident_report->anyone_injured == 1)
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Injured Person Details</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Injury Person Type</th>
                                                        <th>Injury Person Name</th>
                                                        <th>Injury Person Employee ID</th>
                                                        <th>Injury Person Designation</th>
                                                        <th>Injury Person Department</th>
                                                        <th>Nature of Injury</th>
                                                        <th>Injury Body Parts</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($injury_details as $injury)
                                                        <tr>
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
                                                            <td>{{ $injury->emp_id }}</td>
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
                                                                @if ($imgMapDataDecoded)
                                                                    <ul>
                                                                        @foreach ($imgMapDataDecoded['map']['total'] as $key => $value)
                                                                            <li>{{ ucfirst($key) }}:
                                                                                {{ $value }}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if ($rcpa->incident_status >= STATUS_ACTION_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Accelerating Incident Investigations</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Reviewer Name</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->reviewer_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getEHSReview->date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Target Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($incident_report->target_date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="team_id" class="form-label">I.M Team members</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->team_member_names }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="team_id" class="form-label">Incident/Accident Investigation
                                                Report Prepared by</label>
                                            <div class="view_data">
                                                {{ getUsername($incident_report->investigation_reported_by) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label">Remark</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->remark }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            @if ($rcpa->incident_status >= STATUS_ACTION_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Investigation</h4>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Name of the
                                                Witness</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->witness_name ?? "-"}}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Was anything damaged?</label>
                                            <div class="view_data">
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
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Possible Root
                                                Cause
                                                Analysis (PRCA)</label>
                                            <div class="view_data">
                                                @if ($getInvestigation->root_cause_analysis == 1)
                                                    Why Why Analysis
                                                @elseif($getInvestigation->root_cause_analysis == 2)
                                                    Fish Bone Analysis
                                                @else
                                                    NA
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Remarks (If Any)</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->remark }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Investigation Submission Date</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getInvestigation->investigation_date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Investigation Submission Time</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->investigation_time }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Risk Analysis</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->risk_analysis == 1 ? 'Yes' : 'No' }}
                                            </div>
                                        </div>

                                        @if ($getInvestigation->risk_analysis == 2)
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Risk Analysis Remark</label>
                                                <div class="view_data">
                                                    {{ $getInvestigation->risk_analysis_remark }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($getInvestigation->root_cause_analysis == 1)
                                        <div class="row mt-3 whywhy">
                                            <div class="card p-3">
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                                    <h4 class="text-dark mb-0">Why Why Analysis</h4>
                                                </div>

                                                <!-- Table -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered whywhyanalysis text-center">
                                                        <thead class="table-dark">
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
                                                        <tbody id="whywhyanalysisBody">
                                                            @foreach ($getwhywhy as $index => $item)
                                                                <tr id="RowwhywhyanalysisView{{ $index }}">
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_first]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_1 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_second]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_2 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_third]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_3 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_forth]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_4 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_fifth]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_5 }}" readonly>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($getInvestigation->root_cause_analysis == 2)
                                        <div class="row m-5 p-3 fishbone">
                                            <div class="fishbone-container" style="text-align: center;">
                                                <!-- First Cause -->
                                                <div class="cause">
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[first][root_cause]"
                                                            value="{{ $fishboneData['first']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['first']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[first][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Second Cause -->
                                                <div class="cause">
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[second][root_cause]"
                                                            value="{{ $fishboneData['second']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['second']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[second][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Third Cause -->
                                                <div class="cause">
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fourth][root_cause]"
                                                            value="{{ $fishboneData['fourth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['third']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[third][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                                <div class="line"></div>
                                                <!-- Fourth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['fourth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[fourth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fourth][root_cause]"
                                                            value="{{ $fishboneData['fourth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- Fifth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['fifth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[fifth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fifth][root_cause]"
                                                            value="{{ $fishboneData['fifth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- Sixth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['sixth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[sixth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[sixth][root_cause]"
                                                            value="{{ $fishboneData['sixth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="defect-spacer-top"></div>

                                                <!-- Root Cause -->
                                                <div class="defect">
                                                    <div class="defect-text">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[root_cause][main]"
                                                            value="{{ $fishboneData['root_cause']['main'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="defect-spacer-bottom"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">Recommended Corrective & Preventive
                                            Action</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->corrective_preventive_action }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">RCPA</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">RCPA ID</label>
                                            <div class="view_data">
                                                {{ $rcpa->rcpa_id }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Recommended Corrective & Preventive
                                                Action</label>
                                            <div class="view_data">
                                                {{ $rcpa->rcpa }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Responsibility</label>
                                            <div class="view_data">
                                                {{ getUsername($rcpa->responsibility) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Timeline</label>
                                            <div class="view_data">
                                                {{ DisplayDateformat($rcpa->timeline) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">Status</label>
                                            <div class="view_data">
                                                @if ($rcpa->capa_status == 1)
                                                    <span class="badge bg-success">Open</span>
                                                @elseif($rcpa->capa_status == 2)
                                                    <span class="badge bg-warning text-dark">In Progress</span>
                                                @elseif($rcpa->capa_status == 3)
                                                    <span class="badge bg-secondary">Closed</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">Remark if any</label>
                                            <div class="view_data">
                                                {{ $rcpa->remark }}
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">Main Root Cause</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->main_root_cause }}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">Leading Factors</label>
                                            <div class="view_data d-flex gap-3">
                                                <span>
                                                    <i
                                                        class="fas {{ $getInvestigation->leading_factors == 1 ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i>
                                                    Human Factor
                                                </span>
                                                <span>
                                                    <i
                                                        class="fas {{ $getInvestigation->leading_factors == 2 ? ' fa-check-circle text-success' : 'fa-times-circle text-danger' }}"></i>
                                                    System Factor
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Recommended Causes</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12 form-input">
                                            <label for="name" class="form-label">UAUC</label>
                                            <div class="view_data">
                                                @if ($incident_report->ua_uc_yes_no == 1)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </div>
                                        </div>

                                        @if ($incident_report->ua_uc_yes_no == 1)
                                            <div class="mb-3 col-md-12 form-input">
                                                <label for="name" class="form-label">UA/UC</label>
                                                <div class="view_data">
                                                    @php
                                                        $ua_uc_values = explode(',', $incident_report->ua_or_uc);
                                                    @endphp

                                                    <span>Unsafe Act: {!! in_array('1', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                    <br>
                                                    <span>Unsafe Condition: {!! in_array('2', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                    <br>
                                                    <span>Natural Causes: {!! in_array('2', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                </div>


                                            </div>
                                            {{-- <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label">Description of UAUC</label>
                                                <div class="view_data">
                                                    {{ $incident_report->description_uauc }}
                                                </div>
                                            </div> --}}
                                        @endif
                                    </div>

                                    @if ($getInvestigation->risk_analysis != 2)
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Risk Level</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md-12 form-input">
                                                <label for="name" class="form-label">Risk Level</label>
                                                <div class="view_data">
                                                    @if ($getrisklevel->risk_level == 1)
                                                        Low
                                                    @elseif($getrisklevel->risk_level == 2)
                                                        Medium
                                                    @else
                                                        High
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label">Description of Corrective Action & Preventive Action</label>
                                                <div class="view_data">
                                                    {{ $getrisklevel->description_ca }}
                                                </div>
                                            </div>

                                        </div>
                                    @endif
                                </div>
                            @endif

                            @if ($rcpa->incident_status == STATUS_ACTION_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Action submission</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="action_submission"
                                            action="{{ admin_url('incident/initial-incident/actiontaken/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="incident_id"
                                                    id="incident_id" value="{{ encryptId($incident_report->id) }}">
                                                <input type="hidden" class="form-control" name="rcpa_id"
                                                    id="incident_id" value="{{ encryptId($rcpa->id) }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Submission By</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="action_submission_date"
                                                            id="" class="form-control" placeholder="Date"
                                                            readonly value="{{ todaydate() }}">
                                                    </div>
                                                </div>
                                                <div id="file-upload-container" class="row mt-3">
                                                    <div class="col-12 mb-3">
                                                        <button class="btn btn-primary addmorebutton" type="button"
                                                            id="dynamic-add-more">
                                                            Add
                                                        </button>
                                                    </div>

                                                    <div class="col-md-4 mb-3 file-upload-block" id="file-upload-0">
                                                        <label for="evidence_0"
                                                            class="form-label require">Evidence</label>
                                                        <input type="file" class="form-control validate-file-required"
                                                            name="evidence[0][]" id="evidence_0" multiple>
                                                        <div class="text-danger"></div>
                                                        <small>Allowed file types: png, jpeg , jpg</small>
                                                        <div class="preview-container mt-2 d-flex flex-wrap gap-2"
                                                            id="preview-container-0"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Action Taken</label>
                                                        <textarea name="action_submission_description" id="action_submission_description" class="form-control"
                                                            rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('incident/initial-incident/list') }}"></x-button-cancel>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @elseif($rcpa->incident_status > STATUS_ACTION_PENDING && $rcpa->incident_status != STATUS_EHSAPPROVAL_REJECTED)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Action submission</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Submission By</label>
                                            <div class="view_data">
                                                {{ getUsername($rcpa->action_submission_by) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($rcpa->action_submission_date) }}
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Evidence</label>
                                            @if (!$capaEvidence->isEmpty())
                                                <div class="row">
                                                    @foreach ($capaEvidence as $key => $capaEvidence)
                                                        <div class="col-md-3 col-sm-6 mb-2">
                                                            <div class="existing-evidence text-center">
                                                                <a href="{{ asset($capaEvidence->file_path) }}"
                                                                    target="_blank">
                                                                    <img src="{{ asset($capaEvidence->file_path) }}"
                                                                        alt="Evidence" class="img-fluid rounded shadow"
                                                                        style="max-width: 20%; height: auto;">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Action Taken</label>
                                            <div class="view_data">
                                                {{ $rcpa->action_submission_description }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            @if ($rcpa->incident_status == STATUS_EHSAPPROVAL_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Approval</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="ehs_approval"
                                            action="{{ admin_url('incident/initial-incident/ehApproval/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="incident_id"
                                                    id="incident_id" value="{{ encryptId($incident_report->id) }}">
                                                <input type="hidden" class="form-control" name="rcpa_id" id="rcpa_id"
                                                    value="{{ encryptId($rcpa->id) }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Approval By</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="date" id=""
                                                            class="form-control" placeholder="Date" readonly
                                                            value="{{ todaydate() }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="d-flex float-end gap-2 mx-auto">
                                                <button type="submit" name="approve" value="approve"
                                                    class="btn btn-success w-100">Approve</button>
                                                <button type="submit" name="reject" value="reject"
                                                    class="btn btn-danger w-100">Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @elseif($rcpa->incident_status == STATUS_INCIDENT_CLOSED)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Approval</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Approval By</label>
                                            <div class="view_data">
                                                {{ $getEHSApprovalincident->reviewer_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getEHSApprovalincident->date) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label">Remark</label>
                                            <div class="view_data">
                                                {{ $getEHSApprovalincident->remark }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
            flatpickr("#target_date", {
                dateFormat: "d-m-Y",
                minDate: "today" // Allows only future dates
            });
            const maxUploads = 5;

            $('#dynamic-add-more').on('click', function() {
                let currentFileUploads = $('.file-upload-block').length;

                if (currentFileUploads >= maxUploads) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!',
                        text: 'Maximum 5 records only.',
                    });
                    return;
                }

                // Create the new file upload block
                let newFileUploadBlock = `
                <div class="col-md-4 mb-3 file-upload-block" id="file-upload-${currentFileUploads}">
                    <label for="evidence_${currentFileUploads}" class="form-label require">Evidence</label>
                    <input type="file" class="form-control  validate-file-required"
                        name="evidence[${currentFileUploads}][]" id="evidence_${currentFileUploads}" multiple>
                    <div class="text-danger"></div>
                    <small>Allowed file types: png, jpeg , jpg</small>
                    <button type="button" class="btn btn-danger btn-sm remove-upload-block">
                        <i class="fas fa-trash"></i>
                    </button>
                    <div class="preview-container mt-2 d-flex flex-wrap gap-2" id="preview-container-${currentFileUploads}"></div>
                </div>
            `;

                // Append new block
                $('#file-upload-container').append(newFileUploadBlock);

                $('#evidence_' + currentFileUploads).rules("add", {
                    required: true,
                    extension: "png|jpeg|jpg",
                    messages: {
                        required: "This field is required.",
                        extension: "Allowed file types: png, jpeg, jpg",
                    }
                });


            });

            // Handling file input validation for dynamic removal of blocks (if applicable)
            $(document).on('click', '.remove-upload-block', function() {
                $(this).closest('.file-upload-block').remove();
            });



            $(document).on('change', 'input[type="file"]', function(event) {
                let input = $(this);
                let fileInputId = input.attr('id').split('_')[2];
                let previewContainer = $('#preview-container-' + fileInputId);

                previewContainer.html("");

                let files = event.target.files;
                if (files.length > 0) {
                    Array.from(files).forEach(file => {
                        if (file.type.startsWith("image/")) {
                            let reader = new FileReader();
                            reader.onload = function(e) {
                                let img = $("<img>").attr("src", e.target.result)
                                    .addClass("img-thumbnail")
                                    .css({
                                        width: "100px",
                                        height: "100px",
                                        objectFit: "cover",
                                        marginRight: "5px"
                                    });

                                previewContainer.append(img);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            });


            $('#team_id,#reported_by').select2({
                placeholder: "Select Team Members",
                allowClear: true,
                closeOnSelect: true,
                ajax: {
                    url: "{{ admin_url('incident/initial-incident/teamMembers') }}",
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // Search query
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log("Error in AJAX request:", textStatus, errorThrown);
                    }
                },
                minimumInputLength: 3,
                width: '100%',
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });


            // $('#team_id').select2({
            //     placeholder: "Select Team members",
            //     allowClear: true,
            //     closeOnSelect: false,
            // });

            $('#ehs_head_review').validate({
                rules: {
                    "team_member[]": {
                        required: true,
                    },
                    reported_by: {
                        required: true,
                    },
                    target_date: {
                        required: true,
                    },
                    remark: {
                        required: true,
                        minlength: 10,
                        maxlength: 2000,
                    }
                },
                messages: {
                    "team_member[]": {
                        required: "Please select a team member.",
                    },
                    reported_by: {
                        required: "Please select a Incident/Accident Investigation Report Prepared by.",
                    },
                    target_date: {
                        required: "Please select a Target Date.",
                    },
                    remark: {
                        required: "Please provide a remark.",
                        minlength: "Minimum 10 characters required.",
                        maxlength: "Maximum 2000 characters allowed.",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
            $('#ehs_head_verify').validate({
                rules: {
                    "team_member[]": {
                        required: true,
                    },
                    target_date: {
                        required: true,
                    },
                    remark: {
                        required: true,
                        minlength: 10,
                        maxlength: 2000,
                    }
                },
                messages: {
                    "team_member[]": {
                        required: "Please select a Assignee.",
                    },
                    target_date: {
                        required: "Please provide Target Date.",

                    },
                    remark: {
                        required: "Please provide a remark.",
                        minlength: "Minimum 10 characters required.",
                        maxlength: "Maximum 2000 characters allowed.",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
            $('#action_submission').validate({
                rules: {

                    action_submission_description: {
                        required: true,
                        minlength: 10,
                        maxlength: 2000,
                    },
                    'evidence[0][]': {
                        extension: "png|jpeg|jpg",
                    }
                },
                messages: {

                    action_submission_description: {
                        required: "Please provide Action Taken.",
                        minlength: "Minimum 10 characters required.",
                        maxlength: "Maximum 2000 characters allowed.",
                    },
                    'evidence[0][]': {
                        extension: "Allowed file types: png, jpeg, jpg",

                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
            $('#ehs_approval').validate({
                rules: {

                    remark: {
                        required: true,
                        minlength: 10,
                        maxlength: 2000,
                    }
                },
                messages: {

                    remark: {
                        required: "Please provide remark.",
                        minlength: "Minimum 10 characters required.",
                        maxlength: "Maximum 2000 characters allowed.",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
        });
    </script>
@endpush
