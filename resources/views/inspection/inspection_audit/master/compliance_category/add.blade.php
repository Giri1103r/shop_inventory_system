@extends('admin.layouts.admin')
@section('title', 'Compliance Category')
@section('pageurl', admin_url('audit/master/compliance_category/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">Company Add</h4> --}}

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
                                    <x-button-back href="{{ admin_url('audit/master/compliance_category/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="complianceCategory" action="{{ admin_url('audit/master/compliance_category/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{__('inspection.compliance_category')}}</label>
                                                    <input type="text" name="compliance_category" id = "compliance_category" class="form-control"
                                                        placeholder="Compliance Category">
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                            href="{{ admin_url('audit/master/compliance_category/list') }}"></x-button-cancel>
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
            $('#complianceCategory').validate({
                rules: {
                    compliance_category: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        remote: {
                            url: '{{ admin_url("audit/master/compliance_category/unique") }}',
                            type: 'post',
                            data: {
                                compliance_category: function() {
                                    return $('#compliance_category').val();
                                }
                            }
                        }
                    },

                },
                messages: {
                    compliance_category: {
                        required: "{{ __('Compliance Category is Required') }}",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                        remote: "{{ __('Compliance Category should be unique') }}",

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
