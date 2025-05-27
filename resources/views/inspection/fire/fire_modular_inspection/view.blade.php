@extends('admin.layouts.admin')
@section('title', 'Fire Modular Inspection')
@section('pageurl', admin_url('fire/fire-modular-inspection/checklist/list'))
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
                                        href="{{ admin_url('fire/fire-modular-inspection/checklist/list') }}"></x-button-back>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname($inspection->location) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Shift</label>
                                                <div class="view_data">
                                                    {{ getShiftName($inspection->shift) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->next_due) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection->frequency) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.upload_image') }}</label>
                                                <div class="view_data">
                                                    <img src="{{ admin_url($inspection_image) }}"
                                                        style="width:50px; height:50px;" alt="" srcset="">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- @php
                                            $signature = GetFireSignature(
                                                $inspection->created_by,
                                                $inspection->id,
                                                FIRE_MODULAR_INSPECTION,
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
                                    <div class="form-observation">
                                        <div class="row mt-4 form-obs">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Fire Modular Inspection Observation</h4>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.obs') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection->observation == 1 ? 'Yes' : ' No' }}
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
                                                    <h4 class="text-white">Fire Modular Inspection Checklist</h4>
                                                </div>

                                                <!-- SR No -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <div class="view_data">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Resource Code -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->resource_code }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Department -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                        <div class="view_data">
                                                            {{ GetDeptName($details->department) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.location') }}</label>
                                                        <div class="view_data">
                                                            {{ getLocationname($details->location) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.types_of_equipment') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->types_of_equipment }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.capacity_of_equipment') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->capacity_of_equipment }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.capacity_of_equipment') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->capacity_of_equipment }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.working_temperature') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->working_temperature }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sprinkler_head') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->sprinkler_head }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.neck_ring') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->neck_ring }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.cylinder_pressure') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->cylinder_pressure }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Remarks -->
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
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
                                                    // $signature = GetFireSignature(
                                                    //     $inspection->verified_by,
                                                    //     $inspection->id,
                                                    //     FIRE_MODULAR_INSPECTION,
                                                    // );

                                                    $updated_time = GetFireUpdatedTime(
                                                        $inspection->verified_by,
                                                        $inspection->id,
                                                        FIRE_MODULAR_INSPECTION,
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
                                                    {{-- @php
                                                        $signature = GetFireSignature(
                                                            $inspection->approved_by,
                                                            $inspection->id,
                                                            FIRE_MODULAR_INSPECTION,
                                                        );
                                                    @endphp --}}
                                                @endif
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
                                                // $signature = GetFireSignature(
                                                //     $inspection->created_by,
                                                //     $inspection->id,
                                                //     FIRE_MODULAR_INSPECTION,
                                                // );

                                                $updated_time = GetFireUpdatedTime(
                                                    $inspection->created_by,
                                                    $inspection->id,
                                                    FIRE_MODULAR_INSPECTION,
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
                                            // $signature = GetFireSignature(
                                            //     $inspection->verified_by,
                                            //     $inspection->id,
                                            //     FIRE_MODULAR_INSPECTION,
                                            // );

                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->verified_by,
                                                $inspection->id,
                                                FIRE_MODULAR_INSPECTION,
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
                                            // $signature = GetFireSignature(
                                            //     $inspection->l1_manager_verified_by,
                                            //     $inspection->id,
                                            //     FIRE_MODULAR_INSPECTION,
                                            // );

                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->l1_manager_verified_by,
                                                $inspection->id,
                                                FIRE_MODULAR_INSPECTION,
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
                                            // $signature = GetFireSignature(
                                            //     $inspection->l2_manager_verified_by,
                                            //     $inspection->id,
                                            //     FIRE_MODULAR_INSPECTION,
                                            // );

                                            $updated_time = GetFireUpdatedTime(
                                                $inspection->l2_manager_verified_by,
                                                $inspection->id,
                                                FIRE_MODULAR_INSPECTION,
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
                                        @if ($inspection->approved_by)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername($inspection->approved_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        {{-- @php
                                            $signature = GetFireSignature(
                                                $inspection->approved_by,
                                                $inspection->id,
                                                FIRE_MODULAR_INSPECTION,
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
                                                            <th>Approved By</th>
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
