@extends('admin.layouts.admin')
@section('title', 'Tax Master Edit')
@section('pageurl', admin_url('master/tax/list'))
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
                                <x-button-back href="{{ admin_url('master/tax/list') }}"></x-button-back>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="edit_page" action="{{ admin_url('master/tax/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($tax->id) }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Tax Id</label>
                                                    <input type="text" name ="tax_id" class="form-control"
                                                        placeholder="Enter the Tax Id" value="{{ $tax->tax_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Tax Name</label>
                                                    <input type="text" name="tax_name" id="tax_name" value="{{ $tax->tax_name }}"
                                                        class="form-control" placeholder="Enter the Tax Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">HSN Codes</label>
                                                    <input type="text" name="hsn_codes" id="hsn_codes" value="{{ $tax->hsn_codes }}"
                                                        class="form-control" placeholder="Enter the HSN Code">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Tax Percentage</label>
                                                    <input type="text" name="tax_percetage" id="tax_percetage" value="{{ $tax->tax_percetage }}"
                                                        class="form-control" placeholder="Enter the Tax Percentage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">CGST Percentage</label>
                                                    <input type="text" name="cgst_percentage" id="cgst_percentage" value="{{ $tax->cgst_percentage }}"
                                                        class="form-control" placeholder="Enter the CGST Percentage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">SGST Percentage</label>
                                                    <input type="text" name="sgst_percentage" id="sgst_percentage" value="{{ $tax->sgst_percentage }}"
                                                        class="form-control" placeholder="Enter the SGST Percentage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">IGST Percentage</label>
                                                    <input type="text" name="igst_percentage" id="igst_percentage"  value="{{ $tax->igst_percentage }}"
                                                        class="form-control" placeholder="Enter the IGST Percentage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">CESS Percentage</label>
                                                    <input type="text" name="cess_percentage" id="cess_percentage" value="{{ $tax->cess_percentage }}"
                                                        class="form-control" placeholder="Enter the CESS Percentage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Effective From</label>
                                                    <input type="text" name="effective_from" id="effective_from" value="{{ $tax->effective_from }}"
                                                        class="form-control datepickersearch" placeholder="Please Select the date">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Effective To</label>
                                                    <input type="text" name="effective_to" id="effective_to" value="{{ $tax->effective_to }}"
                                                        class="form-control enddatepickersearch" placeholder="Please Select the date">
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Description</label>
                                                    <textarea name="description" class="form-control" placeholder="Enter the Description">{{ $tax->description }}</textarea>
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
                    tax_name: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/tax/unique') }}',
                            type: 'post',
                            data: {
                                tax_name: function() {
                                    return $('#tax_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    hsn_codes: {
                        required: true,
                    },
                    tax_percetage: {
                        required: true,
                    },
                    cgst_percentage: {
                        required: true,
                    },
                    sgst_percentage: {
                        required: true,
                    },
                    igst_percentage: {
                        required: true,
                    },

                },
                messages: {
                    tax_name: {
                        required: "Tax Name is Required",
                        remote: "Tax Name is should be unique",

                    },

                    hsn_codes: {
                        required: "Tax Name is Required",
                    },
                    tax_percetage: {
                        required: "Tax Percentage is Required",
                    },
                    cgst_percentage: {
                        required: "CGST Percentage is Required",
                    },
                    sgst_percentage: {
                        required: "SGST Percentage is Required",
                    },
                    igst_percentage: {
                        required: "IGST Percentage is Required",
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
