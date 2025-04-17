@extends('admin.layouts.admin')
@section('title', 'Initial Incident/Accident Report')
@section('pageurl', admin_url('incident/initial-incident/list'))



@section('content')
    @push('style')
        <style>
            .addbodyparts {
                display: none;
            }

            canvas {
                pointer-events: none;
                position: absolute;
            }

            audio,
            canvas,
            progress,
            video {
                display: inline-block;
                vertical-align: baseline;
            }

            .imgmap_css_container {
                height: 250px !important;
                width: 250px !important;
            }

            .col-lg-4,
            .col-md-4,
            .col-sm-4 {
                float: left;
                position: relative;
                min-height: 1px;
                padding-right: 15px;
                padding-left: 15px;
            }

            .fa-trash-o {
                margin-top: 5px !important;
            }

            .fa-trash-o:before {
                color: red !important;
                content: "\f014" !important;
            }


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
    @endpush
    @php
        $is_ready_only = '';
    @endphp
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

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="incidentAdd"
                                        action="{{ admin_url('incident/initial-incident/add/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Incident Reported By</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Name</label>
                                                    <div class="col-sm-6" style="width: 100%">
                                                        <select name="reported_name" id="reported_name" style="width: 100%"
                                                            class="form-control reported_name">
                                                            <option value="">Select Name</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation</label>
                                                    <input type="text" name="designation"
                                                        class="form-control designation" placeholder="Designation">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department" class="form-control department">
                                                        <option value="">Select Department</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Code</label>
                                                    <input type="text" name="employee_code"
                                                        class="form-control employee_code" placeholder="Employee Code"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time of reporting</label>
                                                    <input type="text" name="time_of_reporting" id = "time_of_reporting"
                                                        class="form-control time_of_reporting"
                                                        placeholder="Time of reporting">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Reporting Media</label>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_phone" class="form-check-input"
                                                            value="1">
                                                        <label class="form-check-label"
                                                            for="reporting_media_phone">Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_walkietalkie" class="form-check-input"
                                                            value="2">
                                                        <label class="form-check-label"
                                                            for="reporting_media_walkietalkie">Walkie Talkie</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_extension" class="form-check-input"
                                                            value="3">
                                                        <label class="form-check-label"
                                                            for="reporting_media_extension">Extension</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_others" class="form-check-input"
                                                            value="4">
                                                        <label class="form-check-label"
                                                            for="reporting_media_others">Others</label>
                                                    </div>
                                                    <div class="text-danger "></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4" id = "reporting_media_othersdiv" style="display: none">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Others</label>
                                                    <input type="text" name="reporting_media_othersdesc"
                                                        id = "reporting_media_othersdesc"
                                                        class="form-control reporting_media_othersdesc" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Incident/Accident Details</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Sr. No</label>
                                                    <input type="text" name="sr_no" id="sr_no"
                                                        class="form-control" placeholder=""
                                                        value = "{{ getsequence('incident') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date and Time</label>
                                                    <input type="text" name="incident_date_time"
                                                        id="incident_date_time" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <input type="text" name="shift" id="shift"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                        @foreach ($locationList as $location)
                                                            <option value="{{ encryptId($location->id) }}">
                                                                {{ $location->location_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Exact Location</label>
                                                    <input type="text" name="exact_location" id="exact_location"
                                                        class="form-control" placeholder="Exact Location">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">IIR Type</label>
                                                    @foreach ($incTypeList as $incType)
                                                        <div class="form-check">
                                                            <input type="radio" name="iir_type"
                                                                id="iir_type_{{ $incType->id }}" class="form-check-input"
                                                                value="{{ $incType->id }}">
                                                            <label class="form-check-label"
                                                                for="iir_type_{{ $incType->id }}">{{ $incType->incident_type_name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Brief Description</label>
                                                    <textarea type="text" name="brief_description" id = "brief_description" class="form-control brief_description"
                                                        placeholder=""></textarea>
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
                                                    <label for="evidence_0" class="form-label require">Evidence</label>
                                                    <input type="file" class="form-control validate-file-required"
                                                        name="evidence[0][]" id="evidence_0" multiple>
                                                    <div class="text-danger"></div>
                                                    <small>Allowed file types: png, jpeg , jpg, pdf, doc, docx, mp4</small>
                                                    <div class="preview-container mt-2 d-flex flex-wrap gap-2"
                                                        id="preview-container-0"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Immediate Action Taken</label>
                                                    <textarea type="text" name="immediate_action_taken" id = "immediate_action_taken"
                                                        class="form-control immediate_action_taken" placeholder=""></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">If any person has injured?</label>
                                                    <div class="form-check">
                                                        <input type="radio" name="anyone_injured" id="injured_yes"
                                                            class="form-check-input" value="1">
                                                        <label class="form-check-label" for="injured_yes">Yes</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="radio" name="anyone_injured" id="injured_no"
                                                            class="form-check-input" value="0">
                                                        <label class="form-check-label" for="injured_no">No</label>
                                                    </div>
                                                    <div class="text-danger"></div>
                                                </div>

                                            </div>

                                            <div class="row mt-2 injuryDetails" style="display: none;">

                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <a class="text-white card-link">Injured Person Details</a>
                                                    <div class="btn btn-warning btn-sm addMoreInjuryDetails">Add</div>
                                                </div>


                                                <div class="injury-details-templat">
                                                    <div class="row injury-append" style="margin-top: 20px;">
                                                        <div class="col-md-4 form-input">
                                                            <label for="" class="form-label require">Injury Person
                                                                Type</label>
                                                            <select
                                                                class="form-control require single-select selectInjPersontype"
                                                                name="injury_person[0][injury_person_type]" alt="0"
                                                                style="width: 100%" id="RowInjTypedata_0">
                                                                <option value="">Select Person Type</option>
                                                                <option value="{{ encryptId('1') }}">Employee</option>
                                                                <option value="{{ encryptId('2') }}">Worker</option>
                                                                <option value="{{ encryptId('3') }}">Others</option>
                                                            </select>
                                                        </div>
                                                        <!-- Injury Person Name (Text Inputs) -->
                                                        <div class="col-md-4 form-input" id="injuryPersonTextContainer_0">
                                                            <label class="form-label require">Injury Person Name</label>
                                                            <input type="text"
                                                                class="form-control injuryPersonName require"
                                                                name="injury_person[0][injury_person_name]" alt="0"
                                                                id="RowInjothersdata_0"
                                                                placeholder="Enter Injury Person Name">
                                                        </div>
                                                        <!-- Injury Person Name (Dropdown) -->
                                                        <div class="col-md-4 form-input d-none"
                                                            id="injuryPersonDropdownContainer_0">
                                                            <label class="form-label require">Injury Person Name</label>
                                                            <select alt="0"
                                                                class="form-control require injuryPersonName single-select"
                                                                style="width: 100%"
                                                                name="injury_person[0][injury_person_id]"
                                                                id="RowInjEmpdata_0">
                                                                <option value="" disabled selected>Select Injury
                                                                    Person
                                                                    Name
                                                                </option>
                                                            </select>
                                                        </div>



                                                        <!-- Designation -->
                                                        <div class="col-md-4 form-input">
                                                            <label class="form-label require">Injury Person
                                                                Designation</label>
                                                            <input type="text" alt="0"
                                                                name="injury_person[0][injury_person_designation]"
                                                                class="form-control InjPerDest" id="InjPerDest_0">
                                                        </div>

                                                        <!-- Department -->
                                                        <div class="col-md-4 form-input " id="injuryPersonDepttexxt_0">
                                                            <label class="form-label require">Injury Person
                                                                Department</label>
                                                            <input type="text" alt="0"
                                                                name="injury_person[0][injury_person_department_id]"
                                                                class="form-control InjPerDept" id="InjPerDept_0">
                                                        </div>

                                                        <!-- Department Dropdown for Others -->
                                                        {{-- <div class="col-md-4 form-input" id="injuryPersonDeptDropdown_0">
                                                            <label class="form-label require">Injury Person Department</label>
                                                            <select alt="0" class="form-control single-select"
                                                                name="injury_person[0][injury_person_department_id]"
                                                                style="width: 100%">
                                                                <option value="">Select Department</option>
                                                                @foreach ($departmentList as $department)
                                                                    <option value="{{ encryptId($department->id) }}">
                                                                        {{ $department->department_name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div> --}}
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label for="nature_of_injury" class="form-label">Nature of
                                                                    Injury</label>
                                                                <select alt="0"
                                                                    name="injury_person[0][nature_of_injury]"
                                                                    id="nature_of_injury_0" style="width: 100%"
                                                                    class="form-control single-select">
                                                                    <option value="">Select Nature of Injury</option>
                                                                    <option value="{{ encryptId('1') }}">Major</option>
                                                                    <option value="{{ encryptId('2') }}">Minor</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-2 form-input">
                                                            <label for="inputFirstName" class="form-label">Location of
                                                                the
                                                                Injury</label>
                                                            <br>
                                                            <span class="input-group-addon injury-btn btn btn-info"
                                                                data-id="0" data-injid="0" attr_emp=""
                                                                alt="0"><i class="fa fa-male"
                                                                    aria-hidden="true"></i></span>
                                                        </div>
                                                        <div class="col-md-2 text-right">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm removeInjuryDetails"
                                                                style="margin-top: 35px;">Remove</button>
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
    <!--injury model-->

    <div id="injury_model" class="modal  fade" role="dialog" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header panel-box-header" style="display: flex;justify-content: end">

                    <h4 class="modal-title panel-box-title" style="color:#000000 !important;"></h4>
                    <button type="button" class="close" style="opacity: 1;" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body modal-pic ">
                    <!-- model content here -->
                    <!-- invetigation form start-->

                    <form id="injuryform" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="body_prim_id" id="body_prim_id" value="">
                        <input type="hidden" name="injury_id" id="injury_id" value="">
                        <input type="hidden" name="bodypartimage" id="bodypartimage">
                        <div class="container-fluid1">

                            <div class="box-body1 box-group">

                                <svg id="bodyparts" xmlns="http://www.w3.org/2000/svg" height="500" width="400"
                                    viewBox="0 0 68.587668 92.604164">
                                    <path id="head" alt="Head" data-name="Head" style="opacity:1"
                                        fill=""
                                        d="m 11.671635,6.3585449 -0.0482,-2.59085 4.20648,-2.46806 4.42769,2.95361 -0.0405,1.94408 0.24197,-3.34467 -2.03129,-2.31103004 -2.84508,-0.51629 -2.20423,0.52915 -1.9363,2.63077004 z" />
                                    <path id="face" alt="Face" data-name="Face" style="opacity:1"
                                        fill=""
                                        d="m 19.748825,6.7034949 0.0203,-2.20747 -3.96689,-2.7637 -3.74099,2.23559 -0.006,2.63528 -0.60741,0.0403 0.27408,1.82447 0.97635,0.33932 0.44244,2.1802901 1.82222,2.06556 2.03518,-0.0607 1.79223,-1.94408 0.35957,-2.2406601 0.97616,-0.33932 0.25159,-1.78416 z" />
                                    <path id="neck" alt="Neck" data-name="Neck" style="opacity:1"
                                        fill=""
                                        d="m 13.304665,11.910505 1.64975,2.35202 0.74426,2.62159 -1.73486,-1.38354 -0.86649,-2.97104 z m 5.08047,0 -1.64975,2.35202 -0.74538,2.62234 1.73486,-1.38354 0.86649,-2.97104 z" />

                                    <path id="left_shoulder" alt="Left Shoulder" data-name="Left Shoulder"
                                        style="opacity:1" fill=""
                                        d="m 19.047795,13.248365 3.55748,1.97916 0.72653,-0.35074 z m -0.107,0.43288 -0.37119,1.73073 2.1846,0.53561 1.40116,-0.49436 z m 3.98151,1.97595 0.75814,-0.41 2.40806,1.66799 1.17364,1.50707 0.62662,1.5626 -0.0464,3.70194 -1.3284,-1.72153 0.0407,-2.59376 -0.48842,-0.50049 c 0,0 -3.09778,-3.19058 -3.14371,-3.21401 z m -0.2409,0.10873 c -0.001,0.0525 3.32987,3.54733 3.32987,3.54733 l 0.10067,3.10396 -1.15426,-1.97782 -2.22547,-0.94804 -1.56576,-2.88481 z" />
                                    <path id="right_shoulder" alt="Right Shoulder" data-name="Right Shoulder"
                                        style="opacity:1" fill=""
                                        d="m 12.624785,13.248365 -3.5574599,1.97916 -0.72653,-0.35074 z m 0.107,0.43288 0.37119,1.73073 -2.18459,0.53561 -1.4011499,-0.49436 z m -3.9814899,1.97595 -0.75814,-0.41 -2.40806,1.66799 -1.17364,1.50707 -0.62662,1.56259 0.0464,3.70195 1.3284,-1.72153 -0.0407,-2.59376 0.48843,-0.5005 c 0,0 3.09777,-3.19057 3.1437,-3.214 z m 0.2409,0.10873 c 0.002,0.0525 -3.32987,3.54733 -3.32987,3.54733 l -0.10067,3.10396 1.15426,-1.97782 2.22547,-0.94804 1.5657499,-2.88481 z" />

                                    <path id="left_arm" alt="Left Arm" data-name="Left Arm" style="opacity:1"
                                        fill=""
                                        d="m 27.621665,30.814715 -0.33838,1.70499 -1.81932,-2.54418 -0.6629,-1.26895 z m -2.85271,-2.6096 c -0.0259,-0.0144 -0.0536,-0.0254 -0.0824,-0.0324 l -1.48333,-4.95503 1.00456,-2.08428 1.65511,1.74532 2.23034,6.67667 0.0415,0.93739 c -1.06528,-0.84215 -2.18962,-1.60679 -3.36434,-2.28803 z m 1.6945,-5.75654 1.64893,6.43421 -0.36469,-4.92266 z" />
                                    <path id="left_elbow" alt="Left Elbow" data-name="Left Elbow" style="opacity:1"
                                        fill=""
                                        d="m 28.325215,27.370125 -0.005,3.09419 0.57959,1.91184 0.54538,-2.41185 z" />
                                    <path id="left_forearm" alt="Left Forearm" data-name="Left Forearm"
                                        style="opacity:1" fill=""
                                        d="m 26.955425,32.969125 1.30083,10.28927 -1.10778,0.01 -1.89387,-7.99609 0.19174,-4.53719 z m 1.21978,-1.94971 -0.58729,2.58635 1.11876,9.15614 0.55849,-0.21663 0.2304,-6.77018 z" />
                                    <path id="left_hands" alt="Left Hands" data-name="Left Hands" style="opacity:1"
                                        fill=""
                                        d="m 27.140245,43.563145 1.5198,0.0506 0.76631,-0.67111 1.21262,2.15766 0.86245,3.32873 -0.49386,0.22113 -0.59815,-2.20238 -0.50016,0.25356 0.35639,2.49422 -0.62382,0.24345 -0.41402,-2.49194 -0.55839,0.17851 0.2262,2.76603 -0.76938,0.32268 -0.25788,-2.86764 -0.4578,-0.0181 -0.16611,2.6524 -0.65997,0.26329 -0.0712,-4.56643 -0.34158,-0.19428 -1.35316,1.68368 -0.32832,-0.34355 0.72644,-2.0551 z" />


                                    <path id="right_arm" alt="Right Arm" data-name="Right Arm" style="opacity:1"
                                        fill=""
                                        d="m 4.0746451,30.814715 0.33838,1.70499 1.81931,-2.54418 0.66289,-1.26895 z m 2.8527,-2.6096 c 0.0259,-0.0144 0.0536,-0.0254 0.0824,-0.0324 l 1.48332,-4.95503 -1.00455,-2.08428 -1.65509,1.74532 -2.23034,6.67667 -0.0415,0.93739 c 1.06528,-0.84215 2.18961,-1.60679 3.36433,-2.28803 z m -1.6945,-5.75654 -1.64891,6.43421 0.36468,-4.92266 z" />
                                    <path id="right_elbow" alt="Right Elbow" data-name="Right Elbow" style="opacity:1"
                                        fill=""
                                        d="m 3.2054751,27.370125 0.005,3.09419 -0.57959,1.91184 -0.54539,-2.41185 z" />
                                    <path id="right_forearm" alt="Right Forearm" data-name="Right Forearm"
                                        style="opacity:1" fill=""
                                        d="m 4.5752651,32.969125 -1.30083,10.28927 1.10778,0.01 1.89387,-7.99609 -0.19174,-4.53719 z m -1.21978,-1.94971 0.58728,2.58635 -1.11875,9.15614 -0.55849,-0.21663 -0.2304,-6.77018 z" />
                                    <path id="right_hand" alt="Right Hand" data-name="Right Hand" style="opacity:1"
                                        fill=""
                                        d="m 4.3904451,43.563145 -1.5198,0.0506 -0.76631,-0.67112 -1.21261996,2.15767 -0.86245,3.32873 0.49386,0.22113 0.59814996,-2.20238 0.50016,0.25356 -0.35639,2.49422 0.62382,0.24345 0.41402,-2.49194 0.55839,0.17851 -0.2262,2.76603 0.76938,0.32268 0.25788,-2.86764 0.4578,-0.0181 0.16611,2.65239 0.65997,0.2633 0.0712,-4.56643 0.34158,-0.19428 1.35316,1.68367 0.32832,-0.34354 -0.72644,-2.0551 z" />


                                    <path id="left_chest" alt="Left Chest" data-name="Left Chest" style="opacity:1"
                                        fill=""
                                        d="m 20.337455,17.085495 1.72942,3.09103 1.89346,0.94785 -1.15295,0.90662 -0.90604,2.63773 -2.09968,0.86537 -3.34524,-1.655 0.83425,-6.50527 z" />
                                    <path id="right_chest" alt="Right Chest" data-name="Right Chest" style="opacity:1"
                                        fill=""
                                        d="m 11.351215,17.085495 -1.7294199,3.09103 -1.89346,0.94785 1.15295,0.90662 0.90586,2.63773 2.0996699,0.86537 3.34636,-1.655 -0.83462,-6.50527 z" />

                                    <path id="left_ribs" alt="Left Ribs" data-name="Left Ribs" style="opacity:1"
                                        fill=""
                                        d="m 19.288925,26.151995 -3.11202,-1.40604 0.0937,2.27965 2.80119,1.43603 z m 1.93471,1.66849 -1.29355,0.7212 0.14997,-1.70898 z m -1.05303,-1.63718 2.47968,-1.03241 -0.9336,2.52093 z m 1.53164,1.73729 -1.69005,1.03372 -0.28871,2.0678 1.64975,-1.07533 z m -2.91143,1.10421 -0.0622,1.62387 -2.30308,-0.49961 -0.12448,-2.21722 z m -0.1556,2.4045 0.0311,1.99844 -2.20953,0.59391 -0.0311,-3.1227 z m 2.65459,-0.98535 -1.48383,1.03372 -0.20622,2.10905 1.64862,-1.32355 z" />
                                    <path id="left_belly" alt="Left Belly" data-name="Left Belly" style="opacity:1"
                                        fill=""
                                        d="m 19.641935,34.707615 1.81341,-1.36479 0.15748,1.83347 1.28642,2.37338 -1.98044,2.73652 -1.03109,0.16554 -0.37026,-3.88816 z" />

                                    <path id="right_ribs" alt="Right Ribs" data-name="Right Ribs" style="opacity:1"
                                        fill=""
                                        d="m 12.399365,26.152365 3.11202,-1.40603 -0.0937,2.27965 -2.80138,1.4364 z m -1.93508,1.6685 1.29355,0.72139 -0.14997,-1.70899 z m 1.05303,-1.637 -2.4793099,-1.03259 0.93361,2.52148 z m -1.5316399,1.73729 1.6900499,1.03372 0.28871,2.06743 -1.64881,-1.07515 z m 2.9114199,1.10421 0.0623,1.62387 2.30327,-0.49961 0.12448,-2.21703 z m 0.15561,2.40432 -0.0309,1.99844 2.20973,0.59353 0.0311,-3.1227 z m -2.6546,-0.98516 1.48384,1.0339 0.20622,2.10905 -1.64975,-1.32355 z" />
                                    <path id="Right Belly" alt="Right Belly" data-name="Right Belly" style="opacity:1"
                                        fill=""
                                        d="m 12.045985,34.707615 -1.81341,-1.36479 -0.15748,1.83347 -1.2856799,2.37432 1.9804499,2.73595 1.03109,0.16554 0.37119,-3.88721 z" />

                                    <path id="belly" alt="Belly" data-name="Belly" style="opacity:1"
                                        fill=""
                                        d="m 15.636055,44.919735 -0.60647,-5.91209 -0.015,-3.84879 -2.18479,-1.07533 -0.24746,7.03017 z m 0.41581,-5.7e-4 0.60628,-5.91209 0.0154,-3.84915 2.18404,-1.07515 0.24746,7.03017 z" />

                                    <path id="genitalia" alt="Genitalia" data-name="Genitalia" style="opacity:1"
                                        fill=""
                                        d="m 14.404465,45.040075 0.0221,-0.0277 -0.14866,-0.37945 -3.10172,-3.40449 -0.23283,-0.0825 2.05918,5.32009 z m -1.17263,2.01833 1.27705,3.29948 0.42631,-4.04862 -0.25196,-0.64303 z m 4.05219,-2.01795 -0.0221,-0.0281 0.14867,-0.37926 3.10171,-3.40449 0.23246,-0.0825 -2.05843,5.3199 z m 1.17263,2.01795 -1.27706,3.29948 -0.42631,-4.04843 0.25197,-0.64303 z" />

                                    <path id="left_thigh" alt="Left Thigh" data-name="Left Thigh" style="opacity:1"
                                        fill=""
                                        d="m 23.419015,50.399125 -0.15504,4.75091 -2.40263,6.60949 0.7362,1.90021 2.36401,-8.34435 z m -0.58154,-11.60825 -0.15485,4.00722 1.31793,7.93154 0.61977,-6.40308 z m -0.38731,5.12268 -2.75152,6.07258 -0.62015,4.87425 1.16232,6.85771 2.51886,-6.98144 0.15504,-7.18764 z" />
                                    <path id="left_innerthigh" alt="Left Innerthigh" data-name="Left Innerthigh"
                                        style="opacity:1" fill=""
                                        d="m 22.063225,39.369605 v 4.21363 l -2.94574,5.82511 -1.86027,5.78349 0.19365,-4.0072 z m -3.24944,13.42596 -0.0649,0.15467 -1.21294,2.90207 0.78325,7.18803 1.23619,-0.66122 -1.0714,-6.69272 z" />
                                    <path id="left_knee" alt="Left Knee" data-name="Left Knee" style="opacity:1"
                                        fill=""
                                        d="m 21.404635,64.784375 0.1243,1.12295 -0.87118,1.08171 -0.29058,1.70599 -0.58116,0.24933 -0.49774,-2.57866 -0.33182,-0.91486 0.29058,-0.58247 z m -3.85853,0.0832 0.6224,1.74685 1.3273,2.57867 -0.33182,2.37095 -0.95423,-2.66209 -0.78738,-1.49734 z m 4.97811,-2.37039 -0.95423,5.11609 0.62241,-0.33295 0.49773,1.66381 z" />
                                    <path id="left_calf" alt="Left Calf" data-name="Left Calf" style="opacity:1"
                                        fill=""
                                        d="m 18.251375,70.441125 0.29058,0.91486 0.6224,3.8681 0.0829,5.15733 -0.87136,5.03304 0.0412,-6.44714 -0.91242,-2.57848 -0.12561,-2.82837 z m 1.9915,2.32915 -0.20753,7.73637 -1.65949,6.23904 1.80478,-0.853 3.00816,-10.83583 -1.03727,-6.82095 z" />
                                    <path id="left_feet" alt="Left Feet" data-name="Left Feet" style="opacity:1"
                                        fill=""
                                        d="m 17.255895,87.868445 0.1243,3.45228 0.28983,1.20638 h 0.87136 l 0.24897,-0.83181 0.29058,-0.0416 -0.0624,0.83181 1.09914,-0.33332 0.29058,-0.16629 1.24444,-0.27033 0.0416,-0.97748 -1.20319,-2.03743 -0.82974,-1.0399 -2.03294,-0.83181 z" />


                                    <path id="right_thigh" alt="Right Thigh" data-name="Right Thigh" style="opacity:1"
                                        fill=""
                                        d="m 8.2694651,50.399125 0.15504,4.75053 2.4026299,6.60968 -0.73638,1.90021 -2.3640099,-8.34435 z m 0.58117,-11.60768 0.15503,4.00684 -1.31754,7.93154 -0.61978,-6.40308 z m 0.38769,5.1223 2.7515099,6.07239 0.61997,4.87425 -1.16232,6.85771 -2.5190499,-6.98163 -0.15504,-7.18801 z" />
                                    <path id="right_innerthigh" alt="Right Innerthigh" data-name="Right Innerthigh"
                                        style="opacity:1" fill=""
                                        d="m 9.6258251,39.369415 v 4.21363 l 2.9451699,5.8253 1.86028,5.78349 -0.19366,-4.0072 z m 3.2488699,13.42559 0.0647,0.15485 1.21294,2.90207 -0.78307,7.18803 -1.23618,-0.66102 1.0714,-6.69273 z" />
                                    <path id="right_knee" alt="Right Knee" data-name="Right Knee" style="opacity:1"
                                        fill=""
                                        d="m 10.284405,64.784375 -0.12448,1.12295 0.87118,1.08171 0.29058,1.70599 0.58116,0.24933 0.49774,-2.57866 0.33182,-0.91486 -0.29058,-0.58247 z m 3.85854,0.0832 -0.62241,1.74685 -1.32767,2.57867 0.33182,2.37095 0.95423,-2.66209 0.78832,-1.4964 z m -4.9786799,-2.37058 0.9542299,5.11609 -0.6223999,-0.33313 -0.49793,1.6638 z" />
                                    <path id="right_calf" alt="Right Calf" data-name="Right Calf" style="opacity:1"
                                        fill=""
                                        d="m 13.437675,70.440945 -0.29058,0.91486 -0.62241,3.86828 -0.0829,5.15733 0.87174,5.03304 -0.0418,-6.44714 0.91298,-2.57848 0.1243,-2.82837 z m -1.99151,2.32914 0.20735,7.73637 1.65968,6.23904 -1.80497,-0.85299 -3.0079799,-10.83584 1.03728,-6.82095 z" />
                                    <path id="feet_right" alt='Feet Right' data-name="Feet Right" style="opacity:1"
                                        fill=""
                                        d="m 14.433335,87.868265 -0.12448,3.45228 -0.29058,1.20637 h -0.87118 l -0.24877,-0.83181 -0.29059,-0.0416 0.0623,0.83181 -1.09934,-0.33333 -0.29058,-0.16629 -1.2448,-0.27033 -0.0412,-0.97747 1.2031899,-2.03781 0.82975,-1.04009 2.03294,-0.83181 z" />



                                    <path id="back_head" alt="Back Head" data-name="Back Head" style="opacity:1"
                                        fill=""
                                        d="m 48.157455,6.3585449 0.44208,-0.14964 0.16111,0.16427 1.48163,4.0475101 2.32401,1.45118 2.39971,-1.52387 0.97577,-3.6896901 0.52752,-0.55908 0.23367,0.0981 0.24198,-3.34467 -2.03129,-2.31103004 -2.84509,-0.51629 -2.20422,0.52915 -1.93631,2.63077004 z" />
                                    <path id="nape" alt="Nape" data-name="Nape" style="opacity:1"
                                        fill=""
                                        d="m 52.369695,12.105075 -2.35767,-1.55045 -1.47119,-3.9514301 -0.60741,0.0403 0.27409,1.82447 0.97635,0.33932 0.7613,2.2157201 0.33017,1.06849 0.0895,2.14894 1.16448,0.008 0.10563,-0.70833 0.54716,-0.0606 z m 1.01793,1.47595 0.23768,0.64982 1.38107,-0.004 0.01,-2.38784 0.25971,-0.79061 0.57215,-2.1698001 0.76359,-0.41018 0.25158,-1.78416 -0.62859,0.0193 -1.08488,3.8998101 -2.39725,1.46684 0.2768,1.48507 z" />


                                    <path id="left_clavicule" alt="Left Clavicule" data-name="Left Clavicule"
                                        style="opacity:1" fill=""
                                        d="m 49.625175,14.629325 0.063,-2.62462 -0.71441,1.15181 -4.37994,1.49796 4.97857,8.36746 1.83043,5.08188 -0.21949,-13.55362 z" />
                                    <path id="left_back" alt="Left Back" data-name="Left Back" style="opacity:1"
                                        fill=""
                                        d="m 42.200945,16.586495 -1.57473,1.56517 -0.81404,2.06905 -0.38603,2.52859 1.83679,-1.23927 2.76223,-1.15538 1.84691,3.4342 1.13679,5.49715 0.0767,5.8593 4.07066,1.10938 -0.10355,-7.94098 -1.94107,-4.90022 -5.04395,-8.19334 z" />
                                    <path id="left_armback" alt="Left Armback" data-name="Left Armback"
                                        style="opacity:1" fill=""
                                        d="m 43.185645,27.069445 0.4297,-1.4164 1.30458,-1.68577 -1.39393,-2.96155 -2.28367,0.92162 -1.83567,1.7467 -0.53524,1.78673 0.27068,4.30806 z m -2.46869,15.35539 -1.5182,0.0863 -0.78184,-0.65295 -1.16168,2.1855 -0.78414,3.34805 0.49892,0.20949 0.54632,-2.2158 0.50597,0.24175 -0.29779,2.5019 0.62936,0.22875 0.35546,-2.50096 0.56242,0.16536 -0.16126,2.77057 0.77674,0.30455 0.19056,-2.87291 0.45724,-0.0289 0.22827,2.64778 0.66597,0.24774 -0.0359,-4.56685 0.33693,-0.20224 1.39227,1.65147 0.32017,-0.35115 -0.77444,-2.03749 z m -0.97726,-0.17765 -1.43509,-0.746 -0.30622,-7.00985 c 0,0 0.64359,-2.77938 0.63694,-3.06274 l 0.6093,-1.21924 3.62552,-2.56583 -0.68276,1.9919 0.41561,4.74788 -1.80402,7.69727 z" />


                                    <path id="right_clavicule" alt="Right Clavicule" data-name="Right Clavicule"
                                        style="opacity:1" fill=""
                                        d="m 55.439085,14.728535 -0.063,-2.62463 0.71441,1.15181 4.37994,1.49796 -4.97857,8.36746 -1.83043,5.08189 0.21949,-13.55362 z" />
                                    <path id="right_back" alt="Right Back" data-name="Right Back" style="opacity:1"
                                        fill=""
                                        d="m 62.863315,16.685695 1.57473,1.56518 0.81404,2.06904 0.0384,2.52859 -1.48921,-1.23926 -2.76223,-1.15539 -1.84691,3.4342 -1.13679,5.49715 -0.0767,5.8593 -4.07066,1.10938 0.10355,-7.94098 1.94107,-4.90021 5.04395,-8.19335 z" />
                                    <path id="right_armback" alt="Right Armback" data-name="Right Armback"
                                        style="opacity:1" fill=""
                                        d="m 61.657445,27.250625 -0.32785,-1.05121 -1.27383,-2.05489 1.38708,-2.96476 2.28579,0.91634 1.83971,1.74245 0.53937,1.78549 -0.26073,4.30868 z m 2.64394,15.3417 1.51839,0.0828 0.78033,-0.65476 1.16673,2.18281 0.79187,3.34623 -0.49843,0.21064 -0.55144,-2.21453 -0.50541,0.24292 0.30356,2.5012 -0.62882,0.23021 -0.36124,-2.50014 -0.56203,0.16666 0.16765,2.77019 -0.77603,0.30634 -0.19719,-2.87245 -0.45732,-0.0278 -0.22215,2.64829 -0.66539,0.24928 0.0254,-4.56692 -0.3374,-0.20146 -1.38845,1.65469 -0.32098,-0.35041 0.76973,-2.03928 z m 0.97685,-0.1799 1.43335,-0.74932 0.29002,-7.01054 c 0,0 -0.65,-2.77789 -0.64401,-3.06126 l -0.61212,-1.21783 -3.98124,-2.57566 1.0222,1.93525 -0.38967,4.82212 1.8218,7.69308 z" />

                                    <path id="column" alt="Column" data-name="Column" style="opacity:1"
                                        fill=""
                                        d="m 51.733705,14.788555 0.53876,25.33066 0.48967,-0.0297 0.65658,-25.3387 -0.28147,-0.84188 -1.25059,-4.9e-4 z" />
                                    <path id="loin" alt="Loin" data-name="Loin" style="opacity:1"
                                        fill=""
                                        d="m 51.818445,37.309575 0.14418,2.97292 1.15984,-0.0241 0.048,-2.96488 2.80867,-0.81981 2.34029,-0.7541 1.34121,3.73319 -4.77886,1.36455 -2.33301,1.2158 -2.37536,-1.2333 -5.45663,-1.37716 1.51961,-3.95743 z" />
                                    <path id="buttock" alt="Buttock" data-name="Buttock" style="opacity:1"
                                        fill=""
                                        d="m 44.742845,39.689035 5.48374,1.86457 2.27386,1.3378 2.74195,-1.74412 4.51804,-1.28077 0.90009,2.29721 0.675,3.4346 -0.81272,5.02838 -2.82636,0.16819 -4.11256,-1.67581 -1.00814,0.39118 -0.95849,-0.39888 -4.44053,1.94411 -2.77023,-0.51478 -0.95181,-6.15325 0.36754,-2.7864 z" />

                                    <path id="left_leg" alt="Left Leg" data-name="Left Leg" style="opacity:1"
                                        fill=""
                                        d="m 51.176145,64.073985 -1.20605,3.01461 0.70738,0.26558 0.89754,3.51771 -0.55801,-4.01191 z m -5.08496,-3.15003 0.63355,1.8609 0.16813,2.03261 0.61314,1.93117 -0.90585,-0.0851 -0.28534,2.15982 z m 4.3014,6.58834 1.27664,4.99697 -0.28984,3.02284 -0.67869,10.06546 -1.66325,0.63506 -3.50399,-11.96959 1.24985,-7.17525 z m 0.54053,20.8287 0.85194,1.3581 0.37189,0.79238 -0.15588,1.21774 -0.76984,0.74446 -1.51185,0.12543 -1.1299,-0.29192 -0.24225,-0.95894 0.80765,-1.30405 -0.22562,-0.85987 0.29679,-0.84153 -0.0194,-1.81524 1.53568,-0.54817 z m -1.19598,0.4675 0.15943,1.25776 -0.6023,0.97431 m -0.54436,0.29544 1.06474,0.40084 1.55326,-0.65137 m -4.19331,-39.53466 4.55099,-2.03879 0.63802,0.23079 0.0353,1.80672 0.075,4.64669 -1.97837,6.04282 0.47612,1.41403 -1.42812,3.29446 -1.76611,-0.30111 -0.50079,-2.11605 -0.1695,-1.75674 -2.42102,-8.15763 -0.34279,-3.64687 z" />
                                    <path id="right_reg" alt="Right Reg" data-name="Right Reg" style="opacity:1"
                                        fill=""
                                        d="m 54.019305,64.073985 1.20605,3.01461 -0.70737,0.26558 -0.89755,3.51771 0.55802,-4.01191 z m 5.08496,-3.15003 -0.63355,1.8609 -0.16813,2.03261 -0.61313,1.93117 0.90584,-0.0851 0.28534,2.15982 z m -4.3014,6.58834 -1.27664,4.99697 0.28984,3.02284 0.67869,10.06546 1.66325,0.63506 3.504,-11.96959 -1.24986,-7.17525 z m -0.54053,20.8287 -0.85194,1.3581 -0.37189,0.79238 0.15589,1.21774 0.76983,0.74446 1.51186,0.12543 1.12989,-0.29192 0.24225,-0.95894 -0.80765,-1.30405 0.22563,-0.85987 -0.29679,-0.84153 0.0194,-1.81524 -1.53568,-0.54817 z m 1.19598,0.4675 -0.15943,1.25776 0.6023,0.97431 m 0.54436,0.29544 -1.06474,0.40084 -1.55326,-0.65137 m 3.56525,-39.90247 -3.97962,-1.70224 -0.56389,0.27131 -0.0528,1.79746 -0.075,4.64669 1.97837,6.04282 -0.47612,1.41403 1.42813,3.29446 1.7661,-0.30111 0.50079,-2.11605 0.1695,-1.75674 2.42102,-8.15763 0.009,-3.68308 z" />
                                </svg>
                                <div class="row mt-3">
                                    <div class="col">
                                        <table id="selected-areas" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Selected Areas</th>
                                                    <th>Color Input</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Selected areas will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <input type="hidden" name="injuredPerson" id="injuredPerson" value="">

                                @if ($is_ready_only != 1)
                                    <div class="savesubmit text-center">
                                        <div class="">
                                            <button name="save_inj"
                                                style="background-color: #086ca6 !important;border-color: #086ca6 !important;"
                                                type="submit" id="button" value="Save & Submit"
                                                class="btn btn-secondary save_inj">{{ 'Save' }}</button>

                                            <button type="button"
                                                style="background-color: #fd3550;border-color: #fd3550;"
                                                class="btn btn-secondary btn-warnings injcancel center"
                                                data-bs-dismiss="modal">{{ 'Cancel' }}</button>
                                            <!--<button type="button" style="background-color: #ffc107;border-color: #ffc107;" class="btn btn-secondary btn-warnings clearbodyparts" data-bs-dismiss="modal">Clear</button> -->

                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- invetigation form end-->
                    </form>
                </div>
                <!--<div class="modal-footer">
                                                                 <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>-->
            </div>
        </div>
    </div>
    <!--end model-->
@stop

@push('script')
    <script>
        $("#injury_model").on("shown.bs.modal", function() {

            $('.injury-box').removeClass('hide');
        });
        $(document).on('click', '.injury-btn', function() {

            var getid = $(this).data('id');
            var inj_id = $(this).data('injid');
            var injuredPerson_type = $('#RowInjTypedata_' + getid).val();
            var injuredPerson_emp = $('#RowInjEmpdata_' + getid).val();
            var injuredPerson_others = $('#RowInjothersdata_' + getid).val();
            var injuredPerson_empName = $('#RowInjothersdata_' + getid).val();

            var errorcount = '0';
            var injuredPerson = '0';

            if (injuredPerson_emp == '' && injuredPerson_empName == '') {
                Swal.fire('Error', 'Please Select Victim Name', 'error');
                errorcount = '1';
            } else {
                errorcount = '0';
                if (injuredPerson_emp != '') {
                    injuredPerson = injuredPerson_emp;
                } else {
                    injuredPerson = injuredPerson_empName;
                }
            }

            if (errorcount == '1') {

                return false;
            } else {

                $('#injuredPerson').val(injuredPerson);
                var accident_id = $('#accident_id').val();
                var acc_prim_add = $('#acc_prim_add').val();

                var emp_details = get_emp_details_by_id(injuredPerson, accident_id, acc_prim_add, inj_id,
                    injuredPerson_type);


                $("#injury_model [name='injperson']").val(injuredPerson);
                $("#injury_model").modal("show");
            }

        });

        function get_emp_details_by_id(injuredPerson, accident_id, acc_prim_add, inj_id, injuredPerson_type) {
            var url = "{{ admin_url('incident/initial-incident/investigation/getbodyEmpdetails') }}";
            var data = {
                partyname: injuredPerson,
                accident_id: accident_id,
                acc_prim_add: acc_prim_add,
                injuredPerson_type: injuredPerson_type,
            };

            $.ajax({
                type: 'POST',
                url: url,
                data: data,
                success: function(data) {
                    console.log(data); // Inspect the response
                    if (data['empdata'] && data['empdata'].length > 0) {
                        $.each(data['empdata'], function(i, emp) {
                            $("#imgMapdata1").val(emp['imgMapdata']);
                            $("#body_prim_id").val(emp['id']);
                            $("#injury_id").val(inj_id);
                        });
                    } else {
                        $("#body_prim_id").val(0);
                        $("#injury_id").val(inj_id);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        }

        $("path").on("click", function() {
            var dataname = $(this).data("name");
            var dataid = $(this).data("name");

            if (!$(this).attr("partclicked")) {
                $(this).attr("partclicked", "true").css("fill", "orange");

                $('#selected-areas tbody').append('<tr data-area="' + dataid + '"><td>' + dataname +
                    '</td><td><input type="text" class="color-input form-control"></td><td><button class="delete-btn btn btn-danger">Delete</button></td></tr>'
                );
            } else {
                $('tr[data-area="' + dataid + '"]').remove();
                $(this).removeAttr("partclicked");

            }
        });

        $("path").on("mouseenter", function() {
            if (!$(this).attr("partclicked")) {
                $(this).css("fill", "blue"); // Change to any hover color
            }
        });

        $("path").on("mouseleave", function() {
            if (!$(this).attr("partclicked")) {
                $(this).css("fill", ""); // Reset or change to default color
            }
        });

        $(document).on('input', '.color-input', updateColor);


        function updateColor() {
            const $input = $(this);
            const $row = $input.closest('tr');
            const areaId = $row.data('area');

            const $path = $('path[data-name="' + areaId + '"]');

            if ($input.val().trim() !== '') {
                $path.css('fill', 'red');
            } else {
                $path.css('fill', 'orange');
            }
        }


        $(document).on('click', '.delete-btn', function() {
            const areaName = $(this).closest('tr').data('area');

            const $path = $('path[data-name="' + areaName + '"]');
            $path.removeAttr('partclicked').css('fill', '');

            $(this).closest('tr').remove();
        });

        $("#injuryform").validate({

            // rules: {
            //     "inci_event_related": {
            //         required: true,
            //         maxlength: 100,
            //         //programming_char:true,
            //         minlength: 3
            //     },

            // },
            // messages: {

            //     "inci_event_related": {
            //         required: "Incident Event Related Name is required"
            //     },

            // },
            submitHandler: function(form) {
                var formDatas = $('#injuryform').serialize();

                var imgdata = $('#injuryform').serializeArray();
console.log(imgdata);
                if (imgdata[1]['name'] == "imgMapdata" && imgdata[1]['value'] ==
                    '{"map":{}}') {
                    Swal.fire('Error', 'Please Select Body Parts', 'error');

                } else {

                    var allFilleddesc = true;
                    $(".img-desc .des_injury1_img").each(function(i, ele) {
                        if ($(ele).val() == '') {
                            allFilleddesc = false;
                            return false; // Exit the loop early
                        }
                    });


                    var url =
                        "{{ admin_url('incident/initial-incident/addInjury') }}";

                    $("#bodypartimage").val("");
                    // const image = document.getElementById('img-imgmap1');
                    // const canvas = document.getElementById('image1_canvas_marked');

                    // const tempCanvas = document.createElement('canvas');

                    // const tempCtx = tempCanvas.getContext('2d');
                    // tempCanvas.width = canvas.width;
                    // tempCanvas.height = canvas.height;

                    // tempCtx.drawImage(image, 0, 0);
                    // tempCtx.drawImage(canvas, 0, 0);

                    // const combinedImageUrl = tempCanvas.toDataURL('image/png');
                    // $("#bodypartimage").val(combinedImageUrl);
                    // console.log(combinedImageUrl);

                    // var accident_id = $("#accident_id").val();
                    var formDatas = new URLSearchParams($('#injuryform')
                        .serialize());
                    // formDatas.append('accident_id',
                    //     accident_id); // Append the new key-value pair
                    var data = formDatas.toString()


                    $.ajax({
                        type: 'ajax',
                        dataType: 'json',
                        method: 'post',
                        data: data,
                        url: url,
                        success: function(data) {
                            $('.alert-msg').html(
                                '<span style="color:green;">Body Part Saved Successfully!</span>'
                            );
                            $(".alert-msg").show().delay(3000)
                                .fadeOut();
                            setTimeout(function() {
                                $("#injury_model").modal(
                                    'hide');
                                setTimeout(function() {}, 500);
                            }, 1000);

                            var myModal = $('#injury_model').on('shown',
                                function() {
                                    clearTimeout(myModal.data(
                                        'hideInteval'))
                                    var id = setTimeout(function() {
                                        myModal.modal(
                                            'hide');
                                    });
                                })

                        }
                    });
                }
            }
        });
        $('#download-btn').on('click', function() {
            const svgElement = document.getElementById("bodyparts");
            const svgData = new XMLSerializer().serializeToString(svgElement);

            const svgBlob = new Blob([svgData], {
                type: "image/svg+xml;charset=utf-8"
            });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                const padding = 20; // Padding in pixels (top and bottom)

                const canvas = document.createElement("canvas");
                canvas.width = svgElement.clientWidth;
                canvas.height = svgElement.clientHeight + (padding * 2);
                const context = canvas.getContext("2d");
                context.fillStyle = "#ffffff";
                context.fillRect(0, 0, canvas.width, canvas.height);
                // Draw SVG image with padding at top
                context.drawImage(img, 0, padding);


                URL.revokeObjectURL(url);

                // Get base64 PNG
                const base64Data = canvas.toDataURL("image/png");

                // Set it in textarea
                $('#base64-textarea').val(base64Data);


                // Download the image
                const pngUrl = canvas.toDataURL("image/png");

                const downloadLink = document.createElement("a");
                downloadLink.href = pngUrl;
                downloadLink.download = "svg_image.png";
                downloadLink.click();
            };

            img.src = url;
        });
    </script>
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        flatpickr("#incident_date_time", {
            enableTime: true,
            dateFormat: "d-m-Y H:i",
            time_24hr: true,
            maxDate: new Date(),
            onChange: function(selectedDates, dateStr, instance) {
                validateReportingTime();
            }
        });

        flatpickr("#time_of_reporting", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            onChange: function(selectedDates, dateStr, instance) {
                validateReportingTime();
            }
        });

        function validateReportingTime() {
            var incidentDateTimeStr = $("#incident_date_time").val();
            var reportingTimeStr = $("#time_of_reporting").val();

            if (incidentDateTimeStr && reportingTimeStr) {
                // Parse incident full datetime
                var incidentDateTime = moment(incidentDateTimeStr, "D-M-YYYY HH:mm");

                // Extract just the date portion
                var incidentDateOnly = moment(incidentDateTimeStr, "D-M-YYYY HH:mm").format("D-M-YYYY");

                // Combine the same date with the reporting time
                var reportingDateTime = moment(incidentDateOnly + " " + reportingTimeStr, "D-M-YYYY HH:mm");

                // Check if reporting is BEFORE incident
                if (reportingDateTime.isBefore(incidentDateTime)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Time',
                        text: 'Time of reporting cannot be before the Incident Date an  d Time.',
                        confirmButtonText: 'OK'
                    });
                    $("#time_of_reporting").val('');
                }
            }
        }


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
                    <small>Allowed file types: png, jpeg , jpg, pdf, doc,docx, mp4</small>
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
                extension: "png|jpeg|jpg|pdf|doc|docx|mp4",
                messages: {
                    required: "This field is required.",
                    extension: "Allowed file types: png, jpeg, jpg, pdf, doc, docx, mp4",
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


        $('#reporting_media_others').on('change', function() {
            if ($(this).is(':checked')) {
                $('#reporting_media_othersdiv').show();
            } else {
                $('#reporting_media_othersdiv').hide();
            }
        });
        $('.reported_name').select2({
            ajax: {
                url: "{{ admin_url('incident/initial-incident/employeename') }}",
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
            minimumInputLength: 3,
            dropdownCssClass: 'form-control',
            selectionCssClass: 'form-control'
        });


        $(document).on("change", ".reported_name", function() {
            var emp_id = $(this).val();
            var currentRow = $(this).closest(".row");

            if (emp_id) {
                $.ajax({
                    url: "{{ url('incident/initial-incident/fetchEmployeeDetails') }}/" + emp_id,
                    type: "GET",
                    success: function(data) {
                        if (data.employee) {
                            currentRow.find('.employee_code').val(data.employee.emp_id);
                            currentRow.find('.designation').val(data.employee.designation);

                            var departmentDropdown = currentRow.find('.department');
                            departmentDropdown.empty();
                            departmentDropdown.append('<option value="">Select Department</option>');

                            if (data.departments && data.departments.length > 0) {
                                data.departments.forEach(function(department) {
                                    var selected = data.employee.department == department.id ?
                                        "selected" : "";
                                    departmentDropdown.append(
                                        `<option value="${department.id}" ${selected}>${department.department_name}</option>`
                                    );
                                });
                            } else {
                                departmentDropdown.append(
                                    '<option value="">No departments available</option>');
                            }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Employee data could not be fetched.",
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while fetching employee details.",
                        });
                    }
                });
            } else {
                currentRow.find('.employee_code').val("");
                currentRow.find('.designation').val("");
                var departmentDropdown = currentRow.find('.department');
                departmentDropdown.empty();
                departmentDropdown.append('<option value="">Select Department</option>');
            }
        });


        $(document).ready(function() {
            $('input[name="anyone_injured"]').on('change', function() {
                if ($(this).val() == '1') {
                    $('.injuryDetails').show();
                } else {
                    $('.injuryDetails').hide();
                }
            });

            function initializeSelect2() {
                $('.responsible_person').select2({
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
            }

            // Initialize select2 on page load
            initializeSelect2();

            let injuryIndex = 0;

            // Add new injury details row
            $(document).on("click", ".addMoreInjuryDetails", function() {
                injuryIndex++;
                let newRow = `
           <div class="row injury-append" style="margin-top: 20px;" id="injuryDetails_${injuryIndex}">
            <div class="col-md-4 form-input">
                    <label for="" class="form-label require">Injury Person Type</label>
                    <select class="form-control require single-select selectInjPersontype" alt="${injuryIndex}" name="injury_person[${injuryIndex}][injury_person_type]" style="width: 100%" id="RowInjTypedata_${injuryIndex}">
                        <option value="">Select Person Type</option>
                        <option value="{{ encryptId('1') }}">Employee</option>
                        <option value="{{ encryptId('2') }}">Worker</option>
                        <option value="{{ encryptId('3') }}">Others</option>
                    </select>
            </div>
            <div class="col-md-4 form-input" id="injuryPersonTextContainer_${injuryIndex}">
                    <label class="form-label require">Injury Person Name</label>
                <input type="text"  alt="${injuryIndex}" class="form-control injuryPersonNamerequire"
                    name="injury_person[${injuryIndex}][injury_person_name]" id="RowInjothersdata_${injuryIndex}"
                    placeholder="Enter Injury Person Name">
            </div>
                    <!-- Injury Person Name (Dropdown) -->
                <div class="col-md-4 form-input d-none" id="injuryPersonDropdownContainer_${injuryIndex}">
                    <label class="form-label require">Injury Person Name</label>
                    <select class="form-control require injuryPersonName single-select" alt="${injuryIndex}"  style="width: 100%" name="injury_person[${injuryIndex}][injury_person_id]"  id="RowInjEmpdata_${injuryIndex}">
                        <option value="" disabled selected>Select Injury Person Name  </option> </select>
                </div>

            <div class="col-md-4 form-input">
                <label class="form-label require">Injury Person Designation</label>
                <input type="text" alt="${injuryIndex}" name="injury_person[${injuryIndex}][injury_person_designation]" id="InjPerDest_${injuryIndex}" class="form-control InjPerDest">
            </div>
                <div class="col-md-4 form-input" id="injuryPersonDepttexxt_${injuryIndex}">
                    <label class="form-label require">Injury Person Department</label>
                    <input type="text" alt="${injuryIndex}"  name="injury_person[${injuryIndex}][injury_person_department_id]"
                    class="form-control InjPerDept" id="InjPerDept_${injuryIndex}"> 
                    
                </div>



            <div class="col-md-4">
                <div class="form-group form-input">
                <label for="nature_of_injury_${injuryIndex}" class="form-label">Nature of  Injury</label>
                    <select name="injury_person[${injuryIndex}][nature_of_injury]" alt="${injuryIndex}"  id="nature_of_injury_${injuryIndex}"
                    style="width: 100%" class="form-control single-select">
                        <option value="">Nature of Injury</option>
                        <option value="{{ encryptId('1') }}">Major</option>
                        <option value="{{ encryptId('2') }}">Minor</option>
                            </select>
                </div>
             </div>   

                <div class="col-md-2 form-input">
                    <label for="inputFirstName" class="form-label require">Location of the Injury</label></br>
                    <span class="input-group-addon injury-btn btn btn-info" data-id='${injuryIndex}' data-injid="${injuryIndex}" attr_emp="" alt="${injuryIndex}"><i class="fa fa-male" aria-hidden="true"></i></span>
                </div>
            <div class="col-md-2 text-right">
                <button type="button" class="btn btn-danger btn-sm removeInjuryDetails" data-index="${injuryIndex}" style="margin-top: 35px;">Remove</button>
            </div>
              </div>`;

                $(".injury-details-templat").append(newRow);
                $(".single-select").select2();

                addInjuryPersonValidation(injuryIndex);
                initializeSelect2();

            });

            $(document).on("change", "[name^='injury_person'][name$='[injury_person_type]']", function() {
                var injury_person_type = $(this).val();
                var injuryIndex = $(this).attr("alt");
                // Get the index of the current row
                var injuryPersonDropdownContainer = $("#injuryPersonDropdownContainer_" + injuryIndex);
                var injuryPersonTextContainer = $("#injuryPersonTextContainer_" + injuryIndex);
                var injuryPersonDropdown = $('#RowInjEmpdata_' + injuryIndex);
                var injuryPersonDeptDropdown = $("#injuryPersonDeptDropdown_" + injuryIndex);
                var injuryPersonDepttexxt = $("#injuryPersonDepttexxt_" + injuryIndex);

                // Reset Fields
                injuryPersonDropdown.empty().append('<option value="">Select Injury Person Name</option>');
                $('#RowInjothersdata_' + injuryIndex).val("");
                $('#InjPerDest_' + injuryIndex).val("");
                $('#InjPerDept_' + injuryIndex).val("");

                if (injury_person_type === "{{ encryptId('1') }}" || injury_person_type ===
                    "{{ encryptId('2') }}") {
                    // Show Injury Person Name Dropdown, Hide Text Field
                    injuryPersonDropdownContainer.removeClass("d-none");
                    injuryPersonTextContainer.addClass("d-none");
                    injuryPersonDeptDropdown.addClass("d-none");

                    // Fetch Employee/Worker List
                    $.ajax({
                        url: "{{ url('incident/initial-incident/fetchEmployeeOrWorkerList') }}/" +
                            injury_person_type,
                        type: "GET",
                        dataType: "json",
                        success: function(data) {
                            if (data.length > 0) {
                                $.each(data, function(index, item) {
                                    injuryPersonDropdown.append(
                                        `<option value="${item.id}">${item.text}</option>`
                                    );
                                });
                            } else {
                                Swal.fire("No Data", "No records found for the selected type.",
                                    "info");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "An error occurred while fetching the list.",
                                "error");
                        }
                    });

                } else if (injury_person_type === "{{ encryptId('3') }}") {
                    // Show Input Fields for Others
                    injuryPersonTextContainer.removeClass("d-none");
                    injuryPersonDropdownContainer.addClass("d-none");
                    injuryPersonDeptDropdown.removeClass("d-none");

                } else {
                    injuryPersonDropdownContainer.addClass("d-none");
                    injuryPersonTextContainer.addClass("d-none");
                    injuryPersonDeptDropdown.removeClass("d-none");
                }
            });

            $(document).on("change", ".injuryPersonName", function() {
                var $this = $(this);
                var injury_person_id = $this.val();
                var injuryIndex = $this.attr("alt");
                var injury_person_type = $("#RowInjTypedata_" + injuryIndex).val();
                var isAlreadySelected = false;
                $(".injuryPersonName").not(this).each(function() {
                    var existing_person_id = $(this).val();
                    var existing_index = $(this).attr("alt");
                    var existing_person_type = $("#RowInjTypedata_" + existing_index).val();
                    if (existing_person_id === injury_person_id && existing_person_type ===
                        injury_person_type && injury_person_id !== "") {
                        isAlreadySelected = true;
                        return false; // Exit loop
                    }
                });

                if (isAlreadySelected) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Selected value already exists for the same injury type.",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $this.val("").trigger("change"); // Reset field after alert is closed
                    });
                }

                // Define Designation and Department fields
                var injuryPersonDesignation = $("#InjPerDest_" + injuryIndex);
                var injuryPersonDeptInput = $("#InjPerDept_" + injuryIndex);
                if (injury_person_type != 'R1ZPdDJJQnR5WmZNUVJUaDhaelhIdz09') {
                    $.ajax({
                        url: "{{ url('incident/initial-incident/fetchPersonDetails') }}/" +
                            injury_person_id +
                            "/" + injury_person_type,
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            if (response.employee || response.worker) {
                                let person = response.employee || response.worker;

                                if (person.designation) {
                                    injuryPersonDesignation.val(person.designation).prop(
                                        "readonly",
                                        true);
                                } else {
                                    injuryPersonDesignation.val("").prop("readonly", false);
                                }

                                if (person.department_name) {
                                    injuryPersonDeptInput.val(person.department_name).prop(
                                        "readonly", true);


                                } else {
                                    injuryPersonDeptInput.val("").prop("readonly", false);
                                }
                            } else {
                                Swal.fire("Error", "Data could not be fetched.", "error");
                            }
                        },
                        error: function() {
                            Swal.fire("Error", "An error occurred while fetching details.",
                                "error");
                        }
                    });
                }
            });

            // Remove injury details row
            $(document).on("click", ".removeInjuryDetails", function() {
                var $row = $(this).closest(".injury-append");

                if ($(".injury-append").length > 1) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you really want to delete this row?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $row.remove();
                            Swal.fire(
                                'Deleted!',
                                'The row has been deleted.',
                                'success'
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        'Action Denied',
                        'At least one row is required.',
                        'warning'
                    );
                }
            });

            function addInjuryPersonValidation(injuryIndex) {
                $(`select[name="injury_person[${injuryIndex}][injury_person_type]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Injury Person Type is required."
                    }
                });
                $(`input[name="injury_person[${injuryIndex}][injury_person_id]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Injury Person Name is required."
                    }
                });
                $(`input[name="injury_person[${injuryIndex}][injury_person_name]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Injury Person Name is required."
                    }
                });
                $(`input[name="injury_person[${injuryIndex}][injury_person_designation]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Injury Person Designation is required."
                    }
                });
                $(`select[name="injury_person[${injuryIndex}][injury_person_department_id]"], input[name="injury_person[${injuryIndex}][injury_person_department_id]"]`)
                    .rules("add", {
                        required: true,
                        messages: {
                            required: "Injury Person Department is required."
                        }
                    });
            }
            $(function() {
                $('#accidentinvestigation').validate({
                    rules: {
                        'injury_person[0][injury_person_type]': {
                            required: true,
                        },
                        'injury_person[0][injury_person_id]': {
                            required: true,
                        },
                        'injury_person[0][injury_person_name]': {
                            required: true,
                        },
                        'injury_person[0][injury_person_designation]': {
                            required: true,
                        },
                        'injury_person[0][injury_person_department_id]': {
                            required: true,
                        },
                        'witness_id[]': {
                            required: true,
                        },
                        'is_damaged[]': {
                            required: true,
                        },
                        root_cause_analysis: {
                            required: true,
                        },
                        is_treatment: {
                            required: true,
                        },
                        action_taken: {
                            required: true,
                            minlength: 10,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/,
                        },
                        details: {
                            required: function(element) {
                                return $('input[name="is_treatment"]:checked').val() === '1';
                            },
                            minlength: 3,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/,
                        },
                        corrective_preventive_action: {
                            required: true,
                            minlength: 10,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/,
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
                        'injury_person[0][injury_person_type]': {
                            required: "Injury Person Type is required."
                        },
                        'injury_person[0][injury_person_id]': {
                            required: "Injury Person Name is required."
                        },
                        'injury_person[0][injury_person_name]': {
                            required: "Injury Person Name is required."
                        },
                        'injury_person[0][injury_person_designation]': {
                            required: "Injury Person Designation is required."
                        },
                        'injury_person[0][injury_person_department_id]': {
                            required: "Injury Person Department is required."
                        },
                        'witness_id[]': {
                            required: "Witness ID is required.",
                        },
                        'is_damaged[]': {
                            required: "Was anything damaged is required.",
                        },
                        root_cause_analysis: {
                            required: "Root cause analysis is required.",
                        },
                        is_treatment: {
                            required: "Where the injured person receiving any treatment at present is required.",
                        },
                        action_taken: {
                            required: "Action taken is required.",
                            minlength: "Minimum 10 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
                        },
                        details: {
                            required: "Please provide details of the treatment.",
                            minlength: "Details must be at least 3 characters long.",
                            maxlength: "Details cannot exceed 2000 characters.",
                            pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
                        },
                        corrective_preventive_action: {
                            required: "Corrective/preventive action is required.",
                            minlength: "Minimum 10 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \",”.; ( ) are allowed.",
                        },
                        responsible_person_id: {
                            required: "Responsible person ID is required.",
                        },
                        target_date: {
                            required: "Target date is required.",
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

        $(function() {
            $('#incidentAdd').validate({
                rules: {
                    incident_date_time: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    shift: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    location_id: {
                        required: true,
                    },
                    exact_location: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    iir_type: {
                        required: true,
                    },
                    reported_name: {
                        required: true,
                    },
                    designation: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    employee_code: {
                        required: true,
                    },
                    time_of_reporting: {
                        required: true,
                    },
                    'reporting_media[]': {
                        required: true,
                        minlength: 1,
                    },
                    brief_description: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'",”%+\/!\\()]+$/

                    },
                    immediate_action_taken: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'",”%+\/!\\()]+$/

                    },
                    'evidence[0][]': {
                        required: true,
                        extension: "png|jpeg|jpg|pdf|doc|docx|mp4"
                    },

                    anyone_injured: {
                        required: true,
                    },
                },
                messages: {
                    incident_date_time: {
                        required: "Date and Time is required.",
                    },
                    unit_id: {
                        required: "Unit is required.",
                    },
                    shift: {
                        required: "Shift is required.",
                        minlength: "Shift Required must be exactly 2 characters.",
                        maxlength: "Shift Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    location_id: {
                        required: "Location is required.",
                    },
                    exact_location: {
                        required: "Exact Location is required.",
                        minlength: "Exact Location Required must be exactly 2 characters.",
                        maxlength: "Exact Location Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    iir_type: {
                        required: "IIR Type is required.",
                    },
                    reported_name: {
                        required: "Name is required.",
                    },
                    designation: {
                        required: "Designation is required.",
                    },
                    department: {
                        required: "Department is required.",
                    },
                    employee_code: {
                        required: "Employee Code is required.",
                    },
                    time_of_reporting: {
                        required: "Time of reporting is required.",
                    },
                    'reporting_media[]': {
                        required: "At least one Reporting Media is required.",
                        minlength: "At least one Reporting Media must be selected.",
                    },
                    brief_description: {
                        required: "Brief Description is required.",
                        minlength: "Brief Description Required must be exactly 2 characters.",
                        maxlength: "Brief Description Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (”%+-_/!\,-, _, ‘, “, ()) are allowed.",

                    },
                    immediate_action_taken: {
                        required: "Immediate Action Taken is required.",
                        minlength: "Immediate Action Taken Required must be exactly 2 characters.",
                        maxlength: "Immediate Action Taken Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (”%+-_/!\,-, _, ‘, “, ()) are allowed.",

                    },
                    'evidence[0][]': {
                        required: "Evidence is required.",
                        extension: "Invalid file type (Allowed: png, jpeg, jpg, pdf, doc, docx, mp4)"
                    },
                    anyone_injured: {
                        required: "If any person has injured is required.",
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');

                    // Handle error placement for checkboxes
                    if (element.attr("name") === "reporting_media[]") {
                        element.closest('.form-input').find('.text-danger').html(error);
                    } else {
                        element.closest('.form-input').append(error);
                    }
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
                                `Field: ${error.element.name}, Error: ${error.message}`);
                        });
                    }
                },
            });

        });
    </script>
@endpush
