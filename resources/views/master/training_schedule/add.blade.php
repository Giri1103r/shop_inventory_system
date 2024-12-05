@extends('admin.layouts.admin')
@section('title', 'Training Schedule Add')
@section('pageurl', admin_url('training_schedule/list'))


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
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="training_scheduleadd"
                                        action="{{ admin_url('training_schedule/add/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="from_date" class="form-label require">From Date</label>
                                                    <input type="text" name ="from_date" id="from_date_datepicker"
                                                        class="form-control" placeholder="From Date">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="to_date" class="form-label require">To Date</label>
                                                    <input type="text" name ="to_date" id="to_date_datepicker"
                                                        class="form-control" placeholder="To Date">
                                                </div>
                                            </div>
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
                                                    <label for="department_id" class="form-label require">Department
                                                    </label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>
                                                        @foreach ($departmentList as $department)
                                                            <option value="{{ encryptId($department->id) }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Trainees</label>
                                                    <input type="text" name="target_trainees" id="target_trainees"
                                                        class="form-control" placeholder="Target Trainees">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="venue_id" class="form-label require">Venue/Location
                                                    </label>
                                                    <select name="venue_id" id="venue_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Venue/Location </option>
                                                        @foreach ($venueList as $venue)
                                                            <option value="{{ encryptId($venue->id) }}">
                                                                {{ $venue->name_of_the_conference_hall }}</option>
                                                        @endforeach
                                                    </select>
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
            $(document).ready(function() {
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });
            });
            flatpickr("#from_date_datepicker", {
                dateFormat: "d-m-Y",
            });
            flatpickr("#to_date_datepicker", {
                dateFormat: "d-m-Y",
            });

            $('#training_scheduleadd').validate({
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
