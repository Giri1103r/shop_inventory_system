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
                                    <hr />
                                @endforeach

                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_CAPA_ACTION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
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
                                                    Without the Floor Manager Action?</label>
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
                                                    <label for="gemba_walk_verified_by" class="form-label">Singnature Upload</label>
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
                                @endif


                                @if ($gembaWalk->gemba_walk_status == GEMBA_WALK_INSPECTION_WAITING_FOR_FLOOR_MANAGER_VERIFICATION)
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">CAPA Action</h4>
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
                                                    <label class="form-label">Date</label>
                                                    <div class="view_data">
                                                        {{ isset($gembaWalk_ehs_capa_details->date) ? $gembaWalk_ehs_capa_details->date : '' }}
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

                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Floor Manager Verification</h4>
                                                </div>
                                            </div>
                                            <form method="POST" id="forklistassessmentAdd"
                                                action="{{ admin_url('inspection/gemba-walk/floor-manager/review/submit') }}"
                                                autocomplete="off" enctype="multipart/form-data">
                                                @csrf


                                                <input type="hidden" value="{{ encryptId($gembaWalk->gemba_walk_id) }}"
                                                    name="id">

                                                <div class="row">

                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label">Floor Manager Name</label>
                                                            <input type="text" name="officer_name"
                                                                class="form-control" value="{{ Auth::user()->name }}"
                                                                readonly>
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



                                                    <div class="col-md-4 mb-2" id="capa_recomendation">
                                                        <div class="form-group form-input">
                                                            <label class="form-label">Upload Image</label>
                                                            <input type="file" name="capa_image" class="form-control">
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
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">EHS OFFICER </h4>
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
                                                            {{ isset($gembaWalk_ehs_capa_details->date) ? $gembaWalk_ehs_capa_details->date : '' }}
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
                                            </div>


                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Floor Manager Verification</h4>
                                                </div>

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
                                                        <label class="form-label">Date</label>
                                                        <div class="view_data">
                                                            {{ isset($gembaWalk_ehs_floor_manager_details->date) ? $gembaWalk_ehs_floor_manager_details->date : '' }}
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
                                            </div>

                                            <div class="row mt-3">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">EHS Officer Verification</h4>
                                                </div>

                                                <div class="row">
                                                    <form method="POST" id="forklistassessmentAdd"
                                                        action="{{ admin_url('inspection/gemba-walk/ehs-officer/review/submit') }}"
                                                        autocomplete="off" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="row">
                                                            <input type="hidden"
                                                                value="{{ encryptId($gembaWalk->gemba_walk_id) }}"
                                                                name="id">

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
                                                                        class="form-control" placeholder="Select Date">

                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-2" id="remarkField">
                                                                <div class="form-group form-input">
                                                                    <label class="form-label">Remark</label>
                                                                    <textarea name="capa_remark" class="form-control"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 file-upload-block" id="file-upload-0">
                                                                <label for="gemba_walk_verified_by" class="form-label">Singnature Upload</label>
                                                                <input type="file"
                                                                    class="form-control validate-file-accept validate-file-required"
                                                                    name="gemba_walk_verified_by" id="gemba_walk_verified_by">
            
                                                            </div>


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
            flatpickr("#capa_date", {
                dateFormat: "d-m-Y",
                minDate: "today"
            });


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
