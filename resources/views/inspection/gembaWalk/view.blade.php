@extends('admin.layouts.admin')
@section('title', 'Gemba Walk Inspection (Safety Observation)')
@section('pageurl', admin_url('inspection/gemba-walk/list'))

@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center"></div>
    </div>

    <div class="content-body default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                @php
                                    $gembaWalk = $gembaWalk_details->first();
                                @endphp

                                @if ($gembaWalk)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Gemba Walk Inspection (Safety Walk Observation)</h4>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Gemba Walk ID</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->gemba_walk_auto_id) ? $gembaWalk->gemba_walk_auto_id : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Gemba Walk Document No</label>
                                                <div class="view_data">
                                                    {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Issue Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Revision Date</label>
                                                <div class="view_data">
                                                    {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date) ? $gembaWalk->date : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Time</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->time) ? $gembaWalk->time : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Shift</label>
                                                <div class="view_data">
                                                    {{ getShift(isset($gembaWalk->shift_id) ? $gembaWalk->shift_id : '') }}
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Executive Person</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($gembaWalk->executive_person_id) ? $gembaWalk->executive_person_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                         <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Responsibile Person</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($gembaWalk->responsible_person_id) ? $gembaWalk->responsible_person_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2 col-md-4">
                                            <div class=" form-group form-input mb-2">
                                                <label class="form-label"
                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($gembaWalk_approved_singnature) }}"
                                                    alt="Signature Upload" style="width: 100px; margin-top:-10px">

                                            </div>
                                        </div> --}}
                                    </div>
                                @endif

                                @foreach ($gembaWalk_details as $gembaWalk)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white"> {{ __('inspection.checklist_details') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Gemba Walk Serial No</label>
                                                <div class="view_data">
                                                    {{ $loop->iteration }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label"> {{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname(isset($gembaWalk->location_id) ? $gembaWalk->location_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname(isset($gembaWalk->unit_id) ? $gembaWalk->unit_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('common.department') }}</label>
                                                <div class="view_data">
                                                    {{ getDepartment(isset($gembaWalk->department_id) ? $gembaWalk->department_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.exact_location') }}</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->exact_location) ? $gembaWalk->exact_location : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label">{{ __('inspection.date_of_observation') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date_of_observation) ? $gembaWalk->date_of_observation : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.risk_category') }}</label>
                                                <div class="view_data">
                                                    {{ getRiskCategory(isset($gembaWalk->risk_category) ? $gembaWalk->risk_category : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.description') }}</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->description) ? $gembaWalk->description : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Observation Type</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk->observation_type_id == '1' ? 'Unsafe Act' : 'Unsafe Condition' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.hazard') }}</label>
                                                @php
                                                    $hazards = explode(',', $gembaWalk->hazard);
                                                @endphp

                                                <div class="view_data">
                                                    @foreach ($hazards as $hazardId)
                                                        {{ getGembaWalkHazardName($hazardId) }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Recommended CAPA Action</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk->capa_needed == '1' ? 'YES' : 'NO' }}
                                                </div>
                                            </div>
                                        </div>
                                        @if ($gembaWalk->capa_needed == '1')
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Evidence</label>
                                                    <div class="view_data">
                                                        @if ($gembaWalk)
                                                            <a href="{{ asset($gembaWalk->file_path) }}" target="_blank">
                                                                <img src="{{ asset('public/' . $gembaWalk->file_path) }}"
                                                                    alt="image"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                            </a>
                                                        @else
                                                            <small class="text-muted">No file uploaded yet.</small>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.capa') }}</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk->capa) ? $gembaWalk->capa : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="col-md-8 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_verificatioin_details->remarks) ? $gembaWalk_ehs_verificatioin_details->remarks : '-' }}

                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.observer_person') }}</label>

                                                @php
                                                    $responsibility_id = explode(',', $gembaWalk->responsibility_id);
                                                @endphp

                                                <div class="view_data">
                                                    @foreach ($responsibility_id as $responsibilityId)
                                                        {{ getUsername($responsibilityId) }}@if (!$loop->last)
                                                            ,
                                                        @endif
                                                    @endforeach
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">GembaWalk Status</label>
                                                <div class="view_data">
                                                    {{ getGembaWalkStatus(isset($gembaWalk->gemba_walk_checklist_status) ? $gembaWalk->gemba_walk_checklist_status : '') }}
                                                </div>
                                            </div>
                                        </div>



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Remark</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->remark) ? $gembaWalk->remark : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Name Of the Observer</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($gembaWalk->created_by) ? $gembaWalk->created_by : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Date of Observation</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date_of_compliance) ? $gembaWalk->date_of_compliance : '') }}
                                                </div>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">Observation</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk->observation_needed == '1' ? 'YES' : 'NO' }}
                                                </div>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="col-md-12 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Observations</label>
                                                <div class="view_data">
                                                    @php
                                                        $observations = json_decode($gembaWalk->observation, true);
                                                    @endphp

                                                    @if (is_array($observations) && !empty($observations))
                                                        <ul>
                                                            @foreach ($observations as $obs)
                                                                <li>{{ $obs }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        No observations recorded.
                                                    @endif
                                                </div>
                                            </div>
                                        </div> --}}








                                    </div>
                                @endforeach



                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION)
                                    {{-- <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Recommended CAPA Action</h4>
                                        </div>
                                    </div> --}}


                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.observer_action') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.observer_name') }}</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk_ehs_floor_manager_details->name) ? $gembaWalk_ehs_floor_manager_details->name : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Remarks</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk_ehs_floor_manager_details->remarks }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Uploaded File</label>
                                                <div class="view_data">
                                                    @if ($gembaWalk_ehs_floor_manager_details)
                                                        <a href="{{ asset($gembaWalk_ehs_floor_manager_details->file_path) }}"
                                                            target="_blank">
                                                            <img src="{{ asset($gembaWalk_ehs_floor_manager_details->file_path) }}"
                                                                alt="image"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">{{ __('inspection.capa_action_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat(isset($gembaWalk_ehs_floor_manager_details->capa_action_date) ? $gembaWalk_ehs_floor_manager_details->capa_action_date : '') }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endif


                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_CLOSED)


                                    @if (isset($gembaWalk_ehs_floor_manager_details))
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Action Taken</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.observer_name') }}</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_floor_manager_details->name) ? $gembaWalk_ehs_floor_manager_details->name : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <div class="view_data">
                                                        {{ $gembaWalk_ehs_floor_manager_details->remarks }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Uploaded File</label>
                                                    <div class="view_data">
                                                        @if ($gembaWalk_ehs_floor_manager_details)
                                                            <a href="{{ asset($gembaWalk_ehs_floor_manager_details->file_path) }}"
                                                                target="_blank">
                                                                <img src="{{ asset($gembaWalk_ehs_floor_manager_details->file_path) }}"
                                                                    alt="image"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                            </a>
                                                        @else
                                                            <small class="text-muted">No file uploaded yet.</small>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.capa_action_date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($gembaWalk_ehs_floor_manager_details->capa_action_date) }}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif

                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Officer Approval</h4>
                                        </div>
                                    </div>
                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Approved by</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk_ehs_verificatioin_details->name) ? $gembaWalk_ehs_verificatioin_details->name : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($gembaWalk_ehs_verificatioin_details->created_at) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Remarks</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk_ehs_verificatioin_details->remarks) ? $gembaWalk_ehs_verificatioin_details->remarks : '-' }}

                                                </div>
                                            </div>
                                        </div>

                                        {{-- <div class="m-2">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label"
                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($gembaWalk_verified_singnature) }}"
                                                    alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                            </div>
                                        </div> --}}

                                    </div>
                                @endif
                            </div>


                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status logs</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>From Status</th>
                                                    <th>To Status</th>
                                                    <th>Approved By</th>
                                                    <th>Remarks</th>
                                                    <th>Date</th>

                                                </tr>
                                            </thead>

                                            <tbody>
                                                @if ($status_log->isEmpty())
                                                    <tr>
                                                        <td class="text-center" colspan="5">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($status_log as $status)
                                                        <tr>
                                                            <td>{{ getGembaWalkLogStatus($status['from_status'] ?? null) }}
                                                            </td>
                                                            <td>{{ getGembaWalkLogStatus($status['to_status'] ?? null) }}
                                                            </td>
                                                            <td>{{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}
                                                            </td>
                                                            <td>{{ $status['remarks'] ?? '-' }}</td>
                                                            <td>{{ Displaydateformat($status['created_at'] ?? null) ?? '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>

                                        </table>

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
