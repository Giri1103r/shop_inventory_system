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
                                                <label class="form-label">Shift</label>
                                                <div class="view_data">
                                                    {{ getShift(isset($gembaWalk->shift_id) ? $gembaWalk->shift_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                   

                                    <div class="m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($gembaWalk_approved_singnature) }}"
                                                alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                           
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Gemba Walk Checklist</h4>
                                        </div>
                                    </div>
                                @endif

                                @foreach ($gembaWalk_details as $gembaWalk)
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
                                                    {{ getObservationType(isset($gembaWalk->observation_type_id) ? $gembaWalk->observation_type_id : '') }}
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
                                                <label class="form-label">Date of Observation</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($gembaWalk->date_of_compliance) ? $gembaWalk->date_of_compliance : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">Observation</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk->observation_needed == '1' ? 'YES' : 'NO' }}
                                                </div>
                                            </div>
                                        </div>

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



                                        <div class="col-md-12 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Uploaded File</label>
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

                                        <hr />


                                    </div>
                                @endforeach

                                @if (
                                    $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION ||
                                        $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_REJECTED)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">CAPA Action</h4>
                                        </div>
                                    </div>
                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">EHS officer Name</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk_ehs_capa_details->name) ? $gembaWalk_ehs_capa_details->name : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Remarks</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk_ehs_capa_details->remarks }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">CAPA Action</label>
                                                <div class="view_data">
                                                    @if (isset($gembaWalk_ehs_capa_details->capa))
                                                        {{ $gembaWalk_ehs_capa_details->capa == 1 ? 'YES' : 'NO' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        {{-- <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Uploaded File</label>
                                                <div class="view_data">
                                                    @if ($gembaWalk_ehs_capa_details)
                                                        <a href="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                            target="_blank">
                                                            <img src="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                                alt="image"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif

                                                </div>
                                            </div>
                                        </div> --}}

                                     



                                    </div>
                                @endif

                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS OFFICER</h4>
                                        </div>
                                    </div>
                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">EHS Officer Name</label>
                                                <div class="view_data">
                                                    {{ isset($gembaWalk_ehs_capa_details->name) ? $gembaWalk_ehs_capa_details->name : '' }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Remarks</label>
                                                <div class="view_data">
                                                    {{ $gembaWalk_ehs_capa_details->remarks }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">CAPA Action</label>
                                                <div class="view_data">
                                                    @if (isset($gembaWalk_ehs_capa_details->capa))
                                                        {{ $gembaWalk_ehs_capa_details->capa == 1 ? 'YES' : 'NO' }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Uploaded File</label>
                                                <div class="view_data">
                                                    @if ($gembaWalk_ehs_capa_details)
                                                        <a href="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                            target="_blank">
                                                            <img src="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                                alt="image"
                                                                style="max-width: 100px; max-height: 100px;">
                                                        </a>
                                                    @else
                                                        <small class="text-muted">No file uploaded yet.</small>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Floor Manager Action</h4>
                                        </div>
                                    </div>
                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Floor Manager Name</label>
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

                                    </div>
                                @endif


                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_CLOSED)
                                    @if (isset($gembaWalk_ehs_capa_details))

                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">CAPA Action</h4>
                                            </div>
                                        </div>
                                        <div class="row">



                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">EHS officer Name</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_capa_details->name) ? $gembaWalk_ehs_capa_details->name : '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($gembaWalk_ehs_capa_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">Remarks</label>
                                                    <div class="view_data">
                                                        {{ $gembaWalk_ehs_capa_details->remarks }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">CAPA Action</label>
                                                    <div class="view_data">
                                                        @if (isset($gembaWalk_ehs_capa_details->capa))
                                                            {{ $gembaWalk_ehs_capa_details->capa == 1 ? 'YES' : 'NO' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Uploaded File</label>
                                                    <div class="view_data">
                                                        @if ($gembaWalk_ehs_capa_details)
                                                            <a href="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                                target="_blank">
                                                                <img src="{{ asset($gembaWalk_ehs_capa_details->file_path) }}"
                                                                    alt="image"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                            </a>
                                                        @else
                                                            <small class="text-muted">No file uploaded yet.</small>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif

                                    @if (isset($gembaWalk_ehs_floor_manager_details))
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Floor Manager Action</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Floor Manager Name</label>
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
                                                    {{ isset($gembaWalk_ehs_verificatioin_details->remarks) ? $gembaWalk_ehs_verificatioin_details->remarks : '' }}

                                                </div>
                                            </div>
                                        </div>



                                        <div class="m-2">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label"
                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($gembaWalk_verified_singnature) }}"
                                                    alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                               
                                            </div>
                                        </div>

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
                                                            <td>{{ getGembaWalkLogStatus($status['from_status'] ?? null) }}</td>
                                                            <td>{{ getGembaWalkLogStatus($status['to_status'] ?? null) }}</td>
                                                            <td>{{ isset($status['approved_by']) ? getUsername($status['approved_by']) : '-' }}</td>
                                                            <td>{{ $status['remarks'] ?? '-' }}</td>
                                                            <td>{{ Displaydateformat($status['created_at'] ?? null) ?? '-' }}</td>
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
