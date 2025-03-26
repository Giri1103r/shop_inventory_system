@extends('admin.layouts.admin')
@section('title', 'RRAA View')
@section('pageurl', admin_url('rraa/ohc_fire_environment_compliance/list'))

@section('content')

<style>
    .card-header-inner {
        padding: 11px;
    }
</style>

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
                                <x-button-back href="{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}"></x-button-back>

                            </div>
                        </div>

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">RRAA Details</h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">Document Number</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->document_number) ? $rraa_details->document_number : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Issue Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->issue_date) ? $rraa_details->issue_date : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Revision & Data') }}</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->revision_date) ? $rraa_details->revision_date : '' }}
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created by') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created Date') }}</label>
                                    <div class="view_data">
                                        {{ displaydateformat(isset($rraa_details->created_at) ? $rraa_details->created_at : '') }}
                                    </div>
                                </div>

                                @php
                                    $signature = GetSignature(
                                        $inspection_details->created_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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

                        </div>

                        @foreach ($rraa_checkList as $item)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">RRAA CheckList</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Serial Number') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->serial_number) ? $item->serial_number : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('category') }}</label>
                                    <div class="view_data">
                                        {{ getCategoryname($item->category) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('OHS Compliance Index') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->ohs_compliance_index) ? $item->ohs_compliance_index : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Frequency') }}</label>
                                    <div class="view_data">
                                        {{ getFrequencyname($item->frequency) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Scope') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->scope) ? $item->scope : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Responsibility') }}</label>
                                    <div class="view_data">
                                        {{ getUsername($item->responsibility) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Authority') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->authority) ? $item->authority : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Accountability') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->accountability) ? $item->accountability : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remark') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->remark) ? $item->remark : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created by') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($item->created_by) ? $item->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created Date') }}</label>
                                    <div class="view_data">
                                        {{ displaydateformat(isset($item->created_at) ? $item->created_at : '') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        {{-- @if ($inspection_details->inspection_status != WAITING_FOR_EHS_OFFICER_VERIFICATION)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                </div>
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
                                        $signature = GetSignature(
                                            $inspection_details->verified_by,
                                            $inspection_details->id,
                                            RRAA_INSPECTION,
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
                                            <img src="{{ admin_url($signature) }}"
                                                alt="Signature Upload"
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
                                            $signature = GetSignature(
                                                $inspection_details->approved_by,
                                                $inspection_details->id,
                                                RRAA_INSPECTION,
                                            );
                                        @endphp
                                    @endif
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
                        </div>
                        @endif --}}

                        @if (isset($inspection_details->capa_remarks))
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.fire_associate_action') }}</h4>
                                </div>
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
                                    $signature = GetSignature(
                                        $inspection_details->verified_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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
                        </div>
                        @endif

                        @if ($inspection_details->capa_ehs_remarks)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                    </h4>
                                </div>
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
                                    $signature = GetSignature(
                                        $inspection_details->verified_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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
                        </div>
                        @endif

                        @if (isset($inspection_details->level_one_manager_remarks))
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.level_one_manager_action') }}
                                    </h4>
                                </div>
                            </div>
                            <div class="row">
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
                                    $signature = GetSignature(
                                        $inspection_details->l1_manager_verified_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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
                        </div>
                        @endif
                        @if (isset($inspection_details->level_two_manager_remarks))
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.level_two_manager_action') }}
                                    </h4>
                                </div>
                            </div>
                            <div class="row">
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
                                    $signature = GetSignature(
                                        $inspection_details->l2_manager_verified_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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
                                    $signature = GetSignature(
                                        $inspection_details->approved_by,
                                        $inspection_details->id,
                                        RRAA_INSPECTION,
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
                        </div>
                        @endif
                        {{-- <div class="card-body ">
                            <div class="row mt-3">
                                <div class="card-header-inner">
                                    <h4 class="text-white">{{ __('inspection.status_log') }}</h4>
                                </div>
                            </div>
                            <div class="row">
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
                                                            <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}</td>
                                                            <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}</td>
                                                            <td>{{ displaydateformat($log->created_at) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="card-body">
                                            <p class="text-dark">{{ __('No status logs available.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div> --}}

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
