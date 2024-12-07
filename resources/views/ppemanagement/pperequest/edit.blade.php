@extends('admin.layouts.admin')
@section('title', 'PPE Request Edit')
@section('pageurl', admin_url('ppe_request/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">ppe_request Add</h4> --}}

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
                                {{-- <h4 class="card-title">{{ __('master.ppe_request_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="pperequestedit"
                                        action="{{ admin_url('ppe_request/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <input type="hidden" name="emp_id" id="emp_id" value="{{ $employee->emp_id }}">
                                        <input type="hidden" name="emp_name" id="emp_name"
                                            value="{{ $employee->emp_name }}">
                                        <input type="hidden" name="department" id="department"
                                            value="{{ $employee->department }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE type</option>

                                                        @foreach ($ppetypedata as $ppetype)
                                                            <option value="{{ $ppetype->id }}"
                                                                @if ($pperequest->ppe_type == $ppetype->id) selected @endif>
                                                                {{ $ppetype->ppe_type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <select name="ppe_name" id="ppe_name" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE name</option>
                                                        @foreach ($ppetypemaster as $ppetypemasters)
                                                            <option value="{{ $ppetypemasters->id }}"
                                                                @if ($pperequest->ppe_name == $ppetypemasters->id) selected @endif>
                                                                {{ $ppetypemasters->ppe_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>

                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input" id="ppe_image">

                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_type/list') }}"></x-button-cancel>
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
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(document).ready(function() {
            $(document).on('change', '#ppe_type', function() {
                var ppeTypeId = $(this).val();
                if (ppeTypeId) {
                    $.ajax({
                        url: "{{ admin_url('ppe_ppetype_master/ajax-list') }}/" + ppeTypeId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {

                            $('#ppe_name').empty().append(
                                '<option value="">Select PPE Name</option>'
                            );


                            $.each(data, function(key, value) {
                                $('#ppe_name').append('<option value="' + value.id +
                                    '">' + value.ppe_name + '</option>');
                            });
                        },
                        error: function(xhr) {
                            alert('Error fetching PPE Names. Please try again.');
                        }
                    });
                } else {
                    $('#ppe_name').empty().append('<option value="">Select PPE Name</option>');
                }
            });

        });
        $(document).ready(function() {
            $('#pperequestedit').validate({
                rules: {
                    ppe_name: {
                        required: true,
                    },
                    ppe_type: {
                        required: true,
                    },
                },
                messages: {

                    ppe_name: {
                        required: "Please Select the PPE Name.",
                    },
                    ppe_type: {
                        required: "Please Select the PPE Type.",
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
