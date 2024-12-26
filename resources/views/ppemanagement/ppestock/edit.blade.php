@extends('admin.layouts.admin')

@section('title', 'PPE Stock Inventory Edit')

@section('pageurl', admin_url('ppe_stock_inventory/list'))

@section('content')
    @push('style')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
        </div>
    </div>
    <div class="content-body default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_stock_inventory/list') }}"></x-button-back>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="ppeExemptionForm"
                                        action="{{ admin_url('ppe_stock_inventory/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="org" class="form-label require">Org</label>
                                                    <input type="text" name="org"
                                                        class="form-control form-control-sm" id="org"
                                                        value="{{ $ppestock->org }}">
                                                    <div class="text-danger" id="org_error"></div>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="item_code" class="form-label require">Item code</label>
                                                    <input type="text" name="item_code"
                                                        class="form-control form-control-sm" id="item_code"
                                                        value="{{ $ppestock->item_code }}">
                                                    <div class="text-danger" id="item_code_error"></div>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="item_inventory_id" class="form-label require">Item Inventory
                                                        Id</label>
                                                    <input type="text" name="item_inventory_id" id="item_inventory_id"
                                                        class="form-control form-control-sm"
                                                        value="{{ $ppestock->inventory_item_id }}">
                                                    <div class="text-danger" id="item_inventory_error"></div>

                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="sub" class="form-label require">SUB</label>

                                                <input type="text" class="form-control form-conrol-sm" name="sub"
                                                    value="{{ $ppestock->sub }}" id="sub">
                                                <div class="text-danger" id="sub_error"></div>

                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="uom" class="form-label require">UOM</label>
                                                <input type="text" class="form-control form-conrol-sm" name="uom"
                                                    value="{{ $ppestock->uom }}" id="uom">
                                                <div class="text-danger" id="uom_error"></div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="quantity" class="form-label require">Quantity</label>
                                                <input type="text" class="form-control form-conrol-sm" name="quantity"
                                                    value="{{ $ppestock->quantity }}" id="quantity">
                                                <div class="text-danger" id="quantity_error"></div>


                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <label for="reason" class="form-label require">Item Description</label>
                                                <textarea name="item_description" id="item_description" cols="3" rows="4"
                                                    class="form-control form-control-sm" placeholder="Enter the item description">{{ $ppestock->item_description }}</textarea>
                                                <div class="text-danger" id="item_description_error"></div>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ppe_stock_inventory/list') }}"></x-button-cancel>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });



            $('#ppeExemptionForm').validate({
                rules: {
                    org: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex:  /^[a-zA-Z0-9]*$/
                    },
                    item_code: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z0-9-\s]*$/,
                    },
                    item_inventory_id: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[0-9]*$/
                    },
                    sub: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z0-9]*$/
                    },
                    uom: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,
                        regex: /^[a-zA-Z0-9]*$/
                    },
                    quantity: {
                        required: true,
                        digits: true
                    },
                    reason: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,

                    },

                },
                messages: {
                    org: {
                        required: "Org cannot be empty.",
                        minlength: "Org must contain between 3 and 30 characters.",
                        maxlength: "Org must contain between 3 and 30 characters.",
                        regex: "Org must be alphanumeric."
                    },
                    item_code: {
                        required: "Item code cannot be empty.",
                        minlength: "Item code must contain between 3 and 30 characters.",
                        maxlength: "Item code must contain between 3 and 30 characters.",
                        regex: "Item code can contain alphabets, numbers, and hyphens."
                    },
                    item_inventory_id: {
                        required: "Item Inventory Id cannot be empty.",
                        minlength: "Item Inventory Id must contain between 3 and 30 characters.",
                        maxlength: "Item Inventory Id must contain between 3 and 30 characters.",
                        regex: "Item Inventory Id must be numeric."
                    },
                    sub: {
                        required: "SUB cannot be empty.",
                        minlength: "SUB must contain between 3 and 30 characters.",
                        maxlength: "SUB must contain between 3 and 30 characters.",
                        regex: "SUB must contain only alphanumeric."
                    },
                    uom: {
                        required: "UOM cannot be empty.",
                        minlength: "UOM must contain between 3 and 30 characters.",
                        maxlength: "UOM must contain between 3 and 30 characters.",
                        regex: "UOM must be alphanumeric."
                    },
                    quantity: {
                        required: "Quantity cannot be empty.",
                        digits: "Quantity must be a positive number."
                    },
                    reason: {
                        required: "Item Description cannot be empty.",
                        maxlength: "Item Description must contain between 3 and 600 characters.",
                        minlength: "Item Description must contain between 3 and 600 characters.",
                    },

                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    $('#submit').prop('disabled', false);
                }
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                var re = new RegExp(regexp);
                return this.optional(element) || re.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
