@extends('admin.layouts.admin')
@section('title', 'Training Feedback')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')

    <style>
        .required {
            color: red;
            font-weight: bold;
        }

        .table_card {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        .table_card th,
        .table_card td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table_card th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .table_card tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table_card tr:hover {
            background-color: #f1f1f1;
        }

        .table_card td {
            text-align: center;
        }

        .table-container {
            padding: 20px;
        }

        .form-check-input {
            width: 15px;
            height: 15px;
            background-color: #f0f0f0;
            border-radius: 5px;
            border: 2px solid #007bff;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: #007bff;
            border-color: #0056b3;
        }

        .form-check-input:focus {
            box-shadow: none;
            border-color: #0056b3;
        }

        .form-check-label:hover {
            color: black;

            cursor: pointer;
        }

        .form-check-label {
            color: black;
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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="card-body" id="training_feedback_details">
                                    <form method="POST" id="training_details"
                                        action="{{ admin_url('training/feedback_approve/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="training_schedule_id" id="training_schedule_id"
                                            value="{{ encryptId($training_schedule->id) }}">

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
                                                <div class="card-body" id="training_assessment_details">

                                                    <div class="row">
                                                        <div class="card-header-inner">
                                                            <h4 class="text-white">Training Assessment</h4>
                                                        </div>
                                                    </div>
                                                    @if (isset($trainingAssessmentList) && $trainingAssessmentList->isNotEmpty())
                                                        <div class="basic-form">
                                                            <div class="row">

                                                                <table class="table_card" style="margin-top: 20px;">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="form-label">Employee Name</th>
                                                                            <th class="form-label">Attendee/Non-Attendee
                                                                            </th>
                                                                            <th class="form-label">Mark</th>
                                                                            <th class="form-label">Assessment</th>
                                                                            <th class="form-label">Feedback</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody id="lesson_learned_block">
                                                                        @foreach ($trainingAssessmentList as $assessment)
                                                                            <tr>

                                                                                <td>{{ $assessment->emp_name ?? '' }}</td>
                                                                                <td>
                                                                                    @if ($assessment->attended_status == 1)
                                                                                        <i class="fa fa-check"
                                                                                            style="font-size:24px;color: green;"></i>
                                                                                    @else
                                                                                        <i class="fa fa-close"
                                                                                            style="font-size:24px;color:red"></i>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $assessment->mark ?? '-' }}</td>
                                                                                <td>
                                                                                    @if ($assessment->assessment == 1)
                                                                                        Pass
                                                                                    @elseif($assessment->assessment == 2)
                                                                                        Fail
                                                                                    @elseif($assessment->assessment == 3)
                                                                                        Not Attended
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ strip_tags($assessment->feedback) ?? '-' }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                                <hr>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <p>No training assessment data available.</p>
                                                    @endif
                                                </div>
                                                <div class="mb-3 col-md-12 form-input" style="font-size: 17px;">
                                                    <label class="form-label fw-bold" for="feedback">
                                                        Are you sure you want to send the feedback link to the attending
                                                        trainees?
                                                    </label>
                                                    <input class="form-check-input" type="checkbox" id="feedback"
                                                        name="feedback" value="1">
                                                    <label class="form-check-label" for="feedback">
                                                        Yes, send feedback link.
                                                    </label>
                                                </div>

                                            </div>

                                            <div class="text-end mt-4">
                                                <x-button-submit class="btn btn-primary me-2" />
                                                <x-button-reset class="btn btn-secondary me-2" />
                                                <x-button-cancel class="btn btn-danger"
                                                    href="{{ admin_url('training_schedule/list') }}" />
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
        $('#resetform').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
        $('#training_details').validate({
            rules: {
                feedback: {
                    required: true
                },
            },
            messages: {
                feedback: {
                    required: "You must select the checkbox to send the feedback link."
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
                if ($('#training_details').data('conflict') === true) {
                    // Prevent form submission if there are conflicts
                    return false;
                } else {
                    form.submit(); // Submit the form when valid
                }
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
    </script>
@endpush
