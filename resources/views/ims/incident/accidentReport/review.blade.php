@extends('admin.layouts.admin')
@section('title', 'Accident Report Review')
@section('pageurl', admin_url('accidentReport/list'))


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
                                    <x-button-back href="{{ admin_url('accidentReport/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Accident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $accident_report->accident_report_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($accident_report->date_and_time) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $accident_report->emp_code }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ $accident_report->unit_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Designation</label>
                                        <div class="view_data">
                                            {{ $accident_report->designation }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ $accident_report->department_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ $accident_report->shift }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Accident Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->location_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Exact Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->exact_location }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Address of the injured person</label>
                                        <div class="view_data">
                                            {{ $accident_report->address_of_the_injuredperson }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($accident_report->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($accident_report->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($accident_report->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
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
                                        action="{{ admin_url('accidentReport/ehs_head_review/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" class="form-control" name="accident_report_id" id="accident_report_id"
                                                value="{{ encryptId($accident_report->id) }}">

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
                                                    <select name="team_member[]" id="team_id" class="form-control team_name" multiple="multiple" style="width: 100%">
                                                        <option value="">Select Team Members</option>
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
                                                href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
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
                    url: "{{ url('accidentReport/getemployeename') }}", 
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term, 
                            _token: "{{ csrf_token() }}" 
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
                    cache: true,
                    error: function(xhr, textStatus, errorThrown) {
                        console.log("Error in AJAX request:", textStatus, errorThrown);
                    }
                },
                minimumInputLength: 1,
                width: '100%',
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
