@extends('admin.layouts.admin')
@section('title', 'Monthly Fire Pump House Inspection')
@section('pageurl', admin_url('fire/monthly-fire-pumphouse-inspection/list'))


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
                                        href="{{ admin_url('fire/monthly-fire-pumphouse-inspection/list') }}"></x-button-back>

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
                                                {{ isset($inspection_details->doc_no) ? $inspection_details->doc_no : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->revision_data) ? $inspection_details->revision_data : '' }}
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
                                            <label class="form-label ">{{ __('inspection.resource_code') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->resource_code) ? $inspection_details->resource_code : '' }}
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
                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">
                                                {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $user_response = json_decode($inspection_details->responses, true);
                                        $srNo = 1;
                                    @endphp

                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Check Points
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Response
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user_response as $subcategory => $questions)
                                                @foreach ($questions as $questionId => $answer)
                                                    <tr>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                                            {{ $srNo }}
                                                        </td>
                                                        <td style="border: 1px solid black; padding: 8px;">
                                                            {{ GetChecklistTypeDate($questionId) }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            @php
                                                                $responseText = $answer['response'] ?? '-';
                                                            @endphp
                                                            @if ($responseText == 'YES')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($responseText == 'NO' || $responseText == 'N/A')
                                                                <span style="color: red; font-size: 20px;">X</span>
                                                            @else
                                                                {{ $responseText }}
                                                            @endif
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            {{ $answer['remark'] ?? '-' }}
                                                        </td>
                                                    </tr>
                                                    @php $srNo++; @endphp
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="row mt-3">
                                        @if ($inspection_details->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
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
                                                @if (isset($inspection_details->approved_by))
                                                    @if ($inspection_details->verified_by == $inspection_details->approved_by)
                                                        <div class="col-md-4 mb-2">
                                                            <div class="form-group form-input">
                                                                <label
                                                                    class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                                <div class="view_data">
                                                                    {{ getUsername($inspection_details->approved_by) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $signature = GetSafetySignature(
                                                                $inspection_details->approved_by,
                                                                $inspection_details->id,
                                                                SAFETY_GALLERY_INSPECTION,
                                                            );
                                                        @endphp
                                                    @endif
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
                                                            <label
                                                                class="form-label">{{ __('inspection.remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection_details->remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
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
                                                        SAFETY_GALLERY_INSPECTION,
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
                                    </div>

                                    @if ($inspection_details->capa_ehs_remarks)
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                            </h4>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
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
                                                    SAFETY_GALLERY_INSPECTION,
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
                                                    SAFETY_GALLERY_INSPECTION,
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
                                                        class="form-label ">{{ __('inspection.level_one_manager') }}</label>
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
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->l2_manager_verified_by,
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
                                            @php
                                                $signature = GetSafetySignature(
                                                    $inspection_details->approved_by,
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
