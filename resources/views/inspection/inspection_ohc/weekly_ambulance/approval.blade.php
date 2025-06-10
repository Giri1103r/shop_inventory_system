@extends('admin.layouts.admin')
@section('title', 'Weekly Ambulance Inspection Checklist')
@section('pageurl', admin_url('ohc/weekly-ambulance/inspection/checklist/list'))

@push('style')
    <style>
        .view_label {
            display: block;

        }

        .image-wrapper {
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endpush


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.weekly_ambulance_inspection') }}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.next_due') }}</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->next_due) ? $weekAmbualance->next_due : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('inspection.date_of_inspection') }}</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->date_of_inspection) ? $weekAmbualance->date_of_inspection : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.shifts') }}</label>
                                        <div class="view_data">
                                            {{ getShift(isset($weekAmbualance->shift) ? $weekAmbualance->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.location') }}</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($weekAmbualance->location) ? $weekAmbualance->location : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($weekAmbualance->unit) ? $weekAmbualance->unit : '') }}
                                        </div>
                                    </div>
                                    {{-- @if (!empty($requestorsignature) && !empty($requestorsignature->requestor_file_path))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label" style="display: block;">
                                                    {{ __('inspection.signature') }}
                                                </label>
                                                <img src="{{ admin_url($requestorsignature->requestor_file_path) }}"
                                                    alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @elseif (!empty($signatureview) && !empty($signatureview->signature_upload))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label" style="display: block;">
                                                    {{ __('inspection.signature') }}
                                                </label>
                                                <img src="{{ admin_url($signatureview->signature_upload) }}"
                                                    alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif --}}
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($weekAmbualance->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($weekAmbualance->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($weekAmbualance->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.weekly_ambulance_inspection_checklist') }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary text-white">
                                                <tr>
                                                    <th colspan="3">Check Points</th>
                                                    <th colspan="3">Status</th>
                                                    <th colspan="3">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $decodedData = json_decode($inspectionCkeclist->checklist, true);
                                                    $checkItems = $decodedData['check_item'] ?? [];
                                                    $statuses = $decodedData['status'] ?? [];
                                                    $remarks = $decodedData['remarks'] ?? [];
                                                @endphp

                                                @foreach ($checkItems as $groupId => $checkPoints)
                                                    @foreach ($checkPoints as $index => $checkPoint)
                                                        <tr>
                                                            <td colspan="3">{{ getSubcategoryDataname($checkPoint) }}
                                                            </td>

                                                            @php
                                                                $status = strtolower(
                                                                    trim($statuses[$checkPoint] ?? ''),
                                                                );
                                                            @endphp

                                                            <td colspan="3">
                                                                @if ($status === 'ok')
                                                                    <span>Ok</span>
                                                                @elseif ($status === 'not ok')
                                                                    <span>Not Ok</span>
                                                                @else
                                                                    <span>N/A</span>
                                                                @endif
                                                            </td>

                                                            <td colspan="3">
                                                                {{ $remarks[$checkPoint] ?? 'No Remarks' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>



                                        </table>
                                    </div>
                                </div>
                                @if (
                                    ($weekAmbualance->approve_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && CheckUserRole(ROLE_EHS_OFFICER)) ||
                                        ($weekAmbualance->approve_status == WAITING_FOR_EHS_OFFICER_VERIFICATION && CheckUserRole(ROLE_SUPERADMIN)))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/ehsofficer/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($weekAmbualance->id) }}" name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}

                                            <div class="col-md-12 form-input">
                                                <label class="form-label require">Whether the Inspection has been
                                                    passed Without the CAPA
                                                    ?</label>
                                                <div class="mb-3 form-input">
                                                    <input type="radio" id="yes" name="is_passed"
                                                        class="validate-radio-required" value="{{ 1 }}">
                                                    <label for="yes">YES</label>

                                                    <input type="radio" id="no" name="is_passed"
                                                        class="validate-radio-required" value="{{ 0 }}">
                                                    <label for="no">NO</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="remarks">
                                                <label for="remarks " class="form-label require">Remarks</label>
                                                <textarea id="remarks" class="form-control" rows="3" placeholder="Please Enter Remarks" name="remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <button class="btn btn-success">Verify</button>
                                                <x-button-cancel
                                                    href="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                        <div class="row mb-2">
                                            @if (isset($weekAmbualance->verified_by))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($weekAmbualance->verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetOHCSignature(
                                                        $weekAmbualance->verified_by,
                                                        $weekAmbualance->id,
                                                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                                                    );
                                                @endphp --}}
                                            @endif

                                            @if (isset($weekAmbualance->created_at))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($weekAmbualance->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            {{-- @if (isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif --}}
                                            @if ($weekAmbualance->approved_by)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUsername($weekAmbualance->approved_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($weekAmbualance->capa_recomendation))
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.capa_recomendation') }}</label>
                                                        <div class="view_data">
                                                            {{ $weekAmbualance->capa_recomendation }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $weekAmbualance->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if (isset($weekAmbualance->capa_remarks))
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.fire_associate_action') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.name') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($weekAmbualance->created_by) }}
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($weekAmbualance->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetOHCSignature(
                                                        $weekAmbualance->created_by,
                                                        $weekAmbualance->id,
                                                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
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
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.capa_action_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $weekAmbualance->capa_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if ($weekAmbualance->capa_ehs_remarks)
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                                </h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($weekAmbualance->verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($weekAmbualance->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- @php
                                                    $signature = GetOHCSignature(
                                                        $weekAmbualance->verified_by,
                                                        $weekAmbualance->id,
                                                        OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
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
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.capa_reverifcation_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $weekAmbualance->capa_ehs_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if (isset($weekAmbualance->level_one_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Level One Manager Action</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($weekAmbualance->l1_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($weekAmbualance->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- @php
                                                $signature = GetOHCSignature(
                                                    $weekAmbualance->l1_manager_verified_by,
                                                    $weekAmbualance->id,
                                                    OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
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
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $weekAmbualance->level_one_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($weekAmbualance->level_two_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.level_two_manager_action') }}
                                                </h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_two_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($weekAmbualance->l2_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($weekAmbualance->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- @php
                                                $signature = GetOHCSignature(
                                                    $weekAmbualance->l2_manager_verified_by,
                                                    $weekAmbualance->id,
                                                    OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                                                );
                                            @endphp
                                            @if (isset(Auth::user()->signature_upload))
                                                <label class="form-label"
                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                <img src="{{ Auth::user()->signature_upload }}" alt="Signature Upload"
                                                    style="width: 150px; margin-top:-10px">
                                            @elseif(isset($signature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endif --}}
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_two_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $weekAmbualance->level_two_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endif
                                @endif

                                @if (
                                    $weekAmbualance->approve_status == WAITING_FOR_CAPA_ACTION ||
                                        $weekAmbualance->approve_status == L2_MANAGER_REJECTED ||
                                        $weekAmbualance->approve_status == EHS_OFFICER_REJECTED ||
                                        ($weekAmbualance->approve_status == L1_MANAGER_REJECTED && CheckUserRole(ROLE_FIRE_ASSOCIATES)) ||
                                        ($weekAmbualance->approve_status == WAITING_FOR_CAPA_ACTION ||
                                            $weekAmbualance->approve_status == L2_MANAGER_REJECTED ||
                                            $weekAmbualance->approve_status == EHS_OFFICER_REJECTED ||
                                            ($weekAmbualance->approve_status == L1_MANAGER_REJECTED && CheckUserRole(ROLE_SUPERADMIN))))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.capa_action') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="capaAction"
                                        action="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/capa/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($weekAmbualance->id) }}"
                                            name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                            <div class="col-md-12 mb-2 form-input" id="capa_remarks">
                                                <label for="capa_remarks " class="form-label require">Remarks</label>
                                                <textarea id="capa_remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                    name="capa_remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-cancel
                                                    href="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif

                                @if (
                                    ($weekAmbualance->approve_status == WAITING_FOR_CAPA_VERIFICATION && CheckUserRole(ROLE_EHS_OFFICER)) ||
                                        ($weekAmbualance->approve_status == WAITING_FOR_CAPA_VERIFICATION && CheckUserRole(ROLE_SUPERADMIN)))
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/capa/reverify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($weekAmbualance->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                            </div>

                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                            <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                                <label for="remarks" class="form-label require">Remarks</label>
                                                <textarea id="" class="form-control" rows="3" placeholder="Please Provide Remarks" name="remarks"></textarea>
                                            </div>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif

                                @if (
                                    ($weekAmbualance->approve_status == WAITING_FOR_L1_VERIFICATION && CheckUserRole(ROLE_L1_MANAGER)) ||
                                        ($weekAmbualance->approve_status == WAITING_FOR_L1_VERIFICATION && CheckUserRole(ROLE_SUPERADMIN)))
                                    <form method="POST" id="levelOneManager"
                                        action="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/level-one/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($weekAmbualance->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_one_manager_verifcation_action') }}</h4>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                        </div>
                                        <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                            <label for="remarks" class="form-label require">Remarks</label>
                                            <textarea id="remarks" class="form-control" rows="3" placeholder="Please Provide Remarks"
                                                name="level_one_manager"></textarea>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif

                                @if (
                                    ($weekAmbualance->approve_status == WAITING_FOR_L2_VERIFICATION && CheckUserRole(ROLE_L2_MANAGER)) ||
                                        ($weekAmbualance->approve_status == WAITING_FOR_L2_VERIFICATION && CheckUserRole(ROLE_SUPERADMIN)))
                                    <form method="POST" id="levelTwoManager"
                                        action="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/level-two/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($weekAmbualance->id) }}"
                                            name="id">
                                        <div class="row mt-3">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">
                                                    {{ __('inspection.level_two_manager_verifcation_action') }}</h4>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label require">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            {{-- <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                        </div>
                                        <div class="col-md-12 mb-2 form-input" id="capa_recomendation">
                                            <label for="remarks" class="form-label require">Remarks</label>
                                            <textarea id="remarks " class="form-control" rows="3" placeholder="Please Provide Remarks"
                                                name="level_two_manager"></textarea>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-approve></x-button-approve>
                                            <x-button-reject></x-button-reject>
                                        </div>
                                    </form>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @stop
    @push('script')
        <script>
            $('#forklistassessmentAdd').validate({
                rules: {
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        noSpaces: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {
                    remarks: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 600",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File size must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
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
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });

            $.validator.addMethod("noSpaces", function(value) {
                return value.trim().length > 0;
            }, "Spaces are not allowed");

            $('#capaAction').validate({
                rules: {
                    capa_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        noSpaces: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {
                    capa_remarks: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 600",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File size must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
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
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });

            $('#levelOneManager').validate({
                rules: {
                    level_one_manager: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        noSpaces: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {
                    level_one_manager: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 600",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
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
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });

            $('#levelTwoManager').validate({
                rules: {
                    level_two_manager: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,
                        noSpaces: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {
                    level_two_manager: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 600",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File size must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
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
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {});
                }
            });
        </script>
    @endpush
