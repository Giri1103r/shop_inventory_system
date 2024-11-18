@extends('admin.layouts.admin')
@section('title', 'Location Type Add')
@section('pageurl', admin_url('location_type/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 style="color:#0D173F">{{ __('administration.role') }}</h2>

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
                                fill="#aaa9ff"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_2') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_4') }}</a></li>
           
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
                                <h4 class="card-title">{{ __('administration.role_add') }}</h4>
                                <div>
                                    <x-button-back href="{{ admin_url('administration/role/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">
                                    <form method="POST" id="locationtypeedit"
                                        action="{{ admin_url('administration/role/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{ encryptId($role->id) }}">
                                        <div class="row">
                                            <div class="mb-3 col-md-6 form-input">
                                                <label
                                                    class="form-label required">{{ __('administration.role_id') }}</label>
                                                <input type="text" readonly value="{{ $role->role_id }}"
                                                    class="form-control" placeholder="">
                                            </div>
                                            <div class="mb-3 col-md-6 form-input">
                                                <label
                                                    class="form-label required">{{ __('administration.role_name')}}</label>
                                                <input type="text" name="role_name" value="{{ $role->role_name }}" id="role_name" class="form-control"
                                                    placeholder="">
                                            </div>
                                        </div>
                                        <hr>

                                        <div class="">
                                            <x-button-submit></x-button-submit>
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
    </div>

@stop

@push('script')
    <script type="text/javascript">
        $(function() {
            $('#locationtypeedit').validate({
                rules: {

                    location_type_name: {
                        required: true,
                        minlength: 3,

                        remote: {
                            url: '{{ admin_url("location_type/unique") }}',
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
