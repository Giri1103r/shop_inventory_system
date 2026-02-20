@extends('admin.layouts.admin')
@section('title', 'Incident Type Edit')
@section('pageurl', admin_url('ohc/vendor/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('ohc/vendor Edit') }}</h4> --}}

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
                                    <form method="POST" id="vendoredit"
                                        action="{{ admin_url('incident/type-master/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($incident_type->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2 ">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type ID</label>
                                                    <input type="text" name ="incident_type_id" id="incident_type_id"
                                                        class="form-control" placeholder="Incident Type ID"
                                                        value="{{ $incident_type->incident_type_id }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type Name</label>
                                                    <input type="text" name="incident_type_name" id="incident_type_name"
                                                        class="form-control" placeholder="Incident Type Name"
                                                        value="{{ $incident_type->incident_type_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Incident Type Short Name</label>
                                                    <input type="text" name="short_name" id="short_name"
                                                        class="form-control" placeholder="Incident Type Short Name"
                                                        value="{{ $incident_type->short_name }}">
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
            $('#vendoredit').validate({
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
                                incident_type_name: function() {
                                    return $('#incident_type_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    short_name: {
                        required: true,
                        minlength: 2,
                        maxlength: 10,
                        pattern: /^[a-zA-Z0-9\-_'"()\s]+$/,

                    },
                },
                messages: {
                    incident_type_name: {
                        required: "Incident Type Name is required.",
                        minlength: " Minimum character should not less than 2",
                        maxlength: "Maximum Characters should not exceed 100",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                        remote: "{{ __('Incident Type Name should be unique') }}"
                    },
                    short_name: {
                        required: "Incident Short Name is required.",
                        minlength: "Minimum character should not less than 2",
                        maxlength: "Maximum Characters should not exceed 10",

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
