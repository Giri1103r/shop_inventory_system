@extends('admin.layouts.admin')
@section('title', 'Observation FollowUp Approve')
@section('pageurl', admin_url('fire/checklist-observation/list'))
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
                                        href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($observation->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $observation->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
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
                                                    <label class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->sr_no }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Resource Code -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->equipment_code }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Department -->
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('Unit') }}</label>
                                                    <div class="view_data">
                                                        {{ getUnitname($observation->unit_id) }}
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.department') }}</label>
                                                    <div class="view_data">
                                                        {{ GetDeptName($observation->department_id) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('Name of Equipment') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->equipment_name }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('Resource Code of Equipment') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->equipment_code }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('Observation') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->observation }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('Date of Observation / Inspection') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($observation->date) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('Observation of the Month') }}</label>
                                                    <div class="view_data">
                                                        {{ $observation->month }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>

                                @if (
                                    $observation->observation_status == WAITING_FOR_EHS_OFFICER_VERIFICATION ||
                                        $observation->observation_status == EHS_OFFICER_REJECTED ||
                                        $observation->observation_status == L1_MANAGER_REJECTED || $observation->observation_status == L2_MANAGER_REJECTED)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                            </div>
                                        </div>
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('fire/checklist-observation/ehsofficer/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($observation->inspectionid) }}"
                                                name="id">
                                            <input type="hidden" value="{{ encryptId($observation->observationid) }}"
                                                name="observation_id">
                                            <div class="row">

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
                                                                                class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Why 2</td>
                                                                        <td><i class="fas fa-arrow-right text-primary"></i>
                                                                        </td>
                                                                        <td><input type="text" name="why_2"
                                                                                class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Why 3</td>
                                                                        <td><i class="fas fa-arrow-right text-primary"></i>
                                                                        </td>
                                                                        <td><input type="text" name="why_3"
                                                                                class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Why 4</td>
                                                                        <td><i class="fas fa-arrow-right text-primary"></i>
                                                                        </td>
                                                                        <td><input type="text" name="why_4"
                                                                                class="form-control"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Why 5</td>
                                                                        <td><i class="fas fa-arrow-right text-primary"></i>
                                                                        </td>
                                                                        <td><input type="text" name="why_5"
                                                                                class="form-control"></td>
                                                                    </tr>

                                                                    <tr class="table-light fw-semibold">
                                                                        <td>Main Root Cause</td>
                                                                        <td><i class="fas fa-arrow-right text-danger"></i>
                                                                        </td>
                                                                        <td><input type="text" name="main_root_cause"
                                                                                class="form-control"></td>
                                                                    </tr>

                                                                    <tr class="table-light fw-semibold">
                                                                        <td>Corrective & Preventive Action</td>
                                                                        <td><i class="fas fa-arrow-right text-success"></i>
                                                                        </td>
                                                                        <td><input type="text" name="corrective_action"
                                                                                class="form-control"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label for="responsible_person_id"
                                                                class="form-label require">Responsible Person</label>
                                                            <select name="responsible_person_id"
                                                                id="responsible_person_id"
                                                                class="form-control single-select" style="width: 100%">
                                                                <option value="">Select Responsible Person</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mt-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Target Date</label>
                                                            <input type="text" name="target_date" id="target_date"
                                                                class="form-control">
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 form-group form-input mb-2">
                                                        <label class="form-label ">{{ __('inspection.name') }}</label>
                                                        <input type="text" name="ehs_verify_by" id = "name"
                                                            class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                            readonly>
                                                    </div>
                                                    <div class="col-md-4 form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <input type="text" name="date" id = "date"
                                                            class="form-control" value="{{ todayDate() }}" readonly>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-md-12 mt-2 form-input">
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
                                                </div> --}}
                                                {{-- 

                                                <div class="col-md-12 mb-2 form-input" id="remarks">
                                                    <label for="remarks" class="form-label">Remarks</label>
                                                    <textarea id="remarks" class="form-control" rows="3" placeholder="Please Enter Remarks" name="remarks"></textarea>
                                                </div> --}}
                                                <div class="submit-button" style="text-align: right;">
                                                    <button class="btn btn-success">Verify</button>
                                                    <x-button-cancel
                                                        href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-cancel>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <div class="basic-form mx-3">
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
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
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
                                    </div>
                                @endif

                                @if ($observation->observation_status == WAITING_FOR_CAPA_ACTION)
                                    <div class="basic-form mx-3">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                            </div>
                                        </div>
                                        <form method="POST" id="capaAction"
                                            action="{{ admin_url('fire/checklist-observation/capa/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($observation->inspectionid) }}"
                                                name="id">
                                            <input type="hidden" value="{{ encryptId($observation->observationid) }}"
                                                name="observation_id">
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
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Closed Date</label>
                                                        <input type="text" name="closed_date" id="closed_date"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class = "row">
                                                    <div class="col-md-4">
                                                        <label for="capa_status"
                                                            class="form-label ">{{ __('common.status') }}</label>
                                                        <select name="capa_status" id="capa_status" style="width: 100%"
                                                            class="form-control single-select">
                                                            <option value="">Select Status</option>
                                                            <option value="{{ encryptId(1) }}">Open</option>
                                                            <option value="{{ encryptId(2) }}">In-Progress</option>
                                                            <option value="{{ encryptId(3) }}">Closed</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="capa_remarks" class="form-label">Remarks</label>
                                                        <textarea id="capa_remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                            name="capa_remarks"></textarea>
                                                    </div>
                                                </div>
                                                <div class="submit-button mt-3" style="text-align: right;">
                                                    <x-button-submit class="submit"></x-button-submit>
                                                    <x-button-cancel
                                                        href="{{ admin_url('fire/checklist-observation/list') }}"></x-button-cancel>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @elseif (
                                    $observation->observation_status >= WAITING_FOR_CAPA_ACTION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED && $observation->observation_status != L1_MANAGER_REJECTED && $observation->observation_status != L2_MANAGER_REJECTED)
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
                                                <div class="col-md-4 mt-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Closed
                                                            Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($observation->closed_date) }}
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
                                    $observation->observation_status == WAITING_FOR_CAPA_VERIFICATION &&
                                        $observation->observation_status != EHS_OFFICER_REJECTED )
                                    <div class="basic-form mx-3">
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('fire/checklist-observation/capa/reverify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($observation->inspectionid) }}"
                                                name="id">
                                            <input type="hidden" value="{{ encryptId($observation->observationid) }}"
                                                name="observation_id">
                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">{{ __('EHS CAPA Verification') }}</h4>
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
                                    </div>
                                @elseif ($observation->observation_status >= WAITING_FOR_CAPA_VERIFICATION && $observation->observation_status != EHS_OFFICER_REJECTED && $observation->observation_status != L1_MANAGER_REJECTED && $observation->observation_status != L2_MANAGER_REJECTED)
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
                                               
                                                @php
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
                                                @endif
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

                                @if ($observation->observation_status == WAITING_FOR_L1_VERIFICATION)
                                    <div class="basic-form mx-3">
                                        <form method="POST" id="levelOneManager"
                                            action="{{ admin_url('fire/checklist-observation/level-one/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($observation->inspectionid) }}"
                                                name="id">
                                            <input type="hidden" value="{{ encryptId($observation->observationid) }}"
                                                name="observation_id">
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
                                    </div>
                                @elseif ($observation->observation_status >= WAITING_FOR_L1_VERIFICATION && $observation->observation_status != EHS_OFFICER_REJECTED && $observation->observation_status != L1_MANAGER_REJECTED && $observation->observation_status != L2_MANAGER_REJECTED)
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
                                                            {{ Displaydateformat($observation->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
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
                                                            <img src="{{ admin_url($signature) }}"
                                                                alt="Signature Upload"
                                                                style="width: 150px; margin-top: -10px;" />
                                                        </div>
                                                    </div>
                                                @endif
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

                                @if ($observation->observation_status == WAITING_FOR_L2_VERIFICATION)
                                    <div class="basic-form mx-3">
                                        <form method="POST" id="levelTwoManager"
                                            action="{{ admin_url('fire/checklist-observation/level-two/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($observation->inspectionid) }}"
                                                name="id">
                                            <input type="hidden" value="{{ encryptId($observation->observationid) }}"
                                                name="observation_id">
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
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    @stop

    @push('script')
        <script>
            $(document).ready(function() {


                flatpickr("#target_date", {
                    dateFormat: "d-m-Y",
                    minDate: "today"
                });
                flatpickr("#closed_date", {
                    dateFormat: "d-m-Y",
                    maxDate: "today"
                });

                $('#responsible_person_id').select2({
                    ajax: {
                        url: "{{ url('fire/checklist-observation/employeeName') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
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
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });
            });
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
