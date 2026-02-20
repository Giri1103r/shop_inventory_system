@extends('admin.layouts.admin')
@section('title', 'Road Side First Aid Edit')
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

                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/roadside-first-aid/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="roadsidefirstaid"
                                        action="{{ admin_url('ohc/roadside-first-aid/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" class="form-control" id="id"
                                            value="{{ encryptId($opd_roadside_first_aid->id) }}">
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Person Injured Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Injured Person Name"
                                                        value="{{ $opd_roadside_first_aid->name }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2 department">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date of incident</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_incident" id="date_of_incident"
                                                            value="{{ displaydateformat($opd_roadside_first_aid->date_of_incident) }}"
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
                                                            value="{{ $opd_roadside_first_aid->time_of_incident }}"
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
                                                        id="location_of_incident"
                                                        value="{{ $opd_roadside_first_aid->location_of_incident }}"
                                                        class="form-control" placeholder="Location of the incident">
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
                                                            <option value="{{ $list->id }}"
                                                                @if ($list->id == $opd_roadside_first_aid->person_condtion) selected @endif>
                                                                {{ $list->injured_condtion }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aid Provided</label>
                                                    <input type="text" name="first_aid_provided" id="first_aid_provided"
                                                        value="{{ $opd_roadside_first_aid->first_aid_provided }}"
                                                        class="form-control" placeholder="First Aid Provided">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Transport to Medical Facility</label>
                                                    <select name="transport_to_medical_facility"
                                                        id="transport_to_medical_facility" class="form-select single-select"
                                                        style="width:100%">
                                                        <option value="">Select the Option</option>
                                                        <option value="1"
                                                            {{ isset($opd_roadside_first_aid) && $opd_roadside_first_aid->transport_to_medical_facility == 1 ? 'selected' : '' }}>
                                                            Yes
                                                        </option>
                                                        <option value="2"
                                                            {{ isset($opd_roadside_first_aid) && $opd_roadside_first_aid->transport_to_medical_facility == 2 ? 'selected' : '' }}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="roadsidefirstaid row" style="display: none;">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="hospital_id" class="form-label require">Hospital
                                                            Name</label>
                                                        <select name="hospital_id" id="hospital_id"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">select the Hospital Name</option>
                                                            @foreach ($hospital as $list)
                                                                <option value="{{ encryptId($list->id) }}"
                                                                    @if ($list->id == $opd_roadside_first_aid->hospital_id) selected @endif>
                                                                    {{ $list->hospital_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="first_aider" class="form-label require">First
                                                            Aider</label>
                                                        <select name="first_aider" id="first_aider" class="form-control "
                                                            style="width: 100%">
                                                            <option value="">select the First Aider</option>
                                                            @if (isset($opd_roadside_first_aid->first_aider_name) && isset($opd_roadside_first_aid->first_aider_name))
                                                                <option
                                                                    value="{{ $opd_roadside_first_aid->first_aider_name }}"
                                                                    selected>
                                                                    {{ $opd_roadside_first_aid->first_aider_name }}
                                                                </option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Mobile Number</label>
                                                        <input type="text" name="mobile_no" id="mobile_no"
                                                            value="{{ $opd_roadside_first_aid->mobile_no }}"
                                                            class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="transport_method" class="form-label require">Transport
                                                            Method</label>
                                                        <select name="transport_method" id="transport_method"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">select the Vechicle</option>
                                                            @foreach ($reffered as $list)
                                                                <option value="{{ encryptId($list->id) }}"
                                                                    @if ($list->id == $opd_roadside_first_aid->transport_method) selected @endif>
                                                                    {{ $list->refered_vechicle }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row other_vechicles"style="display: none;">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="vechicle" class="form-label require">Reffered By
                                                                other Vechicle</label>
                                                            <input name="other_vechicle" id="vechicle"
                                                                class="form-control">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Report Filed</label>
                                                    <select name="incident_report_filled" id="incident_report_filled"
                                                        class="form-select single-select" style="width:100%">
                                                        <option value="">Select the Option</option>
                                                        <option value="1"
                                                            {{ $opd_roadside_first_aid->incident_report_filled == 1 ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="2"
                                                            {{ $opd_roadside_first_aid->incident_report_filled == 2 ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <textarea name="remarks" id="remarks" cols="30" rows="5" class="form-control">{{ $opd_roadside_first_aid->remarks }}</textarea>
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
        // reset

        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();

            });
        });
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
                
            });



            $(document).ready(function() {
                function toggleDetailsField() {
                    var selectedValue = $('#transport_to_medical_facility').val();
                    if (selectedValue === '1') {
                        $('.roadsidefirstaid').show();
                    } else {
                        $('.roadsidefirstaid').hide();
                    }
                }


                toggleDetailsField();


                $('#transport_to_medical_facility').on('change', toggleDetailsField);
            });

            $('#first_aider').select2({
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
                            regex: /^(?!\s*$)[a-zA-Z0-9\s]+$/,

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
                        first_aider: {
                            required: function() {
                                return $('#transport_to_medical_facility').val() == '1';
                            },
                        },
                        transport_method: {
                            required: function() {
                                return $('#transport_to_medical_facility').val() == '1';
                            },

                        },
                        hospital_id: {
                            required: function() {
                                return $('#transport_to_medical_facility').val() == '1';
                            },

                        },
                        mobile_no: {
                            required: function() {
                                return $('#transport_to_medical_facility').val() == '1';
                            },

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
                        first_aider: {
                            required: "Please enter the first aider's name."
                        },
                        transport_method: {
                            required: "Please specify the transport method.",

                        },
                        hospital_id: {
                            required: "Please select the hospital name.",

                        },
                        mobile_no: {
                            required: "Please enter the mobile number.",

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
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log("Form has " + errors + " invalid fields.");
                    },
                });

            });
        });
    </script>
@endpush
