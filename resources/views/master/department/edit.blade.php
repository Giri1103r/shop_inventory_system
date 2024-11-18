@extends('admin.layouts.admin')
@section('title', 'Department Edit')
@section('pageurl', admin_url('department/list'))


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
