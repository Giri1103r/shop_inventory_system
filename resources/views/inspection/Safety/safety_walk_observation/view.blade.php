@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation')
@section('pageurl', admin_url('safety/safety-walk-observation/list'))
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('safety/safety-walk-observation/list') }}"></x-button-back>
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
                                                    {{ Displaydateformat($inspection_details->date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.shifts') }}</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Month</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->month }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">{{ __('inspection.safety_walk_taken_by') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername($inspection_details->safety_walk_taken_by) }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <hr>

                                    <div class="form-wrapper">
                                        <div class="row mt-4 form-set">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Safety Walk Observation</h4>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.location') }}</label>
                                                    <div class="view_data">
                                                        {{ getLocationName($inspection_details->location_id) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.exact_location') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->excat_location }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.date_of_observation') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->observation_date) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.observation') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->observation }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.recomended_action') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->recomended_action }}
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.employee') }}</label>
                                                    @php
                                                        $person = explode(',', $inspection_details->responsible_persion);
                                                    @endphp

                                                    <div class="view_data">
                                                        @foreach ($person as $personId)
                                                            {{ getUsername($personId) }}@if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.observation_status') }}</label>
                                                    <div class="view_data">
                                                        @if ($inspection_details->observing_status == 1)
                                                            Active
                                                        @elseif($inspection_details->observing_status == 0)
                                                            Deactive
                                                        @else
                                                            Unknown
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $images = GetSafetyWalkImage($inspection_details->id);
                                            @endphp

                                            <div class="col-md-4 mb-2">
                                                @if ($images !== false)
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.image') }}</label>
                                                    <a href="{{ admin_url($images) }}" target="_blank">
                                                        <img src="{{ admin_url($images) }}" style="width: 100px;" />
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (
                                       ( $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_PENDING ||
                                            $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_APPROVED ||
                                            $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_REJECTED) && !empty($inspection_details->observer_person))
                                        <div>
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Action Required</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Responsible Person</label>
                                                        <div class="view_data">
                                                            {{ getUsername($inspection_details->observer_person) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                        <div class="view_data">
                                                            {{ displaydateformat($inspection_details->date_of_compilance) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->observer_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (
                                       ( $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_APPROVED ||
                                            $inspection_details->observation_status == SAFETY_WALK_EHS_OFFICER_REJECTED) && !empty($inspection_details->approver_id))
                                        <div>
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">EHS Officer Approval</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Approver Name</label>
                                                        <div class="view_data">
                                                            {{ getUsername($inspection_details->approver_id) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Approved Date</label>
                                                        <div class="view_data">
                                                            {{ displaydateformat($inspection_details->approver_date) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">Remarks</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->approval_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row mt-4">
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
                                                                    <td>{{ getSafetyWalkStatus($log->from_status) }}
                                                                    </td>
                                                                    <td>{{ getSafetyWalkStatus($log->to_status) }}
                                                                    </td>
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
                                                    <p class="text-white">No status logs available.</p>
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
        </div>

    @stop
    @push('script')
    @endpush
