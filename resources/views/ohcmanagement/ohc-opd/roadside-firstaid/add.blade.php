@extends('admin.layouts.admin')
@section('title', 'Road Side First Aid')
@section('pageurl', admin_url('ohc/roadside-first-aid/list'))


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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/roadside-first-aid/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="roadsidefirstaid"
                                        action="{{ admin_url('ohc/roadside-first-aid/add/submit') }}">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Person Injured Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Injured Person Name">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2 department">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date of incident</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_incident" id="date_of_incident"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time of Incident</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="time_of_incident" id="time_of_incident"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fas fa-clock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location of Incident</label>
                                                    <input type="text" name="location_of_incident"
                                                        id="location_of_incident" class="form-control"
                                                        placeholder="Location of the incident">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="Fitness" class="require">Injured Person's Condition
                                                    </label>
                                                    <select name="person_condtion" id="person_condtion"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the condition
                                                        </option>
                                                        @foreach ($injuredCondition as $list)
                                                            <option value="{{ $list->id }}">
                                                                {{ $list->injured_condtion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aid Provided</label>
                                                    <input type="text" name="first_aid_provided" id="first_aid_provided"
                                                        class="form-control" placeholder="First Aid Provided">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aider Name</label>

                                                    <select name="first_aider_name" id="first_aider_name"
                                                        placeholder="First Aider Name" class="form-control "
                                                        style="width: 100%">
                                                        <option value="">select the First Aider</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Transport to Medical
                                                        Facility</label>
                                                    <select name="transport_to_medical_facility"
                                                        id="transport_to_medical_facility" class="form-select single-select"
                                                        style="width:100%">
                                                        <option value="">Select the Option</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">NO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 transport_method" style="display: none">
                                                <div class="form-group form-input">
                                                    <label for="follow" class="form-label require">Transport
                                                        Method</label>
                                                    <input type="text" name="transport_method" class="form-control"
                                                        id="transport_method">
                                                </div>

                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Report Filed</label>
                                                    <select name="incident_report_filled" id="incident_report_filled"
                                                        class="form-select single-select" style="width:100%">
                                                        <option value="">Select the Option</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">NO</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <textarea name="remarks" id="remarks" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/roadside-first-aid/list') }}"></x-button-cancel>
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
    <script>
        // follow up required

        $(document).ready(function() {

            var fromDatepicker = flatpickr("#date_of_incident", {
                dateFormat: "d-m-Y",
                maxDate: new Date(),

            });
            // time
            var currentTime = new Date().toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',

            });

            var timepicker = flatpickr("#time_of_incident", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: currentTime,
                minTime: currentTime,
            });

            // treatment start and time

            let startPicker = flatpickr("#treatment_start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
                onChange: function(selectedDates, dateStr) {
                    endPicker.set("minTime", dateStr);
                }
            });

            let endPicker = flatpickr("#treatment_end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
            });
            // follow up required

            $('#transport_to_medical_facility').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '1') {
                    $('.transport_method').show();
                } else {
                    $('.transport_method').hide();
                }
            });
        });


        $('#first_aider_name').select2({
            ajax: {
                url: '{{ admin_url('ohc/prescribe-to-patient/firstaider') }}',
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

        // validation
        $(function() {
            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#roadsidefirstaid').validate({
                rules: {
                    emp_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z0-9_]*$/,
                        remote: {
                            url: '{{ admin_url('ohc/roadside-first-aid/unique') }}',
                            type: 'post',
                            data: {
                                emp_name: function() {
                                    return $('#emp_name').val();
                                }
                            }
                        }
                    },
                    date_of_incident: {
                        required: true,
                    },
                    time_of_incident: {
                        required: true,
                    },
                    location_of_incident: {
                        required: true,
                    },
                    incident_report_filled: {
                        required: true,
                    },
                    first_aid_provided: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    person_condtion: {
                        required: true,
                    },
                    transport_to_medical_facility: {
                        required: true,
                    },
                    first_aider_name: {
                        required: true,
                    },
                    transport_method: {
                        required: function() {
                            return $('#transport_to_medical_facility').val() == '1';
                        },
                        minlength: 3,
                        maxlength: 100
                    },
                },
                messages: {
                    emp_name: {
                        required: "Injured person Name is required.",
                        minlength: "Injured person Name must be at least 3 characters.",
                        maxlength: "Injured person Name must not exceed 30 characters.",
                        remote: "Injured Person Name should be Unique",
                        regex: "Injured Person Name has invalid characters",

                    },
                    date_of_incident: {
                        required: "Please select the date of the incident."
                    },
                    time_of_incident: {
                        required: "Please enter the time of the incident."
                    },
                    location_of_incident: {
                        required: "Please provide the location of the incident."
                    },
                    incident_report_filled: {
                        required: "Please specify if the incident report is filled."
                    },
                    first_aid_provided: {
                        required: "Please specify the first aid provided.",
                        minlength: "First aid description must be at least 3 characters.",
                        maxlength: "First aid description must not exceed 20 characters."
                    },
                    person_condtion: {
                        required: "Please specify the person's condition."
                    },
                    transport_to_medical_facility: {
                        required: "Please indicate if transport to a medical facility was required."
                    },
                    first_aider_name: {
                        required: "Please enter the first aider's name."
                    },
                    transport_method: {
                        required: "Please specify the transport method.",
                        minlength: "Transport method must be at least 3 characters.",
                        maxlength: "Transport method must not exceed 100 characters."
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
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log("Form has " + errors + " invalid fields.");
                },
            });

        });
    </script>
@endpush
