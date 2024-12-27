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
                                            value="{{ encryptId($trainingScheduleId) }}">

                                        <div class="basic-form">
                                            <div class="row">
                                                <div class="mb-3 col-md-12">
                                                    <label class="form-label fw-bold" for="feedback">
                                                        Are you sure you want to send the feedback link to trainees?
                                                    </label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="feedback"
                                                            name="feedback" value="1">
                                                        <label class="form-check-label" for="feedback">
                                                            Yes, send feedback link.
                                                        </label>
                                                    </div>
                                                </div>
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
    </script>
@endpush
