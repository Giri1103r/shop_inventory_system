@extends('admin.layouts.admin')
@section('title', 'Monthly Eye Wash Inspection Add')
@section('pageurl', admin_url('safety/eye-wash-inspection/monthly/list'))
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
                                        href="{{ admin_url('safety/eye-wash-inspection/monthly/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->doc_no) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->revision_date }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname($inspection_details->location) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Shift</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->next_due) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection_details->frequency) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @foreach ($inspection as $details)
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Monthly Eye Wash Inspection Checklist</h4>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->sr_no }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <div class="view_data">
                                                            {{ getLocationName($details->location) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->resource_code }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.condition') }}</label>
                                                        <div class="view_data">
                                                            @if ($details->inspection_condition == GOOD)
                                                                Good
                                                            @elseif($details->inspection_condition == FAIR)
                                                                Fair
                                                            @elseif($details->inspection_condition == POOR)
                                                                Poor
                                                            @else
                                                                Unknown
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.value') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->value }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.hfsov') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->hand_free_stay_open_value }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.foot_pedal_value') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->foot_pedal_value }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.eyewash_heads') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->eyewash_heads_value }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.receptacle') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->receptacle }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.water') }}</label>
                                                        <div class="view_data">
                                                            @if ($details->water == GOOD)
                                                                Good
                                                            @elseif($details->water == FAIR)
                                                                Fair
                                                            @elseif($details->water == POOR)
                                                                Poor
                                                            @else
                                                                Unknown
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quality') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->quality }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.pressure') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->pressure }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.temperature') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->temperature }}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($inspection_details->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/ehsofficer/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-12 form-input">
                                                <label class="form-label required">Whether the Inspection has been
                                                    passed Without the CAPA
                                                    ?</label>
                                                <div class="mb-3 form-input">
                                                    <input type="radio" id="yes" name="is_passed"
                                                        class="validate-radio-required" value="{{ 1 }}">
                                                    <label for="yes">YES</label>

                                                    <input type="radio" id="no" name="is_passed"
                                                        class="validate-radio-required" value="{{ 0 }}">
                                                    <label for="no">NO</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="remarks">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea id="remarks" class="form-control" rows="3" placeholder="Please Enter Remarks" name="remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <button class="btn btn-success">Verify</button>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                        <div class="row mb-2">
                                            @if (isset($inspection_details->verified_by))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->verified_by,
                                                        $inspection_details->id,
                                                        MONTHLY_FORKLIFT_INSPECTION,
                                                    );
                                                @endphp
                                            @endif
                                            @if (isset($inspection_details->created_at))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($inspection_details->approved_by)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUsername($inspection_details->approved_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($inspection_details->capa_recomendation))
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.capa_recomendation') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->capa_recomendation }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if (isset($inspection_details->capa_remarks))
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.fire_associate_action') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.name') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->created_by) }}
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->created_by,
                                                        $inspection_details->id,
                                                        MONTHLY_FORKLIFT_INSPECTION,
                                                    );
                                                @endphp
                                                @if (isset($signature))
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label"
                                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                                            <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                                style="width: 150px; margin-top: -10px;" />
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.capa_action_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->capa_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($inspection_details->capa_ehs_remarks)
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                                </h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->verified_by,
                                                        $inspection_details->id,
                                                        MONTHLY_FORKLIFT_INSPECTION,
                                                    );
                                                @endphp
                                                @if (isset($signature))
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label"
                                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                                            <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                                style="width: 150px; margin-top: -10px;" />
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.capa_reverifcation_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->capa_ehs_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if (isset($inspection_details->level_one_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.level_one_manager_action') }}
                                                </h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->l1_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->l1_manager_verified_by,
                                                    $inspection_details->id,
                                                    MONTHLY_FORKLIFT_INSPECTION,
                                                );
                                            @endphp
                                            @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->level_one_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($inspection_details->level_two_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.level_two_manager_action') }}
                                                </h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_two_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->l2_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_two_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->level_two_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->l2_manager_verified_by,
                                                    $inspection_details->id,
                                                    MONTHLY_FORKLIFT_INSPECTION,
                                                );
                                            @endphp
                                            @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif

                                @if (
                                    $inspection_details->inspection_status == WAITING_FOR_CAPA_ACTION ||
                                        $inspection_details->inspection_status == L2_MANAGER_REJECTED ||
                                        $inspection_details->inspection_status == EHS_OFFICER_REJECTED ||
                                        $inspection_details->inspection_status == L1_MANAGER_REJECTED)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="capaAction"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/capa/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="capa_remarks">
                                                <label for="capa_remarks" class="form-label">Remarks</label>
                                                <textarea id="capa_remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                    name="capa_remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif

                                @if ($inspection_details->inspection_status == WAITING_FOR_CAPA_VERIFICATION)
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/capa/reverify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                            </div>

                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea id="" class="form-control" rows="3" placeholder="Please Provide Remarks" name="remarks"></textarea>
                                            </div>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif

                                @if ($inspection_details->inspection_status == WAITING_FOR_L1_VERIFICATION)
                                    <form method="POST" id="levelOneManager"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/level-one/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_one_manager_verifcation_action') }}</h4>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea id="remarks" class="form-control" rows="3" placeholder="Please Provide Remarks"
                                                name="level_one_manager"></textarea>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif

                                @if ($inspection_details->inspection_status == WAITING_FOR_L2_VERIFICATION)
                                    <form method="POST" id="levelTwoManager"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/level-two/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_two_manager_verifcation_action') }}</h4>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea id="remarks" class="form-control" rows="3" placeholder="Please Provide Remarks"
                                                name="level_two_manager"></textarea>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    @stop
