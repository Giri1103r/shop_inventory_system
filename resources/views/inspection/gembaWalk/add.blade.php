@extends('admin.layouts.admin')
@section('title', 'Gemba Walk ')
@section('pageurl', admin_url('gemba-walk/add'))

@section('content')
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
                                    <x-button-back href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="accidentReportAdd" enctype="multipart/form-data"
                                        action="{{ admin_url('inspection/gemba-walk/add/submit') }}">
                                        @csrf

                                        <div class="row">

                                            <div class="row">

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Gemba Walk ID</label>
                                                        <input type="text" name="gemba_walk_id" id="gemba_walk_id"
                                                            class="form-control" placeholder=""
                                                            value = "{{ getsequence('gembaWalk') }}" readonly>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Document No </label>
                                                        <input type="text" name="document_no" id="document_no"
                                                            class="form-control" placeholder=" Enter Document Number ">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Date</label>
                                                        <input type="text" name="document_upload_date"
                                                            id="document_upload_date" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Document Revision Date</label>
                                                        <input type="text" name="document_revision_date"
                                                            id="document_revision_date" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 file-upload-block" id="file-upload-0">
                                                    <label for="gemba_walk_prepared_by" class="form-label">Singnature Upload</label>
                                                    <input type="file"
                                                        class="form-control validate-file-accept validate-file-required"
                                                        name="gemba_walk_prepared_by" id="gemba_walk_prepared_by">
                                                    <div class="text-danger"></div>

                                                </div>
                                            </div>


                                            <div class="mt-4 row">
                                                <div
                                                    class="card-header-inner d-flex justify-content-between align-items-center">
                                                    <h4 class="text-white">Checklist Details</h4>
                                                    <button type="button" class="btn btn-primary addChecklistDetails">Add
                                                        More</button>
                                                </div>
                                                <div id="gemba_walk_checklist">
                                                    <div class="row gemba_walk_checklist_add " data-index="0">
                                                        <div class="row">
                                                            <div class=" col-md-4 form-group form-input">
                                                                <label class="form-label">Sr. No</label>
                                                                <input type="text"
                                                                    name="gemba_walk[0][gemba_walk_report_no]"
                                                                    id="gemba_walk_report_no_0" class="form-control"
                                                                    placeholder="" readonly>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group form-input">
                                                                    <label for="location_id" class="form-label">
                                                                        Location</label>
                                                                    <select name="gemba_walk[0][location_id]"
                                                                        id="location_id_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Location</option>
                                                                        @foreach ($locationList as $loc)
                                                                            <option value="{{ encryptId($loc->id) }}">
                                                                                {{ $loc->location_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group form-input">
                                                                    <label for="unit_id" class="form-label">
                                                                        Unit</label>
                                                                    <select name="gemba_walk[0][unit_id]" id="unit_id_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Unit</option>
                                                                        @foreach ($unitList as $unit)
                                                                            <option value="{{ encryptId($unit->id) }}">
                                                                                {{ $unit->unit_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Date of
                                                                        Observation</label>
                                                                    <input type="text"
                                                                        name="gemba_walk[0][date_of_observation]"
                                                                        id="date_of_observation_0" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label for="observation_type" class="form-label">
                                                                        Observation Type </label>
                                                                    <select name="gemba_walk[0][observation_type]"
                                                                        id="observation_type_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Observation Type
                                                                        </option>
                                                                        <option value="1">Unsafe Act</option>
                                                                        <option value="2">Unsafe Condition</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Description</label>
                                                                    <textarea class="form-control" name="gemba_walk[0][checklist_description]" id="checklist_description_0"></textarea>

                                                                </div>
                                                            </div>


                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Hazard</label>
                                                                    <input type="text" name="gemba_walk[0][hazard]"
                                                                        id="hazard_0" class="form-control"
                                                                        placeholder=" Enter Hazard Observation">
                                                                </div>
                                                            </div>


                                                            <div class="col-md-4 mt-2 file-upload-block"
                                                                id="file-upload-0">
                                                                <label for="evidence_0"
                                                                    class="form-label">Evidence</label>
                                                                <input type="file"
                                                                    class="form-control validate-file-accept validate-file-required"
                                                                    name="gemba_walk[0][evidence]" id="evidence_0">
                                                                <div class="text-danger"></div>

                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">CAPA</label>
                                                                    <input type="text"
                                                                        name="gemba_walk[0][checklist_capa]"
                                                                        id="checklist_capa" class="form-control"
                                                                        placeholder=" Enter Recommended actions  ">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Date of Compliance
                                                                    </label>
                                                                    <input type="text"
                                                                        name="gemba_walk[0][date_of_compliance]"
                                                                        id="date_of_compliance_0" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label for="unit_id" class="form-label">
                                                                        Responsibility </label>
                                                                    <select name="gemba_walk[0][responsibility_id]"
                                                                        id="responsibility_id_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Responsibility Person
                                                                        </option>
                                                                        @foreach ($employeeList as $employee)
                                                                            <option value="{{ encryptId($unit->id) }}">
                                                                                {{ $employee->emp_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label for="" class="form-label">
                                                                        Status </label>
                                                                    <select name="gemba_walk[0][current_status]"
                                                                        id="current_status_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Current Status
                                                                        </option>
                                                                        <option value="1">Open</option>
                                                                        <option value="2">Closed</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Remark</label>
                                                                    <textarea class="form-control" name="gemba_walk[0][checklist_remark]" id="checklist_remark_0"></textarea>

                                                                </div>
                                                            </div>


                                                        </div>




                                                        <div class="row mt-2 observationContainer">
                                                            <div class="col-md-4 form-input observationRow">
                                                                <label class="form-label">Observation</label>
                                                                <textarea class="form-control" name="gemba_walk[0][checklist_observation][0]" id="checklist_observation_0"></textarea>

                                                            </div>

                                                            <div class="col-md-2 mt-2">
                                                                <button type="button"
                                                                    class="btn btn-primary addChecklistObservation"
                                                                    style="margin-top: 30px;" data-index=0>Add
                                                                    More</button>
                                                            </div>
                                                        </div>





                                                    </div>



                                                </div>

                                                <div class="submit-button" style="text-align: right;">
                                                    <x-button-submit class="submit"></x-button-submit>
                                                    <x-button-reset class=""></x-button-reset>
                                                    <x-button-cancel
                                                        href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-cancel>
                                                </div>

                                            </div>



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


@endsection



@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            flatpickr("#document_revision_date", {
                dateFormat: "d-m-Y"
            });
            flatpickr("#document_upload_date", {
                dateFormat: "d-m-Y"
            });
            flatpickr("#date_of_observation_0", {
                enableTime: false,
                dateFormat: "d-m-Y"
            });
            flatpickr("#date_of_compliance_0", {
                enableTime: false,
                dateFormat: "d-m-Y"
            });

            let checklistIndex = 1;
            let checklistCount = 1;

            function generateSerialNumber(index) {
                return `SER${index.toString().padStart(4, '0')}`;
            }

            function updateSerialNumbers() {
                $(".gemba_walk_checklist_add").each(function(i) {
                    let newIndex = i + 1;
                    $(this).attr("id", `checklistDetails_${newIndex}`).attr("data-index", newIndex);
                    $(this).find("[id^='gemba_walk_report_no_']").attr("id",
                        `gemba_walk_report_no_${newIndex}`).val(generateSerialNumber(newIndex));
                });
                index = $(".gemba_walk_checklist_add").length;
            }

            $("#gemba_walk_report_no_0").val(generateSerialNumber(1));

            $(".addChecklistDetails").on("click", function() {
                const newChecklistField = `
                    <div class="row gemba_walk_checklist_add" data-index="${checklistIndex}" id="checklistDetails_${checklistIndex}" >

                            <hr class="mt-3">
                         <div class="row">

                            <div class="col-md-4 form-group form-input">
                                <label class="form-label">Sr. No</label>
                                <input type="text" name="gemba_walk[${checklistIndex}][gemba_walk_report_no]"
                                    id="gemba_walk_report_no_${checklistIndex}" class="form-control"
                                    value="${generateSerialNumber(checklistCount)}" readonly>
                            </div>

                            <div class="col-md-4 form-input">
                                <label class="form-label">Location</label>
                                <select name="gemba_walk[${checklistIndex}][location_id]" id="location_id_${checklistIndex}" 
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Location</option>
                                    @foreach ($locationList as $loc)
                                        <option value="{{ encryptId($loc->id) }}">{{ $loc->location_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-input">
                                <label class="form-label">Unit</label>
                                <select name="gemba_walk[${checklistIndex}][unit_id]" id="unit_id_${checklistIndex}" 
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Unit</option>
                                    @foreach ($unitList as $unit)
                                        <option value="{{ encryptId($unit->id) }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Date of Observation</label>
                                <input type="text" name="gemba_walk[${checklistIndex}][date_of_observation]" 
                                    id="date_of_observation_${checklistIndex}" class="form-control date_of_observation">
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Observation Type</label>
                                <select name="gemba_walk[${checklistIndex}][observation_type]" id="observation_type_${checklistIndex}" 
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Observation Type</option>
                                    <option value="1">Unsafe Act</option>
                                    <option value="2">Unsafe Condition</option>
                                </select>
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_description]" id="checklist_description_${checklistIndex}"></textarea>
                            </div>

                            <div class="col-md-4 form-input mt-2">
                            <label class="form-label">Hazard</label>
                            <input type="text" name="gemba_walk[${checklistIndex}][hazard]" id="hazard_${checklistIndex}"
                                class="form-control" placeholder="Enter Hazard Observation">
                            </div>

                            <div class="col-md-4 form-input mt-2 file-upload-block">
                                <label class="form-label">Evidence</label>
                                <input type="file" class="form-control validate-file-accept validate-file-required"
                                    name="gemba_walk[${checklistIndex}][evidence]" id="evidence_${checklistIndex}">
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">CAPA</label>
                                <input type="text" name="gemba_walk[${checklistIndex}][checklist_capa]" id="checklist_capa_${checklistIndex}" 
                                    class="form-control" placeholder="Enter Recommended actions">
                            </div>


                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label date">Date of Compliance</label>
                                <input type="text" name="gemba_walk[${checklistIndex}][date_of_compliance]" 
                                    id="date_of_compliance_${checklistIndex}" class="form-control date_of_compliance">
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Responsibility</label>
                                <select name="gemba_walk[${checklistIndex}][responsibility_id]" id="responsibility_id_${checklistIndex}"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Responsibility Person</option>
                                    </option>
                                        @foreach ($employeeList as $employee)
                                            <option value="{{ encryptId($unit->id) }}">
                                                {{ $employee->emp_name }}</option>
                                        @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Status</label>
                                <select name="gemba_walk[${checklistIndex}][current_status]" id="current_status_${checklistIndex}" 
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Current Status</option>
                                    <option value="1">Open</option>
                                    <option value="2">Closed</option>
                                </select>
                            </div>

                            <div class="col-md-4 form-input mt-2">
                                <label class="form-label">Remark</label>
                                <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_remark]" id="checklist_remark_${checklistIndex}"></textarea>
                            </div> 

                          </div>  


                            <div class=" row col-md-6">
                                <div class="observationContainer">
                                    <div class="row observationRow">
                                        <div class="col-md-8 form-input">
                                            <label class="form-label">Observation</label>
                                            <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_observation][0]"></textarea>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" class="btn btn-primary addChecklistObservation"  data-index=
                                            "${checklistIndex}" >Add More</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mb-3 mt-3 d-flex align-items-end justify-content-end">
                                <button type="button" class="btn btn-danger btn-sm removeChecklistDetails">Remove</button>
                            </div>
                </div>`;


                if ($(".gemba_walk_checklist_add").length >= 200) {
                    Swal.fire({
                        icon: "error",
                        title: "Sorry!",
                        text: "You cannot add more than 200 checklist items."
                    });
                    return;
                }

                $("#gemba_walk_checklist").append(newChecklistField);

                updateSerialNumbers();
                checklistCount++;
                checklistIndex++;

                $(".single-select").select2({
                    width: "100%"
                });
                $(".date_of_compliance, .date_of_observation").flatpickr({
                    dateFormat: "d-m-Y"
                });
            });

            $(document).on("click", ".addChecklistObservation", function() {
                const checklistRow = $(this).closest(".gemba_walk_checklist_add");
                const rowIndex = $(this).data("index");
                const observationContainer = checklistRow.find(".observationContainer");
                const observationIndex = checklistRow.find(".observationRow")
                    .length; // Correct index assignment
                // alert(rowIndex,observationIndex);

                if (observationIndex >= 5) {
                    Swal.fire({
                        icon: "error",
                        title: "Sorry!",
                        text: "Maximum 5 observations allowed."
                    });
                    return;
                }

                const newObservationField = `
            <div class="row mt-2 observationRow">
                <div class="col-md-8 form-input">
                    <label class="form-label">Observation</label>
                    <textarea class="form-control" name="gemba_walk[${rowIndex}][checklist_observation][${observationIndex}]"></textarea>
                </div>
                <div class="col-md-4 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm removeChecklistObservation">
                        <i class="fa-solid fa-trash text-light"></i>
                    </button>
                </div>
            </div>`;

                observationContainer.append(newObservationField);
            });

            $(document).on("click", ".removeChecklistObservation", function() {
                $(this).closest(".observationRow").remove();
            });

            $(document).on("click", ".removeChecklistDetails", function() {
                $(this).closest(".gemba_walk_checklist_add").remove();
                checklistIndex--;
                updateSerialNumbers();
            });
        });
    </script>
@endpush
