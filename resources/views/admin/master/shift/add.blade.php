@extends('admin.layouts.admin')
@section('title', 'Shift Master Add')
@section('pageurl', admin_url('master/shift/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('master/shift/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="shiftadd"
                                        action="{{ admin_url('master/shift/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift ID</label>
                                                    <input type="text" name ="shift_code" class="form-control"
                                                        placeholder="Shift ID" value="{{ getsequence('shift') }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift Name</label>
                                                    <input type="text" name="shift_name" id="shift_name"
                                                        class="form-control" placeholder="Shift Name"
                                                        value="{{ old('shift_name') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Start Time</label>
                                                    <div class="input-group">
                                                        <input type="text" name="start_time" id="start_time"
                                                            class="form-control clockpicker" placeholder="HH:MM"
                                                            value="{{ old('start_time') }}">
                                                        <span class="input-group-text"><i
                                                                class="fa-regular fa-clock"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">End Time</label>
                                                    <div class="input-group">
                                                        <input type="text" name="end_time" id="end_time"
                                                            class="form-control clockpicker" placeholder="HH:MM"
                                                            value="{{ old('end_time') }}">
                                                        <span class="input-group-text"><i
                                                                class="fa-regular fa-clock"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Total Hours</label>
                                                    <input type="text" name="total_hours" id="total_hours"
                                                        class="form-control" placeholder="Total Hours"
                                                        value="{{ old('total_hours') }}">
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('master/shift/list') }}"></x-button-cancel>
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

            /* Fill in the span between the two times so the user does not have to. */
            $('#start_time, #end_time').on('change', function() {
                var start = $('#start_time').val();
                var end = $('#end_time').val();

                if (!start || !end) {
                    return;
                }

                var startParts = start.split(':');
                var endParts = end.split(':');
                var minutes = (endParts[0] * 60 + endParts[1] * 1) - (startParts[0] * 60 + startParts[1] * 1);

                if (minutes <= 0) {
                    minutes += 24 * 60; // shift runs past midnight
                }

                $('#total_hours').val((minutes / 60).toFixed(2));
            });
        });

        $(function() {
            $('#shiftadd').validate({
                rules: {
                    shift_name: {
                        required: true,
                        minlength: 3,
                        maxlength: 70,
                        remote: {
                            url: '{{ admin_url('master/shift/unique') }}',
                            type: 'post',
                            data: {
                                shift_name: function() {
                                    return $('#shift_name').val();
                                },
                            }
                        }
                    },
                    start_time: {
                        required: true,
                    },
                    end_time: {
                        required: true,
                    },
                    total_hours: {
                        required: true,
                        number: true,
                    },

                },
                messages: {
                    shift_name: {
                        required: "{{ __('Shift Name is Required') }}",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 70",
                        remote: "{{ __('Shift Name should be unique') }}",
                    },
                    start_time: {
                        required: "{{ __('Start Time is Required') }}",
                    },
                    end_time: {
                        required: "{{ __('End Time is Required') }}",
                    },
                    total_hours: {
                        required: "{{ __('Total Hours is Required') }}",
                        number: "{{ __('Total Hours should be a number') }}",
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
