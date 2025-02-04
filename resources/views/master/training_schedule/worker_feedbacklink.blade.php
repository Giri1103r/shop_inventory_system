@extends('admin.layouts.admin')
@section('title', 'Training Worker Feedback')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')

    <style>
        .required {
            color: red;
            font-weight: bold;
        }
    </style>

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

                            </div>
                            <div class="tab-content">
                                <div class="card-body" id="training_feedback_details">
                                    <form method="POST" id="training_details"
                                        action="{{ admin_url('training/feedback_link/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <input type="hidden" name="training_schedule_id" id="training_schedule_id"
                                            value="{{ encryptId($training_schedule->id) }}">

                                        <input type="hidden" name="emp_worker_type" id="emp_worker_type"
                                            value="2">

                                        <div class="basic-form">
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
                                                    <label class="form-label view_label">Venue</label>
                                                    <div class="view_data">
                                                        {{ $training_schedule->name_of_the_conference_hall ?? '' }}
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Select Worker Id</label>
                                                        <select name="emp_id" id="emp_id"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Worker </option>
                                                            @foreach ($empIds as $emp)
                                                                <option value="{{ $emp->emp_id }}">
                                                                    {{ $emp->emp_id }}
                                                                    ({{ $emp->emp_name }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Feedback about the Trainer</label>
                                                        <textarea name="trainer_feedback" id="trainer_feedback" class="form-control" rows="4">{{ old('trainer_feedback') }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Feedback about the
                                                            Training</label>
                                                        <textarea name="training_feedback" id="training_feedback" class="form-control" rows="4">{{ old('training_feedback') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-end mt-4">
                                            <button type="submit" class="btn btn-primary me-2">Submit</button>
                                            <button type="reset" id="resetform"
                                                class="btn btn-secondary me-2">Reset</button>
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

            $('#training_details').validate({
                rules: {
                    emp_id: {
                        required: true,
                        remote: {
                            url: "{{ admin_url('training/workerId/unique') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                emp_id: function() {
                                    return $('#emp_id').val();
                                },
                                training_schedule_id: function() {
                                    return $('#training_schedule_id').val();
                                }
                            }
                        }
                    },
                    trainer_feedback: {
                        required: true,
                        maxlength: 1000
                    },
                    training_feedback: {
                        required: true,
                        maxlength: 1000
                    }
                },
                messages: {
                    emp_id: {
                        required: "Select Worker ID.",
                        remote: "This Worker has already submitted feedback for this training."
                    },
                    trainer_feedback: {
                        required: "Please provide feedback about the trainer.",
                        maxlength: "Feedback cannot exceed 1000 characters."
                    },
                    training_feedback: {
                        required: "Please provide feedback about the training.",
                        maxlength: "Feedback cannot exceed 1000 characters."
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('text-danger'); // Apply red color to error messages

                    if (element.attr("name") == "emp_id") {
                        error.appendTo(element.closest('.col-md-4'));
                    } else {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid'); // Highlight input with red border
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });


        });
    </script>
@endpush
