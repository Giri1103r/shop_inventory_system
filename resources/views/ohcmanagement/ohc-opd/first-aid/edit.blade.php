@extends('admin.layouts.admin')
@section('title', 'First Aid Edit')
@section('pageurl', admin_url('ohc/first-aid/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/first-aid/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="firstaid"
                                        action="{{ admin_url('ohc/first-aid/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" class="id" id="id"
                                            value="{{ encryptId($opd_first_aid->id) }}">
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee code</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                        @if (isset($opd_first_aid->emp_id) && isset($opd_first_aid->emp_id))
                                                            <option value="{{ $opd_first_aid->emp_id }}" selected>
                                                                {{ $opd_first_aid->emp_id }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name"
                                                        value="{{ $opd_first_aid->emp_name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2 department">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date Of incident</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_incident" id="date_of_incident"
                                                            value="{{ $opd_first_aid->date_of_incident }}"
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
                                                            value="{{ $opd_first_aid->time_of_incident }}"
                                                            class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fas fa-clock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medicine Name</label>
                                                    <select name="medicine_id[]" multiple id="medicine_id"
                                                        class="select2 form-control">
                                                        <option value="">Select medicine Name</option>
                                                        @foreach ($medicine as $list)
                                                            <option value="{{ $list->id }}"
                                                                @if (in_array($list->id, explode(',', $opd_first_aid->medicine_id ?? ''))) selected @endif>
                                                                {{ $list->medicine }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Treatment Provided</label>
                                                    <textarea name="treatment_provided" id="treatment_provided" cols="30" rows="5" class="form-control">{{ $opd_first_aid->treatment_provided }}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Cheif Complaint</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaint" cols="30" rows="5" class="form-control">{{ $opd_first_aid->cheif_complaint }}"</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Treatment Start Time</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="treatment_start_time"
                                                            value="{{ $opd_first_aid->treatment_start_time }}"
                                                            id="treatment_start_time" class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fas fa-clock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Treatment End Time</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="treatment_end_time"
                                                            value="{{ $opd_first_aid->treatment_end_time }}"
                                                            id="treatment_end_time" class="form-control">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fas fa-clock"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="hospital_id" class="form-label require">Hospital
                                                        Name</label>

                                                    <select name="hospital_id" id="hospital_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the Suggested By</option>
                                                        @foreach ($hospital as $list)
                                                            <option value="{{ encryptId($list->id) }}"
                                                                @if ($list->id == $opd_first_aid->hospital_id) selected @endif>
                                                                {{ $list->hospital_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aider Name</label>

                                                    <select name="first_aider_name" id="first_aider_name"
                                                        placeholder="First Aider Name" class="form-control "
                                                        style="width: 100%">
                                                        <option value="">select the First Aider</option>
                                                        @if (isset($opd_first_aid->first_aider_name) && isset($opd_first_aid->first_aider_name))
                                                            <option value="{{ $opd_first_aid->first_aider_name }}"
                                                                selected>
                                                                {{ $opd_first_aid->first_aider_name }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Follow Up Required</label>
                                                    <select name="follow_up" id="follow_up"
                                                        class="form-select single-select" style="width:100%">
                                                        <option value="">Select the Option</option>
                                                        <option value="1"
                                                            {{ $opd_first_aid->follow_up_required == 1 ? 'selected' : '' }}>
                                                            Yes</option>
                                                        <option value="2"
                                                            {{ $opd_first_aid->follow_up_required == 2 ? 'selected' : '' }}>
                                                            No</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <textarea name="remarks" id="remarks" cols="30" rows="5" class="form-control">{{ $opd_first_aid->remarks }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/first-aid/list') }}"></x-button-cancel>
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
            $('#medicine_id').select2({
                placeholder: "Select medicine name ",
                allowClear: true,
                closeOnSelect: true,
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

                minTime: currentTime,
            });

            // treatment start and time

            let startPicker = flatpickr("#treatment_start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                defaultDate: "{{ $opd_first_aid->treatment_start_time }}",
                time_24hr: false,
                onChange: function(selectedDates, dateStr) {
                    endPicker.set("minTime", dateStr);
                }
            });

            let endPicker = flatpickr("#treatment_end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                defaultDate: "{{ $opd_first_aid->treatment_end_time }}",
                time_24hr: false,
            });

            // follow up required
            function toggleDetailsField() {
                var selectedValue = $('#follow_up').val();
                if (selectedValue == '1') {
                    $('.refered_to').show();
                } else {
                    $('.refered_to').hide();
                }
            }
            toggleDetailsField();


            $('#follow_up').change(toggleDetailsField);


        });
        // getting the employee/worker details
        $('#emp_id').select2({
            ajax: {
                url: '{{ admin_url('ohc/prescribe-to-patient/fetchemployeename') }}',
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

        // department and number & emp name

        $(document).on('change', '#emp_id', function() {
            var empId = $(this).val();
            if (empId) {
                $.ajax({
                    url: "{{ admin_url('ohc/prescribe-to-patient/emp-details/') }}" + empId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.employee) {
                            $('#emp_name').val(response.employee.emp_name).prop('readonly', false);

                        } else {
                            alert("No employee details found.");
                        }
                    },
                    error: function(xhr) {
                        alert('Error fetching mobile number and department. Please try again.');
                    }
                });
            } else {
                $('#emp_name, #mobile_no, #department_id').val('').prop('disabled', true);
            }
        });
        // first aider

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

            $('#firstaid').validate({
                rules: {
                    emp_name: {
                        required: true,
                    },
                    emp_id: {
                        required: true,
                    },
                    date_of_incident: {
                        required: true,
                    },
                    time_of_incident: {
                        required: true,
                    },
                    treatment_provided: {
                        required: true,
                    },
                    treatment_start_time: {
                        required: true,
                    },
                    treatment_end_time: {
                        required: true,
                    },
                    first_aider_name: {
                        required: true,
                    },
                    follow_up: {
                        required: true,
                    },
                    refered_to: {
                        required: function() {
                            return $('#follow_up').val() == '1';
                        },
                        minlength: 3,
                        maxlength: 100
                    },
                    hospital_id: {
                        required: true,
                    },
                    'medicine_id[]': {
                        required: true,
                    },
                    cheif_complaint: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                },
                messages: {
                    emp_name: {
                        required: "Employee name is required.",
                    },
                    emp_id: {
                        required: "Employee ID is required.",
                    },
                    'medicine_id[]': {
                        required: "Medicine Name is required.",
                    },
                    date_of_incident: {
                        required: "Date of incident is required.",
                    },
                    time_of_incident: {
                        required: "Time of incident is required.",
                    },
                    hospital_id: {
                        required: "Hospital Name is required.",
                    },
                    cheif_complaint: {
                        required: "Cheif Complaint is required.",
                        minlength: "Minimum 3 characters are required",
                        maxlength: "Maximum 100 characters are required",
                    },
                    treatment_provided: {
                        required: "Please specify the treatment provided.",
                    },
                    treatment_start_time: {
                        required: "Treatment start time is required.",
                    },
                    treatment_end_time: {
                        required: "Treatment end time is required.",
                    },
                    first_aider_name: {
                        required: "First aider's name is required.",
                    },
                    follow_up: {
                        required: "Please specify if a follow-up is needed.",
                    },
                    hospital_name: {
                        required: "Hospital name is required if follow-up is needed.",
                        minlength: "Hospital name must be at least 3 characters long.",
                        maxlength: "Hospital name must not exceed 100 characters.",
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
    </script>
@endpush
