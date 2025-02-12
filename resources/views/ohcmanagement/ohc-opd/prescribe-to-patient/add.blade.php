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
                                        action="{{ admin_url('ohc/prescribe-to-patient/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"> Is OutSide Worker</label><br>
                                                    <input type="checkbox" id="is_outside_worker" name="is_outside_worker"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Employee code</label>
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
                                                        class="form-control" placeholder="Employee Name" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2 department">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <input type="text" name="department_id" id="department_id"
                                                        class="form-control" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2 unit" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
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
                                                    <label class="form-label require">Emergency Contact</label>
                                                    <input type="text" name="emergency_contact" id="emergency_contact"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="dob" class="form-label ">Date Of Birth</label>
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
                                                    <label class="form-label ">Address</label>
                                                    <textarea name="address" id="addresss" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Gender</label>
                                                    <select name="gender" id="gender" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">select the gender</option>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                        <option value="others">Others</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="date" class="form-label ">Date</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="date"
                                                        id="date" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-calendar"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 mb-3 form-input">
                                                <label for="date" class="form-label ">Time</label>
                                                <div class="input-group date form-input  custom-height">
                                                    <input type="text" class="form-control " name="time"
                                                        id="time" autocomplete="off">
                                                    <div class="input-group-addon input-group-text">
                                                        <span class="fa fa-clock-o"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Cheif Complaint</label>
                                                    <textarea name="cheif_complaint" id="cheif_complaints" cols="30" rows="5" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Vital Checkup</label><br>
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
                                                    <input name="details" id="details" placeholder="Enter the Details" class="form-control">

                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">First Aid Treatment</label><br>
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
                                                                            <input type="text" name="quantity[0]"
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
                                                    <label class="form-label">Is Reffered</label><br>
                                                    <input type="checkbox" id="is_reffered" name="is_reffered"
                                                        value="1">
                                                </div>
                                            </div>
                                            <div class="isReffered row" style="display: none;">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="hospital_name" class="form-label require">Hospital
                                                            Name</label>
                                                        <input type="text" name="hospital_name" id="hospital_name"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label for="first_aider" class="form-label require">First
                                                            Aider</label>
                                                        <select name="first_aider" id="first_aider"
                                                            class="form-control " style="width: 100%">
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
                                                            @foreach ($reffered  as $list)
                                                            <option value="{{ $list->id }}">{{ $list->refered_vechicle }}
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
                                                            @foreach ($patientstatus  as $list)
                                                            <option value="{{ $list->id }}">{{ $list->patient_status }}
                                                            </option>
                                                        @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row close" style="display: none;">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label for="Fitness" class="require">Fitness Certificate</label>
                                                            <select name="fitness_certificate" id="fitness_certificate"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">select the Fitness certificate
                                                                </option>
                                                                <option value="Required">Required</option>
                                                                <option value="Not Required">Not Required</option>

                                                            </select>
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

                                                <div class="row other_vechicles"style="display: none;" >
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

            // unit and department

            $('#is_outside_worker').change(function() {
                if ($(this).is(':checked')) {
                    $('.department').hide();
                    $('.unit').show();

                } else {
                    $('.department').show();
                    $('.unit').hide();
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

                        $('#is_reffered_mobile_no').val(data).prop('disable', true);
                    },
                    error: function(xhr) {
                        alert('Error fetching mobile number. Please try again.');
                    }
                });
            } else {

                $('#is_reffered_mobile_no').val('').prop('disable', true);
            }
        });
    </script>
@endpush
