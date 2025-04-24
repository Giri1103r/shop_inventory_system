@extends('admin.layouts.admin')
@section('title', 'Incident Investigation')
@section('pageurl', admin_url('incident/initial-incident/list'))


@section('content')
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .modal-dialog {
            max-width: 600px;
            width: 100%;
            /* Removed height to avoid fixed height issues */
            margin: 0 auto;
        }

        .modal-content {
            overflow: visible;
            /* Ensure that the modal content is fully visible */
        }

        #existingdiv {
            display: none;
            /* Hide dropdown initially, show it on button click */
        }

        #hira_id {
            width: 100%;
            /* Ensure dropdown takes up the full width */
        }

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
            {{-- <h4 class="text-black">{{ __('incident/initial-incident Edit') }}</h4> --}}

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
                                        <h4 class="text-white">Incident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $incident_report->sr_no ?? null }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($incident_report->incident_date_time) }}
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
                                        <label class="form-label require">Location</label>
                                        <div class="view_data">
                                            {{ $incident_report->location_name }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">IIR Type</label>
                                        <div class="view_data">
                                            {{ $incident_report->incident_type_name }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Incident Reported By</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Name</label>
                                        <div class="view_data">
                                            {{ $incident_report->reported_by }}
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
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $incident_report->employee_code }}
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
                            <div class="card-body">
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
                                        <label for="team_id" class="form-label">I.M Team members</label>
                                        <div class="view_data">
                                            {{ $getEHSReview->team_member_names }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label for="team_id" class="form-label">Incident/Accident Investigation Report Prepared by</label>
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
                            <div class="card-body">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Investigation</h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="incidentinvestigation"
                                        action="{{ admin_url('incident/initial-incident/investigation/submit') }}">
                                        @csrf
                                        <input type="hidden" name="incident_id" id="incident_id"
                                            value="{{ encryptId($incidentId) }}">
                                        <input type="hidden" name="fishbone_image" id="fishbone_image">
                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="witness_id" class="form-label">Name of the
                                                        Witness</label>
                                                    <select name="witness_id[]" id="witness_id"
                                                        class="form-control witness" style="width: 100%" multiple>
                                                        <option value="">Select Name of the Witness</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label for="anything_damaged" class="form-label require">Was anything
                                                        damaged?</label><br>
                                                    <input type="checkbox" id="Man" name="anything_damaged[]"
                                                        value="1">
                                                    <label for="Man">Man</label>
                                                    <input type="checkbox" id="Machine" name="anything_damaged[]"
                                                        value="2">
                                                    <label for="Machine">Machine</label><br>
                                                    <input type="checkbox" id="Materials" name="anything_damaged[]"
                                                        value="3">
                                                    <label for="Materials"> Materials</label>
                                                    <input type="checkbox" id="NA" name="anything_damaged[]"
                                                        value="4">
                                                    <label for="NA"> NA</label><br>
                                                </div>
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">HIRA</label>

                                                </div>
                                                <a href="{{ admin_url('incident/initial-incident/existingHira/' . encryptId($incidentId)) }}"
                                                class="btn btn-primary popupwindow"
                                                data-id="{{ $incidentId }}"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                             </a>
                                            </div>

                                            <div class="modal fade" id="hiraModal" tabindex="-1"
                                                aria-labelledby="hiraModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl custom-modal-width">
                                                    <div class="modal-content">

                                                        <div class="modal-body">
                                                            <p>Loading...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-2 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">MOC</label>

                                                </div>
                                                <a href="{{ admin_url('incident/initial-incident/existingMOC/' . encryptId($incidentId)) }}"
                                                class="btn btn-primary popupwindow"
                                                data-id="{{ $incidentId }}"
                                                title="View">
                                                <i class="fas fa-eye"></i>
                                                </a>
                                            </div>


                                            <div class="modal fade" id="mocModal" tabindex="-1"
                                                aria-labelledby="mocModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-body">
                                                            <p>Loading...</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label for="root_cause_analysis" class="form-label require">Possible
                                                        Root
                                                        Cause
                                                        Analysis (PRCA)</label>
                                                    <select name="root_cause" id="root_cause_analysis"
                                                        style="width: 100%" class="form-control single-select">
                                                        <option value="">Select PRCA</option>
                                                        <option value="1">Why - Why Analysis</option>
                                                        <option value="2">Fish Bone Analysis</option>
                                                        <option value="3">NA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Immediate action taken
                                                        (If any)</label>
                                                    <input type="text" name="action_taken" id="action_taken"
                                                        class="form-control" placeholder="Immediate action taken">
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label for="responsible_person_id"
                                                        class="form-label require">Responsible Person</label>
                                                    <select name="responsible_person_id" id="responsible_person_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Responsible Person</option>
                                                    </select>
                                                </div>
                                            </div> --}}

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Date</label>
                                                    <input type="text" name="target_date" id="target_date"
                                                        class="form-control"
                                                        value="{{ Displaydateformat($incident_report->target_date) }}"
                                                        disabled>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Remarks (If Any)</label>
                                                    <textarea class="form-control" name="remark" id="remark"></textarea>

                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-3">
                                                <div class="form-group">
                                                    <label class="form-label require d-block">Risk Analysis</label>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" id="yes" name="risk_analysis"
                                                            value="1" class="form-check-input">
                                                        <label for="yes" class="form-check-label">Yes</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input type="radio" id="no" name="risk_analysis"
                                                            value="2" class="form-check-input">
                                                        <label for="no" class="form-check-label">No</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2" id="risk_analysis_remark_container"
                                                style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Risk Analaysis Remarks</label>
                                                    <textarea class="form-control" name="risk_analysis_remark" id="risk_analysis_remark"></textarea>

                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mt-3 whywhy" style="display: none;">
                                            <div class="card p-3">
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                                    <h4 class="text-dark mb-0">Why Why Analysis</h4>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success addwhywhyanalysis">
                                                        Add More
                                                    </button>
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
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="whywhyanalysisBody">
                                                            <tr id="RowwhywhyanalysisView0">
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[0][whywhyanalysis_first]"
                                                                        class="form-control"></td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[0][whywhyanalysis_second]"
                                                                        class="form-control"></td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[0][whywhyanalysis_third]"
                                                                        class="form-control"></td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[0][whywhyanalysis_forth]"
                                                                        class="form-control"></td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i></td>
                                                                <td><input type="text"
                                                                        name="whywhyanalysis[0][whywhyanalysis_fifth]"
                                                                        class="form-control"></td>
                                                                <td><button type="button"
                                                                        class="btn btn-sm  removewhywhyanalysisRow"> <i
                                                                            class="fa-solid fa-trash text-danger"></i></button>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row m-5 p-3 fishbone" style="display: none;">

                                            <div class="fishbone-container " style="text-align: center;">
                                                <!-- Mensch -->
                                                <div class="cause">
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[first][root_cause]">
                                                    </div>
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat "
                                                                placeholder="Enter value"
                                                                name = "fishbone[first][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[first][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[first][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[first][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[first][sub][category_5]">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Maschine -->
                                                <div class="cause">
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[second][root_cause]">
                                                    </div>
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[second][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[second][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[second][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[second][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[second][sub][category_5]">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Milieu -->
                                                <div class="cause">
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[third][root_cause]">
                                                    </div>
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[third][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[third][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[third][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[third][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[third][sub][category_5]">
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Divider Line -->
                                                <div class="line"></div>

                                                <!-- Messung -->
                                                <div class="cause">
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fourth][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fourth][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fourth][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fourth][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fourth][sub][category_5]">
                                                        </div>
                                                    </div>
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[fourth][root_cause]">
                                                    </div>
                                                </div>

                                                <!-- Material -->
                                                <div class="cause">
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fifth][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fifth][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fifth][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fifth][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[fifth][sub][category_5]">
                                                        </div>

                                                    </div>
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[fifth][root_cause]">
                                                    </div>

                                                </div>

                                                <!-- Methoden -->
                                                <div class="cause">
                                                    <div class="subcause">
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[sixth][sub][category_1]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[sixth][sub][category_2]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[sixth][sub][category_3]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[sixth][sub][category_4]">
                                                        </div>
                                                        <div class="stat">
                                                            <input type="text" class="form-control sub-stat"
                                                                placeholder="Enter value"
                                                                name = "fishbone[sixth][sub][category_5]">
                                                        </div>
                                                    </div>
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value"
                                                            name = "fishbone[sixth][root_cause]">
                                                    </div>
                                                </div>

                                                <!-- Defect Section -->
                                                <div class="defect-spacer-top"></div>
                                                <div class="defect">
                                                    <div class="defect-text">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name = "fishbone[root_cause][main]">
                                                    </div>
                                                </div>
                                                <div class="defect-spacer-bottom"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Recommended Corrective & Preventive
                                                        Action</label>
                                                    <textarea type="text" name="corrective_preventive_action" id="corrective_preventive_action" class="form-control"
                                                        placeholder="Recommended Corrective & Preventive Action"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-4" id="form-wrapper">
                                            <div class="form-set">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white p-1">RCPA</h4>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row" style="width: 84px;">
                                                        Add
                                                    </button>
                                                    <button type="button" class="btn btn-danger remove-row">
                                                        <i class="fa-solid fa-trash"></i> Remove
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Serial Number</label>
                                                            <input type="text" name="serial_number[1]"
                                                                class="form-control" placeholder="Serial Number"
                                                                value="RCPA-00001" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Recommended Corrective &
                                                                Preventive Action</label>
                                                            <input type="text" name="rcpa[1]" class="form-control"
                                                                placeholder="Recommended Corrective & Preventive Action"
                                                                value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Responsibility</label>
                                                            <select name="responsibility[1]"
                                                                class="form-control responsibility-select"></select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Timeline</label>
                                                            <input type="text" name="timeline[1]"
                                                                class="form-control timeline-picker" value="">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Status</label>
                                                            <select name="capa_status[1]"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">Select RCPA Status
                                                                </option>
                                                                <option value="{{ encryptId(YES) }}">Open</option>
                                                                <option value="{{ encryptId(NO) }}">In-Progress</option>
                                                                <option value="{{ encryptId(NO) }}">Closed</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mt-2 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label">Remark if any</label>
                                                            <textarea name="capa_remark[1]" class="form-control" placeholder="Remark" rows="3"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Main Root Cause</label>
                                                    <textarea type="text" name="main_root_cause" id="main_root_cause" class="form-control"
                                                        placeholder="Main Root Cause"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label for="leading_factors" class="form-label require">Leading
                                                        Factors</label><br>
                                                    <input type="radio" id="human" name="leading_factors"
                                                        value="1">
                                                    <label for="human">Human Factor</label>
                                                    <input type="radio" id="system" name="leading_factors"
                                                        value="2">
                                                    <label for="system">System Factor</label><br>

                                                </div>
                                            </div>
                                            <div>
                                                <div class="row mt-2">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">Recommended Causes</h4>
                                                    </div>
                                                </div>
                                            </div>

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
                                                        <div class="form-group">
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="ua" name="ua_or_uc[]" value="1">
                                                                <label class="form-check-label" for="ua">Unsafe
                                                                    Act</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="uc" name="ua_or_uc[]" value="2">
                                                                <label class="form-check-label" for="uc">Unsafe
                                                                    Condition</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="nc" name="ua_or_uc[]" value="3">
                                                                <label class="form-check-label" for="nc">Natural
                                                                    Causes</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="risk_analysis_div mt-2" style="display: none;">
                                                <div class="row">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">Risk Level</h4>
                                                    </div>
                                                </div>
                                                <div class="basic-form">
                                                    <input type="hidden" name="incident_id" id="incident_id"
                                                        value="{{ encryptId($incidentId) }}">

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
                                                                <label class="form-label required">Description of
                                                                    CA</label>
                                                                <textarea class="form-control" name="description_ca" id="description_ca"></textarea>

                                                            </div>
                                                        </div>
                                                    </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {


            $('input[name="risk_analysis"]').on('change', function() {
                if ($('#no').is(':checked')) {
                    $('#risk_analysis_remark_container').show();
                } else {
                    $('#risk_analysis_remark_container').hide();
                }
            });
            $('input[name="ua_uc_yes_no"]').change(function() {
                if ($('#ua_uc_yes').is(':checked')) {
                    $('.ua_uc_div').show();
                } else {
                    $('.ua_uc_div').hide();
                }
            });
            $('input[name="risk_analysis"]').change(function() {
                if ($('#yes').is(':checked')) {
                    $('.risk_analysis_div').show();
                } else {
                    $('.risk_analysis_div').hide();
                }
            });


            $("#root_cause_analysis").change(function() {
                if ($(this).val() == "1") {
                    $(".whywhy").show(); // Show the Why Why Analysis section
                } else {
                    $(".whywhy").hide(); // Hide it when another option is selected
                }
            });

            $("#root_cause_analysis").change(function() {
                if ($(this).val() == "2") {
                    $(".fishbone").show(); // Show the Why Why Analysis section
                } else {
                    $(".fishbone").hide(); // Hide it when another option is selected
                }
            });
            let whywhyanalysisIndex = {{ 1 }};


            $(".addwhywhyanalysis").on("click", function() {
                let rowCount = $("#whywhyanalysisBody tr").length;

                if (rowCount >= 5) {
                    Swal.fire({
                        icon: "warning",
                        title: "Limit Reached",
                        text: "Maximum of 5 rows can be added.",
                        confirmButtonColor: "#d33"
                    });
                    return;
                }
                console.log("Root Cause Selected Value:", $("#root_cause_analysis").val());
                const newRow = `
            <tr id="RowwhywhyanalysisView${whywhyanalysisIndex}">
                <td><input type="text" name="whywhyanalysis[${whywhyanalysisIndex}][whywhyanalysis_first]" class="form-control"></td>
                <td><i class="fas fa-arrow-right text-primary"></i></td>
                <td><input type="text" name="whywhyanalysis[${whywhyanalysisIndex}][whywhyanalysis_second]" class="form-control"></td>
                <td><i class="fas fa-arrow-right text-primary"></i></td>
                <td><input type="text" name="whywhyanalysis[${whywhyanalysisIndex}][whywhyanalysis_third]" class="form-control"></td>
                <td><i class="fas fa-arrow-right text-primary"></i></td>
                <td><input type="text" name="whywhyanalysis[${whywhyanalysisIndex}][whywhyanalysis_forth]" class="form-control"></td>
                <td><i class="fas fa-arrow-right text-primary"></i></td>
                <td><input type="text" name="whywhyanalysis[${whywhyanalysisIndex}][whywhyanalysis_fifth]" class="form-control whywhyanalysis_fifth"></td>
                <td>
                    <button type="button" class="btn btn-sm  removewhywhyanalysisRow">
                        <i class="fa-solid fa-trash text-danger"></i>
                        </button>
                </td>
            </tr>
        `;

                $("#whywhyanalysisBody").append(newRow);
                whywhyanalysisIndex++;

            });

            $(document).on("click", ".removewhywhyanalysisRow", function() {
                const rowCount = $("#whywhyanalysisBody tr").length;
                if (rowCount > 1) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to delete this row?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).closest("tr").fadeOut(300, function() {
                                $(this).remove();
                            });

                            Swal.fire("Deleted!", "The row has been deleted.", "success");
                        }
                    });
                } else {
                    Swal.fire("Warning!", "At least one row is required!", "error");
                }
            });
        });

        $(document).ready(function() {

            $("#root_cause_analysis").change(function() {
                if ($(this).val() == "2") {
                    $(".fishbone").show(); // Show the Why Why Analysis section
                } else {
                    $(".fishbone").hide(); // Hide it when another option is selected
                }
            });
        });

        $(document).ready(function() {
            $(document).on('click', '.popupwindow', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');

                if (href.includes("existingHira")) {
                    $('#hiraModal .modal-body').html('<p>Loading...</p>');
                    $('#hiraModal').modal('show');

                    $.get(href, function(response) {
                        $('#hiraModal .modal-body').html(response);
                    }).fail(function() {
                        $('#hiraModal .modal-body').html('<p>Error loading content.</p>');
                    });

                } else if (href.includes("existingMOC")) {
                    $('#mocModal .modal-body').html('<p>Loading...</p>');
                    $('#mocModal').modal('show');

                    $.get(href, function(response) {
                        $('#mocModal .modal-body').html(response);
                    }).fail(function() {
                        $('#mocModal .modal-body').html('<p>Error loading content.</p>');
                    });
                }
            });


            flatpickr("#target_date", {
                dateFormat: "d-m-Y",
                minDate: "today"
            });


            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $('#responsible_person_id').select2({
                ajax: {
                    url: "{{ url('incident/initial-incident/employeename') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
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
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });
            $('#witness_id').select2({
                ajax: {
                    url: "{{ url('incident/initial-incident/employeename') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
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
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });
            $('#emp_code').select2({
                ajax: {
                    url: "{{ url('incident/initial-incident/getemployeename') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
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
                minimumInputLength: 1,
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });

            $('#emp_code').change(function() {
                var emp_code = $(this).val();

                if (emp_code) {
                    $.ajax({
                        url: "{{ url('incident/initial-incident/fetchEmployeeDetails') }}/" +
                            emp_code,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            if (response.employee) {
                                $('#designation').val(response.employee.designation);


                                if (response.employee.department_name) {
                                    $('#department_id').html('<option value="' + response
                                        .employee
                                        .department_name + '">' + response.employee
                                        .department_name +
                                        '</option>');
                                    $('#department_id').prop('disabled', true);
                                } else {
                                    $('#department_id').prop('disabled', false);
                                    $('#department_id').html(
                                        '<option value="">Select Department</option>'
                                    );
                                }
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Employee data could not be fetched."
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while fetching employee details."
                            });
                        }
                    });
                } else {
                    $('#designation, #department_id').val('');
                    $('#department_id').prop('disabled', false);
                }
            });
            $(function() {
                // Initialize form validation
                $('#incidentinvestigation').validate({
                    rules: {
                        'anything_damaged[]': {
                            required: true,
                        },
                        corrective_preventive_action: {
                            required: true,
                            minlength: 10,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/,
                        },
                        action_taken: {
                            minlength: 10,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/,
                        },
                        root_cause: {
                            required: true,
                        },
                        responsible_person_id: {
                            required: true,
                        },
                        target_date: {
                            required: true,
                        },
                        risk_analysis: {
                            required: true,
                        },
                        risk_analysis_remark: {
                            required: function(element) {
                                return $('input[name="risk_analysis"]:checked').val() === '2';
                            },
                            minlength: 3,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/
                        },

                        remark: {
                            minlength: 10,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/
                        },
                    },
                    messages: {
                        'anything_damaged[]': {
                            required: "Was anything damaged is required.",
                        },
                        corrective_preventive_action: {
                            required: "Corrective/preventive action is required.",
                            minlength: "Minimum 10 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
                        },
                        action_taken: {
                            minlength: "Minimum 10 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
                        },
                        root_cause: {
                            required: "Root Cause Analysis is required.",
                        },
                        responsible_person_id: {
                            required: "Responsible Person is required.",
                        },
                        target_date: {
                            required: "Target Date is required.",
                        },
                        risk_analysis: {
                            required: "Risk Analysis is required.",
                        },
                        risk_analysis_remark: {
                            required: "Risk Analysis Remarks is required.",
                            minlength: "Details must be at least 3 characters long.",
                            maxlength: "Details cannot exceed 2000 characters.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
                        },
                        remark: {
                            minlength: "Minimum 10 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
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
                        // Form is valid, proceed with capturing the fishbone diagram
                        let fishboneContainer = $(".fishbone-container")[
                            0]; // Get the fishbone diagram container

                        // Capture the fishbone diagram as an image
                        html2canvas(fishboneContainer, {
                            scale: 2
                        }).then(function(canvas) {
                            let imageData = canvas.toDataURL(
                                "image/png"); // Convert canvas to base64

                            // Set the image data to the hidden input field
                            $("#fishbone_image").val(imageData);

                            // Now submit the form programmatically
                            form.submit();
                        });
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

        $(document).ready(function() {
            let form_set_count = 2;
            let serial_number = parseInt("{{ getRCPACount() }}", 10) + 1;
            const maxFormSets = 5;
            const minFormSets = 1;

            $(document).on('click', ".add-row", function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum RCPA Reached',
                        text: 'You can only add up to 5 RCPA.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                let newSerialNumber = 'RCPA-' + ('0000' + serial_number).slice(-5);
                var newFormSet = `
                <div class="form-set">
                        <div class="card-header-inner">
                            <h4 class="text-white p-1">RCPA</h4>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-primary add-row me-3" type="button" style="width: 84px;">
                                Add
                            </button>
                            <button type="button" class="btn btn-danger remove-row">
                                <i class="fa-solid fa-trash"></i> Remove
                            </button>
                        </div>
                        <div class="row">

                             <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Recommended Corrective & Preventive Action</label>
                                    <input type="text" name="rcpa[${form_set_count}]" class="form-control" placeholder="Recommended Corrective & Preventive Action" value="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Responsibility</label>
                                    <select name="responsibility[${form_set_count}]" class="form-control responsibility-select"></select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-input">
                                    <label class="form-label require">Timeline</label>
                                    <input type="text" name="timeline[${form_set_count}]" class="form-control timeline-picker">
                                </div>
                            </div>
                            <div class="col-md-4 mt-2">
                                <div class="form-group form-input">
                                    <label class="form-label require">Status</label>
                                    <select name="capa_status[${form_set_count}]" class="form-control single-select" style="width: 100%">
                                        <option value="">Select RCPA Status</option>
                                        <option value="{{ encryptId(YES) }}">Open</option>
                                        <option value="{{ encryptId(NO) }}">In-Progress</option>
                                        <option value="{{ encryptId(NO) }}">Closed</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-md-12 mt-2 mb-2">
                                <div class="form-group form-input">
                                    <label class="form-label">Remarks (If Any)</label>
                                    <textarea name="capa_remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>`;

                $('#form-wrapper').append(newFormSet);
                $("input[name='rcpa[" + form_set_count + "]']").rules('add', {
                    required: true,
                    noSpaces: true,
                    messages: {
                        required: 'Recommended Corrective & Preventive Action is required',
                    }
                });
                $("input[name='timeline[" + form_set_count + "]']").rules('add', {
                    required: true,
                    noSpaces: true,
                    messages: {
                        required: 'Timeline is required',
                    }
                });
                $("select[name='rcpa_status[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'RCPA Status is required',
                    }
                });
                $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Remark is required',
                    }
                });

                let newElement = $('#form-wrapper').last().find('.responsibility-select');
                initResponsibilitySelect(newElement);
                let timelinePicker = $('#form-wrapper').last().find('.timeline-picker');
                initDatePickers(timelinePicker);

                form_set_count++;
                updatePageIndices();
            });

            $(document).on('click', '.remove-row', function() {
                let currentFormSets = $('#form-wrapper .form-set').length;

                if (currentFormSets <= minFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Minimum RCPA Required',
                        text: 'At least 1 RCPA is required.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                $(this).closest('.form-set').remove();
                updatePageIndices();
            });

            function updatePageIndices() {
                $('#form-wrapper .form-set').each(function(index) {
                    const i = index + 1;
                    $(this).find("input[name^='serial_number']").val('RCPA-' + ('0000' + (i)).slice(-5));
                    $(this).find('input[name^="rcpa"]').attr('name', 'rcpa[' + i + ']');
                    $(this).find('input[name^="timeline"]').attr('name', 'timeline[' + i + ']');
                    $(this).find('select[name^="capa_status"]').attr('name', 'capa_status[' + i + ']');
                    $(this).find('select[name^="responsibility"]').attr('name', 'responsibility[' + i +
                        ']');
                    $(this).find('textarea[name^="capa_remark"]').attr('name', 'capa_remark[' + i + ']');
                });
            }


            $(".submit").on('click', function() {
                if ($("#incidentinvestigation").valid()) {
                    $("#incidentinvestigation").submit();
                } else {
                    return false;
                }
            });

            function initResponsibilitySelect(selector) {
                $(selector).select2({
                    placeholder: "Select Responsibility",
                    allowClear: true,
                    closeOnSelect: true,
                    ajax: {
                        url: "{{ admin_url('incident/initial-incident/reportedBy') }}",
                        type: "GET",
                        dataType: "json",
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
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
            }

            function initDatePickers(selector) {
                $(selector).flatpickr({
                    dateFormat: "d-m-Y",
                    minDate: "today"
                });
            }
            initDatePickers('.timeline-picker');
            initResponsibilitySelect('.responsibility-select');

        });
    </script>
@endpush
