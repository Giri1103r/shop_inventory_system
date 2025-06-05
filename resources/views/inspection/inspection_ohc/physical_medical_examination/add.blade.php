@extends('admin.layouts.admin')
@section('title', 'Physical Health Examination Check-up')
@section('pageurl', admin_url('ohc/physical-medical-examination/yearly/list'))


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
                                    <x-button-back
                                        href="{{ admin_url('ohc/physical-medical-examination/yearly/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="physicalHealthForm"
                                        action="{{ admin_url('ohc/physical-medical-examination/yearly/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="{{ $document_no->doc_no }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name ="issue_date" id="issue_date"
                                                            class="form-control" placeholder="Issue Date"
                                                            value="{{ displaydateformat($document_no->issue_date) }}"
                                                            readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision & Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ $document_no->rev_dt }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Form Number</label>
                                                    <input type="text" name="form_number" id="form_number"
                                                        class="form-control" placeholder="Enter the Form Number"
                                                        value="">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Employee Details</h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee ID</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" placeholder="Employee Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Contact Number</label>
                                                    <input type="text" name="contact_number" id="contact_number"
                                                        class="form-control" placeholder="Enter the Contact Number"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">gender</label>
                                                    <input type="text" name="gender" id="gender" class="form-control"
                                                        placeholder="Enter the gender" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date of Birth</label>


                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="dob" id="dob"
                                                            class="form-control" placeholder="Enter the dob">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Age</label>
                                                    <input type="text" name="age" id="age"
                                                        class="form-control" placeholder="Enter the age" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Blood Group</label>
                                                    <input type="text" name="blood_group" id="blood_group"
                                                        class="form-control" placeholder="Enter the Blood Group" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            class="form-control" placeholder="Enter the date " readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <input type="text" name="unit_id" id="unit_id"
                                                        class="form-control" placeholder="Enter the unit " readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <input type="text" name="department_id" id="department_id"
                                                        class="form-control" placeholder="Enter the Department " readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Height(in Meters)</label>
                                                    <input type="text" name="height" id="height"
                                                        class="form-control" placeholder="Enter the Height">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Weight (in Kg)</label>
                                                    <input type="text" name="weight" id="weight"
                                                        class="form-control" placeholder="Enter the weight">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">BMI</label>
                                                    <input type="text" name="bmi" id="bmi"
                                                        class="form-control" placeholder="Enter the Body Mass Index"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" id="address" class="form-control" cols="10" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Clinical Details</h4>
                                            </div>

                                            <div class="mb-2">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Details Of Personal Habits</th>
                                                            <th style="text-align: center">Status</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($personalDetails as $personalDetails)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>

                                                                <td class="text-center">
                                                                    {{ $personalDetails->personal_details }}
                                                                    <input type="hidden"
                                                                        name="persnal_details[{{ $personalDetails->id }}]"
                                                                        value="{{ $personalDetails->id }}">
                                                                </td>

                                                                <td class="text-center">
                                                                    <input type="hidden"
                                                                        name="status[{{ $personalDetails->id }}]"
                                                                        value="0">
                                                                    <input type="checkbox"
                                                                        name="status[{{ $personalDetails->id }}]"
                                                                        value="1">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>

                                                </table>


                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Present Complaints</label>
                                                    <textarea name="present_complaints" id="present_complaints" class="form-control" cols="10" rows="5"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Past History</label>
                                                    <textarea name="past_history" id="past_history" class="form-control" cols="10" rows="5"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Famiy History</h4>
                                            </div>

                                            <div class="mb-2">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Details Of Personal Habits</th>
                                                            <th style="text-align: center">Status</th>
                                                            <th style="text-align: center">Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($familyHistory as $familyHistory)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>

                                                                <td class="text-center">
                                                                    {{ $familyHistory->family_history }}
                                                                    <input type="hidden"
                                                                        name="id[{{ $familyHistory->id }}]"
                                                                        value="{{ encryptId($familyHistory->id) }}">
                                                                </td>

                                                                <td class="text-center">
                                                                    <input type="hidden"
                                                                        name="status[{{ $familyHistory->id }}]"
                                                                        value="0">
                                                                    <input type="checkbox"
                                                                        name="status[{{ $familyHistory->id }}]"
                                                                        value="1">
                                                                </td>

                                                                <td class="text-center">
                                                                    <div class="form-group form-input">
                                                                        <input type="text"
                                                                            name="family_remarks[{{ $familyHistory->id }}]"
                                                                            class="form-control form-control-sm"
                                                                            placeholder="Enter remarks">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Vital Check Points</h4>
                                            </div>

                                            <div class="mb-2">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Check Points</th>
                                                            <th style="text-align: center">Reading Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($check_points as $label => $items)
                                                            @foreach ($items as $point)
                                                                <tr>
                                                                    <td class="text-center">{{ $loop->iteration }}</td>

                                                                    <td class="text-center">
                                                                        {{ $point->name }}
                                                                        <input type="hidden"
                                                                            name="id[{{ $point->id }}]"
                                                                            value="{{ encryptId($point->id) }}">
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <div class="form-group form-input">
                                                                            <input type="text"
                                                                                name="reading_value[{{ $point->id }}]"
                                                                                class="form-control form-control-sm"
                                                                                placeholder="Enter Reading value">
                                                                        </div>

                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endforeach


                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>

                                        <div class="row mt-2">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">EYE Check Up</h4>
                                            </div>

                                            <div class="mb-2">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Vision</th>
                                                            <th style="text-align: center">Without Glasses(Right)</th>
                                                            <th style="text-align: center">With Glasses(Left)</th>
                                                            <th style="text-align: center">Color Blindness</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Distance</td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <input type="text" name="distance_with_out_glasses"
                                                                        id="distance_with_out_glasses"
                                                                        class="form-control">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <input type="text" name="distance_with_glasses"
                                                                        id="distance_with_glasses" class="form-control">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <select name="distance_with_out_glasses_yes"
                                                                        id="distance_with_out_glasses_yes"
                                                                        class="form-control single-select "
                                                                        style="width:100%">
                                                                        <option value="">Select the Option</option>
                                                                        <option value="{{ encryptId(1) }}">Yes</option>
                                                                        <option value="{{ encryptId(0) }}">NO</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Near</td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <input type="text" name="near_with_out_glasses"
                                                                        id="near_with_out_glasses" class="form-control">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <input type="text" name="near_with_glasses"
                                                                        id="near_with_glasses" class="form-control">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group form-input">
                                                                    <select name="near_with_out_glasses_yes"
                                                                        id="near_with_out_glasses_yes"
                                                                        class="form-control single-select "
                                                                        style="width:100%">
                                                                        <option value="">Select the Option</option>
                                                                        <option value="{{ encryptId(1) }}">Yes</option>
                                                                        <option value="{{ encryptId(0) }}">NO</option>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>



                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Remarks of Factory Medical
                                                        Officer</label>
                                                    <textarea name="remarks" id="remarks" class="form-control" cols="10" rows="5"></textarea>
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label" style="display: block; ">Factory Medical
                                                        Officer Signature</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Factory Medical
                                                            Officer Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/physical-medical-examination/yearly/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
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
            document.addEventListener("DOMContentLoaded", function() {
                const dobInput = document.getElementById("dob");
                const ageInput = document.getElementById("age");

                dobInput.addEventListener("change", function() {
                    const dob = new Date(this.value);
                    const today = new Date();

                    let age = today.getFullYear() - dob.getFullYear();
                    const m = today.getMonth() - dob.getMonth();

                    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
                        age--;
                    }

                    ageInput.value = (isNaN(age) || age < 0) ? '' : age;
                });
            });
            // calculation of the BMI

            function calculateBMI() {
                let height = parseFloat($('#height').val());
                let weight = parseFloat($('#weight').val());

                if (!isNaN(height) && !isNaN(weight) && height > 0) {
                    // Convert height from cm to meters
                    let heightInMeters = height / 100;
                    let bmi = weight / (heightInMeters * heightInMeters);
                    $('#bmi').val(bmi.toFixed(2)); // round to 2 decimal places
                } else {
                    $('#bmi').val('');
                }
            }

            $(document).on('input', '#height, #weight', function() {
                calculateBMI();
            });
        </script>


        <script>
            $(document).ready(function() {
                flatpickr("#dob", {
                    dateFormat: "d-m-Y",
                    allowInput: true,
                    maxDate: function() {
                        var today = new Date();
                        var minDate = new Date();
                        minDate.setFullYear(today.getFullYear() -
                            18);
                        return minDate;
                    }(),

                });
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });
                var Datepicker = flatpickr("#date", {
                    dateFormat: "d-m-Y",
                    minDate: new Date()

                });
            });

            // employee id
            $('#emp_id').select2({
                ajax: {
                    url: '{{ admin_url('ohc/employee-cum-patient/employeeid') }}',
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
            $(document).on('change', '#emp_id', function() {
                var empId = $(this).val();
                if (empId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/employee-cum-patient/employeename') }}",
                        type: 'GET',
                        data: {
                            empId: empId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.employee) {
                                $('#emp_name').val(response.employee.emp_name).prop('readonly', true);
                                $('#unit_id').val(response.unit ? response.unit.unit_name : '').prop(
                                    'readonly', true);
                                $('#department_id').val(response.department ? response.department
                                    .department_name : '').prop('readonly', true);
                                $('#contact_number').val(response.employee.mobile_no).prop('readonly',
                                    true);
                                $('#gender').val(response.employee.gender).prop('readonly', true);

                                if (response.blood_group) {
                                    $('#blood_group').val(response.blood_group.blood_group_name).prop(
                                        'readonly', true);
                                } else {
                                    $('#blood_group').val('').prop('readonly',
                                        false); // Editable if no blood group
                                }
                            } else {
                                // Reset all fields
                                $('#emp_name, #unit_id, #department_id, #contact_number, #gender, #blood_group')
                                    .val('').prop('readonly', true);
                                $('#blood_group').prop('readonly', false); // Allow editing if no employee
                            }
                        },
                        error: function() {
                            alert('Error fetching employee name. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name, #unit_id, #department_id, #contact_number, #gender, #blood_group').val('').prop(
                        'readonly', true);
                    $('#blood_group').prop('readonly', false); // Editable on empty
                }
            });



            $(function() {
                // Initialize validator
                var validator = $('#physicalHealthForm').validate({
                    rules: {
                        form_number: {
                            required: true
                        },
                        emp_id: {
                            required: true
                        },
                        emp_name: {
                            required: true
                        },
                        dob: {
                            required: true
                        },
                        age: {
                            required: true
                        },
                        unit_id: {
                            required: true
                        },
                        department_id: {
                            required: true
                        },
                        signature_image: {
                            required: true,
                            extension: "png|jpeg|jpg",
                            filesize: 15728640,
                        },
                        contact_number: {
                            required: true
                        },
                        gender: {
                            required: true
                        },
                        date: {
                            required: true
                        },
                        height: {
                            required: true
                        },
                        weight: {
                            required: true
                        },
                        bmi: {
                            required: true
                        },
                        blood_group: {
                            required: true
                        },
                        address: {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        present_complaints: {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        past_history: {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        near_with_glasses: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                        },
                        near_with_out_glasses: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                        },
                        near_with_out_glasses_yes: {
                            required: true,

                        },
                        distance_with_glasses: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                        },
                        distance_with_out_glasses: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                        },
                        distance_with_out_glasses_yes: {
                            required: true,

                        },
                        remarks: {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                    },
                    messages: {
                        form_number: {
                            required: "Form number is required"
                        },
                        emp_id: {
                            required: "Employee ID is required"
                        },
                        emp_name: {
                            required: "Employee name is required"
                        },
                        dob: {
                            required: "Date of Birth is required"
                        },
                        age: {
                            required: "Age is required (auto-calculated from DOB)"
                        },
                        unit_id: {
                            required: "Unit is required"
                        },
                        department_id: {
                            required: "Department is required"
                        },
                        contact_number: {
                            required: "Contact number is required"
                        },
                        gender: {
                            required: "Gender is required"
                        },
                        date: {
                            required: "Date is required"
                        },
                        height: {
                            required: "Height is required"
                        },
                        weight: {
                            required: "Weight is required"
                        },
                        blood_group: {
                            required: "Blood group is required"
                        },
                        bmi: {
                            required: "BMI is required"
                        },
                        near_with_glasses: {
                            required: "Near with Glasses is required",
                            minlength: "Near with Glasses must be at least 3 characters",
                            maxlength: "Near with Glasses must not exceed 300 characters"
                        },
                        near_with_out_glasses: {
                            required: "Near With out Glasses is required",
                            minlength: "Near With out Glasses must be at least 3 characters",
                            maxlength: "Near With out Glasses must not exceed 300 characters"
                        },
                        distance_with_glasses: {
                            required: "Distance with glasses is required",
                            minlength: "Distance with glasses must be at least 3 characters",
                            maxlength: "Distance with glasses must not exceed 300 characters"
                        },
                        distance_with_out_glasses: {
                            required: "distance without glasses is required",
                            minlength: "distance without glasses must be at least 3 characters",
                            maxlength: "distance without glasses must not exceed 300 characters"
                        },
                        distance_with_out_glasses_yes: {
                            required: "Please Select the option",

                        },
                        near_with_out_glasses_yes: {
                            required: "Please Select the option",

                        },
                        address: {
                            required: "Address is required",
                            minlength: "Address must be at least 3 characters",
                            maxlength: "Address must not exceed 300 characters"
                        },
                        present_complaints: {
                            required: "Present Complaints is required",
                            minlength: "Present Complaints must be at least 3 characters",
                            maxlength: "Present Complaints must not exceed 300 characters"
                        },
                        past_history: {
                            required: "Past History is required",
                            minlength: "Past History must be at least 3 characters",
                            maxlength: "Past History must not exceed 300 characters"
                        },
                        remarks: {
                            required: "Remarks is required",
                            minlength: "Remarks must be at least 3 characters",
                            maxlength: "Remarks must not exceed 300 characters"
                        },
                        signature_image: {
                            required: "Please upload your signature image.",
                            extension: "Allowed file types: PNG, JPEG, JPG.",
                            filesize: "File must be less than 15MB."
                        },

                    },

                    errorElement: 'span',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        if (element.is(':radio') || element.is('input[type="checkbox"]')) {
                            element.closest('.form-group').append(error);
                        } else {
                            element.closest('.form-input').append(error);
                        }
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                    },
                    submitHandler: function(form) {
                        console.log('Form submitted');
                        form.submit();
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

                $('input[name^="reading_value"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        number: true,
                        messages: {
                            required: "Reading value is required",
                            number: "Reading value must be a number"
                        }
                    });
                });

                $('input[name^="family_remarks"]').each(function() {
                    const id = $(this).attr('name').match(/\d+/)[0]; // extract ID from name="remarks[ID]"

                    $(this).rules('add', {
                        required: {
                            depends: function() {
                                return $('input[name="status[' + id + ']"]:checked').length > 0;
                            }
                        },
                        minlength: 3,
                        maxlength: 100,
                        messages: {
                            required: "Remarks are required when status is checked",
                            minlength: "Remarks must be at least 3 characters",
                            maxlength: "Remarks must not exceed 100 characters"
                        }
                    });
                });



            });
        </script>
    @endpush
