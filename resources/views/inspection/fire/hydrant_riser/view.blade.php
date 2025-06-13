@extends('admin.layouts.admin')
@section('title', 'Hydrant and Riser ')
@section('pageurl', admin_url('fire/hydrant-riser-inspection/list'))

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
                                        href="{{ admin_url('fire/hydrant-riser-inspection/list') }}"></x-button-back>
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
                                                    {{ Displaydateformat($inspection->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname($inspection->location) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Shift</label>
                                                <div class="view_data">
                                                    {{ getShift($inspection->shift_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->next_due) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection->frequency) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.upload_image') }}</label>
                                                <div class="view_data">
                                                    <img src="{{ admin_url($inspection_image) }}"
                                                        style="width:50px; height:50px;" alt="" srcset="">
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $signature = GetFireSignature(
                                                $inspection->created_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
                                            );
                                        @endphp
                                        {{-- @if (isset($signature))
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
                                    <div class="form-observation">
                                        <div class="row mt-4 form-obs">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Hydrant and Riser Inspection Observation</h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Observation</label>
                                                    <div class="view_data">
                                                        {{ $inspection->observation_needed == '1' ? 'YES' : 'NO' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>


                                    @foreach ($inspection_details as $details)
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2 col-12">
                                                    <h4 class="text-white">Hydrant And Riser Inspection Checklist</h4>
                                                </div>

                                                <!-- SR No -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.sr_no') }}</label>
                                                        <div class="view_data">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Resource Code -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.location') }}</label>
                                                        <div class="view_data">
                                                            {{ getLocationname($details->location_check_id) }}
                                                        </div>
                                                    </div>
                                                </div>



                                                <!-- Hydrant No  -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Hydrant No</label>
                                                        <div class="view_data">
                                                            {{ $details->hydrant_no }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Lugs -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Lugs</label>
                                                        <div class="view_data">
                                                            {{ $details->lugs_id == '1' ? 'Present' : 'Missing' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Rubber Washer</label>
                                                        <div class="view_data">
                                                            {{ $details->rubber_washer == '1' ? 'Intact' : 'Damaged' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Check Nut</label>
                                                        <div class="view_data">
                                                            {{ $details->check_nut == '1' ? 'Present' : 'Missing' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Spindle Wheel</label>
                                                        <div class="view_data">
                                                            {{ $details->spindle_wheel == '1' ? 'Functional' : 'Non-Functional' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Blank Cap</label>
                                                        <div class="view_data">
                                                            {{ $details->blank_cap == '1' ? 'Present' : 'Missing' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Female Coupling</label>
                                                        <div class="view_data">
                                                            {{ $details->female_coupling == '1' ? 'Functional' : 'Non-Functional' }}

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Lever</label>
                                                        <div class="view_data">
                                                            {{ $details->lever == '1' ? 'Functional' : 'Non-Functional' }}

                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Flow Test</label>
                                                        <div class="view_data">
                                                            {{ $details->flow_test }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Physical Condition -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.physical_condition') }}</label>
                                                        <div class="view_data">
                                                            @if ($details->physical_condition == GOOD)
                                                                <p>Good</p>
                                                            @elseif ($details->physical_condition == FAIR)
                                                                <p>Fair</p>
                                                            @else
                                                                <p>Poor</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Condition of ISV</label>
                                                        <div class="view_data">
                                                            {{ $details->condition_of_ivs == '1' ? 'Functional' : 'Non-Functional' }}

                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Approach</label>
                                                        <div class="view_data">
                                                            {{ $details->approach }}
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- Remarks -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->remarks }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Checked Observations -->

                                            </div>
                                        </div>
                                    @endforeach

                                    <hr>
                                </div>

                                <div class="row mt-3">
                                    @if ($inspection->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
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
                                                        HYDRANT_RISER,
                                                    );

                                                    $updated_time = GetFireUpdatedTime(
                                                        $inspection->verified_by,
                                                        $inspection->id,
                                                        HYDRANT_RISER,
                                                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
                                                    );
                                                @endphp
                                            @endif
                                            @if (isset($inspection->is_passed))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('ohc_management.capa') }}</label>
                                                        <div class="view_data">
                                                            {{ isset($inspection->is_passed) && $inspection->is_passed == 1 ? 'Yes' : 'NO' }}
                                                        </div>
                                                    </div>
                                                </div>
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

                                            {{-- @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif --}}
                                            @if (isset($inspection->approved_by))
                                                @if ($inspection->verified_by == $inspection->approved_by)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                            <div class="view_data">
                                                                {{ getUsername($inspection->approved_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php
                                                        $signature = GetFireSignature(
                                                            $inspection->approved_by,
                                                            $inspection->id,
                                                            HYDRANT_RISER,
                                                        );
                                                    @endphp
                                                    {{-- @if (isset($signature))
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label"
                                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url($signature) }}"
                                                                    alt="Signature Upload"
                                                                    style="width: 150px; margin-top: -10px;" />
                                                            </div>
                                                        </div>
                                                    @endif --}}
                                                @endif
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
                                                        <label class="form-label">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    @if (isset($inspection->capa_remarks))
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.fire_associate_action') }}</h4>
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
                                                    HYDRANT_RISER,
                                                );
                                                $updated_time = GetFireUpdatedTime(
                                                    $inspection->created_by,
                                                    $inspection->id,
                                                    HYDRANT_RISER,
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
                                            {{-- @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif --}}
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
                                </div>

                                @if ($inspection->capa_ehs_remarks)
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                        </h4>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                <div class="view_data">
                                                    {{ getUserName($inspection->verified_by) }}
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $signature = GetFireSignature(
                                                $inspection->verified_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
                                            );

                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->verified_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
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
                                        {{-- @if (isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif --}}
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
                                                HYDRANT_RISER,
                                            );
                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->l1_manager_verified_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
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
                                        {{-- @if (isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif --}}
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
                                                    class="form-label ">{{ __('inspection.level_one_manager') }}</label>
                                                <div class="view_data">
                                                    {{ getUserName($inspection->l2_manager_verified_by) }}
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $signature = GetFireSignature(
                                                $inspection->l2_manager_verified_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
                                            );
                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->l2_manager_verified_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
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
                                        {{-- @if (isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif --}}
                                        {{--    @if ($inspection->approved_by)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername($inspection->approved_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @php
                                            $signature = GetFireSignature(
                                                $inspection->approved_by,
                                                $inspection->id,
                                                HYDRANT_RISER,
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
                                        @endif --}}
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
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.status_log') }}</h4>

                                    </div>
                                    <div class="card">
                                        @if (isset($status_log) && $status_log->isNotEmpty())
                                            <div class="card-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>S.NO</th>
                                                            <th>From Status</th>
                                                            <th>To Status</th>
                                                            <th>Remarks</th>
                                                            <th>Approve / Rejected  By</th>
                                                            <th>Created By</th>
                                                            <th>Created At</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($status_log as $log)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ getInspectionStatus($log->from_status) }}</td>
                                                                <td>{{ getInspectionStatus($log->to_status) }}</td>
                                                                <td>{{ $log->remarks ?? 'N/A' }}</td>
                                                                <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                                                </td>
                                                                <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}
                                                                </td>
                                                                <td>{{ displaydateformat($log->created_at) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="card-body">
                                                <p class="text-white">{{ __('No status logs available.') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    @stop
