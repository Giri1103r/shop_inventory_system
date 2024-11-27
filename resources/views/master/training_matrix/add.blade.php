@extends('admin.layouts.admin')
@section('title', 'Training Matrix Add')
@section('pageurl', admin_url('training_matrix/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                    <form method="POST" id="training_matrixadd"
                                        action="{{ admin_url('training_matrix/add/submit') }}"  enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="topic_id" class="form-label require">Training Topic</label>
                                                    <select name="topic_id" id="topic_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Training Topic</option>
                                                        @foreach ($topicList as $topic)
                                                            <option value="{{ encryptId($topic->id) }}">
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
                                                            <option value="{{ encryptId($employee->id) }}">
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
                                                        <option value="{{ encryptId(1) }}">Worker</option>
                                                        <option value="{{ encryptId(2) }}">Executive</option>
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
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="department_id" class="form-label require">Target Department
                                                    </label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Target Department </option>
                                                        @foreach ($departmentList as $department)
                                                            <option value="{{ encryptId($department->id) }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Content Upload</label>
                                                    <input type="file" name="target_content" id="target_content"
                                                        class="form-control" >
                                                    <small class="text-muted">Allowed file types: .xls, .pdf</small>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mode of Training</label>
                                                    <select name="mode_of_training" id="mode_of_training"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Mode of training</option>
                                                        <option value="{{ encryptId(1) }}">Online</option>
                                                        <option value="{{ encryptId(2) }}">Offline</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Training Evaluation</label>
                                                    <select name="training_evaluation" id="training_evaluation"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Training Evaluation</option>
                                                        <option value="{{ encryptId(1) }}">Yes</option>
                                                        <option value="{{ encryptId(2) }}">No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Conditional File Upload for Training Evaluation -->
                                            <div class="col-md-4 d-none" id="questionnaire_upload_section">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Upload Questionnaire</label>
                                                    <input type="file" name="questionnaire" id="questionnaire"
                                                        class="form-control" >
                                                    <small class="text-muted">Allowed file types: .xls, .pdf.</small>
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

@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#training_evaluation').on('change', function() {
                if ($(this).val() === '{{ encryptId(1) }}') { // "Yes" selected
                    $('#questionnaire_upload_section').removeClass('d-none');
                } else {
                    $('#questionnaire_upload_section').addClass('d-none');
                }
            });

            $('#training_matrixadd').validate({
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
                    department_id: {
                        required: true,
                    },
                    target_content: {
                        required: true,
                        extension: "xls|pdf",
                    },
                    mode_of_training: {
                        required: true,
                    },
                    training_evaluation: {
                        required: true,
                    },
                    questionnaire: {
                        required: function() {
                            return $('#training_evaluation').val() ===
                            '{{ encryptId(1) }}'; // Only required if "Yes" is selected
                        },
                        extension: "xls|pdf",
                    }
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
                    department_id: {
                        required: "Please select a Target Department.",
                    },
                    target_content: {
                        required: "Please upload Target Content.",
                        extension: "Only .xls and .pdf file formats are allowed.",
                    },
                    mode_of_training: {
                        required: "Please select the Mode of Training.",
                    },
                    training_evaluation: {
                        required: "Please select Training Evaluation.",
                    },
                    questionnaire: {
                        required: "Please upload the Questionnaire.",
                        extension: "Only .xls and .pdf file formats are allowed.",
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
