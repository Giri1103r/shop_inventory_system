@extends('admin.layouts.admin')
@section('title', 'Permit Extension')
@section('pageurl', admin_url('safetypermit/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="align-back-btc">
                                <x-button-back href="{{ admin_url('safetypermit/list') }}"></x-button-back>

                            </div>
                        </div>

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">Permit Extension</h4>
                                </div>
                            </div>
                            <div class="basic-form">
                                <form method="POST" id="permitextension"
                                    action="{{ admin_url('safetypermit/permitExtension/submit') }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="">
                                        <input type="hidden" id= "permit_id" name="permit_id"
                                            value="{{ $safetypermit->id }}">
                                        <input type="hidden" id= "permit_status" name="permit_status"
                                            value="{{ $safetypermit->permit_status }}">
                                        <div class="mb-3 row">
                                            <div class="col-md-4 mb-3">
                                                <label for="date" class="form-label require">Date</label>
                                                <input type="text" class="form-control form-control-sm" id="date"
                                                    name="date" readonly
                                                    value="{{ Displaydateformat($safetypermit->date) }}">
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Time(To)</label>
                                                    <input type="text" name="time_to" id="time_to" class="form-control"
                                                        placeholder="Time(To)">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1">
                                                    <label for="remarks" class="form-label require">Remarks</label>
                                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="extension_remarks"
                                                        rows="3"></textarea>
                                                    <div class="text-danger" id="remarks_error"></div>
                                                    @error('remarks')
                                                        <span id="remark_error" class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="submit-button float-end">
                                        <x-button-submit class="submit" id="submit"></x-button-submit>
                                        <x-button-reset class="submit"></x-button-reset>
                                        <x-button-cancel href="{{ admin_url('safetypermit/list') }}"></x-button-cancel>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('script')
    <script>
        $(document).ready(function() {

            flatpickr("#time_to", {
                enableTime: true,
                noCalendar: true,
                time_24hr: true,
                minuteIncrement: 5,
                dateFormat: "H:i",
                maxTime: "18:00",
                onOpen: function(selectedDates, dateStr, instance) {
                    const now = new Date();
                    const currentHours = now.getHours();
                    const currentMinutes = now.getMinutes();


                    instance.set('minTime',
                        `${currentHours.toString().padStart(2, '0')}:${currentMinutes.toString().padStart(2, '0')}`
                        );
                }
            });

            $('#permitextension').validate({
                rules: {
                    time_to: {
                        required: true,
                    },
                    extension_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,

                    },


                },
                messages: {
                    time_to: {
                        required: " Time is empty.",

                    },
                    extension_remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 600 characters.",
                        maxlength: "Remarks must contain between 3 and 600 characters.",

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
