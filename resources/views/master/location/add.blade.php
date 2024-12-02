@extends('admin.layouts.admin')
@section('title', 'Location Add')
@section('pageurl', admin_url('location/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Location Add') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('location/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="locationadd" action="{{ admin_url('location/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location ID</label>
                                                    <input type="text" name ="location_id" class="form-control"
                                                        placeholder="Location ID" value="{{ getsequence('location') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Company Name</label>
                                                    <select name="company_id" id="company_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Company Name</option>
                                                        @foreach ($companyList as $company)
                                                            <option value="{{ encryptId($company->id) }}">
                                                                {{ $company->company_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location Name</label>
                                                    <input type="text" name="location_name" id="location_name"
                                                        class="form-control" placeholder="Location Name">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
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


            $(document).on('click', '#resetform', function() {
                $('#locationadd .single-select').val('');
                $('#locationadd .single-select').trigger('change');
                setTimeout(function() {
                    table.draw();
                }, 150);
            });
            $('#locationadd').validate({
                rules: {
                    company_id: {
                        required: true,
                    },
                    location_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                        remote: {
                            url: '{{ admin_url('location/unique') }}',
                            type: 'post',
                            data: {
                                location_name: function() {
                                    return $('#location_name').val();
                                },
                                company_id: function() {
                                    return $('#company_id').val();
                                },
                            }
                        }
                    },

                },
                messages: {
                    company_id: {
                        required: "{{ __('Company Name is Required') }}",
                    },
                    location_name: {
                        required: "{{ __('Location Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 20",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",
                        remote: "{{ __('Location Name should be unique') }}"
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
