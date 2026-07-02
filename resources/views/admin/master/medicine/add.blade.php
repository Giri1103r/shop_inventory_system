@extends('admin.layouts.admin')
@section('title', 'Medicine Master Add')
@section('pageurl', admin_url('master/medicine/list'))
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
                                <x-button-back href="{{ admin_url('master/medicine/list') }}"></x-button-back>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="add_page"
                                        action="{{ admin_url('master/medicine/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medicine Id</label>
                                                    <input type="text" name ="medicine_id" class="form-control"
                                                        placeholder="Enter the Medicine Id"
                                                        value="{{ getsequence('medicine') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Medicine Name</label>
                                                    <input type="text" name="medicine_name" id="medicine_name"
                                                        class="form-control" placeholder="Enter the Medicine Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Generic Names</label>
                                                    <input type="text" name="generic_names" id="generic_names"
                                                        class="form-control" placeholder="Enter the Generic Names">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Category</label>
                                                    <select name="category_id" id="category"
                                                        class="form-control single-select">
                                                        <option value="">Select Category</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Manufacturer</label>
                                                    <select name="manufacturer_id" id="manufacturer"
                                                        class="form-control single-select">
                                                        <option value="">Select Manufacturer</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">UOM</label>
                                                    <select name="uom_id" id="uom"
                                                        class="form-control single-select">
                                                        <option value="">Select UOM</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Strength / Dosage</label>
                                                    <input type="text" name="strength_dosage" id="strength_dosage"
                                                        class="form-control" placeholder="Enter the Strength / Dosage">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Package Size</label>
                                                    <input type="text" name="package_size" id="package_size"
                                                        class="form-control" placeholder="Enter the Package Size">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Scheduled Type</label>
                                                    <select name="scheduled_type" id="scheduled_type"
                                                        class="form-control single-select">
                                                        <option value="">Select Scheduled Type</option>
                                                        <option value="daily">Daily</option>
                                                        <option value="weekly">Weekly</option>
                                                        <option value="monthly">Monthly</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Storage Condition</label>
                                                    <select name="storage_condition" id="storage_condition"
                                                        class="form-control single-select">
                                                        <option value="">Select Storage Condition</option>
                                                        <option value="room_temperature">Room Temperature</option>
                                                        <option value="refrigerated">Refrigerated</option>
                                                        <option value="frozen">Frozen</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Reorder Level</label>
                                                    <input type="text" name="reorder_level" id="reorder_level"
                                                        class="form-control" placeholder="Enter the Reorder Level">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Reorder Quantity</label>
                                                    <input type="text" name="reorder_quantity" id="reorder_quantity"
                                                        class="form-control" placeholder="Enter the Reorder Quantity">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">MRP</label>
                                                    <input type="text" name="mrp" id="mrp"
                                                        class="form-control" placeholder="Enter the MRP">
                                                </div>
                                            </div>


                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Description</label>
                                                    <textarea name="description" class="form-control" placeholder="Enter the Description"></textarea>
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
                    medicine_name: {
                        required: true,
                        remote: {
                            url: '{{ admin_url('master/medicine/unique') }}',
                            type: 'post',
                            data: {
                                medicine_name: function() {
                                    return $('#medicine_name').val();
                                }
                            }
                        }
                    },
                    category_id: {
                        required: true,
                    },
                    tax_id: {
                        required: true,
                    },
                    uom_id: {
                        required: true,
                    },
                    manufacturer_id: {
                        required: true,
                    },


                },
                messages: {
                    medicine_name: {
                        required: "Medicine Name is Required",
                        remote: "Medicine Name is should be unique",

                    },
                    category_id: {
                        required: "Category is Required",
                    },
                    tax_id: {
                        required: "Tax is Required",
                    },
                    uom_id: {
                        required: "UOM is Required",
                    },
                    manufacturer_id: {
                        required: "Manufacturer is Required",
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
