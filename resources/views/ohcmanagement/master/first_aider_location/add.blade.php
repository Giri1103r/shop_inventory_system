@extends('admin.layouts.admin')
@section('title', 'First Aider Location Add')
@section('pageurl', admin_url('ohc/first-aid-location/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/first-aid-location/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="FirstAiderLocationAdd"
                                        action="{{ admin_url('ohc/first-aid-location/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
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
                                                    @error('unit_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Department</label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>

                                                    </select>
                                                    @error('department_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <input type="text" name="location_id" id="location_id"
                                                        class="form-control" placeholder="Location Name">
                                                    @error('location_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Station Master</label>
                                                    <select name="station_master" id="station_master" class=" form-control"
                                                        style="width: 100%">
                                                        <option value="">Select the person</option>
                                                    </select>
                                                    @error('station_master')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Station Number</label>
                                                    <input type="text" name="station_number" id="station_number"
                                                        class="form-control" placeholder="Station Number">
                                                    @error('station_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">First Aid Box Number</label>
                                                    <input type="text" name="first_aid_box_no" id="first_aid_box_no"
                                                        class="form-control" placeholder="First Aid Box Number">
                                                    @error('first_aid_box_no')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('company/list') }}"></x-button-cancel>
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
        $('#station_master').select2({
            ajax: {
                url: '{{ admin_url('ohc/first-aid-location/employeename') }}',
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

        $(document).on('change', '#unit_id', function() {
            var unitId = $(this).val();
            if (unitId) {
                $.ajax({
                    url: "{{ admin_url('department/ajax-list') }}/" + unitId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#department_id').empty().append(
                            '<option value="">Select Department</option>');
                        $.each(data, function(key, value) {
                            $('#department_id').append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                        $('#department_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching department. Please try again.');
                    }
                });
            } else {
                $('#department_id').empty().append('<option value="">Select Department</option>');
                $('#department_id').trigger('change.');
            }
        });
        $(function() {
            $('#FirstAiderLocationAdd').validate({
                rules: {
                    location_id: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        pattern: /^(?=.*[a-zA-Z0-9])[a-zA-Z0-9\s\-_'"()]*$/,
                        remote: {
                            url: '{{ admin_url('ohc/first-aid-location/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                location_id: function() {
                                    return $('#location_id').val();
                                },
                            },
                        },
                    },
                    department_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    station_master: {
                        required: true,
                    },
                    first_aid_box_no: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        pattern: /^(?=.*[a-zA-Z0-9])[a-zA-Z0-9\s\-_'"()]*$/,
                        remote: {
                            url: '{{ admin_url('ohc/first-aid-location/first-aid-box') }}',
                            type: 'POST',
                            type: 'post',
                            data: {
                                first_aid_box_no: function() {
                                    return $('#first_aid_box_no').val();
                                },

                                unit_id: function() {
                                    return $('#unit_id').val();
                                },
                                department_id: function() {
                                    return $('#department_id').val();
                                },
                            }
                        }
                    },

                    station_number: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        pattern: /^(?=.*[a-zA-Z0-9])[a-zA-Z0-9\s\-_'"()]*$/,
                        remote: {
                            url: '{{ admin_url('ohc/first-aid-location/station-number-unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                station_number: function() {
                                    return $('#station_number').val();
                                },
                            },
                        },
                    },
                },
                messages: {
                    location_id: {
                        required: "Location Name is required.",
                        minlength: "Location Name must be at least 3 characters long.",
                        maxlength: "Location Name must not exceed 30 characters.",
                        pattern: "Location Name contains invalid characters.",
                        remote: "Location Name Must Be Unique",
                    },
                    first_aid_box_no: {
                        required: "First Aid Box Number is required.",
                        minlength: "First Aid Box Number must be at least 3 characters long.",
                        maxlength: "First Aid Box Number must not exceed 30 characters.",
                        pattern: "First Aid Box Number contains invalid characters.",
                        remote: "First Aid Box Number Must Be Unique according to unit and department",
                    },
                    department_id: {
                        required: "Department is required.",
                    },
                    unit_id: {
                        required: "Unit is required.",
                    },
                    station_master: {
                        required: "Station master is required.",

                    },
                    station_number: {
                        required: "Station number is required.",
                        minlength: "Station number must be at least 3 characters long.",
                        maxlength: "Station number must not exceed 20 characters.",
                        pattern: "Station number contains invalid characters.",
                        remote: "Station Number Must Be Unique",
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
                },
            });
        });
    </script>
@endpush
