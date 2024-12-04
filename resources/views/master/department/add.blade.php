@extends('admin.layouts.admin')
@section('title', 'Department Add')
@section('pageurl', admin_url('department/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Department Add') }}</h4> --}}

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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('department/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="departmentadd"
                                        action="{{ admin_url('department/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department ID</label>
                                                    <input type="text" name ="department_id" class="form-control"
                                                        placeholder="Department ID" value="{{ getsequence('department') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company_id" id="company_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companyList as $company)
                                                            <option value="{{ encryptId($company->id) }}">
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
                                                    <input type="text" name="department_name" id="department_name"
                                                        class="form-control" placeholder="Department Name">
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

        $(document).on('change', '#company_id', function() {
            var companyId = $(this).val();
            if (companyId) {
                $.ajax({
                    url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#location_id').empty().append('<option value="">Select Location</option>');
                        $.each(data, function(key, value) {
                            $('#location_id').append('<option value="' + value.id + '">' + value
                                .name + '</option>');
                        });
                        $('#location_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching locations. Please try again.');
                    }
                });
            } else {
                $('#location_id').empty().append('<option value="">Select Location</option>');
                $('#location_id').trigger('change.');
            }
        });

        $(document).on('change', '#location_id', function() {
            var locationId = $(this).val();
            if (locationId) {
                $.ajax({
                    url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#unit_id').empty().append('<option value="">Select Unit</option>');
                        $.each(data, function(key, value) {
                            $('#unit_id').append('<option value="' + value.id + '">' + value
                                .name + '</option>');
                        });
                        $('#unit_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching unit. Please try again.');
                    }
                });
            } else {
                $('#unit_id').empty().append('<option value="">Select Unit</option>');
                $('#unit_id').trigger('change.');
            }
        });


        $(function() {
            $('#departmentadd').validate({
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
                        pattern: /^[a-zA-Z0-9\s\-_'"(),&]*$/, 
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
                        pattern: "Only alphanumeric characters and -, _, ', \", (), ,, and & are allowed",
                        remote: "{{ __('Department Name should be unique') }}",
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
