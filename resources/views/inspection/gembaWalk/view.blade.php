@extends('admin.layouts.admin')
@section('title', 'Gemba Walk')
@section('pageurl', admin_url('gemba-walk/list'))

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
                                            <h4 class="text-white">Gemba Walk Inspection</h4>
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
                                                    {{ isset($gembaWalk->document_no) ? $gembaWalk->document_no : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Issue Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->issue_date) ? $gembaWalk->issue_date : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Revision Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->revision_date) ? $gembaWalk->revision_date : '') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @foreach ($gembaWalk_details as $gembaWalk)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Gemba Walk Checklist</h4>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Gemba Walk Serial No</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->gemba_walk_checklist_no) ? $gembaWalk->gemba_walk_checklist_no : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Location ID</label>
                                                <div class="view_data">
                                                    {{ getLocationname(isset($gembaWalk->location_id) ? $gembaWalk->location_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Unit ID</label>
                                                <div class="view_data">
                                                    {{ getUnitname(isset($gembaWalk->unit_id) ? $gembaWalk->unit_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Date of Observation</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date_of_observation) ? $gembaWalk->date_of_observation : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Observation Type</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->observation_type_id) ? $gembaWalk->observation_type_id : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Description</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->description) ? $gembaWalk->description : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Hazard</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->hazard) ? $gembaWalk->hazard : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">CAPA</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk->capa) ? $gembaWalk->capa : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Responsibility ID</label>
                                                <div class="view_data">
                                                    {{ getEmployeename(isset($gembaWalk->responsibility_id) ? $gembaWalk->responsibility_id : '') }}
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
                                                <label class="form-label">Date of Observation</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date_of_compliance) ? $gembaWalk->date_of_compliance : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 mb-2">
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
                                        </div>



                                        <div class="col-md-12 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Uploaded File</label>
                                                <div class="view_data">
                                                    @if ($gembaWalk)
                                                        <a href="{{ asset($gembaWalk->file_path) }}" target="_blank">
                                                            <img src="{{ asset('public/' . $gembaWalk->file_path) }}"
                                                                alt="image" style="max-width: 100px; max-height: 100px;">
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach


                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>

                                    
                                @endif




                            </div>



                            <div class="card-body ">
                                <div class="row mt-3">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Inspection Status</h4>
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
                                                                <td>{{ getGMInspectionStatus($log->from_status) }}</td>
                                                                <td>{{ getGMInspectionStatus($log->to_status) }}</td>
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
                                                <p class="text-dark">{{ __('No status logs available.') }}</p>
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
