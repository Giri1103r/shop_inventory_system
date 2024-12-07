@extends('admin.layouts.admin')
@section('title', 'Training Matrix Edit')
@section('pageurl', admin_url('training_matrix/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Edit') }}</h4> --}}

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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('training_matrix/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="training_matrixedit"
                                        action="{{ admin_url('training_matrix/edit/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($training_matrix->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="topic_id" class="form-label require">Training Topic</label>
                                                    <select name="topic_id" id="topic_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Training Topic</option>
                                                        @foreach ($topicList as $topic)
                                                            <option @if ($training_matrix->topic_id == $topic->id) selected @endif
                                                                value="{{ encryptId($topic->id) }}">
                                                                {{ $topic->topic_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="trainer_id" class="form-label require">Trainer</label>
                                                    <select name="trainer_id" id="trainer_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Trainer</option>
                                                        @foreach ($employeeList as $employee)
                                                            <option @if ($training_matrix->trainer_id == $employee->id) selected @endif
                                                                value="{{ encryptId($employee->id) }}">
                                                                {{ $employee->emp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Training Offered for</label>
                                                    <select name="training_offered_for" id="training_offered_for"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Training Offered for</option>
                                                        <option
                                                            {{ $training_matrix->training_offered_for == 1 ? 'selected' : '' }}
                                                            value="{{ encryptId(1) }}">Worker</option>
                                                        <option
                                                            {{ $training_matrix->training_offered_for == 2 ? 'selected' : '' }}
                                                            value="{{ encryptId(2) }}">Executive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>
                                                        @foreach ($unitList as $unit)
                                                            <option @if ($training_matrix->unit_id == $unit->id) selected @endif
                                                                value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="department_id" class="form-label require">Target Department
                                                    </label>
                                                    <select name="department_id[]" multiple id="department_id"
                                                        class=" form-control select2" style="width: 100%">
                                                        <option value="">Select Target Department </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Content Upload</label>
                                                    <input type="file" name="target_content" id="target_content"
                                                        class="form-control">
                                                    <small class="text-muted">Allowed file types: .xls, .pdf</small>
                                                    @if ($training_matrixFiles_target_content)
                                                        <a href="{{ asset($training_matrixFiles_target_content->file_path) }}"
                                                            target="_blank" class="d-block mt-2">
                                                            <i class="fa-solid fa-eye text-danger"></i> View</a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mode of Training</label>
                                                    <select name="mode_of_training" id="mode_of_training"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Mode of training</option>
                                                        <option
                                                            {{ $training_matrix->mode_of_training == 1 ? 'selected' : '' }}
                                                            value="{{ encryptId(1) }}">Online</option>
                                                        <option
                                                            {{ $training_matrix->mode_of_training == 2 ? 'selected' : '' }}
                                                            value="{{ encryptId(2) }}">Offline</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Training Evaluation</label>
                                                    <select name="training_evaluation" id="training_evaluation"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Training Evaluation</option>
                                                        <option value="{{ encryptId(1) }}"
                                                            {{ $training_matrix->training_evaluation == 1 ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="{{ encryptId(2) }}"
                                                            {{ $training_matrix->training_evaluation == 2 ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 {{ $training_matrix->training_evaluation == 1 ? '' : 'd-none' }}"
                                                id="questionnaire_upload_section">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Upload Questionnaire</label>
                                                    <input type="file" name="questionnaire" id="questionnaire"
                                                        class="form-control">
                                                    <small class="text-muted">Allowed file types: .xls, .pdf.</small>
                                                    @if ($training_matrixFiles_questionnaire)
                                                        <a href="{{ asset($training_matrixFiles_questionnaire->file_path) }}"
                                                            target="_blank" class="d-block mt-2">
                                                            <i class="fa-solid fa-eye text-danger"></i> View
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-cancel></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
@php
    $preselectedDepartmentIds = array_map('encryptId', explode(',', $training_matrix->department_id ?? ''));
@endphp
@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            // Get initial unit ID and preselected department IDs
            var initialUnitId = $('#unit_id').val();
            var preselectedDepartmentIds = @json($preselectedDepartmentIds);

            // Fetch departments on page load if unit ID is present
            if (initialUnitId) {
                fetchDepartments(initialUnitId, preselectedDepartmentIds);
            }

            // Fetch departments on unit change
            $('#unit_id').on('change', function() {
                var unitId = $(this).val();
                fetchDepartments(unitId, []);
            });

            // Function to fetch and populate departments
            function fetchDepartments(unitId, preselectedIds) {
                if (unitId) {
                    $.ajax({
                        url: "{{ admin_url('department/multiple-ajax-list/') }}" + unitId,
                        type: 'GET',
                        data: {
                            preselected_ids: preselectedIds
                        },
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Target Department</option>'
                            );
                            $.each(data, function(key, value) {
                                var selected = preselectedIds.includes(value.id) ? 'selected' :
                                    '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            $('#department_id').trigger('change'); // Update select2
                        },
                        error: function() {
                            alert('Error fetching departments. Please try again.');
                        },
                    });
                } else {
                    $('#department_id').empty().append(
                        '<option value="">Select Target Department</option>'
                    );
                }
            }
        });
        $(document).ready(function() {
            $('#department_id').select2({
                placeholder: "Select Target Department",
                allowClear: true,
                closeOnSelect: true,
            });
            $('#training_evaluation').on('change', function() {
                const selectedValue = $(this).val();
                const showSectionValue = '{{ encryptId(1) }}';

                if (selectedValue === showSectionValue) {
                    $('#questionnaire_upload_section').removeClass('d-none');
                } else {
                    $('#questionnaire_upload_section').addClass('d-none');
                }
            });
            $('#training_matrixedit').validate({
                rules: {
                    topic_id: {
                        required: true,
                    },
                    trainer_id: {
                        required: true,
                    },
                    training_offered_for: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    'department_id[]': {
                        required: true,
                    },

                    mode_of_training: {
                        required: true,
                    },
                    training_evaluation: {
                        required: true,
                    },
                    questionnaire: {
                        extension: "xls|pdf",
                    },

                },
                messages: {
                    topic_id: {
                        required: "Please select a Training Topic.",
                    },
                    trainer_id: {
                        required: "Please select a Trainer.",
                    },
                    training_offered_for: {
                        required: "Please select whom the training is offered for.",
                    },
                    unit_id: {
                        required: "Please select a Unit Name.",
                    },
                    'department_id[]': {
                        required: "Please select a Target Department.",
                    },

                    mode_of_training: {
                        required: "Please select the Mode of Training.",
                    },
                    training_evaluation: {
                        required: "Please select Training Evaluation.",
                    },
                    questionnaire: {
                        extension: "Only .xls and .pdf file formats are allowed.",
                    },

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
                    form.submit(); // Submit the form when valid
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });
        });
    </script>
@endpush
