@extends('admin.layouts.admin')
@section('title', 'Department')
@section('pageurl', admin_url('department/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h4 class="text-black">{{ __('Department Edit') }}</h4>

        </div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#009999"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_4') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_8') }}</a></li>
        </ol>
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
                                <div>
                                    <x-button-back href="{{ admin_url('department/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="departmentedit" action="{{ admin_url('department/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($department->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department ID</label>
                                                    <input type="text" name ="department_id" class="form-control"
                                                        placeholder="Department ID" value="{{ $department->department_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company_id" id="company_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companyList as $company)
                                                            <option @if ($department->company_id == $company->id) selected @endif
                                                                value="{{ encryptId($company->id) }}">
                                                                {{ $company->company_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <input type="text" name="department_name" class="form-control"
                                                        placeholder="Department Name" value="{{ $department->department_name }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-cancel></x-button-cancel>
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
            let selectedCompanyId = "{{ encryptId($department->company_id) }}";
            let selectedLocationId = "{{ encryptId($department->location_id) }}";
            let selectedUnitId = "{{ encryptId($department->unit_id) }}";

            if (selectedCompanyId) {
                populateLocations(selectedCompanyId, selectedLocationId);
            }

            if (selectedLocationId) {
                populateUnits(selectedLocationId, selectedUnitId);
            }

            $(document).on('change', '#company_id', function() {
                let companyId = $(this).val();
                $('#location_id').empty().append('<option value="">Select Location</option>');
                $('#unit_id').empty().append('<option value="">Select Unit</option>');

                if (companyId) {
                    populateLocations(companyId);
                }
            });

            $(document).on('change', '#location_id', function() {
                let locationId = $(this).val();
                $('#unit_id').empty().append('<option value="">Select Unit</option>');

                if (locationId) {
                    populateUnits(locationId);
                }
            });

            // Function to populate Location dropdown
            function populateLocations(companyId, selectedLocationId = '') {
                $.ajax({
                    url: "{{ admin_url('location/ajaxlist') }}",
                    type: 'GET',
                    data: {
                        company_id: companyId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#location_id').empty().append('<option value="">Select Location</option>');
                        $.each(data, function(key, value) {
                            let selected = (value.id === selectedLocationId) ? 'selected' : '';
                            $('#location_id').append('<option value="' + value.id + '" ' +
                                selected + '>' + value.name + '</option>');
                        });

                        // Trigger change event if editing
                        if (selectedLocationId) {
                            $('#location_id').trigger('change');
                        }
                    }
                });
            }

            // Function to populate Unit dropdown
            function populateUnits(locationId, selectedUnitId = '') {
                $.ajax({
                    url: "{{ admin_url('unit/ajaxlist') }}",
                    type: 'GET',
                    data: {
                        location_id: locationId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#unit_id').empty().append('<option value="">Select Unit</option>');
                        $.each(data, function(key, value) {
                            let selected = (value.id === selectedUnitId) ? 'selected' : '';
                            $('#unit_id').append('<option value="' + value.id + '" ' +
                                selected + '>' + value.name + '</option>');
                        });
                    }
                });
            }
        });
        $(function() {
            $('#departmentedit').validate({
                rules: {
                    company_id: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    department_name: {
                        required: true,
                        minlength: 3,
                    },

                },
                messages: {
                    company_id: {
                        required: "{{ __('Company Name is Required') }}",
                    },
                    location_id: {
                        required: "{{ __('Location Name is Required') }}",
                    },
                    unit_id: {
                        required: "{{ __('Unit Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                    },
                    department_name: {
                        required: "{{ __('Department  Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
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
