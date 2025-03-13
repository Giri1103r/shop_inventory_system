@extends('admin.layouts.admin')
@section('title', 'Checklist Sub Type')
@section('pageurl', admin_url('inspection/master/checklist-sub-type/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-sub-type/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="subchecklistedit"
                                        action="{{ admin_url('inspection/master/checklist-sub-type/edit/submit') }}"  autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($checklist_sub_type->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Sub-Type ID</label>
                                                    <input type="text" name ="checklist_subtype_category_id"
                                                        class="form-control" placeholder="Location ID"
                                                        value="{{ $checklist_sub_type->subcategory_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Type Name</label>
                                                    <select name="category_id" id="category_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($checklist_types as $checklist_type)
                                                            <option @if ($checklist_sub_type->category_id == $checklist_type->id) selected @endif
                                                                value="{{ encryptId($checklist_type->id) }}">
                                                                {{ $checklist_type->category_name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Sub-Type Name</label>
                                                    <input type="text" name="subcategory_name" id="subcategory_name"
                                                        class="form-control" placeholder="Location Name"
                                                        value="{{ $checklist_sub_type->subcategory_name }}">
                                                </div>
                                            </div>

                                            <div class="form-group form-input col-md-4 mb-2">
                                                <label class="form-label">Image</label>
                                                <input type="file" name="checklist_file" id="checklist_file"
                                                    class="form-control form-control-sm" accept="image/jpeg"
                                                    placeholder="Enter the image">
                                                    <div>
                                                        <a href="{{ asset($checklist_image->file_path) }}" target="_blank">
                                                            <img src="{{ asset($checklist_image->file_path) }}"
                                                                alt="Image" style="max-width: 50%;">
                                                        </a>
                                                    </div>
                                                <small>Allowed file types: jpg</small>

                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('location/list') }}"></x-button-cancel>
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

            $.validator.addMethod("noSpaces", function(value, element) {
                return this.optional(element) || value.trim().length > 0;
            }, "This field cannot contain only spaces");

            $.validator.addMethod("filesize", function(value, element, param) {
                if (this.optional(element)) {
                    return true;
                }
                var fileSize = element.files[0].size / 1024;
                return fileSize >= param[0] && fileSize <= param[
                    1];
            }, "File size must be between 50KB and 5MB");
            $('#subchecklistedit').validate({
                rules: {
                    category_id: {
                        required: true,
                    },
                    subcategory_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 2000,
                        noSpaces: true,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                        remote: {
                            url: '{{ admin_url('inspection/master/checklist-sub-type/unique') }}',
                            type: 'post',
                            data: {
                                subcategory_name: function() {
                                    return $('#subcategory_name').val();
                                },
                                category_id: function() {
                                    return $('#category_id').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    checklist_file: {
                        extension: "jpg",
                        filesize: [50, 5120],
                    },

                },
                messages: {
                    category_id: {
                        required: "{{ __('Checklist Type Name is required ') }}",
                    },
                    subcategory_name: {
                        required: "{{ __('Checklist Sub-Type Name is Required') }}",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 2000",
                        remote: "{{ __('Checklist Sub-Type Name should be unique') }}",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",
                    },
                    checklist_file: {
                        extension: "Only .jpg files are allowed. Please upload a valid image file.",
                    }

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

                    });
                }
            });
        });
    </script>
@endpush
