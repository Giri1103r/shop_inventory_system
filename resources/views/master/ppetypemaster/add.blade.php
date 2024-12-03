@extends('admin.layouts.admin')
@section('title', 'PPE Type Master')
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
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <input type="text" name="ppe_name" id="ppe_name"
                                                        class="form-control" placeholder="Enter the PPE name">
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select form-select-sm  single-select">
                                                        <option value="">Select the ppe type</option>
                                                        @foreach ($ppetype as $name)
                                                            <option value="{{ $name->id }}">{{ $name->ppe_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Quantity</label>
                                                    <input type="text" name="quantity" id="quantity"
                                                        class="form-control form-control-sm" value="0" readonly>
                                                    <div class="text-danger" id="quantity_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Protection Category</label>
                                                    <input type="text" name="protection_category"
                                                        id="protection_category" class="form-control form-control-sm"
                                                        placeholder="Enter the protection category">
                                                    <div class="text-danger" id="protection_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Standard</label>
                                                    <input type="text" name="ppe_standard" id="ppe_standard"
                                                        class="form-control form-control-sm"
                                                        placeholder="Enter the ppe standard">
                                                    <div class="text-danger" id="ppe_standard_error"></div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Image</label>
                                                    <input type="file" name="ppe_file" id="ppe_file"
                                                        class="form-control form-control-sm" placeholder="Enter the image"
                                                        onchange="validateImage()">
                                                    <small>Allowed file types: png, jpeg</small>
                                                    <div id="ppe_file_error" class="text-danger"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_ppetype_master/list') }}"></x-button-cancel>
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

            $('#PpeTypeMasterForm').on('submit', function(e) {
                let valid = true;

                if (!validateItemCode()) valid = false;
                if (!validatePPEName()) valid = false;
                if (!validateProtectionCategory()) valid = false;
                if (!validateStandard()) valid = false;
                if (!validatePPEType()) valid = false;
                if (!validateImage()) valid = false;

                if (!valid) {
                    e.preventDefault();
                } else {
                    $('#submit').prop('disabled', true);
                }
            });


            function validateItemCode() {
                var itemCode = $('#item_code').val();
                var regex = /^[a-zA-Z0-9_]*$/;

                if (itemCode === "") {
                    $('#item_code_error').text('Item Code cannot be empty.');
                    return false;
                }

                if (!regex.test(itemCode)) {
                    $('#item_code_error').text('Item code should be alphanumeric.');
                    return false;
                }

                $('#item_code_error').text('');
                return true;
            }


            function validatePPEName() {
                var name = $('#ppe_name').val();
                var regex = /^[a-zA-Z0-9\-_'"()\s]{3,30}$/;

                if (name === "") {
                    $('#ppe_name_error').text('PPE Name cannot be empty.');
                    return false;
                }

                if (name.length < 3 || name.length > 30) {
                    $('#ppe_name_error').text('PPE Name must be between 3 and 30 characters.');
                    return false;
                }

                if (!regex.test(name)) {
                    $('#ppe_name_error').text('PPE Name should be alphanumeric and can include -, _, \', ", (, ).');
                    return false;
                }

                $('#ppe_name_error').text('');
                return true;
            }



            function validateProtectionCategory() {
                var protectionCategory = $('#protection_category').val();
                var regex = /^[a-zA-Z0-9\-_'"()\s]{3,30}$/;

                if (protectionCategory === "") {
                    $('#protection_error').text('Protection Category cannot be empty.');
                    return false;
                }

                if (protectionCategory.length < 3 || protectionCategory.length > 30) {
                    $('#protection_error').text('rotection Category must be between 3 and 30 characters.');
                    return false;
                }

                if (!regex.test(protectionCategory)) {
                    $('#protection_error').text(
                        'Protection Category should be alphanumeric and can include -, _, \', ", (, ).');
                    return false;
                }

                $('#protection_error').text('');
                return true;
            }


            function validateStandard() {
                var standard = $('#ppe_standard').val();
                var regex = /^[a-zA-Z0-9\-_'"()\s]+$/;

                if (standard === "") {
                    $('#ppe_standard_error').text('PPE Standard cannot be empty.');
                    return false;
                }

                if (!regex.test(standard)) {
                    $('#ppe_standard_error').text(
                        'PPE Standard should be alphanumeric and can include -, _, \', ", (, ).');
                    return false;
                }

                $('#ppe_standard_error').text('');
                return true;
            }

            function validatePPEType() {
                var type = $('#ppe_type').val();

                if (type === "") {
                    $('#ppe_type_error').text('Please select the PPE Type.');
                    return false;
                }

                $('#ppe_type_error').text('');
                return true;
            }

            function validateImage() {
                var file = $('#ppe_file').val();
                var fileError = $('#ppe_file_error');

                if (file !== "") {
                    var extension = file.split('.').pop().toLowerCase();
                    if ($.inArray(extension, ['png', 'jpeg']) === -1) {
                        fileError.text('Allowed file types: png, jpeg.');
                        return false;
                    } else {
                        fileError.text('');
                        return true;
                    }
                } else {
                    fileError.text('');
                    return true;
                }
            }

        });
    </script>
@endpush
