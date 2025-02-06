@extends('admin.layouts.admin')
@section('title', 'PPE Master Edit')
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
                                        action="{{ admin_url('ppe_ppetype_master/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id" value="{{ $encryptid }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Item Code</label>
                                                    <input type="text" name ="item_code" id="item_code"
                                                        class="form-control" placeholder="Item Code"
                                                        value="{{ $ppetypemaster->item_code }}">
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
                                                        class="form-control" placeholder="Enter the PPE name"
                                                        value="{{ $ppetypemaster->ppe_name }}">
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select single-select ">
                                                        <option value="">Select the ppe type</option>
                                                        @foreach ($ppetype as $ppetypes)
                                                            <option value="{{ $ppetypes->id }}"
                                                                @if ($ppetypemaster->ppe_type == $ppetypes->id) selected @endif>
                                                                {{ $ppetypes->ppe_type }}
                                                            </option>
                                                        @endforeach

                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
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
                                                        placeholder="Enter the protection category"
                                                        value="{{ $ppetypemaster->ppe_category }}">
                                                    @error('protection_category')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="protection_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="ppe_file" id="ppe_file"
                                                        accept="image/png, image/jpeg, image/jpg" class="form-control "
                                                        placeholder="Enter the image" onchange="validateImage()">
                                                    <small>Allowed file types: png, jpeg , jpg</small>
                                                    @if (isset($ppetypemaster) && $ppetypemaster->ppe_image)
                                                        <p>
                                                            <a href="{{ asset('public/' . $ppetypemaster->ppe_image) }}"
                                                                target="_blank" class="d-block mt-2">
                                                                <i class="fa-solid fa-eye text-danger"></i> View
                                                            </a>
                                                        </p>
                                                        <input type="hidden" name="existing_pre_image"
                                                            value="{{ $ppetypemaster->ppe_image }}">
                                                    @else
                                                        <p>No image is uploaded</p>
                                                    @endif
                                                    @error('ppe_file')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div id="ppe_file_error" class="text-danger"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Standard</label>
                                                    <textarea name="ppe_standard"    id="ppe_standard" cols="3" rows="4" class="form-control form-control-sm" placeholder="Enter the ppe standard">{{ $ppetypemaster->ppe_standard }}</textarea>

                                                    @error('ppe_standard')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_standard_error"></div>
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

            $('#PpeTypeMasterForm').validate({
                rules: {
                    item_code: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,

                        remote: {
                            url: '{{ admin_url('ppe_ppetype_master/unique') }}',
                            type: 'get',
                            data: {
                                item_code: function() {
                                    return $('#item_code').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },

                    ppe_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 200,

                    },
                    ppe_type: {
                        required: true
                    },
                    protection_category: {
                        required: true,
                        minlength: 3,
                        maxlength: 30,

                    },
                    ppe_standard: {
                        required: true,
                        minlength: 3,
                        maxlength: 200,

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
                     
                        remote: "Item code already exists."
                    },
                    ppe_name: {
                        required: "PPE Name cannot be empty.",
                        minlength: "PPE Name must be between 3 and 200 characters.",
                        maxlength: "PPE Name must be between 3 and 200 characters.",

                    },
                    ppe_type: {
                        required: "Please select the PPE Type."
                    },
                    protection_category: {
                        required: "Protection Category cannot be empty.",
                        minlength: "Protection Category must be between 3 and 200 characters.",
                        maxlength: "Protection Category must be between 3 and 200 characters.",
                    },
                    ppe_standard: {
                        required: "PPE Standard cannot be empty.",
                        minlength: "PPE  Standard must be between 3 and 200 characters.",
                        maxlength: "PPE  Standard must be between 3 and 200 characters.",

                    },
                    ppe_file: {
                        extension: "Please select a file with .jpeg,.jpg,.png"
                    }
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

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");

        });
    </script>
@endpush
