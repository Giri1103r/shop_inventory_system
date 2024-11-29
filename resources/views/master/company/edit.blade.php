@extends('admin.layouts.admin')
@section('title', 'Company Edit')
@section('pageurl', admin_url('company/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Edit') }}</h4> --}}

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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('company/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="companyedit" action="{{ admin_url('company/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($company->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company ID</label>
                                                    <input type="text" name ="company_id" id="company_name"
                                                        class="form-control" placeholder="Company ID"
                                                        value="{{ $company->company_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <input type="text" name="company_name" class="form-control"
                                                        placeholder="Company Name" value="{{ $company->company_name }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Short Name</label>
                                                    <input type="text" name="short_name" class="form-control"
                                                        placeholder="Short Name" value="{{ $company->short_name }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Address</label>
                                                    <textarea name="address" class="form-control" placeholder="Company Address">{{ $company->address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('company/list') }}"></x-button-cancel>
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
        $(function() {
            $('#companyedit').validate({
                rules: {
                    company_name: {
                        required: true,
                        minlength: 3,

                        remote: {
                            url: '{{ admin_url('company/unique') }}',
                            type: 'post',
                            data: {
                                location_type_name: function() {
                                    return $('#company_name').val();
                                },
                                id: function() {
                                    return $('#id').val();
                                }
                            }
                        }
                    },
                    short_name: {
                        required: true,
                        minlength: 3,
                    },
                    address: {
                        required: true,
                    },
                },
                messages: {
                    company_name: {
                        required: "{{ __('Company Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        remote: "{{ __('Company Name should be unique') }}"
                    },
                    short_name: {
                        required: "{{ __('Short Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                    },
                    address: {
                        required: "{{ __('Company Address is Required') }}",
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
                    console.log('test');
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
