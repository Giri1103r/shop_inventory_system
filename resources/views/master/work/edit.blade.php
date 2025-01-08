@extends('admin.layouts.admin')
@section('title', 'Worker Master Edit')
@section('pageurl', admin_url('work/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Department Edit') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('work/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="workedit" action="{{ admin_url('work/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($work->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Worker ID</label>
                                                    <input type="text" name ="emp_id" class="form-control"
                                                        placeholder="Worker ID" value="{{ $work->emp_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Worker Name</label>
                                                    <input type="text" name ="emp_name" class="form-control"
                                                        placeholder="Worker Name" value="{{ $work->emp_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Gender</label>
                                                    <select name="gender" id="gender" class="form-control single-select"
                                                        style="width: 100%;">
                                                        <option value="">Select Gender</option>
                                                        <option value="M"
                                                            {{ $work->gender === 'M' ? 'selected' : '' }}>Male</option>
                                                        <option value="F"
                                                            {{ $work->gender === 'F' ? 'selected' : '' }}>Female</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Nationality</label>
                                                    <input type="text" name ="nationality" class="form-control"
                                                        placeholder="Nationality" value="{{ $work->nationality }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Biometric Code</label>
                                                    <input type="text" name ="biometric_code" class="form-control"
                                                        placeholder="Biometric Code" value="{{ $work->biometric_code }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">DOI</label>
                                                    <input type="text" name="doi" id="doi-datetime-datepicker"
                                                        class="form-control" placeholder="DOI"
                                                        value="{{ $work->doi ? date('Y-m-d', strtotime($work->doi)) : '' }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Exit Date</label>
                                                    <input type="text" name ="exit_date"
                                                        id="exit-date-datetime-datepicker" class="form-control"
                                                        placeholder="Exit Date"
                                                        value="{{ $work->exit_date ? Displaydatetimeformat($work->exit_date) : '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Mobile No</label>
                                                    <input type="text" name ="mobile_no" class="form-control"
                                                        placeholder="Mobile No" value="{{ $work->mobile_no }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company" id="company_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companyList as $company)
                                                            <option @if ($work->company == $company->id) selected @endif
                                                                value="{{ encryptId($company->id) }}">
                                                                {{ $company->company_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <select name="location" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <select name="department" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department Name</option>

                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">SubDepartment Name</label>
                                                    <input type="text" name ="subdepartment" class="form-control"
                                                        placeholder="SubDepartment Name"
                                                        value="{{ $work->subdepartment }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Designation </label>
                                                    <input type="text" name ="designation" class="form-control"
                                                        placeholder="Designation " value="{{ $work->designation }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">WFEmptype </label>
                                                    <input type="text" name ="wfemptype" class="form-control"
                                                        placeholder="WFEmptype " value="{{ $work->wfemptype }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Skill </label>
                                                    <input type="text" name ="skill" class="form-control"
                                                        placeholder="Skill " value="{{ $work->skill }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('work/list') }}"></x-button-cancel>
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
        $(document).ready(function() {


            flatpickr("#doi-datetime-datepicker", {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true,
                minuteIncrement: 5,
                minDate: "01-01-1995",
                clickOpens: true,
                disableMobile: true,
                allowInput: false,
            });
            flatpickr("#exit-date-datetime-datepicker", {
                enableTime: true,
                dateFormat: "d-m-Y H:i",
                time_24hr: true,
                minuteIncrement: 5,
                minDate: "01-01-1995",
                clickOpens: true,
                disableMobile: true,
                allowInput: false,
            });
            var initialCompanyId = $('#company_id').val();
            var preselectedLocationId = "{{ encryptId($work->location) ?? '0' }}";
            var preselectedUnitId = "{{ encryptId($work->unit) ?? '0' }}";
            var preselectedDepartmentId = "{{ encryptId($work->department) ?? '0' }}";

            if (initialCompanyId) {
                fetchLocations(initialCompanyId, preselectedLocationId, function() {
                    var location_id = preselectedLocationId;
                    fetchUnits(location_id, preselectedUnitId, function() {
                        var unit_id = preselectedUnitId;
                        fetchDepartments(unit_id, preselectedDepartmentId);
                    });
                });
            }

            $('#company_id').on('change', function() {
                var company_id = $(this).val();
                fetchLocations(company_id, preselectedLocationId, function() {
                    $('#location_id').trigger('change');
                });
            });

            $('#location_id').on('change', function() {
                var location_id = $(this).val();
                fetchUnits(location_id, preselectedUnitId, function() {
                    $('#unit_id').trigger('change');
                });
            });

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
            });

            function fetchLocations(company_id, preselectedLocationId, callback) {
                if (company_id) {
                    $.ajax({
                        url: "{{ admin_url('location/ajax-list/') }}" + company_id + '/' +
                            preselectedLocationId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#location_id').empty().append(
                                '<option value="">Select Location</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedLocationId) ?
                                    'selected' : '';
                                $('#location_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#location_id').empty().append('<option value="">Select Location</option>');
                }
            }

            function fetchUnits(location_id, preselectedUnitId, callback) {
                if (location_id) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list/') }}" + location_id + '/' + preselectedUnitId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append('<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedUnitId) ? 'selected' :
                                    '';
                                $('#unit_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                }
            }

            function fetchDepartments(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }
        });


        $(function() {
            $('#workedit').validate({
                rules: {
                    emp_name: {
                        required: true,
                    },
                    nationality: {
                        required: true,
                    },
                    biometric_code: {
                        required: true,
                    },
                    doi: {
                        required: true,
                    },
                    mobile_no: {
                        required: true,
                    },
                    company: {
                        required: true,
                    },
                    unit: {
                        required: true,
                    },
                    department: {
                        required: true,
                    },
                    subdepartment: {
                        required: true,
                    },
                    designation: {
                        required: true,
                    },
                    wfemptype: {
                        required: true,
                    },
                    skill: {
                        required: true,
                    },

                },
                messages: {
                    emp_name: {
                        required: "{{ __('Worker Name is Required') }}",
                    },
                    nationality: {
                        required: "{{ __('Nationality is Required') }}",
                    },
                    biometric_code: {
                        required: "{{ __('Biometric Code is Required') }}",
                    },
                    doi: {
                        required: "{{ __('DOI is Required') }}",
                    },
                    mobile_no: {
                        required: "{{ __('Mobile No is Required') }}",
                    },
                    company: {
                        required: "{{ __('Company  Name is Required') }}",
                    },
                    unit: {
                        required: "{{ __('Unit  Name is Required') }}",
                    },
                    department: {
                        required: "{{ __('Department  Name is Required') }}",
                    },
                    subdepartment: {
                        required: "{{ __('SubDepartment  Name is Required') }}",
                    },
                    designation: {
                        required: "{{ __('Designation is Required') }}",
                    },
                    wfemptype: {
                        required: "{{ __('WFEmptype is Required') }}",
                    },
                    skill: {
                        required: "{{ __('Skil is Required') }}",
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
                    console.log('test');
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
        });
    </script>
@endpush
