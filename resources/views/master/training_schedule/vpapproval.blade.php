@extends('admin.layouts.admin')
@section('title', 'EHS Head Approval')
@section('pageurl', admin_url('training_schedule/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Training Schedule Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">From Date</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($training_schedule->from_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">To Date</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($training_schedule->to_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Start Time</label>
                                        <div class="view_data">
                                            {{ Displaytimeformat($training_schedule->start_time) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">End Time</label>
                                        <div class="view_data">
                                            {{ Displaytimeformat($training_schedule->end_time) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Topic</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Trainer </label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->emp_name) ? $training_schedule->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->unit_name) ? $training_schedule->unit_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->department_name) ? $training_schedule->department_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Target Trainees</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->target_trainees) ? $training_schedule->target_trainees : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Venue/Location</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Man Hours</label>
                                        <div class="view_data">
                                            {{ $training_schedule->training_man_hours ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($training_schedule->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($training_schedule->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($training_schedule->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Head Approval</h4>
                                    </div>
                                </div>

                                <div class="basic-form">
                                    <form method="POST" id="vp_approval"
                                        action="{{ admin_url('training_schedule/ehs_approval/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="id" id="id"
                                                value="{{ encryptId($training_schedule->id) }}">

                                            <input type="hidden" name="approver_emp_id" id="approver_emp_id"
                                                class="form-control" value="{{ Auth::user()->employee_id ?? '' }}">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="name" class="form-label">Approver Name</label>
                                                    <input type="text" name="approver_name" id="approver_name"
                                                        class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                        readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="date" class="form-label require">Date</label>
                                                    <input type="text" name ="date" id="date_datepicker"
                                                        class="form-control" placeholder="Date" readonly
                                                        value="{{ todaydate() }}">
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Remark</label>
                                                    <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <div class="d-flex float-end gap-2 mx-auto">
                                                <button type="submit" name="action" value="approve"
                                                    class="btn btn-success w-100">Approve</button>
                                                <button type="submit" name="action" value="reject"
                                                    class="btn btn-danger w-100">Reject</button>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('training_schedule/list') }}"></x-button-cancel>
                                            </div>

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
        // document.addEventListener("DOMContentLoaded", function() {
        //     const fromDate =
        //         "{{ $training_schedule->from_date }}"; 
        //     const formattedFromDate = flatpickr.formatDate(new Date(fromDate),
        //         "d-m-Y"); 
        //     flatpickr("#date_datepicker", {
        //         dateFormat: "d-m-Y",
        //         minDate: formattedFromDate // Use the formatted from_date as the minimum date
        //     });
        // });

        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
            // flatpickr("#date_datepicker", {
            //     dateFormat: "d-m-Y",
            //     minDate: "today",
            // });

            $('#vp_approval').validate({
                rules: {
                  
                    remark: {
                        required: true,
                        maxlength: 1000
                    }
                },
                messages: {
                  
                    remark: {
                        required: "Please provide Remark.",
                        maxlength: "Remark cannot exceed 1000 characters."
                    }
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
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
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
