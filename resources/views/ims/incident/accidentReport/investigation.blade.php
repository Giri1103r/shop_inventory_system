@extends('admin.layouts.admin')
@section('title', 'Accident Investigation')
@section('pageurl', admin_url('incident/accidentReport/list'))


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
    
    input:checked + .slider {
      background-color: #2196F3;
    }
    
    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }
    
    input:checked + .slider:before {
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
    </style>
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('incident/accidentReport Edit') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('incident/accidentReport/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="accidentinvestigation"
                                        action="{{ admin_url('incident/accidentReport/investigation/submit') }}">
                                        @csrf
                                        <input type="hidden" name="accident_id" id="accident_id"
                                            value="{{ encryptId($accidentId) }}">
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="nature_of_injury" class="form-label">Nature of
                                                        Injury</label>
                                                    <select name="nature_of_injury" id="nature_of_injury"
                                                        style="width: 100%" class="form-control single-select">
                                                        <option value="">Nature of Injury</option>
                                                        <option value="1">Major</option>
                                                        <option value="2">Minor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Location of the Injury</label>
                                                    <input type="text" name="location_of_injury" id="location_of_injury"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="team_id" class="form-label require">Name of the
                                                        Witness</label>
                                                    <select name="witness_id" id="witness_id"
                                                        class="form-control witness" style="width: 100%">
                                                        <option value="">Select Name of the Witness</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="emp_code" class="form-label require">Employee Code</label>
                                                    <select name="emp_code" id="emp_code"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Employee Code</option>
                                                        {{-- @foreach ($employeeList as $emp)
                                                            <option value="{{ $emp->emp_id }}">
                                                                {{ $emp->emp_id }}</option>
                                                        @endforeach --}}
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation</label>
                                                    <input type="text" name="designation" id="designation"
                                                        class="form-control" placeholder="Designation">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Department</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label for="is_damaged" class="form-label require">Was anything
                                                        damaged?</label><br>
                                                    <input type="checkbox" id="Man" name="is_damaged" value="1">
                                                    <label for="Man">Man</label>
                                                    <input type="checkbox" id="Machine" name="is_damaged" value="2">
                                                    <label for="Machine">Machine</label><br>
                                                    <input type="checkbox" id="Materials" name="is_damaged" value="3">
                                                    <label for="Materials"> Materials</label>
                                                    <input type="checkbox" id="NA" name="is_damaged" value="4">
                                                    <label for="NA"> NA</label><br>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">HIRA</label>
                                                    <label class="switch">
                                                        <input type="checkbox" name="hira">
                                                        <span class="slider round"></span>
                                                      </label>
                                                  
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">MOC</label>
                                                    <label class="switch">
                                                        <input type="checkbox" name="moc">
                                                        <span class="slider round"></span>
                                                      </label>
                                                </div>
                                               
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label for="root_cause_analysis" class="form-label">Possible Root Cause
                                                        Analysis (PRCA)</label>
                                                    <select name="root_cause_analysis" id="root_cause_analysis"
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
                                            <div class="col-md-4 mt-3">
                                                <div class="form-group form-input">
                                                    <label for="treatment" class="form-label require">Where the injured person receiving any treatment at present?</label>
                                                    <input type="checkbox" id="Yes" name="is_treatment" value="1">
                                                    <label for="Yes">Yes</label>
                                                    <input type="checkbox" id="No" name="is_treatment" value="2">
                                                    <label for="No">No</label><br>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Details</label>
                                                    <input type="text" name="details" id="details"
                                                        class="form-control" placeholder="Details">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Recommended Corrective & Preventive Action</label>
                                                    <input type="text" name="corrective_preventive_action" id="corrective_preventive_action"
                                                        class="form-control" placeholder="Recommended Corrective & Preventive Action">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label for="responsible_person_id" class="form-label require">Responsible Person</label>
                                                    <select name="responsible_person_id" id="responsible_person_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Responsible Person</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Date</label>
                                                    <input type="text" name="target_date" id="target_date"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Remarks (If Any)</label>
                                                    <textarea class="form-control" name="remark" id="remark"></textarea>

                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('incident/accidentReport/list') }}"></x-button-cancel>
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

            flatpickr("#target_date", {
                dateFormat: "d-m-Y",
            });


            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            $('#responsible_person_id').select2({
                ajax: {
                    url: "{{ url('incident/accidentReport/getemployeename') }}",
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
                    url: "{{ url('incident/accidentReport/getemployeename') }}",
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
                    url: "{{ url('incident/accidentReport/getemployeename') }}",
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
                        url: "{{ url('incident/accidentReport/fetchEmployeeDetails') }}/" +
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
                $('#accidentinvestigation').validate({
                    rules: {
                        date_and_time: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        shift: {
                            required: true,
                        },
                        location_id: {
                            required: true,
                        },
                        designation: {
                            required: true,
                        },
                        department_id: {
                            required: true,
                        },
                        emp_code: {
                            required: true,
                        },
                        address_of_the_injuredperson: {
                            required: true,
                            minlength: 3,
                            maxlength: 2000,
                            pattern: /^[a-zA-Z0-9\s\-\_\'\"()\n\r]+$/,
                        },
                    },
                    messages: {
                        date_and_time: {
                            required: "Date and Time is required.",
                        },
                        unit_id: {
                            required: "Unit is required.",
                        },
                        shift: {
                            required: "Shift is required.",
                        },
                        location_id: {
                            required: "Accident Location is required.",
                        },
                        designation: {
                            required: "Designation is required.",
                        },
                        department_id: {
                            required: "Department is required.",
                        },
                        emp_code: {
                            required: "Employee Code is required.",
                        },
                        address_of_the_injuredperson: {
                            required: "Address of the injured person is required.",
                            minlength: "Minimum 3 characters required.",
                            maxlength: "Maximum 2000 characters allowed.",
                            pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
                        form.submit();
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
    </script>
@endpush
