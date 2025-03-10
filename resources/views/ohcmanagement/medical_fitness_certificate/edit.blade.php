@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate Edit')
@section('pageurl', admin_url('ohc/medical-fitness/list'))
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
                                    <x-button-back href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="MedicineRequisitionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medical-fitness/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($medicalfitness->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 employee-id mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Employee code</label>
                                                    <select name="emp_id" class="form-control " id="emp_id"
                                                        style="width: 100%">
                                                        <option value="">Select the Employee ID</option>
                                                        @if (isset($medicalfitness->emp_id) && isset($medicalfitness->emp_id))
                                                            <option value="{{ $medicalfitness->emp_id }}" selected>
                                                                {{ $medicalfitness->emp_id }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name" id="emp_name"
                                                        class="form-control" value="{{$medicalfitness->emp_name}}" placeholder="Employee Name" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Date
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date" id="date"
                                                            value="{{ displaydateformat($medicalfitness->date) }}"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medical Fitness Certificate
                                                        Upload</label>
                                                    <input type="file" name="file" id="file"
                                                        class="form-control">
                                                    <small>Allowed file types: png, jpeg , jpg</small>
                                                    @if (isset($medicalfitness) && $medicalfitness->file)
                                                        <p>
                                                            <a href="{{ asset('public/' . $medicalfitness->file) }}"
                                                                target="_blank" class="d-block mt-2">
                                                                <i class="fa-solid fa-eye text-danger"></i> View
                                                            </a>
                                                        </p>
                                                        <input type="hidden" name="existing_pre_image"
                                                            value="{{ $medicalfitness->file }}">
                                                    @else
                                                        <p>No file is uploaded</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Remarks</label>
                                                    <textarea name="remarks" id="remarks" class="form-control " cols="30" rows="5">{{ $medicalfitness->remarks }}</textarea>
                                                </div>
                                            </div>

                                        </div>



                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-cancel>
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
            var fromDatepicker = flatpickr("#date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });

        $(document).ready(function() {
            // Initialize select2 for Employee ID
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

            // Function to fetch and set Employee Name based on Employee ID
            function fetchEmployeeName(empId) {
                if (empId) {
                    $.ajax({
                        url: "{{ admin_url('ohc/employee-cum-patient/employeename') }}",
                        type: 'GET',
                        data: {
                            empId: empId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.employee) {
                                $('#emp_name').val(response.employee.emp_name).prop('readonly', true);
                            } else {
                                $('#emp_name').val('').prop('readonly', true);
                            }
                        },
                        error: function(xhr) {
                            alert('Error fetching employee name. Please try again.');
                        }
                    });
                } else {
                    $('#emp_name').val('').prop('readonly', true);
                }
            }

            // Handle Employee ID change event
            $('#emp_id').on('change', function() {
                var empId = $(this).val();
                fetchEmployeeName(empId);
            });

            // Set preselected values if available
            // var empId = '{{ $medicalfitness->emp_id ?? '' }}';
            // var empName = '{{ $medicalfitness->emp_name ?? '' }}';

            // if (empId && empName) {
            //     var newOption = new Option(empName, empId, true, true);
            //     $('#emp_id').append(newOption).trigger('change');
            //     $('#emp_name').val(empName).prop('readonly', true);
            // }
        });

        $(function() {

            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#medicalfitnessform').validate({
                rules: {
                    emp_id: {
                        required: true,
                    },
                    emp_name: {
                        required: true,
                    },

                    date: {
                        required: true,
                    },
                    file: {
                        required: true,
                    },
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,

                    },


                },
                messages: {
                    emp_id: {
                        required: "Please select the Employee Code.",
                    },
                    emp_name: {
                        required: "Please select the Employee Name.",
                    },

                    date: {
                        required: "Please select the date.",
                    },
                    file: {
                        required: 'File is required',
                    },
                    remarks: {
                        required: 'Remarks is required',
                        minlength: 3,
                        maxlength: 600,
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
