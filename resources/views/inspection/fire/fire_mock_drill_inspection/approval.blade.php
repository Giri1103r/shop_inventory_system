@extends('admin.layouts.admin')
@section('title', 'Fire MockDrill Observation Report')
@section('pageurl', admin_url('fire/fire-mock-drill-observation/list'))
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
                                        href="{{ admin_url('fire/fire-mock-drill-observation/list') }}"></x-button-back>
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
                                                <label class="form-label ">{{ __('inspection.rev_data') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->date) }}
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $signature = GetFireSignature(
                                                $inspection->created_by,
                                                $inspection->id,
                                                FIRE_MOCK_DRILL_INSPECION,
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
                                        @endif
                                        <hr>
                                        @foreach ($inspection_details as $details)
                                            <div class="form-wrapper">
                                                <div class="row mt-4 form-set">
                                                    <div class="card-header-inner p-2">
                                                        <h4 class="text-white">Fire MockDrill Observation Report</h4>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.sr_no') }}</label>
                                                            <div class="view_data">
                                                                {{ $loop->iteration }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.observation') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->observation }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.date_of_observation') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($details->date_of_observation) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.shifts') }}</label>
                                                            <div class="view_data">
                                                                {{ getShiftname($details->shift_id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.date_of_observation') }}</label>
                                                            <div class="view_data">
                                                                {{ getUnitname($details->unit_id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.corrective_action') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->capa_remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.action_taken') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->action_taken }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.employee') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->emp_id }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($details->date_of_compliance) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.date_of_closure') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->date_of_closure }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label">{{ __('inspection.observation_status') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->observation_status == '1' ? 'Active' : 'Inactive' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if (
                                        ($inspection->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION &&
                                            (checkUserRole(ROLE_EHS_OFFICER)) ||  ($inspection->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION &&
                                            isAdmin())) )
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                            </div>
                                        </div>
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('fire/fire-mock-drill-observation/ehsofficer/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection->id) }}" name="id">
                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
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
                                                        href="{{ admin_url('fire/fire-mock-drill-observation/list') }}"></x-button-cancel>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                            </div>
                                            <div class="row mb-2">
                                                @if (isset($inspection->verified_by))
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                            <div class="view_data">
                                                                {{ getUserName($inspection->verified_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $signature = GetFireSignature(
                                                            $inspection->verified_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                        );

                                                        $updated_time = GetFireUpdatedTime(
                                                            $inspection->verified_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                            WAITING_FOR_EHS_OFFICER_VERIFICATION,
                                                        );
                                                    @endphp
                                                @endif

                                                @if (isset($inspection->created_at))
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.date') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($updated_time->created_at) }}
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

                                                @if ($inspection->approved_by)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                            <div class="view_data">
                                                                {{ getUsername($inspection->approved_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if (isset($inspection->capa_recomendation))
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label">{{ __('inspection.capa_recomendation') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection->capa_recomendation }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label">{{ __('inspection.remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection->remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            @if (isset($inspection->capa_remarks))
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">{{ __('inspection.fire_associate_action') }}
                                                    </h4>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.name') }}</label>
                                                            <div class="view_data">
                                                                {{ getUserName($inspection->created_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $signature = GetFireSignature(
                                                            $inspection->created_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                        );

                                                        $updated_time = GetFireUpdatedTime(
                                                            $inspection->created_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                            WAITING_FOR_CAPA_ACTION,
                                                        );
                                                    @endphp
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.date') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($updated_time->created_at) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (isset($signature))
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label"
                                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url($signature) }}"
                                                                    alt="Signature Upload"
                                                                    style="width: 150px; margin-top: -10px;" />
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.capa_action_remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection->capa_remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($inspection->capa_ehs_remarks)
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.ehs_officer_reverification') }}
                                                    </h4>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                            <div class="view_data">
                                                                {{ getUserName($inspection->verified_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $signature = GetFireSignature(
                                                            $inspection->verified_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                        );

                                                        $updated_time = GetFireUpdatedTime(
                                                            $inspection->verified_by,
                                                            $inspection->id,
                                                            FIRE_MOCK_DRILL_INSPECION,
                                                            WAITING_FOR_CAPA_VERIFICATION,
                                                        );
                                                    @endphp
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.date') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($updated_time->created_at) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (isset($signature))
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label"
                                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url($signature) }}"
                                                                    alt="Signature Upload"
                                                                    style="width: 150px; margin-top: -10px;" />
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-12 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.capa_reverifcation_remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection->capa_ehs_remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        @if (isset($inspection->level_one_manager_remarks))
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
                                                            {{ getUserName($inspection->l1_manager_verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $signature = GetFireSignature(
                                                        $inspection->l1_manager_verified_by,
                                                        $inspection->id,
                                                        FIRE_MOCK_DRILL_INSPECION,
                                                    );

                                                    $updated_time = GetFireUpdatedTime(
                                                        $inspection->l1_manager_verified_by,
                                                        $inspection->id,
                                                        FIRE_MOCK_DRILL_INSPECION,
                                                        WAITING_FOR_L1_VERIFICATION,
                                                    );
                                                @endphp
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($updated_time->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
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
                                                            {{ $inspection->level_one_manager_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset($inspection->level_two_manager_remarks))
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
                                                            {{ getUserName($inspection->l2_manager_verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $signature = GetFireSignature(
                                                        $inspection->l2_manager_verified_by,
                                                        $inspection->id,
                                                        FIRE_MOCK_DRILL_INSPECION,
                                                    );

                                                    $updated_time = GetFireUpdatedTime(
                                                        $inspection->l2_manager_verified_by,
                                                        $inspection->id,
                                                        FIRE_MOCK_DRILL_INSPECION,
                                                        WAITING_FOR_L2_VERIFICATION,
                                                    );
                                                @endphp
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($updated_time->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
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
                                                            class="form-label ">{{ __('inspection.level_two_manager_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection->level_two_manager_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    @if (
                                       ( ($inspection->inspection_status == WAITING_FOR_CAPA_ACTION ||
                                            $inspection->inspection_status == L2_MANAGER_REJECTED ||
                                            $inspection->inspection_status == EHS_OFFICER_REJECTED ||
                                            $inspection->inspection_status == L1_MANAGER_REJECTED) &&
                                            (checkUserRole(ROLE_FIRE_ASSOCIATES)) ||  ($inspection->inspection_status == WAITING_FOR_CAPA_ACTION ||
                                            $inspection->inspection_status == L2_MANAGER_REJECTED ||
                                            $inspection->inspection_status == EHS_OFFICER_REJECTED ||
                                            $inspection->inspection_status == L1_MANAGER_REJECTED) &&
                                            isAdmin()))
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                            </div>
                                        </div>
                                        <form method="POST" id="capaAction"
                                            action="{{ admin_url('fire/fire-mock-drill-observation/capa/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection->id) }}"
                                                name="id">
                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
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
                                                        href="{{ admin_url('fire/fire-mock-drill-observation/list') }}"></x-button-cancel>
                                                </div>
                                            </div>
                                        </form>
                                    @endif

                                    @if ((($inspection->inspection_status == WAITING_FOR_CAPA_VERIFICATION && checkUserRole(ROLE_EHS_OFFICER))) || ($inspection->inspection_status == WAITING_FOR_CAPA_VERIFICATION && isAdmin()) )
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('fire/fire-mock-drill-observation/capa/reverify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection->id) }}"
                                                name="id">
                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                                </div>

                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
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

                                    @if (($inspection->inspection_status == WAITING_FOR_L1_VERIFICATION && checkUserRole(ROLE_L1_MANAGER)) ||($inspection->inspection_status == WAITING_FOR_L1_VERIFICATION && isAdmin()))
                                        <form method="POST" id="levelOneManager"
                                            action="{{ admin_url('fire/fire-mock-drill-observation/level-one/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection->id) }}"
                                                name="id">
                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.level_one_manager_verifcation_action') }}</h4>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
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

                                    @if (($inspection->inspection_status == WAITING_FOR_L2_VERIFICATION && checkUserRole(ROLE_L2_MANAGER)) || ($inspection->inspection_status == WAITING_FOR_L2_VERIFICATION && isAdmin()) )
                                        <form method="POST" id="levelTwoManager"
                                            action="{{ admin_url('fire/fire-mock-drill-observation/level-two/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection->id) }}"
                                                name="id">
                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">
                                                        {{ __('inspection.level_two_manager_verifcation_action') }}</h4>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
                                                </div>
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
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
        </div>

    @stop
    @push('script')
    <script>
        $('#forklistassessmentAdd').validate({
            rules: {
                remarks: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    noSpaces: true,
                },
                signature_image: {
                    required: true,
                }
            },
            messages: {
                remarks: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 100",
                },
                signature_image: {
                    required: "Signature is Required",
                }
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

        $.validator.addMethod("noSpaces", function(value) {
            return value.trim().length > 0;
        }, "Spaces are not allowed");

        $('#capaAction').validate({
            rules: {
                capa_remarks: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    noSpaces: true,
                },
                signature_image: {
                    required: true,
                }
            },
            messages: {
                capa_remarks: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 100",
                },
                signature_image: {
                    required: "Signature is Required",
                }
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

        $('#levelOneManager').validate({
            rules: {
                level_one_manager: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    noSpaces: true,
                },
                signature_image: {
                    required: true,
                }
            },
            messages: {
                level_one_manager: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 100",
                },
                signature_image: {
                    required: "Signature is Required",
                }
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

        $('#levelTwoManager').validate({
            rules: {
                level_two_manager: {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    noSpaces: true,
                },
                signature_image: {
                    required: true,
                }
            },
            messages: {
                level_two_manager: {
                    required: "Remarks is Required",
                    minlength: "Minimum Characters should be 3",
                    maxlength: "Maximum Characters should not exceed 100",
                },
                signature_image: {
                    required: "Signature is Required",
                }
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
