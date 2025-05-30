@extends('admin.layouts.admin')
@section('title', 'Gemba Walk Inspection (Safety Observation)')
@section('pageurl', admin_url('inspection/gemba-walk/add'))

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
                                    <form method="POST" id="gembaWalkReportAdd" enctype="multipart/form-data"
                                        action="{{ admin_url('inspection/gemba-walk/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require ">Gemba Walk ID</label>
                                                        <input type="text" name="gemba_walk_id" id="gemba_walk_id"
                                                            class="form-control" placeholder=""
                                                            value = "{{ getsequence('gembaWalk') }}" readonly>
                                                    </div>
                                                </div>


                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Document No </label>
                                                        <input type="text" name="document_no" id="document_no"
                                                            class="form-control" placeholder=" Enter Document Number "
                                                            value="{{ $document_no->doc_no }}" readonly>
                                                    </div>
                                                </div>



                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                        <input type="text" name="issue_date" id = ""
                                                            class="form-control" placeholder="Issued Date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Document Revision Date</label>
                                                        <input type="text" name="document_revision_date"
                                                            id="document_revision_date" value="{{ $document_no->rev_dt }}"
                                                            readonly class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require ">Date</label>
                                                        <input type="text" name="document_upload_date"
                                                            id="document_upload_date" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Shift</label>
                                                        <select name="shift" id="shift" style="width: 100%"
                                                            class="form-control single-select">
                                                            <option value="">Select the option</option>
                                                            @foreach ($shift as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->shift }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>



                                                {{-- <div class="col-md-4 form-group form-input mb-2">
                                                    @if (isset(Auth::user()->signature_upload))
                                                        <label class="form-label"
                                                            style="display: block; ">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                            alt="Signature Upload" style="width: 150px; margin-top:10px">
                                                    @else
                                                        <div class="col-md-12 mt-2 file-upload-block">
                                                            <label class="form-label require">Signature</label>
                                                            <input type="file" class="form-control"
                                                                name="gemba_walk_prepared_by" id="gemba_walk_prepared_by"
                                                                placeholder="Enter the image">
                                                            <small>Allowed file types: jpg, jpeg, png</small>
                                                            <div class="text-danger"></div>
                                                        </div>
                                                    @endif
                                                </div> --}}



                                                {{-- <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label for="responsible_person_id"
                                                            class="form-label require">Executive Person</label>
                                                        <select name="executive_person_id" id="executive_person_id"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Executive Person</option>
                                                        </select>
                                                    </div>
                                                </div> --}}


                                                <input type="hidden" name="document_reference_id"
                                                    value="{{ $document_no->id }}">
                                            </div>

                                            <div class="mt-4 row">
                                                <div
                                                    class="card-header-inner d-flex justify-content-between align-items-center">
                                                    <h4 class="text-white">{{ __('inspection.checklist_details') }}</h4>
                                                    {{-- <button type="button"
                                                        class="btn mb-2 btn-primary addChecklistDetails">Add
                                                        More</button> --}}
                                                </div>
                                                <div id="gemba_walk_checklist">

                                                    <div class="row gemba_walk_checklist_add " data-index="0">

                                                        <div class="row">

                                                            <div class="col-md-4">
                                                                <div class="form-group  form-input">
                                                                    <label for="location_id" class="require form-label">
                                                                        {{ __('inspection.location') }}</label>
                                                                    <select name="gemba_walk[0][location_id]"
                                                                        id="location_id" class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Location</option>
                                                                        @foreach ($locationList as $loc)
                                                                            <option value="{{ encryptId($loc->id) }}">
                                                                                {{ $loc->location_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <div class="form-group form-input">
                                                                    <label
                                                                        class="form-label require">{{ __('common.unit') }}</label>
                                                                    <select name="gemba_walk[0][unit_id]" id="unit_id"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Unit</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mb-3">
                                                                <div class="form-group form-input">
                                                                    <label
                                                                        class="form-label require">{{ __('common.department') }}</label>
                                                                    <select name="gemba_walk[0][department_id]"
                                                                        id="department_id"
                                                                        class=" form-control single-select "
                                                                        style="width: 100%">
                                                                        <option value="">Select Department</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input ">
                                                                    <label
                                                                        class="form-label require">{{ __('inspection.exact_location') }}</label>
                                                                    <input type="text"
                                                                        name="gemba_walk[0][exact_location]"
                                                                        id="exact_location_0" class="form-control"
                                                                        placeholder=" Enter Exact Location  ">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input ">
                                                                    <label
                                                                        class="form-label require">{{ __('inspection.date_of_observation') }}</label>
                                                                    <input type="text"
                                                                        name="gemba_walk[0][date_of_observation]"
                                                                        id="date_of_observation_0" class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label require ">Observation Time</label>
                                                                    <input type="text" name="gemba_walk[0][time]" id="time"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input ">
                                                                    <label for="observation_type"
                                                                        class=" require form-label">
                                                                        {{ __('inspection.observation_type') }}</label>
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
                                                                <div class="form-group form-input ">
                                                                    <label
                                                                        class="form-label require">{{ __('inspection.description') }}</label>
                                                                    <textarea class="form-control" name="gemba_walk[0][checklist_description]" id="checklist_description_0"></textarea>

                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group form-input ">
                                                                    <label for="risk_category"
                                                                        class=" require form-label">
                                                                        {{ __('inspection.risk_category') }}</label>
                                                                    <select name="gemba_walk[0][risk_category]"
                                                                        id="risk_category_0"
                                                                        class=" form-control single-select"
                                                                        style="width: 100%">
                                                                        <option value="">Select Risk Category
                                                                        </option>
                                                                        <option value="{{ encryptId(1) }}">Low</option>
                                                                        <option value="{{ encryptId(2) }}">High</option>
                                                                        <option value="{{ encryptId(3) }}">Moderate
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group  form-input">
                                                                    <label for="hazard" class="require form-label">
                                                                        {{ __('inspection.hazard') }}</label>
                                                                    <select name="gemba_walk[0][hazard][]" id="hazard"
                                                                        class=" form-control single-select" multiple
                                                                        style="width: 100%">
                                                                        @foreach ($hazard as $haza)
                                                                            <option value="{{ encryptId($haza->id) }}">
                                                                                {{ $haza->hazard_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group  form-input">
                                                                    <label for="hazard" class="require form-label">
                                                                        Name of the Observer</label>
                                                                    <input type="text" class="form-control"
                                                                        name="gemba_walk[0][observer_name][]"
                                                                        id="observer_name"
                                                                        value="{{ Auth::user()->name }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mt-4">
                                                                <div class="form-group form-input">
                                                                    <label
                                                                        class="form-label require">{{ __('inspection.capa_is_passed') }}</label>

                                                                    <div class="mb-2">
                                                                        <label class="me-3">
                                                                            <input type="radio" name="is_passed"
                                                                                value="{{ encryptId(1) }}"
                                                                                id="capa_yes"
                                                                                class="validate-radio-required" checked>
                                                                            Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio" name="is_passed"
                                                                                value="{{ encryptId(2) }}"
                                                                                id="capa_no"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12 mb-2" id="verified_by"
                                                                    style="display: none;">
                                                                    <div class="row">
                                                                        <!-- File Upload Input (Left Column) -->
                                                                        <div class="col-md-4 mt-2 form-input file-upload-block"
                                                                            id="file-upload-0">
                                                                            <label for="evidence_0"
                                                                                class="form-label require">
                                                                                {{ __('inspection.evidence') }}
                                                                            </label>
                                                                            <input type="file" class="form-control"
                                                                                name="gemba_walk[0][evidence]"
                                                                                id="evidence_0">
                                                                            <div class="text-danger"></div>
                                                                        </div>

                                                                        <!-- CAPA Textarea (Right Column) -->
                                                                        <div class="col-md-4 mt-2">
                                                                            <div class="form-group form-input">
                                                                                <label class="form-label require">
                                                                                    {{ __('inspection.capa') }}
                                                                                </label>
                                                                                <textarea class="form-control" name="gemba_walk[0][checklist_capa]" id="checklist_capa"
                                                                                    placeholder="Enter Recommended actions"></textarea>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-4 mt-2">
                                                                            <div class="form-group form-input">
                                                                                <label for="responsible_person_id"
                                                                                    class="form-label require">Responsible
                                                                                    Person For Recommended CAPA</label>
                                                                                <select
                                                                                    name="gemba_walk[0][responsible_person_id][]"
                                                                                    id="responsible_person_id"
                                                                                    class="form-control single-select"
                                                                                    multiple style="width: 100%">
                                                                                    <option value="">Select Observer
                                                                                        Person
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-12 mb-2" id="remark_section"
                                                                    style="display: none;">
                                                                    <div class="row">

                                                                        <div class="col-md-12 mt-2">
                                                                            <div class="form-group form-input">
                                                                                <label
                                                                                    class="form-label require">Remark</label>
                                                                                <textarea class="form-control" name="capa_remark" id="checklist_remark_0"></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>



                                                            <div class="col-md-4 mt-2">
                                                                <div class="form-group  form-input">
                                                                    <label for="" class=" require form-label">
                                                                        {{ __('inspection.status') }}</label>
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
                                                                    <textarea class="form-control" name="gemba_walk[0][checklist_remark]" id="gemba_checklist_remark_0"></textarea>

                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>






                                                <div class="submit-button mt-4" style="text-align: right;">
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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        $(document).ready(function() {
            $('#verified_by').hide();
            $('#remark_section').hide();

            function handlePassedSelection() {
                const selectedValue = $('input[name="is_passed"]:checked').val();

                if (selectedValue === '{{ encryptId(1) }}') {
                    $('#verified_by').show();
                    $('#remark_section').hide();
                } else if (selectedValue === '{{ encryptId(2) }}') {
                    $('#remark_section').show();
                    $('#verified_by').hide();
                }
            }

            // Run once on load
            handlePassedSelection();

            // Run on change
            $('input[name="is_passed"]').change(handlePassedSelection);
        });


        $(document).ready(function() {

            $(document).on('change', '#location_id', function() {
                var locationId = $(this).val();
                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append(
                                '<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' +
                                    value.id +
                                    '">' + value
                                    .name + '</option>');
                            });
                            $('#unit_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                    $('#unit_id').trigger('change.');
                }
            });

            $(document).on('change', '#unit_id', function() {
                var unitId = $(this).val();
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list') }}/" + unitId +
                            "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                $('#department_id').append(
                                    '<option value="' + value
                                    .id + '">' + value.name +
                                    '</option>');
                            });
                            $('#department_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#department_id').empty().append(
                        '<option value="">Select Department</option>');
                    $('#department_id').trigger('change.');
                }
            });


            // $('#executive_person_id').select2({
            //     ajax: {
            //         url: "{{ url('ohc/safety-petty-logbook/employeeid') }}",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 search: params.term
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         id: item.id,
            //                         text: item.text
            //                     };
            //                 })
            //             };
            //         },
            //         error: function(xhr, textStatus, errorThrown) {
            //             console.log("Error in AJAX request:", textStatus, errorThrown);
            //         }
            //     },
            //     minimumInputLength: 1,
            //     dropdownCssClass: 'form-control',
            //     selectionCssClass: 'form-control'
            // });

            $(document).ready(function() {
                var $select = $('#responsible_person_id');
                // Initialize Select2
                $select.select2({
                    ajax: {
                        url: "{{ url('inspection/gemba-walk/employeeName') }}",
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
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });



            });


            flatpickr("#document_upload_date", {
                dateFormat: "d-m-Y"
            });
            flatpickr("#date_of_observation_0", {
                enableTime: false,
                dateFormat: "d-m-Y"
            });
            flatpickr("#time", {
                enableTime: true,
                noCalendar: true, // ✅ correct spelling
                dateFormat: "H:i", // 24-hour format (e.g., 14:30)
                time_24hr: true // Optional: force 24hr instead of AM/PM
            });

            flatpickr("#date_of_compliance_0", {
                enableTime: false,
                dateFormat: "d-m-Y"
            });

            let checklistIndex = 1;
            let checklistCount = 1;





            // $(".addChecklistDetails").on("click", function() {
            //     const newChecklistField = `
        //         <div class="row gemba_walk_checklist_add" data-index="${checklistIndex}" id="checklistDetails_${checklistIndex}" >

        //                 <hr class="mt-3">
        //              <div class="row">



        //                 <div class="col-md-4 form-input">
        //                     <label class="form-label">Location</label>
        //                     <select name="gemba_walk[${checklistIndex}][location_id]" id="location_id_${checklistIndex}"
        //                         class="form-control single-select" style="width: 100%">
        //                         <option value="">Select Location</option>
        //                         @foreach ($locationList as $loc)
        //                             <option value="{{ encryptId($loc->id) }}">{{ $loc->location_name }}</option>
        //                         @endforeach
        //                     </select>
        //                 </div>

        //                 <div class="col-md-4 form-input">
        //                     <label class="form-label">Unit</label>
        //                     <select name="gemba_walk[${checklistIndex}][unit_id]" id="unit_id_${checklistIndex}"
        //                         class="form-control single-select" style="width: 100%">
        //                         <option value="">Select Unit</option>
        //                         @foreach ($unitList as $unit)
        //                             <option value="{{ encryptId($unit->id) }}">{{ $unit->unit_name }}</option>
        //                         @endforeach
        //                     </select>
        //                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">Date of Observation</label>
        //                     <input type="text" name="gemba_walk[${checklistIndex}][date_of_observation]"
        //                         id="date_of_observation_${checklistIndex}" class="form-control date_of_observation">
        //                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">Observation Type</label>
        //                     <select name="gemba_walk[${checklistIndex}][observation_type]" id="observation_type_${checklistIndex}"
        //                         class="form-control single-select" style="width: 100%">
        //                         <option value="">Select Observation Type</option>
        //                         <option value="1">Unsafe Act</option>
        //                         <option value="2">Unsafe Condition</option>
        //                     </select>
        //                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">Description</label>
        //                     <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_description]" id="checklist_description_${checklistIndex}"></textarea>
        //                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                 <label class="form-label">Hazard</label>
        //                 <input type="text" name="gemba_walk[${checklistIndex}][hazard]" id="hazard_${checklistIndex}"
        //                     class="form-control" placeholder="Enter Hazard Observation">
        //                 </div>



        //                  <div class="col-md-4 mt-2 form-input file-upload-block"
        //                                                     id="file-upload-0">
        //                                                     <label for="evidence_0"
        //                                                         class="form-label">Evidence</label>
        //                                                     <input type="file" class="form-control"
        //                                                         name="gemba_walk[${checklistIndex}][evidence]" id="evidence_${checklistIndex}">
        //                                                     <div class="text-danger"></div>

        //                                                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">CAPA</label>
        //                     <input type="text" name="gemba_walk[${checklistIndex}][checklist_capa]" id="checklist_capa_${checklistIndex}"
        //                         class="form-control" placeholder="Enter Recommended actions">
        //                 </div>




        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">Status</label>
        //                     <select name="gemba_walk[${checklistIndex}][current_status]" id="current_status_${checklistIndex}"
        //                         class="form-control single-select" style="width: 100%">
        //                         <option value="">Select Current Status</option>
        //                         <option value="1">Open</option>
        //                         <option value="2">Closed</option>
        //                     </select>
        //                 </div>

        //                 <div class="col-md-4 form-input mt-2">
        //                     <label class="form-label">Remark</label>
        //                     <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_remark]" id="gemba_checklist_remark_${checklistIndex}"></textarea>
        //                 </div>

        //               </div>


        //               {{--   observation
            //                 <div class="row mt-2">
            //                     <div class="observationContainer">
            //                         <div class="row observationRow">
            //                             <div class="col-md-4 form-input">
            //                                 <label class="form-label">Observation</label>
            //                                 <textarea class="form-control" name="gemba_walk[${checklistIndex}][checklist_observation][0]"></textarea>
            //                             </div>
            //                             <div class="col-md-4 d-flex align-items-end">
            //                                 <button type="button" class="btn btn-primary addChecklistObservation"  data-index=
            //                                 "${checklistIndex}" >Add More</button>
            //                             </div>
            //                         </div>
            //                     </div>
            //                 </div>
            //                 --}}


        //                 <div class="col-md-12 mb-3 mt-3 d-flex align-items-end justify-content-end">
        //                     <button type="button" class="btn btn-danger btn-sm removeChecklistDetails">Remove</button>
        //                 </div>
        //     </div>`;


            //     if ($(".gemba_walk_checklist_add").length >= 200) {
            //         Swal.fire({
            //             icon: "error",
            //             title: "Sorry!",
            //             text: "You cannot add more than 200 checklist items."
            //         });
            //         return;
            //     }

            //     $("#gemba_walk_checklist").append(newChecklistField);

            //     addValidationRules(checklistIndex);
            //     checklistCount++;
            //     checklistIndex++;

            //     $(".single-select").select2({
            //         width: "100%"
            //     });
            //     $(".date_of_compliance, .date_of_observation").flatpickr({
            //         dateFormat: "d-m-Y"
            //     });



            // });

            // $(document).on("click", ".addChecklistObservation", function() {
            //     const checklistRow = $(this).closest(".gemba_walk_checklist_add");
            //     const rowIndex = $(this).data("index");
            //     const observationContainer = checklistRow.find(".observationContainer");
            //     const observationIndex = checklistRow.find(".observationRow")
            //         .length;

            //     if (observationIndex >= 5) {
            //         Swal.fire({
            //             icon: "error",
            //             title: "Sorry!",
            //             text: "Maximum 5 observations allowed."
            //         });
            //         return;
            //     }

            //     const newObservationField = `
        //     <div class="row mt-2 observationRow">
        //         <div class="col-md-4 form-input">
        //             <label class="form-label">Observation</label>
        //             <textarea class="form-control" name="gemba_walk[${rowIndex}][checklist_observation][${observationIndex}]"></textarea>
        //         </div>
        //         <div class="col-md-4 d-flex align-items-center">
        //             <button type="button" class="btn btn-danger btn-sm removeChecklistObservation">
        //                 <i class="fa-solid fa-trash text-light"></i>
        //             </button>
        //         </div>
        //     </div>`;

            //     observationContainer.append(newObservationField);

            //     $(`[name="gemba_walk[${rowIndex}][checklist_observation][${observationIndex}]"]`).rules(
            //         "add", {
            //             required: true,
            //             minlength: 2,
            //             maxlength: 200,
            //             pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
            //             messages: {
            //                 required: "Observation is required.",
            //                 minlength: "Observation must be at least 2 characters.",
            //                 maxlength: "Observation cannot exceed 200 characters.",
            //                 pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
            //             }
            //         });
            // });

            // $(document).on("click", ".removeChecklistObservation", function() {
            //     $(this).closest(".observationRow").remove();
            // });

            // $(document).on("click", ".removeChecklistDetails", function() {
            //     $(this).closest(".gemba_walk_checklist_add").remove();
            //     checklistIndex--;
            // });

            $(document).ready(function() {



                $.validator.addMethod("customPattern", function(value, element, pattern) {
                    return this.optional(element) || pattern.test(value);
                }, "Invalid format.");

                $.validator.addMethod("filesize", function(value, element, maxSize) {
                    if (element.files.length === 0) return true;
                    return element.files[0].size <= maxSize;
                }, "File size must be less than 15MB.");

                $('#gembaWalkReportAdd').validate({
                    rules: {
                        document_upload_date: {
                            required: true
                        },
                        shift: {
                            required: true
                        },
                        'gemba_walk[0][time]': {
                            required: true
                        },
                        gemba_walk_prepared_by: {
                            required: true,
                            //  extension: "jpg|jpeg|png",
                            filesize: 15728640,
                        },
                        gemba_walk_verified_by: {
                            required: true,
                            filesize: 15728640,
                        },

                        capa_remark: {
                            required: true,
                        },
                        "gemba_walk[0][location_id]": {
                            required: true
                        },
                        "gemba_walk[0][unit_id]": {
                            required: true
                        },
                        "gemba_walk[0][department_id]": {
                            required: true
                        },
                        "gemba_walk[0][exact_location]": {
                            required: true
                        },
                        "gemba_walk[0][date_of_observation]": {
                            required: true,
                        },
                        "gemba_walk[0][observation_type]": {
                            required: true
                        },

                        "gemba_walk[0][checklist_description]": {
                            required: true,
                            minlength: 3,
                            maxlength: 2000,

                        },
                        "gemba_walk[0][hazard][]": {
                            required: true,
                            // minlength: 3,
                            // maxlength: 200,

                        },
                        "gemba_walk[0][risk_category]": {
                            required: true,
                            // minlength: 3,
                            // maxlength: 200,

                        },
                        "gemba_walk[0][evidence]": {
                            required: true,
                            filesize: 15728640,
                        },
                        "gemba_walk[0][checklist_capa]": {
                            required: true,
                            minlength: 3,
                            maxlength: 200,

                        },
                        "gemba_walk[0][date_of_compliance]": {
                            required: true,
                        },
                        "gemba_walk[0][responsible_person_id][]": {
                            required: true
                        },
                        "gemba_walk[0][current_status]": {
                            required: true
                        },
                        "gemba_walk[0][checklist_remark]": {

                            minlength: 3,
                            maxlength: 2000,

                        },
                        "gemba_walk[0][checklist_observation][0]": {
                            required: true,
                            minlength: 3,
                            maxlength: 2000,

                        }
                    },
                    messages: {
                        document_upload_date: {
                            required: "Please select a date."
                        },
                       'gemba_walk[0][time]': {
                            required: "Please select a time."
                        },
                        shift: {
                            required: "Please select a shift."
                        },
                        gemba_walk_prepared_by: {
                            required: "Please upload a signature.",
                            // extension: "Only JPG, JPEG, and PNG files are allowed.",
                            filesize: "File size must be less than 15MB."
                        },
                        // executive_person_id: {
                        //     required: "Please select a Executive person."
                        // },
                        gemba_walk_verified_by: {
                            required: "Please upload a signature.",
                            filesize: "File size must be less than 15MB."
                        },
                        capa_remark: {
                            required: "Please enter a remark.",
                        },
                        responsible_person_id: {
                            required: "Please select a responsible person."
                        },
                        "gemba_walk[0][location_id]": "Please select a location.",
                        "gemba_walk[0][unit_id]": "Please select a unit.",
                        "gemba_walk[0][department_id]": "Please select a department.",
                        "gemba_walk[0][exact_location]": "Please enter an exact location.",
                        "gemba_walk[0][date_of_observation]": "Please enter the date of observation.",
                        "gemba_walk[0][observation_type]": "Please select an observation type.",

                        "gemba_walk[0][checklist_description]": {
                            required: "Please enter a description.",
                            minlength: "Checklist Description must be at least 3 characters.",
                            maxlength: "Checklist Description cannot exceed 2000 characters.",

                        },
                        "gemba_walk[0][risk_category]": {
                            required: "Please select the risk category.",


                        },
                        "gemba_walk[0][hazard]": {
                            required: "Please select a hazard observation.",
                            // minlength: "Hazard must be at least 3 characters.",
                            // maxlength: "Hazard cannot exceed 200 characters.",
                            // customPattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
                        },
                        "gemba_walk[0][evidence]": {
                            required: "Please upload an evidence file.",
                            // extension: "Only JPG, JPEG, PNG, and PDF files are allowed.",
                            filesize: "File size must be less than 15MB."
                        },
                        "gemba_walk[0][checklist_capa]": {
                            required: "Please enter a CAPA.",
                            minlength: "CAPA must be at least 3 characters.",
                            maxlength: "CAPA cannot exceed 200 characters.",
                            customPattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
                        },
                        "gemba_walk[0][date_of_compliance]": "Please enter the compliance date.",
                        "gemba_walk[0][responsible_person_id]": "Please select a observer person.",
                        "gemba_walk[0][current_status]": "Please select a status.",
                        "gemba_walk[0][checklist_remark]": {

                            minlength: "Remark must be at least 3 characters.",
                            maxlength: "Remark cannot exceed 2000 characters.",
                            customPattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
                        },
                        "gemba_walk[0][checklist_observation][0]": {
                            required: "Please enter an observation.",
                            minlength: "Observation must be at least 3 characters.",
                            maxlength: "Observation cannot exceed 2000 characters.",
                            customPattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed."
                        }
                    },
                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        // if (element.attr("type") === "file") {
                        //     error.insertAfter(element.closest('.file-upload-block').find(
                        //         '.text-danger'));
                        // } else {
                        element.closest('.form-input').append(error);
                        // }
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

                    }
                });

            });


            function addValidationRules(index) {
                // Place this outside of addValidationRules()
                $.validator.addMethod("filesize", function(value, element, param) {
                    return this.optional(element) || (element.files[0] && element.files[0].size <= param);
                }, "File size must be less than 5MB.");


                $(`[name="gemba_walk[${index}][location_id]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a Location",

                    }
                });

                $(`[name="gemba_walk[${index}][unit_id]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a Unit",

                    }
                });

                $(`[name="gemba_walk[${index}][date_of_observation]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a Observation date",

                    }
                });

                $(`[name="gemba_walk[${index}][observation_type]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a observation type",

                    }
                });

                $(`[name="gemba_walk[${index}][date_of_compliance]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a compliance date",

                    }
                });

                $(`[name="gemba_walk[${index}][responsibility_id]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a responsible person",

                    }
                });
                $(`[name="gemba_walk[${index}][current_status]"]`).rules("add", {
                    required: true,
                    messages: {
                        required: "Please Select a Status",

                    }
                });


                $(`[name="gemba_walk[${index}][checklist_description]"]`).rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 2000,

                    messages: {
                        required: "Please Enter a Descripotion",
                        minlength: "Checklist Description must be at least 3 characters.",
                        maxlength: "Checklist Description cannot exceed 2000 characters.",

                    }
                });

                $(`[name="gemba_walk[${index}][hazard]"]`).rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 200,

                    messages: {
                        required: "Please Enter a Hazard",
                        minlength: "Hazard must be at least 3 characters.",
                        maxlength: "Hazard cannot exceed 200 characters.",


                    }
                });

                $(`[name="gemba_walk[${index}][checklist_capa]"]`).rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 200,


                    messages: {
                        required: "Please Enter a Checklist Capa",
                        minlength: "CAPA must be at least 3 characters.",
                        maxlength: "CAPA cannot exceed 200 characters.",


                    }
                });

                $(`[name="gemba_walk[${index}][checklist_remark]"]`).rules("add", {

                    minlength: 3,
                    maxlength: 2000,

                    messages: {

                        minlength: "Remark must be at least 3 characters.",
                        maxlength: "Remark cannot exceed 2000 characters.",


                    }
                });


                $(`[name="gemba_walk[${index}][checklist_observation][0]"]`).rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 2000,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    messages: {
                        required: "Observation is required.",
                        minlength: "Observation must be at least 3 characters.",
                        maxlength: "Observation cannot exceed 2000 characters.",

                    }
                });


                $(`[name="gemba_walk[${index}][evidence]"]`).rules("add", {
                    required: true,
                    // extension: "jpg|jpeg|png|pdf",
                    // filesize: 5 * 1024 * 1024,
                    filesize: 15728640,
                    messages: {
                        required: "Evidence file is required.",
                        // extension: "Only JPG, JPEG, PNG, and PDF files are allowed.",
                        filesize: "File size must be less than 15MB."
                    }
                });
            }

        });
    </script>
@endpush
