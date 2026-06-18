@extends('admin.layouts.admin')
@section('title', 'Category Master Add')
@section('pageurl', admin_url('master/category/list'))
@push('style')
    <style>
        .card-header {
            position: relative;
        }

        .align-back-btc d-flex justify-content-end align-items-center {
            display: flex;
        }

        @media (max-width: 480px) {
            .align-back-btc d-flex justify-content-end align-items-center {
                width: 100%;
            }

            .align-back-btc d-flex justify-content-end align-items-center x-button-back,
            .align-back-btc d-flex justify-content-end align-items-center button {
                width: auto;
                max-width: 100%;
            }
        }
    </style>
@endpush
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

        </div>
        {{-- <ol class="breadcrumb">
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
        </ol> --}}
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            {{-- <div class="card-header">
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('master/category/list') }}"></x-button-back>
                                </div>
                            </div> --}}
                            <div class="card-header d-flex justify-content-end align-items-center">
                                <x-button-back href="{{ admin_url('master/category/list') }}"></x-button-back>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="add_page"
                                        action="{{ admin_url('master/category/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.category_id') }}</label>
                                                    <input type="text" name ="category_id" class="form-control"
                                                        placeholder="Enter the Category Id"
                                                        value="{{ getsequence('category') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.category_code') }}</label>
                                                    <input type="text" name="category_code" id="category_code"
                                                        class="form-control" placeholder="Enter the Category code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.category_name') }}</label>
                                                    <input type="text" name="category_name" class="form-control"
                                                        placeholder="Enter the Category">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('common.description') }}</label>
                                                    <textarea name="description" class="form-control" placeholder="Enter the description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit type="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
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
            $('#add_page').validate({
                rules: {
                    category_code: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/category/unique') }}',
                            type: 'post',
                            data: {
                                category_code: function() {
                                    return $('#category_code').val();
                                }
                            }
                        }
                    },
                    category_name: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/category/unique') }}',
                            type: 'post',
                            data: {
                                category_code: function() {
                                    return $('#category_code').val();
                                }
                            }
                        }
                    },

                },
                messages: {
                    category_name: {
                        required: "Category name is Required",
                        remote: "Category name is should be unique",

                    },
                    category_code: {
                        required: "Category Code is Required",
                        remote: "Category Code should be unique",

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
                        // console.log("Field: " + error.element.name + ", Error: " + error
                        //     .message);
                    });
                }
            });
        });
    </script>
@endpush
