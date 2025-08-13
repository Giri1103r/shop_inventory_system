@extends('admin.layouts.admin')
@section('title', 'Prescribe To Patient')
@section('pageurl', admin_url('ohc/prescribe-to-patient/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/prescribe-to-patient/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="opdpatient"
                                        action="{{ admin_url('ohc/prescribe-to-patient/add/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4  mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label" for="is_outside_worker"> Is OutSide
                                                        Worker/Employee</label><br>
                                                    <input type="checkbox" id="is_outside_worker" name="is_outside_worker"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4 employee-id mb-2 ">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Worker/Employee code</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 employecode mb-2" style="display: none">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Worker/Employee code</label>
                                                    <input type="text" name="outside_emp_id" id="emp_id_text"
                                                        class="form-control">

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Worker/Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2 department" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the department</option>
                                                        @foreach ($departmentList as $list)
                                                            <option value="{{ $list->id }}">{{ $list->department_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2 unit">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <input type="text" name="unit_id" id="unit_id"
                                                        class="form-control " readonly>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2 departmentEmployee">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <input type="text" name="department" id="department"
                                                        class="form-control " readonly>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile</label>
                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Emergency Contact</label>
                                                    <input type="text" name="emergency_contact" id="emergency_contact"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="dob" class="form-label require">Date Of Birth</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="dob"
                                                        id="dob" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" id="addresss" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Gender</label>
                                                    <select name="gender" id="gender"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="others">Others</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="date" class="form-label ">Date</label>
                                                <div class="input-group date form-input custom-height">
                                                    <input type="text" class="form-control" name="date"
                                                        id="date" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="time" class="form-label">Time</label>
                                                <div class="input-group date form-input custom-height">
                                                    <input type="text" class="form-control" name="time"
                                                        id="time" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fas fa-clock"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Cheif Complaint</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaint" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require" for="vital_checkup">Vital
                                                        Checkup</label><br>
                                                    <input type="checkbox" id="vital_checkup" name="vital_checkup"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Suggested By</label>
                                                    <select name="suggested_by" id="suggested_by"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the Suggested By</option>
                                                        @foreach ($suggestedBy as $list)
                                                            <option value="{{ $list->id }}">{{ $list->suggested_by }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label for="vechicle" class="form-label require">Details</label>
                                                    <input name="details" id="details" placeholder="Enter the Details"
                                                        class="form-control">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label" for="first_aid_treatment">First Aid
                                                        Treatment</label><br>
                                                    <input type="checkbox" id="first_aid_treatment"
                                                        name="first_aid_treatment" value="1">
                                                </div>
                                            </div>
                                            <div class="treatment" style="display: none;">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Treatment</label>
                                                        <textarea name="treatment" id="treatment" cols="30" rows="5" class="form-control"></textarea>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <div class="col-md-12">
                                                        <table class="table table-bordered ">

                                                            <thead class="bg-secondary" style="color: #ffff">
                                                                <tr>
                                                                    <th>Medicine</th>
                                                                    <th>Available Quantity</th>
                                                                    <th>Quantity</th>
                                                                    <th>Remarks</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>

                                                            <tbody id="medicine-tbody">
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-group form-input">
                                                                            <label for="medicine_id"
                                                                                class="require">Medicine
                                                                                Name</label>
                                                                            <select name="medicine_id[0]" id="medicine_id"
                                                                                class="form-control single-select"
                                                                                style="width: 100%">
                                                                                <option value="">Select the Medicine
                                                                                    Name
                                                                                </option>
                                                                                @foreach ($medicine as $list)
                                                                                    <option
                                                                                        value="{{ encryptId($list->medicine_id) }}">
                                                                                        {{ getmedicinename($list->medicine_id) }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group form-input">
                                                                            <label for="available_quantity"
                                                                                class="require">Available
                                                                                Quantity</label>
                                                                            <input type="text"
                                                                                name="available_quantity[0]"
                                                                                id="available_quantity" value=""
                                                                                placeholder="Available quantity"
                                                                                class="form-control" readonly>
                                                                        </div>
                                                                    </td>

                                                                    <td>
                                                                        <div class="form-group form-input">
                                                                            <label for="quantity"
                                                                                class="require">Quantity</label>
                                                                            <input type="number" name="quantity[0]"
                                                                                id="quantity"
                                                                                placeholder="Enter the quantity"
                                                                                class="form-control">
                                                                            <span id="quantity-error"
                                                                                style=" display:none;"
                                                                                class="text-danger">Quantity must be less
                                                                                than available quantity.</span>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group form-input">
                                                                            <label for="remarks"class="">Remarks</label>
                                                                            <textarea name="remarks[0]" id="remarks" cols="10" rows="2" class="form-control"></textarea>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="row gap-2">
                                                                            <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row"
                                                                                style="width: 30px; height: 30px;">
                                                                                <i class="fa-solid fa-plus"></i>
                                                                            </div>
                                                                            <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row"
                                                                                style="width: 30px; height: 30px;">
                                                                                <i class="fa-solid fa-trash"></i>
                                                                            </div>
                                                                        </div>

                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label" for="is_reffered">Is Reffered</label><br>
                                                    <input type="checkbox" id="is_reffered" name="is_reffered"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="isReffered row" style="display: none;">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="hospital_name" class="form-label require">Hospital
                                                            Name</label>


                                                        <select name="hospital_name" id="hospital_name"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">select the Hospital Name</option>
                                                            @foreach ($hospital as $list)
                                                                <option value="{{ $list->id }}">
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
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Mobile Number</label>
                                                        <input type="text" name="is_reffered_mobile_no"
                                                            id="is_reffered_mobile_no" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="vechicle" class="form-label require">Reffered By
                                                            Vechicle</label>
                                                        <select name="vechicle" id="vechicle"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">select the Vechicle</option>
                                                            @foreach ($reffered as $list)
                                                                <option value="{{ $list->id }}">
                                                                    {{ $list->refered_vechicle }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="vechicle" class="form-label require">Patient
                                                            Status</label>
                                                        <select name="patient_status" id="patient_status"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">select the status</option>
                                                            @foreach ($patientstatus as $list)
                                                                <option value="{{ $list->id }}">
                                                                    {{ $list->patient_status }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row close" style="display: none;">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="Fitness" class="require">Fitness
                                                                Certificate</label>
                                                            <select name="fitness_certificate" id="fitness_certificate"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">select the Fitness certificate
                                                                </option>
                                                                <option value="1">Required</option>
                                                                <option value="2">Not Required</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2 file_upload " style="display: none;">
                                                        <div class="form-group form-input">
                                                            <label for="Fitness" class="require">File Upload</label>
                                                            <input type="file" name="file" id="file"
                                                                class="form-control">
                                                            <small>Allowed file types: PDF, DOCX, DOC ,PNG,JPG,JPEG</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="close" class="form-label require">Close the
                                                                Description</label>
                                                            <textarea name="close_description" id="close_description" cols="30" rows="5" class="form-control"></textarea>
                                                        </div>
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
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/prescribe-to-patient/list') }}"></x-button-cancel>
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
        // date picker and time picker

        $(document).ready(function() {
            var fromDatepicker = flatpickr("#date", {
                dateFormat: "d-m-Y",
                maxDate: new Date(),
                defaultDate: new Date(),
            });

            var currentTime = new Date().toLocaleTimeString('en-GB', {
                hour: '2-digit',
                minute: '2-digit',

            });

            var timepicker = flatpickr("#time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,

            });


        });

        // date of birth
        $(document).ready(function() {
            var fromDobDatepicker = flatpickr("#dob", {
                dateFormat: "d-m-Y",
                maxDate: new Date(new Date().setFullYear(new Date().getFullYear() -
                    18)),

            });

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

            // Check if 'is_outside_worker' is NOT checked before making AJAX request
            if (!$('#is_outside_worker').is(':checked')) {
                if (empId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/prescribe-to-patient/emp-details/') }}" + empId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.employee) {
                                $('#emp_name').val(response.employee.emp_name).prop('readonly', true);
                                $('#mobile_no').val(response.employee.mobile_no).prop('readonly', true);
                                $('#unit_id').val(response.units.unit_name).prop('readonly', true);
                                $('#department').val(response.departments.department_name).prop(
                                    'readonly', true);

                            } else {
                                alert("No employee details found.");
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching employee details. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name, #mobile_no, #department').val('').prop('readonly', false);
                }
            }
        });


        $(document).ready(function() {

            $('#is_reffered').change(function() {
                if ($(this).is(':checked')) {
                    $('.isReffered').slideDown();
                } else {
                    $('.isReffered').slideUp();
                }
            });

            // for the treatment

            $('#first_aid_treatment').change(function() {
                if ($(this).is(':checked')) {
                    $('.treatment').slideDown();
                } else {
                    $('.treatment').slideUp();
                }
            });


            // suggested by

            $('#suggested_by').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '3') {
                    $('#details').closest('.col-md-4').show();
                } else {
                    $('#details').closest('.col-md-4').hide();
                }
            });

            // for the fitness certificate is required

            $('#fitness_certificate').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '1') {
                    $('.file_upload').show();
                } else {
                    $('.file_upload').hide();
                }
            });
            // vechile
            $('#vechicle').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '3') {
                    $('.other_vechicles').show();
                } else {
                    $('.other_vechicles').hide();
                }
            });

            // patient status

            $('#patient_status').change(function() {
                var selectedValue = $(this).val();

                if (selectedValue == '2') {
                    $('.close').show();
                } else {
                    $('.close').hide();
                }
            });
        });

        $(document).ready(function() {
            $("#is_outside_worker").change(function() {
                if ($(this).is(":checked")) {
                    $(".unit").hide();
                    $(".departmentEmployee").hide();

                    $('#emp_id').val(null).trigger('change');
                    $(".department, .company_name, .employecode").show();
                    $(".employee-id").hide();
                    $('#mobile_no').val(null).trigger('change');
                    $("#emp_id").prop("disabled", true);
                    $(".employecode input[name='emp_id']").prop("disabled", false);

                    $("#emp_name").val("").prop("readonly", false);

                    // Add validation for text input
                    $("input[name='emp_id']").rules("add", {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        pattern: /^[a-zA-Z0-9_-]+$/,
                        messages: {
                            required: "Employee code is required",
                            minlength: "Employee code must be at least 3 characters",
                            maxlength: "Employee code must not exceed 30 characters",
                            pattern: "Employee code has invalid characters"
                        }
                    });

                } else {
                    $(".unit").show();
                    $(".departmentEmployee").show();
                    $(".department, .company_name,.employecode").hide();
                    $(".employee-id").show();

                    $(".employecode input[name='emp_id']").prop("disabled", true); // Disable text input
                    $("#emp_id").prop("disabled", false); // Enable select field

                    $("#emp_name").val("").prop("readonly", false);
                    $("#mobile_no").val("").prop("readonly", false);

                    // Add validation for dropdown
                    $("select[name='emp_id']").rules("add", {
                        required: true,
                        messages: {
                            required: "Please select an Employee code"
                        }
                    });
                }

                $("#opdpatient").validate().resetForm();
            });
        });



        // getting the first aider

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

        // getting the first aiders number

        $(document).on('change', '#first_aider', function() {
            var empId = $(this).val();
            if (empId) {
                $.ajax({
                    url: "{{ admin_url('ohc/prescribe-to-patient/first-aider-number') }}",
                    type: 'GET',
                    data: {
                        empId: empId
                    },
                    dataType: 'json',
                    success: function(data) {

                        $('#is_reffered_mobile_no').val(data).prop('readonly', true);
                    },
                    error: function(xhr) {
                        alert('Error fetching mobile number. Please try again.');
                    }
                });
            } else {

                $('#is_reffered_mobile_no').val('').prop('readonly', true);
            }
        });

        // add more for the medicine


        $('#medicine_id').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var availableQuantity = selectedOption.data('available-quantity');

            $('#available_quantity').val(availableQuantity);
        });

        $('#quantity').on('input', function() {
            var availableQuantity = parseInt($('#available_quantity').val());
            var quantity = parseInt($(this).val());


            if (quantity > availableQuantity) {
                $('#quantity-error').show();
                $(this).val(availableQuantity);
            } else {
                $('#quantity-error').hide();
            }
        });


        $(document).ready(function() {

            let rowcount = 1;

            $(".add-row").click(function() {
                var rowCount = $('#medicine-tbody tr').length;


                var newRow = `
            <tr>
                <td>
                    <div class="form-group form-input">
                        <label for="medicine_id" class="require">Medicine Name</label>
                        <select name="medicine_id[${rowcount}]" class="form-control single-select" style="width: 100%">
                            <option value="">Select the Medicine Name</option>
                                    @foreach ($medicine as $list)
                                                                        <option value="{{ encryptId($list->medicine_id) }}">
                                                                            {{ getMedicinename($list->medicine_id) }}
                                                                        </option>
                                                                    @endforeach
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Available Quantity</label>
                        <input type="text" name="available_quantity[${rowcount}]" class="form-control" readonly>
                    </div>
                </td>
                <td>
                    <div class="form-group form-input">
                        <label for="quantity" class="require">Quantity</label>
                        <input type="number"  name="quantity[${rowcount}]"   placeholder="Enter the quantity" class="form-control">
                         <span id="quantity-error" style=" display:none;"  class="text-danger quantity-error">Quantity must be less than available quantity.</span>


                    </div>
                </td>
                 <td>
                    <div class="form-group form-input">
                        <label for="remarks" class="">Remarks</label>
                        <textarea name="remarks[${rowcount}]" cols="10" rows="2" class="form-control"></textarea>
                    </div>
                </td>
                <td>
                    <div class="d-flex justify-content-center align-items-center bg-danger mt-2 ml-2 text-white rounded delete-row" style="width: 30px; height: 30px;">
                        <i class="fa-solid fa-trash"></i>
                    </div>
                </td>
            </tr>`;

                $('#medicine-tbody').append(newRow);


                $('select[name="medicine_id[' + rowcount + ']"]').select2({
                    placeholder: "Select the Medicine Name",
                    width: '100%'
                });


                $('select[name="medicine_id[' + rowcount + ']"]').rules('add', {
                    required: true,
                    messages: {
                        required: 'This Medicine name is required'
                    }
                });

                $('input[name="quantity[' + rowcount + ']"]').rules('add', {
                    required: true,
                    digits: true,
                    messages: {
                        required: 'Quantity is required',
                        digits: 'Quantity must be numeric',
                    }
                });

                rowcount++;
            });






            $(document).on('change', 'select[name^="medicine_id"]', function() {
                var selectedMedicineId = $(this).val();
                var row = $(this).closest('tr');
                var duplicateFound = false;

                $('select[name^="medicine_id"]').each(function() {
                    if ($(this).val() === selectedMedicineId && $(this).attr('name') !== row.find(
                            'select[name^="medicine_id"]').attr('name')) {
                        duplicateFound = true;
                    }
                });

                if (duplicateFound) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Medicine Selected',
                        text: 'This medicine is already selected. Please choose a different one.',
                        confirmButtonColor: '#3085d6'
                    });

                    $(this).val('').trigger('change');
                    row.find('input[name^="available_quantity"]').val('');
                    row.find('input[name^="quantity"]').val('');
                } else {
                    if (selectedMedicineId) {
                        $.ajax({
                            url: "{{ admin_url('ohc/medicine-first-aid/quantity') }}/" +
                                selectedMedicineId,
                            type: 'get',
                            dataType: 'json',
                            success: function(data) {
                                row.find('input[name^="available_quantity"]').val(data
                                    .available_quantity);
                            },
                            error: function() {
                                Swal.fire('Error', 'Something went wrong. Please try again.',
                                    'error');
                            }
                        });
                    } else {
                        row.find('input[name^="available_quantity"]').val('');
                    }
                }
            });


            $(document).on("input", 'input[name^="quantity"]', function() {
                var row = $(this).closest('tr');
                var availableQuantity = parseInt(row.find('input[name^="available_quantity"]').val());
                var quantity = parseInt($(this).val());

                if (quantity > availableQuantity) {
                    row.find('.quantity-error').show();
                    $(this).val(availableQuantity);
                } else {
                    row.find('.quantity-error').hide();
                }
            });


            $(document).on("click", ".delete-row", function() {
                var rowCount = $('#medicine-tbody tr').length;

                if (rowCount > 1) {
                    $(this).closest("tr").remove();
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'At least one row is required.',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
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
            $.validator.addMethod("notEqual", function(value, element, param) {
                let otherValue = $(param).val();
                return this.optional(element) || (otherValue !== undefined && value !== otherValue);
            }, "Emergency contact and mobile number should not be the same.");

            $('#opdpatient').validate({
                rules: {
                    emp_id: {
                        required: true,

                    },
                    emp_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,

                    },
                    unit_id: {
                        required: true,

                    },
                    department: {
                        required: true,

                    },
                    department_id: {
                        required: function() {
                            return $('#is_outside_worker').is(':checked');
                        },

                    },
                    // company_name: {
                    //     required: function() {
                    //         return $('#is_outside_worker').is(':checked');
                    //     },

                    // },

                    dob: {
                        required: true,
                    },
                    emergency_contact: {

                        digits: true,
                        minlength: 10,
                        maxlength: 10,
                        notEqual: "#mobile_no"
                    },
                    mobile_no: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10,
                        notEqual: "#emergency_contact"
                    },
                    suggested_by: {
                        required: true,
                    },
                    date: {
                        required: true,
                    },

                    time: {
                        required: true,
                    },
                    gender: {
                        required: true,
                    },
                    cheif_complaint: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                    address: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                    },
                    treatment: {
                        required: function() {
                            return $('#first_aid_treatment').is(':checked');
                        },
                        minlength: 3,
                        maxlength: 600
                    },
                    'medicine_id[0]': {
                        required: function() {
                            return $('#first_aid_treatment').is(':checked');
                        }
                    },
                    'quantity[0]': {
                        required: function() {
                            return $('#first_aid_treatment').is(':checked');
                        }
                    },
                    // 'remarks[0]': {
                    //     required: function() {
                    //         return $('#first_aid_treatment').is(':checked');
                    //     },
                    //     minlength: 3,
                    //     maxlength: 600
                    // },
                    details: {
                        required: function() {
                            return $('#suggested_by').val() == '3';
                        },
                        minlength: 3,
                        maxlength: 100
                    },
                    hospital_name: {
                        required: function() {
                            return $('#is_reffered').is(':checked');
                        },

                    },
                    first_aider: {
                        required: function() {
                            return $('#is_reffered').is(':checked');
                        }
                    },
                    is_reffered_mobile_no: {
                        required: function() {
                            return $('#is_reffered').is(':checked');
                        },
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    vechicle: {
                        required: function() {
                            return $('#is_reffered').is(':checked');
                        }
                    },
                    patient_status: {
                        required: function() {
                            return $('#is_reffered').is(':checked');
                        }
                    },
                    fitness_certificate: {
                        required: function() {
                            return $('#patient_status').val() ==
                                '2';
                        }
                    },
                    file: {
                        required: function() {
                            return $('#fitness_certificate').val() ==
                                '1';
                        },
                        extension: "pdf|doc|docx|png|jpg|jpeg"
                    },
                    close_description: {
                        required: function() {
                            return $('#patient_status').val() ==
                                '2';
                        },
                        minlength: 3,
                        maxlength: 600
                    },
                    other_vechicle: {
                        required: function() {
                            return $('#vechicle').val() ==
                                '3';
                        },
                        minlength: 3,
                        maxlength: 100
                    }
                },
                messages: {
                    emp_id: {
                        required: "Please enter employee code.",

                    },
                    unit_id: {
                        required: "Please enter unit name.",

                    },
                    file: {
                        required: "File is required.",
                        extension: "Please Select the valid mime Type."
                    },
                    emp_name: {
                        required: "Please enter employee name.",
                        minlength: "employee name must be at least 3 characters.",
                        maxlength: "employee name must not exceed 30 characters.",

                    },

                    department: {
                        required: "Please enter department Name.",
                    },
                    department_id: {
                        required: "Please enter department Name.",
                    },
                    dob: {
                        required: "Please enter the date of birth.",
                    },
                    details: {
                        required: "Please enter the details.",
                        minlength: "Details must be at least 3 characters.",
                        maxlength: "Details must not exceed 100 characters.",
                    },
                    date: {
                        required: "Please select the date.",
                    },
                    suggested_by: {
                        required: "Please select the Suggested By.",
                    },
                    mobile_no: {
                        required: "Please enter the Mobile Number.",
                        digits: "The Moblie contains only the numeric",
                        minlength: "mobile number minimum 10 required",
                        maxlength: "mobile number maximum 10 required",
                        notEqual: "Mobile number and emergency contact should not be the same."
                    },
                    emergency_contact: {

                        digits: "The Moblie contains only the numeric",
                        minlength: "Emergency Contact minimum 10 required",
                        maxlength: "Emergency Contact maximum 10 required",
                        notEqual: "Emergency contact and mobile number should not be the same."
                    },
                    time: {
                        required: "Please select the time.",
                    },
                    gender: {
                        required: "Please select the gender.",
                    },
                    cheif_complaint: {
                        required: 'Chief Complaint is required',
                        minlength: 'Minimum 3 characters are required',
                        maxlength: 'Chief Complaint should not exceed 600 characters',
                    },
                    address: {
                        required: 'Address is required',
                        minlength: 'Minimum 3 characters are required',
                        maxlength: 'Address should not exceed 600 characters',
                    },
                    treatment: {
                        required: "Please enter treatment details .",
                        minlength: "Treatment must be at least 3 characters.",
                        maxlength: "Treatment must not exceed 600 characters.",
                    },
                    'medicine_id[0]': {
                        required: "Please select a medicine .",
                    },
                    'quantity[0]': {
                        required: "Please enter the quantity .",
                    },
                    'remarks[0]': {
                        required: "Please enter remarks .",
                        minlength: "remarks must be at least 3 characters.",
                        maxlength: "remarks must not exceed 100 characters.",
                    },
                    hospital_name: {
                        required: "Hospital name is required .",


                    },
                    first_aider: {
                        required: "Please Select the First aider.",

                    },
                    is_reffered_mobile_no: {
                        required: "Please enter the Mobile Number.",
                        digits: "The Moblie contains only the numeric",
                        minlength: "mobile number minimum 10 required",
                        maxlength: "mobile number maximum 10 required",
                    },
                    vechicle: {
                        required: "vechicle is required ",
                    },
                    fitness_certificate: {
                        required: "Fitness Certificate is required .",
                    },
                    close_description: {
                        required: "Close Description is required .",
                        minlength: "Close Description must be at least 3 characters.",
                        maxlength: "Close Description must not exceed 600 characters.",
                    },
                    other_vechicle: {
                        required: "Other Vechicle is required .",
                        minlength: "Other Vechicle must be at least 3 characters.",
                        maxlength: "Other Vechicle must not exceed 100 characters.",
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
