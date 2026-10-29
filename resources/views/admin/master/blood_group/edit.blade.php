@extends('admin.layouts.admin')
@section('title', 'Blood Group Master Edit')
@section('pageurl', admin_url('master/blood-group/list'))


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
                                    <x-button-back href="{{ admin_url('master/blood-group/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="bloodgroupedit"
                                        action="{{ admin_url('master/blood-group/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($bloodgroup->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Blood Group ID</label>
                                                    <input type="text" name ="bg_code" class="form-control"
                                                        placeholder="Blood Group ID"
                                                        value="{{ $bloodgroup->bg_code }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Blood Group Name</label>
                                                    <input type="text" name="blood_group_name" id="blood_group_name"
                                                        class="form-control" placeholder="Blood Group Name"
                                                        value="{{ old('blood_group_name', $bloodgroup->blood_group_name) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('master/blood-group/list') }}"></x-button-cancel>
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
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });

        $(function() {
            $('#bloodgroupedit').validate({
                rules: {
                    blood_group_name: {
                        required: true,
                        maxlength: 10,
                        remote: {
                            url: '{{ admin_url('master/blood-group/unique') }}',
                            type: 'post',
                            data: {
                                blood_group_name: function() {
                                    return $('#blood_group_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },

                },
                messages: {
                    blood_group_name: {
                        required: "{{ __('Blood Group Name is Required') }}",
                        maxlength: "Maximum Characters should not exceed 10",
                        remote: "{{ __('Blood Group Name should be unique') }}"
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
