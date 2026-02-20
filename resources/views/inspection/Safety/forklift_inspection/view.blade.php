@extends('admin.layouts.admin')
@section('title', 'Forklift Inspection')
@section('pageurl', admin_url('safety/forklift-inspection/list'))
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
                                        href="{{ admin_url('safety/forklift-inspection/list') }}"></x-button-back>
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
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- @php
                                            $signature = GetSafetySignature(
                                                $inspection_details->created_by,
                                                $inspection_details->id,
                                                FORKLIFT_INSPECTION,
                                            );
                                        @endphp --}}
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

                                        <hr>
                                        @foreach ($inspection as $details)
                                            <div class="form-wrapper">
                                                <div class="row mt-4 form-set">
                                                    <div class="card-header-inner p-2">
                                                        <h4 class="text-white">Forklift Inspection</h4>
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
                                                                class="form-label ">{{ __('inspection.department') }}</label>
                                                            <div class="view_data">
                                                                {{ getDepartment($details->department_id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                            <div class="view_data">
                                                                {{ getUnitname($details->unit_id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.identification_no') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->identification_no }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.exact_location') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->exact_location }}
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
                                                                class="form-label ">{{ __('inspection.corrective_action') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->correction_preventive_action }}
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.employee') }}</label>
                                                            <div class="view_data">
                                                                {{ getUsername($details->responsibility) }}
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
                                                                class="form-label ">{{ __('inspection.observation_status') }}</label>
                                                            <div class="view_data">
                                                                @if ($details->observation_status == 1)
                                                                    Open
                                                                @elseif($details->observation_status == 0)
                                                                    Closed
                                                                @else
                                                                    Unknown
                                                                @endif
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
                                        @if (isset($inspection_details->approval_remarks))
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">{{ __('inspection.ehs_head_approval') }}</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->updated_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->updated_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetSafetySignature(
                                                        $inspection_details->updated_by,
                                                        $inspection_details->id,
                                                        FORKLIFT_INSPECTION,
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
                                                @if ($inspection_details->observation_status)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.status') }}</label>
                                                            <div class="view_data">
                                                                {{ $inspection_details->observation_status == 3 ? 'Approved' : 'Rejeceted' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->approval_remarks }}
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
                                                                        <td>{{ getForkLiftInspectionStatus($log->from_status) }}
                                                                        </td>
                                                                        <td>{{ getForkLiftInspectionStatus($log->to_status) }}
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
