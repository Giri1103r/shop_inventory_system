@extends('admin.layouts.admin')
@section('title', 'Training Feedback')
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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                {{-- <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div> --}}
                            </div>
                            <div class="tab-content">
                                <div class="card-body" id="training_feedback_details">
                                    <form method="POST" id="training_details"
                                        action="{{ admin_url('training/feedback_link/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="training_assessment_feedback_id"
                                            id="training_assessment_feedback_id" value="{{ encryptId($feedback_id) }}">

                                        <input type="hidden" name="training_schedule_id"
                                            id="training_schedule_id" value="{{ encryptId($training_schedule->id) }}">

                                        <input type="hidden" name="emp_id"
                                            id="emp_id" value="{{  $trainingAssessmentFeedback->emp_id  }}">
                                        <input type="hidden" name="emp_name"
                                            id="emp_name" value="{{  $trainingAssessmentFeedback->emp_name  }}">

                                        <div class="basic-form">
                                            <div class="row">
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">From Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydatetimeformat($training_schedule->from_date) }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">To Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydatetimeformat($training_schedule->to_date) }}
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
                                                <div class="col-md-12">
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
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endpush
