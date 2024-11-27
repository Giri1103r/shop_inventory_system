@extends('admin.layouts.admin')
@section('title', 'Venue Edit')
@section('pageurl', admin_url('venue/list'))


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
                                    <x-button-back href="{{ admin_url('venue/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="venueedit" action="{{ admin_url('venue/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($venue->id) }}">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Name of the Conference Hall</label>
                                                    <input type="text" name ="name_of_the_conference_hall"
                                                        class="form-control" placeholder="Name of the Conference Hall" value="{{ $venue->name_of_the_conference_hall }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit Name</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit Name</option>
                                                        @foreach ($unitList as $unit)
                                                            <option @if ($venue->unit_id == $unit->id) selected @endif value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                         
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Capacity</label>
                                                    <input type="text" name="capacity" class="form-control"
                                                        placeholder="Capacity" value="{{ $venue->capacity }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Projector/LCD Availability</label>
                                                    <select name="projector_or_lcd_availability"
                                                        id="projector_or_lcd_availability"
                                                        class="form-control single-select" style="width: 100%;">
                                                        <option value="">Select Projector/LCD Availability</option>
                                                        <option value="YES"  {{ $venue->projector_or_lcd_availability == 'YES' ? 'selected' : '' }}>YES</option>
                                                        <option value="NO"  {{ $venue->projector_or_lcd_availability == 'NO' ? 'selected' : '' }}>NO</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-cancel></x-button-cancel>
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
            $('#venueedit').validate({
                rules: {
                    name_of_the_conference_hall: {
                        required: true,
                        minlength: 3,
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
