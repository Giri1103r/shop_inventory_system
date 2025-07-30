@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation')
@section('pageurl', admin_url('safety/safety-walk-observation/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('safety/safety-walk-observation/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.shifts') }}</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Month</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->month }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">{{ __('inspection.safety_walk_taken_by') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername($inspection_details->safety_walk_taken_by) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- @php
                                            $signature = GetSafetySignature(
                                                $inspection_details->created_by,
                                                $inspection_details->id,
                                                SAFETY_WALK_OBSERVATION,
                                            );
                                        @endphp
                                        @if (isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 100px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif --}}
                                    </div>
                                    <hr>
                                    <div class="form-wrapper">
                                        <div class="row mt-4 form-set">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Safety Walk Observation</h4>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.location') }}</label>
                                                    <div class="view_data">
                                                        {{ getLocationName($inspection_details->location_id) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.exact_location') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->excat_location }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.date_of_observation') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->observation_date) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.observation') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->observation }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.recomended_action') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->recomended_action }}
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.employee') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername($inspection_details->responsible_persion) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.observation_status') }}</label>
                                                    <div class="view_data">
                                                        @if ($inspection_details->observation_status == 1)
                                                            Active
                                                        @elseif($inspection_details->observation_status == 0)
                                                            Deactive
                                                        @else
                                                            Unknown
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $images = GetSafetyWalkImage($inspection_details->id);
                                            @endphp

                                            <div class="col-md-4 mb-2">
                                                @if ($images !== false)
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.image') }}</label>
                                                    <a href="{{ admin_url($images) }}" target="_blank">
                                                        <img src="{{ admin_url($images) }}" style="width: 100px;" />
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (
                                    (($inspection_details->responsible_persion == Auth::id() || isAdmin()) &&
                                        $inspection_details->observation_status == RESPONSIBLE_PERSON_APPROVAL_PENDING) ||
                                        $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_ON_PROCESS)
                                    <div>
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">Action Required</h4>
                                        </div>
                                        <form method="POST" id="firstapproval"
                                            action="{{ admin_url('safety/safety-walk-observation/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                                name="id">
                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label
                                                        class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                    <input type="text" name="responsible_person_date" value="{{ today() }}"
                                                        id = "responsible_person_date" class="form-control" readonly>
                                                </div>


                                                <div class="col-md-12 mb-2 form-input" id="capa_remarks">
                                                    <label for="responsible_person_remarks"
                                                        class="form-label">Remarks</label>
                                                    <textarea id="responsible_person_remarks" class="form-control" rows="3"
                                                        placeholder="Please provide Remarks..." name="responsible_person_remarks"></textarea>
                                                </div>
                                                <div class="submit-button" style="text-align: right;">
                                                    <x-button-submit></x-button-submit>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif


                                @if ($inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_PENDING)
                                    <div>
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">Action Required</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Responsible Person</label>
                                                    <div class="view_data">
                                                        {{ getUsername($inspection_details->observer_person) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat($inspection_details->date_of_compilance) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->observer_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                                @if (
                                    (checkuserRole(ROLE_EHS_OFFICER) || isAdmin()) &&
                                        $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_PENDING)
                                    <div>
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">EHS Officer Approval </h4>
                                        </div>
                                        <form method="POST" id="secondApproval"
                                            action="{{ admin_url('safety/safety-walk-observation/approval/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                                name="id">
                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="ehs_name" id = "ehs_name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" value="{{ today() }}"
                                                        id = "date" class="form-control" readonly>
                                                </div>


                                                <div class="col-md-12 mb-2 form-input" id="remarks">
                                                    <label for="remarks" class="form-label">Remarks</label>
                                                    <textarea id="remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                        name="remarks"></textarea>
                                                </div>
                                                <div class="submit-button" style="text-align: right;">
                                                    <button type="submit" name="approved" value="1"
                                                        class="btn btn-success ">{{ __('common.approve') }}</button>

                                                    <button type="submit" name="rejected" value="2"
                                                        class="btn btn-danger">{{ __('common.reject') }}</button>

                                                    <button type="submit" name="on_process" value="3"
                                                        class="btn btn-warning ">{{ __('common.on-process') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
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
       
        // first approval
        $('#firstapproval').validate({
            rules: {
                responsible_person_date: {
                    required: true,
                },
                responsible_person_remarks: {
                    required: true,
                    minlength: 3,
                    maxlength: 600,
                    noSpaces: true,
                },

            },
            messages: {
                responsible_person_date: {
                    required: "Date is required",
                },
                responsible_person_remarks: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 600",
                },

            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-input').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
                $(element).closest('.form-input').find('.invalid-feedback').remove();
            },
            submitHandler: function(form) {
                form.submit();
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                validator.errorList.forEach(function(error) {});
            }
        });
        // second approval
        $('#secondApproval').validate({
            rules: {

                remarks: {
                    required: true,
                    minlength: 3,
                    maxlength: 600,
                    noSpaces: true,
                },

            },
            messages: {

                remarks: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 600",
                },

            },
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-input').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
                $(element).closest('.form-input').find('.invalid-feedback').remove();
            },
            submitHandler: function(form) {
                form.submit();
            },
            invalidHandler: function(event, validator) {
                var errors = validator.numberOfInvalids();
                validator.errorList.forEach(function(error) {});
            }
        });
    </script>
@endpush
