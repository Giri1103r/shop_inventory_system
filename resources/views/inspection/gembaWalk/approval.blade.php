@extends('admin.layouts.admin')
@section('title', 'Gemba Walk Inspection (Safety  Observation)')
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
                                            <h4 class="text-white">Gemba Walk Inspection (Safety  Observation)</h4>
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
                                                <label class="form-label">Responsibile Person</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($gembaWalk->responsible_person_id) ? $gembaWalk->responsible_person_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Executive Person</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($gembaWalk->executive_person_id) ? $gembaWalk->executive_person_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2 col-md-4 ">
                                            <div class="form-group form-input mb-2">
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
                                            <h4 class="text-white">{{ __('inspection.checklist_details') }}</h4>
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
                                                <label class="form-label">{{ __('inspection.observation_type') }}</label>
                                                <div class="view_data">
                                                    {{ getObservationType(isset($gembaWalk->observation_type_id) ? $gembaWalk->observation_type_id : '') }}
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
                                                <label class="form-label">GembaWalk Status</label>
                                                <div class="view_data">
                                                    {{ getGembaWalkStatus(isset($gembaWalk->gemba_walk_checklist_status) ? $gembaWalk->gemba_walk_checklist_status : '') }}
                                                </div>
                                            </div>
                                        </div>

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

                                {{-- @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="capaAction"
                                        action="{{ admin_url('inspection/gemba-walk/capa/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf


                                        <input type="hidden" value="{{ encryptId($gembaWalk->gemba_walk_id) }}"
                                            name="id">

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">EHS Office Name</label>
                                                    <input type="text" name="officer_name" class="form-control"
                                                        value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Date</label>
                                                    <input type="text" name="capa_date" id="capa_date"
                                                        class="form-control" placeholder="Select Date">

                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2" id="remarkField">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Remark</label>
                                                    <textarea name="capa_remark" class="form-control"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label required">Whether the Inspection has been passed
                                                    to the Floor Manager Action?</label>
                                                <div class="mb-3 form-input">
                                                    <input type="radio" id="yes" name="is_passed"
                                                        value="1">
                                                    <label for="yes">YES</label>

                                                    <input type="radio" id="no" name="is_passed"
                                                        value="0">
                                                    <label for="no">NO</label>
                                                </div>
                                            </div>



                                            <div class="col-md-4 mb-2" id="capa_recomendation" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Upload Image</label>
                                                    <input type="file" name="capa_image" class="form-control">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2" id="verified_by" style="display: none;">
                                                <div class="form-group form-input">
                                                    <label for="gemba_walk_verified_by" class="form-label">Singnature
                                                        Upload</label>
                                                    <input type="file"
                                                        class="form-control validate-file-accept validate-file-required"
                                                        name="gemba_walk_verified_by" id="gemba_walk_verified_by">
                                                </div>
                                            </div>


                                            <div class="col-12 text-end mt-3">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-cancel
                                                    href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif --}}


                                @if (
                                    $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION ||
                                        $gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_REJECTED)
                                    <div class="row mt-3">
                                        {{-- <div class="card-header-inner">
                                            <h4 class="text-white">Recommended CAPA Action</h4>
                                        </div> --}}

                                        <div class="row">

                                            {{-- <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">EHS Officer Name</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_capa_details->name) ? $gembaWalk_ehs_capa_details->name : '' }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat(isset($gembaWalk_ehs_capa_details->date) ? $gembaWalk_ehs_capa_details->date : '') }}
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Remarks</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_capa_details->remarks) ? $gembaWalk_ehs_capa_details->remarks : '' }}
                                                    </div>
                                                </div>
                                            </div> --}}



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

                                            {{-- <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Whether the Inspection has been passed
                                                        Without the CAPA?</label>
                                                    <div class="view_data">
                                                        @if (isset($gembaWalk_ehs_capa_details->capa))
                                                            {{ $gembaWalk_ehs_capa_details->capa == 1 ? 'YES' : 'NO' }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div> --}}

                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Action Required</h4>
                                                </div>
                                            </div>
                                            <form method="POST" id="floorManagerVerification"
                                                action="{{ admin_url('inspection/gemba-walk/floor-manager/review/submit') }}"
                                                autocomplete="off" enctype="multipart/form-data">
                                                @csrf


                                                <input type="hidden" value="{{ encryptId($gembaWalk->gemba_walk_id) }}"
                                                    name="id">

                                                @if (isset($floorID))
                                                    <input type="hidden" value="{{ encryptId($floorID->id) }}"
                                                        name="floor_managerId">
                                                @endif



                                                <div class="row">

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label">{{ __('inspection.observer_person') }}</label>
                                                            <input type="text" name="officer_name"
                                                                class="form-control" value="{{ Auth::user()->name }}"
                                                                readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label">{{ __('inspection.date_of_compliance') }}</label>
                                                            <input type="text" name="capa_date" id="capa_date"
                                                                value="{{ todaydate() }}" readonly class="form-control"
                                                                placeholder="Select Date">

                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2" id="remarkField">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Remark</label>
                                                            <textarea name="capa_remark" class="form-control"></textarea>
                                                        </div>
                                                    </div>



                                                    <div class="col-md-4 mb-2" id="capa_recomendation">
                                                        <div class="form-group form-input">
                                                            <label class="form-label require">Upload Image</label>
                                                            <input type="file" name="capa_image" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label">Recommended CAPA action taken
                                                                at</label>
                                                            <input type="text" name="capa_action_date"
                                                                id="capa_action_date" value="{{ todaydate() }}"
                                                                readonly class="form-control" placeholder="Select Date">

                                                        </div>
                                                    </div>


                                                    <div class="col-12 text-end mt-3">
                                                        <x-button-submit class="submit"></x-button-submit>
                                                        <x-button-cancel
                                                            href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-cancel>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif


                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_EHS_OFFICER_VERIFICATION)
                                    <div class="row mt-3">
                                        <div class="row">

                                            <div class="row">
                                                {{-- <div class="card-header-inner">
                                                    <h4 class="text-white">Recommended CAPA Action</h4>
                                                </div>

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
                                                        <label class="form-label">Date</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat(isset($gembaWalk_ehs_capa_details->date) ? $gembaWalk_ehs_capa_details->date : '') }}
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Remarks</label>
                                                        <div class="view_data">
                                                            {{ isset($gembaWalk_ehs_capa_details->remarks) ? $gembaWalk_ehs_capa_details->remarks : '' }}
                                                        </div>
                                                    </div>
                                                </div> --}}



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

                                                {{-- <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Whether the Inspection has been passed
                                                            Without the CAPA?</label>
                                                        <div class="view_data">
                                                            @if (isset($gembaWalk_ehs_capa_details->capa))
                                                                {{ $gembaWalk_ehs_capa_details->capa == 1 ? 'YES' : 'NO' }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            </div>


                                            <div class="row mt-3">

                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Action Taken</h4>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.observer_person') }}</label>
                                                        <div class="view_data">
                                                            {{ isset($gembaWalk_ehs_floor_manager_details->name) ? $gembaWalk_ehs_floor_manager_details->name : '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.date_of_compliance') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat(isset($gembaWalk_ehs_floor_manager_details->date) ? $gembaWalk_ehs_floor_manager_details->date : '') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Remarks</label>
                                                        <div class="view_data">
                                                            {{ isset($gembaWalk_ehs_floor_manager_details->remarks) ? $gembaWalk_ehs_floor_manager_details->remarks : '' }}
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
                                                        <label class="form-label">Recommended CAPA action taken at</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat(isset($gembaWalk_ehs_floor_manager_details->capa_action_date) ? $gembaWalk_ehs_floor_manager_details->capa_action_date : '') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">EHS Officer Verification</h4>
                                                </div>

                                                <div class="row">
                                                    <form method="POST" id="ehsOfficerVerification"
                                                        action="{{ admin_url('inspection/gemba-walk/ehs-officer/review/submit') }}"
                                                        autocomplete="off" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <input type="hidden"
                                                                value="{{ encryptId($gembaWalk->gemba_walk_id) }}"
                                                                name="id">
                                                            @if (isset($ehsId))
                                                                <input type="hidden" value="{{ encryptId($ehsId->id) }}"
                                                                    name="ehs_managerId">
                                                            @endif

                                                            <div class="col-md-4 mb-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">EHS Officer Name</label>
                                                                    <input type="text" name="officer_name"
                                                                        class="form-control"
                                                                        value="{{ Auth::user()->name }}" readonly>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-2">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Date</label>
                                                                    <input type="text" name="capa_date" id="capa_date"
                                                                        value="{{ todaydate() }}" readonly
                                                                        class="form-control" placeholder="Select Date">

                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 mb-2" id="remarkField">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Remark</label>
                                                                    <textarea name="capa_remark" class="form-control"></textarea>
                                                                </div>
                                                            </div>



                                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                                @if (isset(Auth::user()->signature_upload))
                                                                    <label class="form-label"
                                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                                        alt="Signature Upload"
                                                                        style="width: 150px; margin-top:-10px">
                                                                @else
                                                                    <div class="form-input col-md-12 mb-2">
                                                                        <label class="form-label require">Signature</label>
                                                                        <input type="file"
                                                                            name="gemba_walk_verified_by"
                                                                            id="gemba_walk_verified_by"
                                                                            class="form-control form-control-sm"
                                                                            accept="image/*"
                                                                            placeholder="Enter the image">
                                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                                        <div id="gemba_walk_verified_by"
                                                                            class="text-danger"></div>
                                                                    </div>
                                                                @endif
                                                            </div> --}}




                                                            <div class="col-12 text-end mt-3">
                                                                <button type="submit" name="action" value="approve"
                                                                    class="btn btn-success">Approve</button>
                                                                <button type="submit" name="action" value="reject"
                                                                    class="btn btn-warning">Reject</button>
                                                                <x-button-cancel
                                                                    href="{{ admin_url('inspection/gemba-walk/list') }}"></x-button-cancel>
                                                            </div>
                                                        </div>




                                                    </form>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            // flatpickr("#capa_date", {
            //     dateFormat: "d-m-Y",
            //     minDate: "today"
            // });


            $('input[name="is_passed"]').change(function() {
                if ($('#yes').is(':checked')) {
                    $('#capa_recomendation').show();
                    $('#verified_by').hide();

                } else {
                    $('#capa_recomendation').hide();
                    $('#verified_by').show();


                }
            });
        });
    </script>
@endpush


@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {

            // Initialize datepicker
            // flatpickr("#capa_date", {
            //     dateFormat: "d-m-Y",
            //     minDate: "today"
            // });

            $('input[name="is_passed"]').change(function() {
                if ($('#yes').is(':checked')) {
                    $('#capa_recomendation').show();
                    $('#verified_by').hide();
                } else {
                    $('#capa_recomendation').hide();
                    $('#verified_by').show();
                }
            });

            $.validator.addMethod("filesize", function(value, element, param) {
                return this.optional(element) || (element.files[0] && element.files[0].size <= param);
            }, "File size should not exceed 2MB.");

            $.validator.addMethod("fileExtension", function(value, element, param) {
                return this.optional(element) || new RegExp("\\.(" + param + ")$", "i").test(value);
            }, "Invalid file type.");

            $.validator.addMethod("customPattern", function(value, element, pattern) {
                return this.optional(element) || pattern.test(value);
            }, "Invalid format.");

            $('#capaAction').validate({
                rules: {
                    capa_date: {
                        required: true
                    },
                    capa_remark: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,

                    },
                    is_passed: {
                        required: true
                    },
                    capa_image: {
                        required: function() {
                            return $('input[name="is_passed"]:checked').val() === '1';
                        },
                        filesize: 2097152, // 2MB
                        fileExtension: "jpg|jpeg|png|gif"
                    },
                    gemba_walk_verified_by: {
                        required: function() {
                            return $('input[name="is_passed"]:checked').val() === '1';
                        },
                        filesize: 2097152, // 2MB
                        fileExtension: "jpg|jpeg|png|gif"
                    }
                },
                messages: {
                    capa_date: {
                        required: "Date is required"
                    },
                    capa_remark: {
                        required: "Remark is required",
                        minlength: "Minimum 3 characters",
                        maxlength: "Maximum 100 characters",

                    },
                    is_passed: {
                        required: "Please select an option"
                    },
                    capa_image: {
                        required: "Image is required when passed is YES",
                        filesize: "File size should not exceed 2MB.",
                        fileExtension: "Only JPG, JPEG, PNG, and GIF files are allowed."
                    },
                    gemba_walk_verified_by: {
                        required: "Signature is required when passed is YES",
                        filesize: "File size should not exceed 2MB.",
                        fileExtension: "Only JPG, JPEG, PNG, and GIF files are allowed."
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    let wrapper = element.closest('.form-input');
                    if (wrapper.length) {
                        wrapper.append(error);
                    } else {
                        error.insertAfter(element);
                    }
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
                }
            });


            $('#floorManagerVerification').validate({
                rules: {
                    capa_date: {
                        required: true
                    },
                    capa_remark: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,

                    },
                    capa_image: {
                        required: true,
                        filesize: 15728640,
                    }
                },
                messages: {
                    capa_date: {
                        required: "Date is required"
                    },
                    capa_remark: {
                        required: "Remark is required",
                        minlength: "Minimum 3 characters",
                        maxlength: "Maximum 100 characters",

                    },
                    capa_image: {
                        required: "Image is required",
                        filesize: "File size should not exceed 15MB.",

                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    let wrapper = element.closest('.form-input');
                    if (wrapper.length) {
                        wrapper.append(error);
                    } else {
                        error.insertAfter(element);
                    }
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
                }
            });

            // EHS Officer Verification Form Validation
            $('#ehsOfficerVerification').validate({
                rules: {
                    capa_date: {
                        required: true
                    },
                    capa_remark: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,

                    },
                    gemba_walk_verified_by: {
                        required: true,
                        filesize: 15728640,
                    }
                },
                messages: {
                    capa_date: {
                        required: "Date is required"
                    },
                    capa_remark: {
                        required: "Remark is required",
                        minlength: "Minimum 3 characters",
                        maxlength: "Maximum 100 characters",
                     
                    },
                    gemba_walk_verified_by: {
                        required: "Signature is required",
                        filesize: "File size should not exceed 15MB.",

                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    let wrapper = element.closest('.form-input');
                    if (wrapper.length) {
                        wrapper.append(error);
                    } else {
                        error.insertAfter(element);
                    }
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
                }
            });



        });
    </script>
@endpush
