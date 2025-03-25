@extends('admin.layouts.admin')
@section('title', 'Checklist Type')
@section('pageurl', admin_url('inspection/master/checklist-type/list'))


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
                            <div class="card-header">
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-type/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="checklistadd"
                                        action="{{ admin_url('inspection/master/checklist-type/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="form-input col-md-4 mb-2">
                                                <label class="form-label require">Category Id</label>
                                                <input type="text" name="checklist_type_category_id" id = "checklist"
                                                    class="form-control" readonly
                                                    value="{{ getSequence('incident_checklist_type') }}">
                                            </div>


                                            <div class="form-input col-md-4 mb-2">
                                                <label class="form-label require">Category Name</label>
                                                <input type="text" name="checklist_category" id = "checklist_category"
                                                    class="form-control" placeholder="Enter Category Name">
                                            </div>

                                            <div class="form-input col-md-4 mb-2">
                                                <label class="form-label">Image</label>
                                                <input type="file" name="checklist_file" id="checklist_file"
                                                    class="form-control form-control-sm" accept="image/jpeg"
                                                    placeholder="Enter the image">
                                                <small>Allowed file types: jpg</small>
                                                <div id="checklist_file_error" class="text-danger"></div>
                                                {{-- @error('checklist_file')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror --}}
                                            </div>


                                            <div class="mb-3 col-md-4 form-input">
                                                <label for="work_station_id"
                                                    class="col-sm-5 form-label  required">Questionary</label>
                                                <select name="questionary_id" id="questionary_id"
                                                    class=" form-control single-select" style="width: 100%">
                                                    <option value="">Select Questionary</option>
                                                    @foreach ($checklist_options as $checklist_option)
                                                        <option value="{{ encryptId($checklist_option->id) }}">
                                                            {{ $checklist_option->type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('inspection/master/checklist-type/list') }}"></x-button-cancel>
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

            $('#checklistadd').validate({
                rules: {
                    checklist_category: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                        remote: {
                            url: '{{ admin_url('inspection/master/checklist-type/unique') }}',
                            type: 'post',
                            data: {
                                checklist_category: function() {
                                    return $('#checklist_category').val();
                                }
                            }
                        }
                    },
                    questionary_id: {
                        required: true,
                    },
                    checklist_file: {
                        extension: "jpg",
                        filesize: [50, 5120],
                    },
                },
                messages: {
                    checklist_category: {
                        required: "{{ __('Name is Required') }}",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                        remote: "Name should be unique",
                    },
                    questionary_id: {
                        required: "{{ __('inspection.questionary_required') }}",
                    },
                    checklist_file: {
                        extension: "Only .jpg files are allowed. Please upload a valid image file.",
                    }
                },
                errorElement: 'div',
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
                    validator.errorList.forEach(function(error) {});
                }
            });
        });
    </script>
@endpush
