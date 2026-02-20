@extends('admin.layouts.admin')
@section('title', 'Equipment ')
@section('pageurl', admin_url('safety/master/equipment/list'))


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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('safety/master/equipment/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="equipmentEdit"
                                        action="{{ admin_url('safety/master/equipment/edit/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($equipment->id) }}" name="id"
                                            id="id" />

                                        <div class="row">
                                            <div class="form-input col-md-4 mb-2">
                                                <label class="form-label require">{{__('inspection.equipment_name')}}</label>
                                                <input type="text" name="equipment_name" id = "equipment_name"
                                                    class="form-control"
                                                    value="{{ $equipment->equipment_name }}">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/master/equipment/list') }}"></x-button-cancel>
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

            $('#equipmentEdit').validate({
                rules: {
                    equipment_name : {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                        remote: {
                            url: '{{ admin_url('safety/master/equipment/unique') }}',
                            type: 'post',
                            data: {
                                equipment_name: function() {
                                    return $('#equipment_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    }
                },
                messages: {
                    equipment_name: {
                        required: "{{ __('Equipment Name is Required') }}",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                        remote: "Equipment Name should be unique",
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
