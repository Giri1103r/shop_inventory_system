@extends('admin.layouts.admin')
@section('title', 'Safety Gallery Inspection')
@section('pageurl', admin_url('ohc/medicine-requisition/list'))


@section('content')
    <div class="clearfix">
    </div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.safety_gallery_inspection') }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">
                                                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->date_of_inspection) ? $inspection_details->date_of_inspection : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.location') }}</label>
                                            <div class="view_data">
                                                {{ getLocationname(isset($inspection_details->location) ? $inspection_details->location : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.resource_code') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->resource_code) ? $inspection_details->resource_code : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @dd($inspection_details); --}}


                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">
                                                {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
                                            </div>
                                        </div>
                                    </div>




                                    @php
                                        $signature = GetSafetySignature(
                                            $inspection_details->created_by,
                                            $inspection_details->id,
                                            SAFETY_GALLERY_INSPECTION,
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
                                    @php
                                        $user_response = json_decode($inspection_details->responses, true);
                                    @endphp
                                    <table class="container p-5" style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No
                                                </th>
                                                <th colspan="3"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Description
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Status
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $srNo = 1; @endphp
                                            @foreach ($user_response as $index => $item)
                                                <tr>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $srNo++ }}
                                                    </td>
                                                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                                                        {{ GetChecklistTypeDate($index) }}
                                                    </td>
                                                    <td
                                                        style="border: 1px solid black; padding: 8px; text-align: center; color:
                                                {{ strtoupper($item['answer']) == 'YES' ? 'green' : 'red' }};">
                                                        @if (strtoupper($item['answer']) == 'YES')
                                                            ✔
                                                        @else
                                                            ❌
                                                        @endif
                                                    </td>

                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $item['remarks'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if (
                                    $inspection_details->inspection_status == WAITING_FOR_EHS_OFFICER_VERIFICATION &&
                                        (checkUserRole(ROLE_EHS_OFFICER) || isAdmin()))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/safety-gallery-inspection/ehsofficer/verify/submit') }}"
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
                                                        <input type="file" name="signature_image" id="signature_upload"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Enter the image">
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
                                                    href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-cancel>
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
                                                        SAFETY_GALLERY_INSPECTION,
                                                    );
                                                    $updated_time = GetSafetyUpdatedTime(
                                                        $inspection_details->verified_by,
                                                        $inspection_details->id,
                                                        SAFETY_GALLERY_INSPECTION,
                                                        WAITING_FOR_EHS_OFFICER_VERIFICATION,
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
                                                @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->created_by,
                                                        $inspection_details->id,
                                                        SAFETY_GALLERY_INSPECTION,
                                                    );

                                                    $updated_time = GetSafetyUpdatedTime(
                                                        $inspection_details->created_by,
                                                        $inspection_details->id,
                                                        SAFETY_GALLERY_INSPECTION,
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
                                                @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->verified_by,
                                                        $inspection_details->id,
                                                        SAFETY_GALLERY_INSPECTION,
                                                    );

                                                    $updated_time = GetSafetyUpdatedTime(
                                                        $inspection_details->verified_by,
                                                        $inspection_details->id,
                                                        SAFETY_GALLERY_INSPECTION,
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
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->l1_manager_verified_by,
                                                    $inspection_details->id,
                                                    SAFETY_GALLERY_INSPECTION,
                                                );

                                                $updated_time = GetSafetyUpdatedTime(
                                                    $inspection_details->l1_manager_verified_by,
                                                    $inspection_details->id,
                                                    SAFETY_GALLERY_INSPECTION,
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
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->l2_manager_verified_by,
                                                    $inspection_details->id,
                                                    SAFETY_GALLERY_INSPECTION,
                                                );

                                                $updated_time = GetSafetyUpdatedTime(
                                                    $inspection_details->l2_manager_verified_by,
                                                    $inspection_details->id,
                                                    SAFETY_GALLERY_INSPECTION,
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
                                                        {{ $inspection_details->level_two_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                @if (
                                    ($inspection_details->inspection_status == WAITING_FOR_CAPA_ACTION ||
                                        $inspection_details->inspection_status == L2_MANAGER_REJECTED ||
                                        $inspection_details->inspection_status == EHS_OFFICER_REJECTED ||
                                        $inspection_details->inspection_status == L1_MANAGER_REJECTED) &&
                                        (checkUserRole(ROLE_FIRE_ASSOCIATES) || isAdmin()))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="capaAction"
                                        action="{{ admin_url('safety/safety-gallery-inspection/capa/submit') }}"
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
                                                    href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif

                                @if (
                                    $inspection_details->inspection_status == WAITING_FOR_CAPA_VERIFICATION &&
                                        (checkUserRole(ROLE_EHS_OFFICER) || isAdmin()))
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/safety-gallery-inspection/capa/reverify/submit') }}"
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

                                @if (
                                    $inspection_details->inspection_status == WAITING_FOR_L1_VERIFICATION &&
                                        (checkUserRole(ROLE_L1_MANAGER) || isAdmin()))
                                    <form method="POST" id="levelOneManager"
                                        action="{{ admin_url('safety/safety-gallery-inspection/level-one/verify/submit') }}"
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

                                @if (
                                    $inspection_details->inspection_status == WAITING_FOR_L2_VERIFICATION &&
                                        (checkUserRole(ROLE_L2_MANAGER) || isAdmin()))
                                    <form method="POST" id="levelTwoManager"
                                        action="{{ admin_url('safety/safety-gallery-inspection/level-two/verify/submit') }}"
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
                            filesize: 15728640,
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
                            filesize: "File size should not exceed 15MB",
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
                            filesize: 15728640,
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
                            filesize: "File size should not exceed 15MB",
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
                            filesize: 15728640,
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
                             filesize: "File size must be less than 15MB."
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
                           filesize: 15728640,
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
                            filesize: "File size must be less than 15MB."
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
