@extends('admin.layouts.admin')
@section('title', 'Incident Type Add')
@section('pageurl', admin_url('ohc/certified-first-aider/list'))


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

                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('incident/type-master/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="incidentTypeAdd"
                                        action="{{ admin_url('incident/type-master/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type ID</label>
                                                    <input type="text" name="incident_type_id" id="incident_type_id"
                                                        class="form-control"
                                                        placeholder="Enter the Certified First Aider Name"
                                                        value = "{{ getsequence('inctype') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type Name</label>
                                                    <input type="text" name="incident_type_name" id="incident_type_name"
                                                        class="form-control" placeholder="Incident Type Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type Short Name</label>
                                                    <input type="text" name="short_name" id="short_name"
                                                        class="form-control" placeholder="Incident Type Short Name">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('incident/type-master/list') }}"></x-button-cancel>
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
        $(function() {
            $('#incidentTypeAdd').validate({
                rules: {
                    incident_type_name: {
                        required: true,
                        minlength: 2,
                        maxlength: 100,
                        pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                        remote: {
                            url: '{{ admin_url('incident/type-master/unique') }}',
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                incident_type_name: function() {
                                    return $('#incident_type_name').val();
                                },
                            },
                        },
                    },
                    short_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 10,
                        pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,
                    },
                },
                messages: {
                    incident_type_name: {
                        required: "Incident Type Name is required.",
                        minlength: "Incident Short Name must be exactly 2 characters.",
                        maxlength: "Incident Short Name must be exactly 100 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                        remote: "Incident Type Name should be unique",
                    },
                    short_name: {
                        required: "Incident Short Name is required.",
                        minlength: "Incident Short Name must be exactly 3 characters.",
                        maxlength: "Incident Short Name must be exactly 10 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                },

                errorElement: 'span',
                errorPlacement: function(error, element) {
                    // Add the 'invalid-feedback' class to the error element
                    error.addClass('invalid-feedback');
                    // Append the error message to the closest '.form-input' container
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    // Add the 'is-invalid' class to the invalid input
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    // Remove the 'is-invalid' class when the input becomes valid
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    // Submit the form when all validations pass
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    // Handle invalid form submissions
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
