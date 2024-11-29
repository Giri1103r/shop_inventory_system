@extends('admin.layouts.admin')
@section('title', 'Unit Add')
@section('pageurl', admin_url('unit/list'))


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
                                    <x-button-back href="{{ admin_url('unit/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="unitadd" action="{{ admin_url('unit/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit ID</label>
                                                    <input type="text" name ="unit_id" class="form-control"
                                                        placeholder="Unit ID" value="{{ getsequence('unit') }}" readonly>
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
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location Name</option>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <input type="text" name="unit_name" id="unit_name"
                                                        class="form-control" placeholder="Unit Name">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('unit/list') }}"></x-button-cancel>
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
        $(document).on('change', '#company_id', function() {
            let companyId = $(this).val();

            if (companyId) {
                $.ajax({
                    url: "{{ admin_url('location/ajax-list') }}/" + companyId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#location_id').empty().append('<option value="">Select Location</option>');
                        $.each(data, function(key, value) {
                            $('#location_id').append('<option value="' + value.id + '">' + value
                                .name + '</option>');
                        });
                        $('#location_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching locations. Please try again.');
                    }
                });
            } else {
                $('#location_id').empty().append('<option value="">Select Location</option>');
                $('#location_id').trigger('change.');
            }
        });

        $(function() {
            $('#unitadd').validate({
                rules: {
                    company_id: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 20,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,

                        remote: {
                            url: '{{ admin_url('unit/unique') }}',
                            type: 'post',
                            data: {

                                company_id: function() {
                                    return $('#company_id').val();
                                },
                                location_id: function() {
                                    return $('#location_id').val();
                                },
                                unit_name: function() {
                                    return $('#unit_name').val();
                                },
                            }
                        }
                    },

                },
                messages: {
                    company_id: {
                        required: "{{ __('Company Name is Required') }}",
                    },
                    location_id: {
                        required: "{{ __('Location Name is Required') }}",
                    },
                    unit_name: {
                        required: "{{ __('Unit Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 20",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",
                        remote: "{{ __('Unit Name should be unique') }}"
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
