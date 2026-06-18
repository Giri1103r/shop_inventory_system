@extends('admin.layouts.admin')
@section('title', 'Category Master Edit')
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

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header d-flex justify-content-end align-items-center">
                                <x-button-back href="{{ admin_url('master/category/list') }}"></x-button-back>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="edit_page"
                                        action="{{ admin_url('master/category/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{ encryptId($category->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('common.category_id') }}</label>
                                                    <input type="text" name ="category_id" class="form-control"
                                                        placeholder="Enter the Category Id"
                                                        value="{{ $category->category_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.category_code') }}</label>
                                                    <input type="text" name="category_code"
                                                        value="{{ $category->category_code }}" id="category_code"
                                                        class="form-control" placeholder="Enter the Category code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('common.category_name') }}</label>
                                                    <input type="text" name="category_name"
                                                        value="{{ $category->category_name }}" class="form-control"
                                                        placeholder="Enter the Category">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('common.description') }}</label>
                                                    <textarea name="description" class="form-control" placeholder="Enter the description">{{ $category->description }}</textarea>
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
            $('#edit_page').validate({
                rules: {
                    category_code: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/category/unique') }}',
                            type: 'post',
                            data: {
                                category_code: function() {
                                    return $('#category_code').val();
                                },
                                id: function() {
                                    return $('#id').val();
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
                                },
                                id: function() {
                                    return $('#id').val();
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
