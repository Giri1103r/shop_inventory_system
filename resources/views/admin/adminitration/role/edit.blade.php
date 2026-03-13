@extends('admin.layouts.admin')
@section('title', 'Role')
@section('pageurl', admin_url('role/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 style="color:#0D173F">Role</h2>

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
                                <h4 class="card-title">{{ __('administration.role_add') }}</h4>
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('administration/role/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="locationtypeedit"
                                        action="{{ admin_url('administration/role/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($role->id) }}">
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label
                                                    class="form-label required">{{ __('administration.role_id') }}</label>
                                                <input type="text" readonly value="{{ $role->role_id }}"
                                                    class="form-control" placeholder="">
                                            </div>
                                            <div class="mb-3 col-md-6 form-input">
                                                <label
                                                    class="form-label required">{{ __('administration.role_name') }}</label>
                                                <input type="text" name="role_name" value="{{ $role->role_name }}"
                                                    id="role_name" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('administration/role/list') }}"></x-button-cancel>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(function() {
            $('#locationtypeedit').validate({
                rules: {

                    location_type_name: {
                        required: true,
                        minlength: 3,

                        remote: {
                            url: '{{ admin_url('location_type/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#location_type_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                },
                messages: {
                    location_type_name: {
                        required: "{{ __('administration.validate_location_type_name_require') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "{{ __('common.validate_max_length') }}",
                        remote: "{{ __('administration.validate_location_type_name_unique') }}"
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
            });
        });
    </script>
@endpush
