@extends('admin.layouts.admin')
@section('title', 'Prescribe To Patient Edit')
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

                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/prescribe-to-patient/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="opdpatient"
                                        action="{{ admin_url('ohc/prescribe-to-patient/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($opdpatient->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"> Is OutSide Worker</label><br>
                                                    <input type="checkbox" id="is_outside_worker" name="is_outside_worker"
                                                        value="1"
                                                        {{ $opdpatient->is_outside_employee == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-4 employee-id mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Employee code</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                        @if (isset($opdpatient->emp_id) && isset($opdpatient->emp_id))
                                                            <option value="{{ $opdpatient->emp_id }}" selected>
                                                                {{ $opdpatient->emp_id }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 employecode mb-2" style="display: none">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee code</label>
                                                    <input type="text" name="emp_id" id="emp_id"
                                                        value="{{ $opdpatient->emp_id }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name"
                                                       readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2 department"style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the department</option>
                                                        @foreach ($departmentList as $list)
                                                            <option value="{{ ($list->id) }}"
                                                                @if ($list->id == $opdpatient->department_id) selected @endif>
                                                                {{ $list->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2 unit" >
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}"
                                                                @if ($list->id == $opdpatient->unit_id) selected @endif>
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile</label>
                                                    <input type="text" name="mobile_no" id="mobile_no"
                                                        value="{{ $opdpatient->mobile_no }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Emergency Contact</label>
                                                    <input type="text" name="emergency_contact" id="emergency_contact"
                                                        value="{{ $opdpatient->emergency_contact }}" class="form-control">
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 mb-2 company_name"style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <input type="text" name="company_name" id="company_name"
                                                        value="{{ $opdpatient->company_name }}" class="form-control"
                                                        placeholder="Company Name">
                                                </div>
                                            </div> --}}
                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="dob" class="form-label require">Date Of Birth</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="dob"
                                                        value="{{ displaydateformat($opdpatient->dob) }}" id="dob"
                                                        autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" id="addresss" cols="30" rows="5" class="form-control">{{ $opdpatient->address }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Gender</label>
                                                    <select name="gender" id="gender"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the gender</option>
                                                        <option value="Male"
                                                            {{ $opdpatient->gender === 'Male' ? 'selected' : '' }}>Male
                                                        </option>
                                                        <option value="Female"
                                                            {{ $opdpatient->gender === 'Female' ? 'selected' : '' }}>Female
                                                        </option>
                                                        <option value="Other"
                                                            {{ $opdpatient->gender === 'Other' ? 'selected' : '' }}>Other
                                                        </option>


                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="date" class="form-label ">Date</label>
                                                <div class="input-group date form-input custom-height">
                                                    <input type="text" class="form-control" name="date"
                                                        value="{{ displaydateformat($opdpatient->date) }}" id="date"
                                                        autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3 form-input">
                                                <label for="time" class="form-label">Time</label>
                                                <div class="input-group date form-input custom-height">
                                                    <input type="text" class="form-control" name="time"
                                                        value="{{ displaydateformat($opdpatient->time) }}" id="time"
                                                        autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fas fa-clock"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Cheif Complaint</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaint" cols="30" rows="5" class="form-control">{{ $opdpatient->cheif_complaint }}"</textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Vital Checkup</label><br>
                                                    <input type="checkbox" id="vital_checkup" name="vital_checkup"
                                                        value="1"
                                                        {{ $opdpatient->vital_checkup == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Suggested By</label>
                                                    <select name="suggested_by" id="suggested_by"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">select the Suggested By</option>
                                                        @foreach ($suggestedBy as $list)
                                                            <option value="{{ $list->id }}"
                                                                @if ($list->id == $opdpatient->suggested_by) selected @endif>
                                                                {{ $list->suggested_by }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label for="vechicle" class="form-label require">Details</label>
                                                    <input name="details" id="details" placeholder="Enter the Details"
                                                        value="{{ $opdpatient->suggested_details }}"
                                                        class="form-control">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">First Aid Treatment</label><br>
                                                    <input type="checkbox" id="first_aid_treatment"
                                                        name="first_aid_treatment" value="1"
                                                        {{ $opdpatient->first_aid_treatment == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="treatment" style="display: none;">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Treatment</label>
                                                        <textarea name="treatment" id="treatment" cols="30" rows="5" class="form-control">{{ $opdpatient->treatment }}</textarea>
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
                                                                @foreach ($opd_firstaid as $key => $firstaid)
                                                                    <tr class="medicinedetails">
                                                                        <td>
                                                                            <input type="hidden" name="encryptid"
                                                                                class="encryptid"
                                                                                value="{{ encryptId($firstaid->id) }}">
                                                                            <div class="form-group form-input">
                                                                                <label for="medicine_id"
                                                                                    class="require">Medicine
                                                                                    Name</label>
                                                                                <select
                                                                                    name="medicine_id[{{ $key }}]"
                                                                                    id="medicine_id"
                                                                                    class="form-control medicine"
                                                                                    style="width: 100%">
                                                                                    <option value="">Select the
                                                                                        Medicine Name
                                                                                    </option>
                                                                                    @foreach ($medicine as $list)
                                                                                        <option
                                                                                            value="{{ $list->medicine_id }}"
                                                                                            @if ($firstaid->medicine_id == $list->medicine_id) selected @endif>
                                                                                            {{ getMedicinename($list->medicine_id) }}
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
                                                                                    name="available_quantity[{{ $key }}]"
                                                                                    id="available_quantity"
                                                                                    value="{{ $firstaid->available_quantity }}"
                                                                                    placeholder="Available quantity"
                                                                                    class="form-control" readonly>
                                                                            </div>
                                                                        </td>

                                                                        <td>
                                                                            <div class="form-group form-input">
                                                                                <label for="quantity"
                                                                                    class="require">Quantity</label>
                                                                                <input type="text"
                                                                                    name="quantity[{{ $key }}]"
                                                                                    id="quantity"
                                                                                    placeholder="Enter the quantity"
                                                                                    value="{{ $firstaid->quantity }}"
                                                                                    class="form-control">
                                                                                <span id="quantity-error"
                                                                                    style=" display:none;"
                                                                                    class="text-danger">Quantity must be
                                                                                    less
                                                                                    than available quantity.</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group form-input">
                                                                                <label for="remarks"
                                                                                    class="">Remarks</label>
                                                                                <textarea name="remarks[{{ $key }}]" id="remarks" cols="10" rows="2" class="form-control">{{ $firstaid->remarks }}</textarea>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="row gap-2">
                                                                                <div class="d-flex justify-content-center align-items-center bg-primary mt-2 ml-2 text-white rounded add-row"
                                                                                    style="width: 30px; height: 30px;">
                                                                                    <i class="fa-solid fa-plus"></i>
                                                                                </div>
                                                                                <div class="d-flex justify-content-center align-items-center bg-danger mt-2 me-5 text-white rounded delete-row"
                                                                                    style="width: 30px; height: 30px;">
                                                                                    <i class="fa-solid fa-trash"></i>
                                                                                </div>
                                                                            </div>

                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Is Reffered</label><br>
                                                    <input type="checkbox" id="is_reffered" name="is_reffered"
                                                        value="1"{{ $opdpatient->is_refered == 1 ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                            <div class="isReffered row" style="display: none;">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="hospital_name" class="form-label require">Hospital
                                                            Name</label>
                                                        <input type="text" name="hospital_name" id="hospital_name"
                                                            value="{{ $isreffered->hospital_name }}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="first_aider" class="form-label require">First
                                                            Aider</label>
                                                        <select name="first_aider" id="first_aider" class="form-control "
                                                            style="width: 100%">
                                                            <option value="">select the First Aider</option>
                                                            @if (isset($isreffered->first_aider) && isset($isreffered->first_aider))
                                                                <option value="{{ $isreffered->first_aider }}" selected>
                                                                    {{ $isreffered->first_aider }}</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Mobile Number</label>
                                                        <input type="text" name="is_reffered_mobile_no"
                                                            value="{{ $isreffered->mobile_no }}"
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
                                                                <option
                                                                    value="{{ $list->id }}"@if ($isreffered->refered_by_vechicle == $list->id) selected @endif>
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
                                                                <option value="{{ $list->id }}"
                                                                    @if ($opdpatient->patient_status == $list->id) selected @endif>
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
                                                                <option value="1"
                                                                    {{ $opdpatient->fitness_certificate == 1 ? 'selected' : '' }}>
                                                                    Required</option>
                                                                <option value="2"
                                                                    {{ $opdpatient->fitness_certificate == 2 ? 'selected' : '' }}>
                                                                    Not Required</option>

                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="close" class="form-label require">Close the
                                                                Description</label>
                                                            <textarea name="close_description" id="close_description" cols="30" rows="5" class="form-control">{{ $opdpatient->closed_description }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row other_vechicles"style="display: none;">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="vechicle" class="form-label require">Reffered By
                                                                other Vechicle</label>
                                                            <input name="other_vechicle" id="vechicle"
                                                                value="{{ $isreffered->other_vechicle }}"
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
        // company name

        $('#is_outside_worker').change(function() {
            if ($(this).is(':checked')) {

                $('.company_name').show();

            } else {

                $('.company_name').hide();
            }
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
                defaultDate: currentTime,
                minTime: currentTime,
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
        $(document).ready(function() {
            if (!$('#is_outside_worker').is(':checked')) {
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

            // Set selected value if available
            var empId = '{{ $opdpatient->emp_id ?? '' }}';
            var empName = '{{ $opdpatient->emp_id ?? '' }}';
            var phoneNum = '{{ $opdpatient->mobile_no ?? '' }}'
            if (empId && empName) {
                var newOption = new Option(empName, empId, true, true);
                $('#emp_id').append(newOption).trigger('change');
            }
        }
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
                                // $('#mobile_no').val(response.employee.mobile_no).prop('readonly', true);
                                // $('#department_id').val(response.departments.department_name).prop('readonly', true);
                            } else {
                                alert("No employee details found.");
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching employee details. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name, #mobile_no, #department_id').val('').prop('disabled', true);
                }
            }
        });



        $(document).ready(function() {

            if ($('#is_reffered').is(':checked')) {
                $('.isReffered').show();
            } else {
                $('.isReffered').hide();
            }

            // Check initial state for 'first_aid_treatment'
            if ($('#first_aid_treatment').is(':checked')) {
                $('.treatment').show();
            } else {
                $('.treatment').hide();
            }

            // Handle change event for 'is_reffered'
            $('#is_reffered').change(function() {
                if ($(this).is(':checked')) {
                    $('.isReffered').slideDown();
                } else {
                    $('.isReffered').slideUp();
                }
            });

            // Handle change event for 'first_aid_treatment'
            $('#first_aid_treatment').change(function() {
                if ($(this).is(':checked')) {
                    $('.treatment').slideDown();
                } else {
                    $('.treatment').slideUp();
                }
            });



        });
        $(document).ready(function() {
            // Ensure validation is initialized
            let formValidator = $("#opdpatient").validate();

            // Custom validation method for regex
            $.validator.addMethod("regex", function(value, element, regexp) {
                let re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            }, "Invalid format.");



            function toggleWorkerFields() {
                if ($("#is_outside_worker").is(":checked")) {
                    $(".unit").hide();
                    $(".department, .company_name, .employecode").show();
                    $(".employee-id").hide();

                    $("#emp_name").val("{{ $opdpatient->emp_name ?? '' }}").prop("readonly", false);


                    if (formValidator) {
                        $("select[name='emp_id']").rules("remove");
                        $("input[name='emp_id']").rules("add", {
                            required: true,
                            minlength: 3,
                            maxlength: 30,
                            regex: "^[a-zA-Z0-9_-]+$",
                            messages: {
                                required: "Employee code is required",
                                minlength: "Employee code must be at least 3 characters",
                                maxlength: "Employee code must not exceed 30 characters",
                                regex: "Employee code has invalid characters (only letters, numbers, underscores, and hyphens allowed)"
                            }
                        });
                    }

                } else {
                    $(".unit").show();
                    $(".department, .company_name, .employecode").hide();
                    $(".employee-id").show(); // Show dropdown



                    if (formValidator) {
                        $("input[name='emp_id']").rules("remove");
                        $("select[name='emp_id']").rules("add", {
                            required: true,
                            messages: {
                                required: "Please select an Employee code"
                            }
                        });
                    }
                }

                // Reset validation errors after toggling
                formValidator.resetForm();
            }

            // Initial check on page load
            toggleWorkerFields();

            // Event handler for is_outside_worker checkbox
            $("#is_outside_worker").change(function() {
                toggleWorkerFields();
            });
        });


        $(document).ready(function() {

            function toggleDetailsField() {
                var selectedValue = $('#suggested_by').val();
                if (selectedValue == '3') {
                    $('#details').closest('.col-md-4').show();
                } else {
                    $('#details').closest('.col-md-4').hide();
                }
            }


            function toggleVehicleField() {
                var selectedValue = $('#vechicle').val();
                if (selectedValue == '3') {
                    $('.other_vechicles').show();
                } else {
                    $('.other_vechicles').hide();
                }
            }

            function toggleStatusField() {
                var selectedValue = $('#patient_status').val();
                if (selectedValue == '2') {
                    $('.close').show();
                } else {
                    $('.close').hide();
                }
            }

            toggleDetailsField();
            toggleVehicleField();
            toggleStatusField();

            $('#suggested_by').change(toggleDetailsField);
            $('#vechicle').change(toggleVehicleField);
            $('#patient_status').change(toggleStatusField);

        });


        // getting the first aider
        $(document).ready(function() {
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


            var empId = '{{ $isreffered->first_aider ?? '' }}';
            var empName = '{{ $isreffered->first_aider ?? '' }}';

            if (empId && empName) {
                var newOption = new Option(empName, empId, true, true);
                $('#first_aider').append(newOption).trigger('change');
            }

        });



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

        // delete the add more row


        $(document).on('click', '.delete-row', function(event) {
            event.preventDefault(); // Prevents the form from submitting

            var row = $(this).closest(".medicinedetails");
            var rowId = row.find("input[name='encryptid']").val();

            if (rowId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this record?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('ohc/prescribe-to-patient/delete') }}/" +
                                rowId,
                            type: 'POST', // Use POST instead of DELETE
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'POST', // Simulate DELETE method
                                id: rowId
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    row.remove();
                                    Swal.fire('Deleted!', response.msg, 'success');
                                } else {
                                    Swal.fire('Error!', response.msg, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error!',
                                    'Something went wrong. Please try again later.',
                                    'error');
                            }
                        });
                    }
                });
            } else {
                $(this).closest("tr").remove();
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
            $(".medicine").select2({
                placeholder: "Select the Medicine Name",
                width: '100%'
            });
            let rowcount = {{ count($opd_firstaid) }};

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
                                                                        <option value="{{ $list->medicine_id }}">
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
                        <input type="text" name="quantity[${rowcount}]"   placeholder="Enter the quantity" class="form-control">
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
                            url: "{{ admin_url('ohc/medicine-first-aid/editquantity') }}/" +
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

            $('#opdpatient').validate({
                rules: {
                    emp_id: {
                        required: true,

                    },
                    emp_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 30
                    },
                    unit_id: {
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
                    emergency_contact: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    dob: {
                        required: true,
                    },
                    mobile_no: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
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
                        maxlength: 100
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
                        minlength: 3,
                        maxlength: 100
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
                    emp_name: {
                        required: "Please enter employee name.",
                        minlength: "employee name must be at least 3 characters.",
                        maxlength: "employee name must not exceed 30 characters.",
                    },
                    // company_name: {
                    //     required: "Please enter Company name.",
                    // },
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
                    },
                    emergency_contact: {
                        required: "Please enter the Emergency Contact.",
                        digits: "The Moblie contains only the numeric",
                        minlength: "Emergency Contact minimum 10 required",
                        maxlength: "Emergency Contact maximum 10 required",
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
                        maxlength: "Treatment must not exceed 100 characters.",
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
                        minlength: "Hospital name must be at least 3 characters.",
                        maxlength: "Hospital name must not exceed 100 characters.",

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
