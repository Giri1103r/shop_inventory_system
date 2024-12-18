@extends('admin.layouts.admin')
@section('title', 'PPE Type')
@section('pageurl', admin_url('ppe_ppetype_master/list'))
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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_ppetype_master/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="PpeTypeMasterForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ppe_ppetype_master/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Item Code</label>
                                                    <input type="text" name ="item_code" id="item_code"
                                                        class="form-control" placeholder="Item Code">
                                                    <div class="text-danger" id="item_code_error"></div>
                                                    @error('item_code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <input type="text" name="ppe_name" id="ppe_name"
                                                        class="form-control" placeholder="Enter the PPE name">
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select   single-select">
                                                        <option value="">Select the ppe type</option>
                                                        @foreach ($ppetype as $name)
                                                            <option value="{{ $name->id }}">{{ $name->ppe_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Quantity</label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        class="form-control " value="0" readonly>
                                                    <div class="text-danger" id="quantity_error"></div>
                                                </div>
                                            </div> --}}

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Protection Category</label>
                                                    <input type="text" name="protection_category"
                                                        id="protection_category" class="form-control "
                                                        placeholder="Enter the protection category">
                                                    <div class="text-danger" id="protection_error"></div>
                                                    @error('protection_error')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Standard</label>
                                                    <input type="text" name="ppe_standard" id="ppe_standard"
                                                        class="form-control " placeholder="Enter the ppe standard">
                                                    <div class="text-danger" id="ppe_standard_error"></div>
                                                    @error('ppe_standard')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="ppe_file" id="ppe_file"
                                                        class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg" placeholder="Enter the image"
                                                        onchange="validateImage()">
                                                    <small>Allowed file types: png, jpeg , jpg</small>
                                                    <div id="ppe_file_error" class="text-danger"></div>
                                                    @error('ppe_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('ppe_ppetype_master/list') }}"></x-button-cancel>
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

@endsection
@push('script')
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(document).ready(function() {
                    $.validator.addMethod("regex", function(value, element, regexp) {
                        return this.optional(element) || regexp.test(value);
                    }, "Please check your input.");

                    $('#PpeTypeMasterForm').validate({
                            rules: {
                                item_code: {
                                    required: true,
                                    minlength: 3,
                                    maxlength: 30,
                                    regex: /^[a-zA-Z0-9-]*$/,
                                    remote: {
                                        url: '{{ admin_url('ppe_ppetype_master/unique') }}',
                                        type: 'get',
                                        data: {
                                            item_code: function() {
                                                var itemCode = $('#item_code').val();
                                                console.log('Item Code:', itemCode);
                                                return itemCode;
                                            },
                                        }
                                    },
                                    },
                                    ppe_name: {
                                        required: true,
                                        minlength: 3,
                                        maxlength: 30,
                                        regex: /^[a-zA-Z0-9\-_'"()\s]{3,30}$/
                                    },
                                    ppe_type: {
                                        required: true
                                    },
                                    protection_category: {
                                        required: true,
                                        minlength: 3,
                                        maxlength: 30,
                                        regex: /^[a-zA-Z0-9\-_'"()\s]{3,30}$/
                                    },
                                    ppe_standard: {
                                        required: true,
                                        regex: /^[a-zA-Z0-9\-_'"()\s]+$/
                                    },
                                    ppe_file: {
                                        extension: "png|jpeg|jpg"
                                    }
                                },
                                messages: {
                                    item_code: {
                                        required: "Item Code cannot be empty.",
                                        minlength: "Item code must contain between 3 and 30 characters.",
                                        maxlength: "Item code must contain between 3 and 30 characters.",
                                        regex: "Item code should be alphanumeric.",
                                        remote: "Item code already exists."
                                    },
                                    ppe_name: {
                                        required: "PPE Name cannot be empty.",
                                        minlength: "PPE Name must be between 3 and 30 characters.",
                                        maxlength: "PPE Name must be between 3 and 30 characters.",
                                        regex: "PPE Name should be alphanumeric and can include -, _, ', \", (, )."
                                    },
                                    ppe_type: {
                                        required: "Please select the PPE Type."
                                    },
                                    protection_category: {
                                        required: "Protection Category cannot be empty.",
                                        minlength: "Protection Category must be between 3 and 30 characters.",
                                        maxlength: "Protection Category must be between 3 and 30 characters.",
                                        regex: "Protection Category should be alphanumeric and can include -, _, ', \", (, )."
                                    },
                                    ppe_standard: {
                                        required: "PPE Standard cannot be empty.",
                                        regex: "PPE Standard should be alphanumeric and can include -, _, ', \", (, )."
                                    },
                                    ppe_file: {
                                        extension: "Allowed file types: png, jpeg, jpg."
                                    }
                                },
                                errorElement: 'div',
                                errorPlacement: function(error, element) {
                                    var errorDiv = element.siblings('div.text-danger');
                                    if (errorDiv.length === 0) {
                                        errorDiv = $('<div class="text-danger"></div>').insertAfter(element);
                                    }
                                    errorDiv.html(error);
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
                                    console.log(errors + " field(s) are invalid");
                                    validator.errorList.forEach(function(error) {
                                        console.log("Field: " + error.element.name + ", Error: " + error
                                            .message);
                                    });
                                }
                            });
                    });
    </script>
@endpush
