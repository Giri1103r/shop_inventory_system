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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
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
                                            <label class="form-label ">{{ __('inspection.exact_location') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->excat_location) ? $inspection_details->excat_location : '' }}
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

                                    {{-- @php
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
                                    @endif --}}

                                    @php
                                        $user_response = json_decode($inspection_details->responses, true);
                                    @endphp
                                    <table class="container mb-2 p-5" style="width: 100%; border-collapse: collapse;">
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
                                    @if (
                                          !empty( $inspection_details->remarks))
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.l2_manager_verify') }}</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Approver Name</label>
                                                    <div class="view_data">
                                                        {{ getUsername(isset($inspection_details->verified_by) ? $inspection_details->verified_by : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Approved Date</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($inspection_details->verified_date) ? $inspection_details->verified_date : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <div class="view_data">
                                                        {{ isset($inspection_details->remarks) ? $inspection_details->remarks : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (
                                    !empty( $inspection_details->level_two_manager_remarks))
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_head_approval') }}</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Approver Name</label>
                                                    <div class="view_data">
                                                        {{ getUsername(isset($inspection_details->l2_manager_verified_by) ? $inspection_details->l2_manager_verified_by : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Approved Date</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($inspection_details->approved_date) ? $inspection_details->approved_date : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <div class="view_data">
                                                        {{ isset($inspection_details->level_two_manager_remarks) ? $inspection_details->level_two_manager_remarks : '' }}
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
                                                                    <td>{{ getSafetyGalleryinspectionstatus($log->from_status) }}</td>
                                                                    <td>{{ getSafetyGalleryinspectionstatus($log->to_status) }}</td>
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
