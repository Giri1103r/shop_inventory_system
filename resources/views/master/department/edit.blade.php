@extends('admin.layouts.admin')
@section('title', 'Department Master Edit')
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('department/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="departmentedit"
                                        action="{{ admin_url('department/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($department->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department ID</label>
                                                    <input type="text" name ="department_id" class="form-control"
                                                        placeholder="Department ID" value="{{ $department->department_id }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
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
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department Name</label>
                                                    <input type="text" name="department_name" id = "department_name"
                                                        class="form-control" placeholder="Department Name"
                                                        value="{{ $department->department_name }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('department/list') }}"></x-button-cancel>
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

            var initialCompanyId = $('#company_id').val();
            var preselectedLocationId = "{{ encryptId($department->location_id) ?? '0' }}";
            var preselectedUnitId = "{{ encryptId($department->unit_id) ?? '0' }}";

            if (initialCompanyId) {
                fetchLocations(initialCompanyId, preselectedLocationId, function() {
                    var location_id = preselectedLocationId;
                    fetchUnits(location_id, preselectedUnitId);

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
                        maxlength: 70,
                        remote: {
                            url: '{{ admin_url('department/unique') }}',
                            type: 'post',
                            data: {

                                company_id: function() {
                                    return $('#company_id').val();
                                },
                                location_id: function() {
                                    return $('#location_id').val();
                                },
                                unit_id: function() {
                                    return $('#unit_id').val();
                                },
                                department_name: function() {
                                    return $('#department_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
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
                        maxlength: "Maximum Characters should not exceed 70",
                        remote: "{{ __('Department Name should be unique') }}"
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
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
        });
    </script>
@endpush
