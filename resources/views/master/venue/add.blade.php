@extends('admin.layouts.admin')
@section('title', 'Venue Master Add')
@section('pageurl', admin_url('venue/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('venue/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="venueadd" action="{{ admin_url('venue/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Name of the Conference Hall</label>
                                                    <input type="text" name ="name_of_the_conference_hall" id="name_of_the_conference_hall"
                                                        class="form-control" placeholder="Name of the Conference Hall">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>
                                                        @foreach ($unitList as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Capacity</label>
                                                    <input type="text" name="capacity" class="form-control"
                                                        placeholder="Capacity">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Projector/LCD Availability</label>
                                                    <select name="projector_or_lcd_availability"
                                                        id="projector_or_lcd_availability"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Projector/LCD Availability</option>
                                                        <option value="YES">YES</option>
                                                        <option value="NO">NO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('venue/list') }}"></x-button-cancel>
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
            $('#venueadd').validate({
                rules: {
                    name_of_the_conference_hall: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        pattern: /^[a-zA-Z0-9\s\-_'"(),&]*$/,
                        remote: {
                            url: '{{ admin_url('venue/unique') }}',
                            type: 'post',
                            data: {
                                name_of_the_conference_hall: function() {
                                    return $('#name_of_the_conference_hall').val();
                                },
                                unit_id: function() {
                                    return $('#unit_id').val();
                                },
                            }
                        }
                    },
                    unit_id: {
                        required: true,
                    },
                    capacity: {
                        required: true,
                        number: true,
                    },
                    projector_or_lcd_availability: {
                        required: true,
                    },
                },
                messages: {
                    name_of_the_conference_hall: {
                        required: "{{ __('Name of the Conference Hall is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 100",
                        pattern: "Only alphanumeric characters and -, _, ', \", (), ,, and & are allowed",
                        remote: "{{ __('Conference Hall Name should be unique') }}"
                    },
                    unit_id: {
                        required: "{{ __('Unit Name is Required') }}",
                    },
                    capacity: {
                        required: "{{ __('Capacity is Required') }}",
                        number: "{{ __('Capacity must be a numeric value') }}",
                    },
                    projector_or_lcd_availability: {
                        required: "{{ __('Projector/LCD Availability is Required') }}",
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
