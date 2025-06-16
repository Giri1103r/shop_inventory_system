@extends('admin.layouts.admin')
@section('title', 'Observation FollowUp')
@section('pageurl', admin_url('fire/checklist-observation/list'))

@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                        href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Observation FollowUp</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">
                                                {{ $observation->doc_no }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($observation->issue_date) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ $observation->rev_dt }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($observation->date_of_inspection) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-wrapper">
                                    <div class="row mt-4 form-set">
                                        <div class="card-header-inner p-2 col-12">
                                            <h4 class="text-white">Observation</h4>
                                        </div>

                                        <!-- SR No -->
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.sr_no') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->sr_no }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Resource Code -->
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.resource_code') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->equipment_code }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Department -->
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('Unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($observation->unit_id) }}
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.department') }}</label>
                                                <div class="view_data">
                                                    {{ GetDeptName($observation->department_id) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('Name of Equipment') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->equipment_name }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('Resource Code of Equipment') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->equipment_code }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('Observation') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->observation }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">{{ __('Date of Observation / Inspection') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($observation->date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('Observation of the Month') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->month }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if (
                                    $observation->observation_status >= WAITING_FOR_CAPA_ACTION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED &&
                                        $observation->observation_status != L1_MANAGER_REJECTED &&
                                        $observation->observation_status != L2_MANAGER_REJECTED)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                        <div class="row mt-3 whywhy">
                                            <div class="card  shadow-sm border-0">
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                                    <h4 class="text-dark mb-0">Why Why Analysis</h4>

                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered align-middle">
                                                        <thead class="table-dark text-center">
                                                            <tr>
                                                                <th style="width: 15%">Why</th>
                                                                <th style="width: 5%"></th>
                                                                <th style="width: 80%">Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="whywhyanalysisBody">
                                                            <tr>
                                                                <td>Why 1</td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i>
                                                                </td>
                                                                <td><input type="text" name="why_1"
                                                                        class="form-control"
                                                                        value="{{ $observation->why_1 }}" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Why 2</td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i>
                                                                </td>
                                                                <td><input type="text" name="why_2"
                                                                        class="form-control"
                                                                        value="{{ $observation->why_2 }}" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Why 3</td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i>
                                                                </td>
                                                                <td><input type="text" name="why_3"
                                                                        class="form-control"
                                                                        value="{{ $observation->why_3 }}" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Why 4</td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i>
                                                                </td>
                                                                <td><input type="text" name="why_4"
                                                                        class="form-control"
                                                                        value="{{ $observation->why_4 }}" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Why 5</td>
                                                                <td><i class="fas fa-arrow-right text-primary"></i>
                                                                </td>
                                                                <td><input type="text" name="why_5"
                                                                        class="form-control"
                                                                        value="{{ $observation->why_5 }}" readonly>
                                                                </td>
                                                            </tr>

                                                            <tr class="table-light fw-semibold">
                                                                <td>Main Root Cause</td>
                                                                <td><i class="fas fa-arrow-right text-danger"></i>
                                                                </td>
                                                                <td><input type="text" name="main_root_cause"
                                                                        class="form-control"
                                                                        value="{{ $observation->main_root_cause }}"
                                                                        readonly></td>
                                                            </tr>

                                                            <tr class="table-light fw-semibold">
                                                                <td>Corrective & Preventive Action</td>
                                                                <td><i class="fas fa-arrow-right text-success"></i>
                                                                </td>
                                                                <td><input type="text" name="corrective_action"
                                                                        class="form-control"
                                                                        value="{{ $observation->corrective_action }}"
                                                                        readonly></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label for="responsible_person_id" class="form-label ">Responsible
                                                        Person</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->responsible_person_id) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mt-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Target Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($observation->target_date) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->ehs_verify_by) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($observation->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if (
                                    $observation->observation_status >= WAITING_FOR_CAPA_VERIFICATION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED &&
                                        $observation->observation_status != L1_MANAGER_REJECTED &&
                                        $observation->observation_status != L2_MANAGER_REJECTED)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('CAPA Submission') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->responsible_person_id) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($observation->capa_date) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (isset($observation->closed_date))
                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.closed_date') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($observation->closed_date) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Status</label>
                                                        <div class="view_data">
                                                            {{ getObservationCAPAStatus($observation->capa_status) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('Remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $observation->capa_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                                @if (
                                    $observation->observation_status >= WAITING_FOR_L1_VERIFICATION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED &&
                                        $observation->observation_status != L1_MANAGER_REJECTED &&
                                        $observation->observation_status != L2_MANAGER_REJECTED)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('EHS CAPA Verification') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->ehs_capa_verified_by) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($observation->ehs_capa_verified_date) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- @php
                                                    $signature = GetFireSignature(
                                                        $observation->ehs_capa_verified_by,
                                                        $observation->observationid,
                                                        OBSERVATION_FOLLOWUP,
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
                                            </div>


                                            <div class="row mt-2">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('Remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $observation->ehs_capa_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                                @if (
                                    $observation->observation_status >= WAITING_FOR_L2_VERIFICATION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED &&
                                        $observation->observation_status != L1_MANAGER_REJECTED &&
                                        $observation->observation_status != L2_MANAGER_REJECTED)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_one_manager_verifcation_action') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->l1_manager_verified_by) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($observation->l1_manager_verified_date) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetFireSignature(
                                                        $observation->l2_manager_verified_by,
                                                        $observation->observationid,
                                                        OBSERVATION_FOLLOWUP,
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
                                            </div>


                                            <div class="row mt-2">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('Remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $observation->level_one_manager_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                                @if ($observation->observation_status == INSPECTION_APPROVED)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_two_manager_verifcation_action') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($observation->l2_manager_verified_by) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($observation->l2_manager_verified_date) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetFireSignature(
                                                        $observation->l2_manager_verified_by,
                                                        $observation->observationid,
                                                        OBSERVATION_FOLLOWUP,
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
                                            </div>


                                            <div class="row mt-2">
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('Remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $observation->level_two_manager_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
