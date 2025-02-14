@extends('admin.layouts.admin')
@section('title', 'Initial Incident Review')
@section('pageurl', admin_url('incident/initial-incident/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('incident/initial-incident/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Incident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $incident_report->sr_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($incident_report->incident_date_time) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUsername($incident_report->unit_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label require">Shift</label>
                                        <div class="view_data">
                                            {{ $incident_report->shift }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label require">Location</label>
                                        <div class="view_data">
                                            {{ $incident_report->location_id }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">IIR Type</label>
                                        <div class="view_data">
                                            {{ $incident_report->iir_type }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Incident Reported By</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Name</label>
                                        <div class="view_data">
                                            {{ $incident_report->reported_by }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Designation</label>
                                        <div class="view_data">
                                            {{ $incident_report->designation }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ $incident_report->reported_department }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $incident_report->employee_code }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Time of reporting</label>
                                        <div class="view_data">
                                            {{ $incident_report->time_of_reporting }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Reporting Media</label>
                                        <div class="view_data">
                                            {{ implode(', ', $displayMedia) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label require">Brief Description</label>
                                        <div class="view_data">
                                            {{ $incident_report->brief_description }}
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Existing Evidence</label>
                                        @if (!$initialincidentevidence->isEmpty())
                                            <div class="row">
                                                @foreach ($initialincidentevidence as $key => $evidence)
                                                    <div class="col-md-3 col-sm-6 mb-2">
                                                        <div class="existing-evidence text-center">
                                                            <a href="{{ asset($evidence->file_path) }}" target="_blank">
                                                                <img src="{{ asset($evidence->file_path) }}" alt="Evidence"
                                                                    class="img-fluid rounded shadow"
                                                                    style="max-width: 20%; height: auto;">
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Head Review</h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="ehs_head_review"
                                        action="{{ admin_url('incident/initial-incident/ehs_head_review/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="incident_id" id="incident_id"
                                                value="{{ encryptId($incident_report->id) }}">

                                            <input type="hidden" name="reviewer_emp_id" id="reviewer_emp_id"
                                                class="form-control" value="{{ Auth::user()->employee_id ?? '' }}">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="name" class="form-label">Reviewer Name</label>
                                                    <input type="text" name="reviewer_name" id="reviewer_name"
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
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="team_id" class="form-label require">Assign Team
                                                        members</label>
                                                    <select name="team_member[]" id="team_id"
                                                        class="form-control team_name" style="width: 100%" multiple>
                                                        <option value="">Select Team members</option>

                                                    </select>
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
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('incident/initial-incident/list') }}"></x-button-cancel>
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
            $('#team_id').select2({
                placeholder: "Select Team Members",
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: "{{ admin_url('incident/initial-incident/teamMembers') }}",
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // Search query
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.log("Error in AJAX request:", textStatus, errorThrown);
                    }
                },
                minimumInputLength: 3,
                width: '100%',
                dropdownCssClass: 'form-control',
                selectionCssClass: 'form-control'
            });

            // $('#team_id').select2({
            //     placeholder: "Select Team members",
            //     allowClear: true,
            //     closeOnSelect: false,
            // });

            $('#ehs_head_review').validate({
                rules: {
                    "team_id[]": {
                        required: true,
                    },
                    remark: {
                        required: true,
                        maxlength: 1000
                    }
                },
                messages: {
                    "team_id[]": {
                        required: "Please select a team member.",
                    },
                    remark: {
                        required: "Please provide a remark.",
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
