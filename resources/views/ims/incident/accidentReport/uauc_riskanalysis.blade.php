@extends('admin.layouts.admin')
@section('title', 'UAUC and Riskanalysis')
@section('pageurl', admin_url('accidentReport/list'))


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
            {{-- <h4 class="text-black">{{ __('accidentReport Edit') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('accidentReport/list') }}"></x-button-back>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Accident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $accident_report->accident_report_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($accident_report->date_and_time) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $accident_report->emp_code }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ $accident_report->unit_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Designation</label>
                                        <div class="view_data">
                                            {{ $accident_report->designation }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ $accident_report->department_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ $accident_report->shift }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Accident Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->location_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Exact Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->exact_location }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Address of the injured person</label>
                                        <div class="view_data">
                                            {{ $accident_report->address_of_the_injuredperson }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($accident_report->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($accident_report->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($accident_report->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Head Review</h4>
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
                                        <label for="team_id" class="form-label">Assign Team
                                            members</label>
                                        <div class="view_data">
                                            {{ $getEHSReview->team_member_names }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">Remark</label>
                                        <div class="view_data">
                                            {{ $getEHSReview->remark }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card-body">
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
                                                <th>Injury Body Parts</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($accident_investigation_injury as $injury)
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
                                                        @if ($injury->injury_person_type == 1 || $injury->injury_person_type == 2)
                                                            {{ $injury->department_name }}
                                                        @else
                                                            {{ $injury->injury_person_department_id }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($injury->body_part_image)
                                                            <a href="{{ admin_url('storage/app/private/' . $injury->body_part_image) }}" target="_blank">
                                                                <img src="{{ admin_url('storage/app/private/' . $injury->body_part_image) }}"
                                                                     alt="Body Parts Image"
                                                                     style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                            </a>
                                                        @endif
                                                    </td>
                                                 
                                                    
                                                    <td>
                                                        @php
                                                            $imgMapDataDecoded = json_decode($injury->imgMapdata, true);
                                                        @endphp
                                                        @if ($imgMapDataDecoded)
                                                            <ul>
                                                                @foreach ($imgMapDataDecoded['map']['total'] as $key => $value)
                                                                    <li>{{ ucfirst($key) }}: {{ $value }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
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
                                            {{ $getInvestigation->witness_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Was anything
                                            damaged?</label>
                                        <div class="view_data">

                                            @if ($getInvestigation->anything_damaged == 1)
                                                Man
                                            @elseif($getInvestigation->anything_damaged == 2)
                                                Machine
                                            @elseif($getInvestigation->anything_damaged == 3)
                                                Materials
                                            @else
                                                NA
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">HIRA</label>
                                        <div class="view_data">
                                            @if (!empty($getInvestigation->hira_moc[0]['hira_name']))
                                                {{ $getInvestigation->hira_moc[0]['hira_name'] }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">MOC</label>
                                        <div class="view_data">
                                            @if (!empty($getInvestigation->hira_moc[0]['moc_name']))
                                                {{ $getInvestigation->hira_moc[0]['moc_name'] }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label">Possible Root
                                            Cause
                                            Analysis (PRCA)</label>
                                        <div class="view_data">
                                            @if ($getInvestigation->root_cause_analysis == 1)
                                                Why - Why Analysis
                                            @elseif($getInvestigation->root_cause_analysis == 2)
                                                Fish Bone Analysis
                                            @else
                                                NA
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Immediate action taken
                                            (If any)</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->action_taken }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Was the injured person receiving any treatment
                                            at present?</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->is_treatment == 1 ? 'Yes' : 'No' }}
                                        </div>
                                    </div>

                                    @if ($getInvestigation->is_treatment == 1)
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Details</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->details }}
                                            </div>
                                        </div>
                                    @endif


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Recommended Corrective & Preventive
                                            Action</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->corrective_preventive_action }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Responsible Person</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->responsible_person }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Target Date</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($getInvestigation->target_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Remarks (If Any)</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->remark }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Risk Analysis</label>
                                        <div class="view_data">
                                            {{ $getInvestigation->risk_analysis == 1 ? 'Yes':'No' }}
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
                                                                        class="form-control" value="{{ $item->why_1 }}"
                                                                        readonly>
                                                                </td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[{{ $index }}][whywhyanalysis_second]"
                                                                        class="form-control" value="{{ $item->why_2 }}"
                                                                        readonly>
                                                                </td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[{{ $index }}][whywhyanalysis_third]"
                                                                        class="form-control" value="{{ $item->why_3 }}"
                                                                        readonly>
                                                                </td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[{{ $index }}][whywhyanalysis_forth]"
                                                                        class="form-control" value="{{ $item->why_4 }}"
                                                                        readonly>
                                                                </td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[{{ $index }}][whywhyanalysis_fifth]"
                                                                        class="form-control" value="{{ $item->why_5 }}"
                                                                        readonly>
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[first][root_cause]"
                                                        value="{{ $fishboneData['first']['root_cause'] ?? '' }}" readonly>
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[second][root_cause]"
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[fourth][root_cause]"
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[fourth][root_cause]"
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[fifth][root_cause]"
                                                        value="{{ $fishboneData['fifth']['root_cause'] ?? '' }}" readonly>
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
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[sixth][root_cause]"
                                                        value="{{ $fishboneData['sixth']['root_cause'] ?? '' }}" readonly>
                                                </div>
                                            </div>

                                            <div class="defect-spacer-top"></div>

                                            <!-- Root Cause -->
                                            <div class="defect">
                                                <div class="defect-text">
                                                    <input type="text" class="form-control" placeholder="Enter value"
                                                        name="fishbone[root_cause][main]"
                                                        value="{{ $fishboneData['root_cause']['main'] ?? '' }}" readonly>
                                                </div>
                                            </div>

                                            <div class="defect-spacer-bottom"></div>
                                        </div>
                                    </div>

                                @endif

                            </div>
                            @if ($accident_report->accident_status == STATUS_UAUC_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">UAUC</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="uauc"
                                            action="{{ admin_url('accidentReport/uauc/submit') }}">
                                            @csrf

                                            <input type="hidden" name="accident_id" id="accident_id"
                                                value="{{ encryptId($accidentId) }}">

                                            <div class="row">
                                                <div class="col-md-12 mt-3 ua_uc_yes_no">
                                                    <div class="form-group form-input">
                                                        <label for="ua_uc" class="form-label require">UAUC</label><br>
                                                        <input type="radio" id="ua_uc_yes" name="ua_uc_yes_no"
                                                            value="1">
                                                        <label for="ua_uc_yes">Yes</label>
                                                        <input type="radio" id="ua_uc_no" name="ua_uc_yes_no"
                                                            value="2">
                                                        <label for="ua_uc_no">No</label>

                                                    </div>
                                                </div>

                                                <div class = "ua_uc_div" style="display: none;">
                                                    <div class="col-md-12 mt-3">
                                                        <div class="form-group form-input">
                                                            <input type="checkbox" id="ua" name="ua_or_uc[]"
                                                                value="1">
                                                            <label for="ua">UA</label>
                                                            <input type="checkbox" id="uc" name="ua_or_uc[]"
                                                                value="2">
                                                            <label for="uc">UC</label>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label">Description of UAUC</label>
                                                            <textarea class="form-control" name="description_uauc" id="description_uauc"></textarea>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            @elseif($accident_report->accident_status > STATUS_UAUC_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">UAUC</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12 form-input">
                                            <label for="name" class="form-label">UAUC</label>
                                            <div class="view_data">
                                                @if ($accident_report->ua_uc_yes_no == 1)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </div>
                                        </div>

                                        @if ($accident_report->ua_uc_yes_no == 1)
                                            <div class="mb-3 col-md-12 form-input">
                                                <label for="name" class="form-label">UA/UC</label>
                                                <div class="view_data">
                                                    @php
                                                        $ua_uc_values = explode(',', $accident_report->ua_or_uc);
                                                    @endphp

                                                    <span>UA: {!! in_array('1', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                    <br>
                                                    <span>UC: {!! in_array('2', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                </div>


                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label">Description of UAUC</label>
                                                <div class="view_data">
                                                    {{ $accident_report->description_uauc }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if ($accident_report->accident_status == STATUS_RISKANALYSIS_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Risk Level</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="riskanalysis"
                                            action="{{ admin_url('accidentReport/riskAnalysis/submit') }}">
                                            @csrf

                                            <input type="hidden" name="accident_id" id="accident_id"
                                                value="{{ encryptId($accidentId) }}">

                                            <div class="row">
                                                <div class="col-md-12 mt-3">
                                                    <div class="form-group form-input">
                                                        <label for="risk_level" class="form-label require">Risk
                                                            Level</label><br>
                                                        <input type="radio" id="low" name="risk_level"
                                                            value="1">
                                                        <label for="low">Low</label>
                                                        <input type="radio" id="medium" name="risk_level"
                                                            value="2">
                                                        <label for="medium">Medium</label>
                                                        <input type="radio" id="high" name="risk_level"
                                                            value="3">
                                                        <label for="high">High</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Description of CA</label>
                                                        <textarea class="form-control" name="description_ca" id="description_ca"></textarea>

                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
                                            </div>
                                        </form>
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
            $('input[name="ua_uc_yes_no"]').change(function() {
                if ($('#ua_uc_yes').is(':checked')) {
                    $('.ua_uc_div').show();
                } else {
                    $('.ua_uc_div').hide();
                }
            });

            $(function() {
                $('#uauc').validate({
                    rules: {

                        ua_uc_yes_no: {
                            required: true,
                        },
                        'ua_or_uc[]': {
                            required: true,
                        },
                        description_ca: {
                            required: true,
                        },

                    },
                    messages: {

                        ua_uc_yes_no: {
                            required: "UA UC is required.",
                        },
                        'ua_or_uc[]': {
                            required: "This field is required.",
                        },
                        description_uauc: {
                            required: "Description of UA UC is required.",
                        },

                    },

                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            console.log(`There are ${errors} validation errors.`);
                            validator.errorList.forEach(function(error) {
                                console.log(
                                    `Field: ${error.element.name}, Error: ${error.message}`
                                );
                            });
                        }
                    },
                });
            });

            $(function() {
                $('#riskanalysis').validate({
                    rules: {

                        risk_level: {
                            required: true,
                        },
                        description_ca: {
                            required: true,
                        },

                    },
                    messages: {

                        risk_level: {
                            required: "Risk Level is required.",
                        },
                        description_ca: {
                            required: "Description of CA is required.",
                        },

                    },

                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        if (errors) {
                            console.log(`There are ${errors} validation errors.`);
                            validator.errorList.forEach(function(error) {
                                console.log(
                                    `Field: ${error.element.name}, Error: ${error.message}`
                                );
                            });
                        }
                    },
                });
            });
        });
    </script>
@endpush
