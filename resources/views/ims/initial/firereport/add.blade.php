@extends('admin.layouts.admin')
@section('title', 'Initial Fire Incident Add')
@section('pageurl', admin_url('incident/fire-incident/list'))


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
                                    <x-button-back href="{{ admin_url('incident/fire-incident/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="incidentAdd"
                                        action="{{ admin_url('incident/fire-incident/add/submit') }}"  enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Sr. No</label>
                                                    <input type="text" name="sr_no" id="sr_no" class="form-control"
                                                        placeholder="" value = "{{ getsequence('fireincident') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date and Time</label>
                                                    <input type="text" name="incident_date_time" id="incident_date_time"
                                                        class="form-control">
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
                                                    <label class="form-label require">Shift</label>
                                                    <input type="text" name="shift" id="shift"
                                                        class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                        @foreach ($locationList as $location)
                                                            <option value="{{ encryptId($location->id) }}">
                                                                {{ $location->location_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Exact Location</label>
                                                    <input type="text" name="exact_location" id="exact_location"
                                                        class="form-control" placeholder="Exact Location">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">IIR Type</label>
                                                    @foreach ($incTypeList as $incType)
                                                        <div class="form-check">
                                                            <input type="radio" name="iir_type"
                                                                id="iir_type_{{ $incType->id }}" class="form-check-input"
                                                                value="{{ $incType->id }}">
                                                            <label class="form-check-label"
                                                                for="iir_type_{{ $incType->id }}">{{ $incType->incident_type_name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Incident Reported By</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Name</label>
                                                    <div class="col-sm-6" style="width: 100%">
                                                        <select name="reported_name" id="reported_name" style="width: 100%"
                                                            class="form-control reported_name">
                                                            <option value="">Select Name</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation</label>
                                                    <input type="text" name="designation"
                                                        class="form-control designation" placeholder="Designation">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department" class="form-control department">
                                                        <option value="">Select Department</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Employee Code</label>
                                                    <input type="text" name="employee_code"
                                                        class="form-control employee_code" placeholder="Employee Code"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time of reporting</label>
                                                    <input type="text" name="time_of_reporting"
                                                        id = "time_of_reporting" class="form-control time_of_reporting"
                                                        placeholder="Time of reporting">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Reporting Media</label>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_phone" class="form-check-input"
                                                            value="1">
                                                        <label class="form-check-label"
                                                            for="reporting_media_phone">Phone</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_walkietalkie" class="form-check-input"
                                                            value="2">
                                                        <label class="form-check-label"
                                                            for="reporting_media_walkietalkie">Walkie Talkie</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_extension" class="form-check-input"
                                                            value="3">
                                                        <label class="form-check-label"
                                                            for="reporting_media_extension">Extension</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" name="reporting_media[]"
                                                            id="reporting_media_others" class="form-check-input"
                                                            value="4">
                                                        <label class="form-check-label"
                                                            for="reporting_media_others">Others</label>
                                                    </div>
                                                    <div class="text-danger "></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4" id = "reporting_media_othersdiv" style="display: none">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Others</label>
                                                    <input type="text" name="reporting_media_othersdesc"
                                                        id = "reporting_media_othersdesc"
                                                        class="form-control reporting_media_othersdesc" placeholder="">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Brief Description</label>
                                                    <textarea type="text" name="brief_description" id = "brief_description" class="form-control brief_description"
                                                        placeholder=""></textarea>
                                                </div>
                                            </div>
                                            <div id="file-upload-container" class="row mt-3">
                                                <div class="col-12 mb-3">
                                                    <button class="btn btn-primary addmorebutton" type="button"
                                                        id="dynamic-add-more">
                                                        Add
                                                    </button>
                                                </div>

                                                <div class="col-md-4 mb-3 file-upload-block" id="file-upload-0">
                                                    <label for="evidence_0" class="form-label require">Evidence</label>
                                                    <input type="file"
                                                        class="form-control validate-file-accept validate-file-required"
                                                        name="evidence[0][]" id="evidence_0" multiple>
                                                    <div class="text-danger"></div>
                                                    <small>Allowed file types: png, jpeg , jpg, pdf, doc, mp4</small>
                                                    <div class="preview-container mt-2 d-flex flex-wrap gap-2"
                                                        id="preview-container-0"></div>
                                                </div>
                                            </div>


                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('incident/fire-incident/list') }}"></x-button-cancel>
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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        flatpickr("#incident_date_time", {
            enableTime: true,
            dateFormat: "d-m-Y H:i",
            time_24hr: true,
            maxDate: new Date()
        });

        flatpickr("#time_of_reporting", {
            enableTime: true,
            noCalendar: true, // Disables the date selection
            dateFormat: "H:i", // Format to show only hours and minutes
            time_24hr: true // Uses 24-hour format
        });


        const maxUploads = 5;

        $('#dynamic-add-more').on('click', function() {
            let currentFileUploads = $('.file-upload-block').length;

            if (currentFileUploads >= maxUploads) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sorry!',
                    text: 'Maximum 5 records only.',
                });
                return;
            }

            // Create the new file upload block
            let newFileUploadBlock = `
                <div class="col-md-4 mb-3 file-upload-block" id="file-upload-${currentFileUploads}">
                    <label for="evidence_${currentFileUploads}" class="form-label require">Evidence</label>
                    <input type="file" class="form-control validate-file-accept validate-file-required"
                        name="evidence[${currentFileUploads}][]" id="evidence_${currentFileUploads}" multiple>
                    <div class="text-danger"></div>
                    <small>Allowed file types: png, jpeg , jpg, pdf, doc, mp4</small>
                    <button type="button" class="btn btn-danger btn-sm remove-upload-block">
                        <i class="fas fa-trash"></i>
                    </button>
                    <div class="preview-container mt-2 d-flex flex-wrap gap-2" id="preview-container-${currentFileUploads}"></div>
                </div>
            `;

            // Append new block
            $('#file-upload-container').append(newFileUploadBlock);

            // Revalidate the new file input after it's added
            $('#evidence_' + currentFileUploads).rules("add", {
                required: true,
                extension: "png|jpeg|jpg|pdf|doc|mp4",
                messages: {
                    required: "This field is required.",
                    extension: "Allowed file types: png, jpeg, jpg, pdf, doc, mp4",
                }
            });
        });

        // Handling file input validation for dynamic removal of blocks (if applicable)
        $(document).on('click', '.remove-upload-block', function() {
            $(this).closest('.file-upload-block').remove();
        });



        $(document).on('change', 'input[type="file"]', function(event) {
            let input = $(this);
            let fileInputId = input.attr('id').split('_')[2];
            let previewContainer = $('#preview-container-' + fileInputId);

            previewContainer.html("");

            let files = event.target.files;
            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    if (file.type.startsWith("image/")) {
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            let img = $("<img>").attr("src", e.target.result)
                                .addClass("img-thumbnail")
                                .css({
                                    width: "100px",
                                    height: "100px",
                                    objectFit: "cover",
                                    marginRight: "5px"
                                });

                            previewContainer.append(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });


        $('#reporting_media_others').on('change', function() {
            if ($(this).is(':checked')) {
                $('#reporting_media_othersdiv').show();
            } else {
                $('#reporting_media_othersdiv').hide();
            }
        });
        $('.reported_name').select2({
            ajax: {
                url: "{{ admin_url('incident/fire-incident/employeename') }}",
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
            minimumInputLength: 3,
            dropdownCssClass: 'form-control',
            selectionCssClass: 'form-control'
        });


        $(document).on("change", ".reported_name", function() {
            var emp_id = $(this).val();
            var currentRow = $(this).closest(".row");

            if (emp_id) {
                $.ajax({
                    url: "{{ url('incident/fire-incident/fetchEmployeeDetails') }}/" + emp_id,
                    type: "GET",
                    success: function(data) {
                        if (data.employee) {
                            currentRow.find('.employee_code').val(data.employee.emp_id);
                            currentRow.find('.designation').val(data.employee.designation);

                            var departmentDropdown = currentRow.find('.department');
                            departmentDropdown.empty();
                            departmentDropdown.append('<option value="">Select Department</option>');

                            if (data.departments && data.departments.length > 0) {
                                data.departments.forEach(function(department) {
                                    var selected = data.employee.department == department.id ?
                                        "selected" : "";
                                    departmentDropdown.append(
                                        `<option value="${department.id}" ${selected}>${department.department_name}</option>`
                                    );
                                });
                            } else {
                                departmentDropdown.append(
                                    '<option value="">No departments available</option>');
                            }
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "Employee data could not be fetched.",
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while fetching employee details.",
                        });
                    }
                });
            } else {
                currentRow.find('.employee_code').val("");
                currentRow.find('.designation').val("");
                var departmentDropdown = currentRow.find('.department');
                departmentDropdown.empty();
                departmentDropdown.append('<option value="">Select Department</option>');
            }
        });

        $(function() {
            $('#incidentAdd').validate({
                rules: {
                    incident_date_time: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    shift: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    location_id: {
                        required: true,
                    },
                    exact_location: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    iir_type: {
                        required: true,
                    },
                    reported_name: {
                        required: true,
                    },
                    designation: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    employee_code: {
                        required: true,
                    },
                    time_of_reporting: {
                        required: true,
                    },
                    'reporting_media[]': {
                        required: true,
                        minlength: 1,
                    },
                    brief_description: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    'evidence[]': {
                        required: true,
                        // extension: "png|jpeg|jpg|pdf|doc|mp4"
                        imageFormat: true
                    },
                },
                messages: {
                    incident_date_time: {
                        required: "Date and Time is required.",
                    },
                    unit_id: {
                        required: "Unit is required.",
                    },
                    shift: {
                        required: "Shift is required.",
                        minlength: "Brief Description Required must be exactly 2 characters.",
                        maxlength: "Brief Description Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    location_id: {
                        required: "Location is required.",
                    },
                    exact_location: {
                        required: "Exact Location is required.",
                        minlength: "Brief Description Required must be exactly 2 characters.",
                        maxlength: "Brief Description Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    iir_type: {
                        required: "IIR Type is required.",
                    },
                    reported_name: {
                        required: "Name is required.",
                    },
                    designation: {
                        required: "Designation is required.",
                    },
                    department: {
                        required: "Department is required.",
                    },
                    employee_code: {
                        required: "Employee Code is required.",
                    },
                    time_of_reporting: {
                        required: "Time of reporting is required.",
                    },
                    'reporting_media[]': {
                        required: "At least one Reporting Media is required.",
                        minlength: "At least one Reporting Media must be selected.",
                    },
                    brief_description: {
                        required: "Brief Description is required.",
                        minlength: "Brief Description Required must be exactly 2 characters.",
                        maxlength: "Brief Description Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    'evidence[]': {
                        required: "Evidence is required",
                        imageFormat: "Invalid file type"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');

                    // Handle error placement for checkboxes
                    if (element.attr("name") === "reporting_media[]") {
                        element.closest('.form-input').find('.text-danger').html(error);
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
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        console.log(`There are ${errors} validation errors.`);
                        validator.errorList.forEach(function(error) {
                            console.log(
                                `Field: ${error.element.name}, Error: ${error.message}`);
                        });
                    }
                },
            });

        });
    </script>
@endpush
