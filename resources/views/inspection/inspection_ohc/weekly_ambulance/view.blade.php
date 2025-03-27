@extends('admin.layouts.admin')
@section('title', 'Weekly Ambulance Inspection Checklist Show')
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
                                        <h4 class="text-white">Weeky Ambulance Details</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($weekAmbualance->doc_no) ? $weekAmbualance->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review date</label>
                                        <div class="view_data">
                                            {{ isset($weekAmbualance->revision_date) ? $weekAmbualance->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issued Date</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->issue_date) ? $weekAmbualance->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Next Due On</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->next_due) ? $weekAmbualance->next_due : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Inspection</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->date_of_inspection) ? $weekAmbualance->date_of_inspection : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShift(isset($weekAmbualance->shift) ? $weekAmbualance->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Location</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($weekAmbualance->location) ? $weekAmbualance->location : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($weekAmbualance->unit) ? $weekAmbualance->unit : '') }}
                                        </div>
                                    </div>
                                    @if (!empty($requestorsignature) && !empty($requestorsignature->requestor_file_path))
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label" style="display: block;">
                                                {{ __('inspection.signature') }}
                                            </label>
                                            <img src="{{ admin_url($requestorsignature->requestor_file_path) }}"
                                                alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                        </div>
                                    </div>
                                @else
                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label" style="display: block;">
                                            {{ __('inspection.signature') }}
                                        </label>
                                        <img src="{{ admin_url($signatureview->signature_upload) }}"
                                            alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                    </div>
                                </div>
                                @endif
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
                                        <h4 class="text-white">Weeky Ambulance Inspection Checklist</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary text-white">
                                                <tr>
                                                    <th colspan="3">Check Points</th>
                                                    @foreach ($getoption as $option)
                                                        <th>{{ $option }}</th>
                                                    @endforeach
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
                                                    @php $rowCount = count($checkPoints); @endphp

                                                    @foreach ($checkPoints as $index => $checkPoint)
                                                        <tr>
                                                            @if ($index == 0)
                                                                <td rowspan="{{ $rowCount }}">
                                                                    {{ getSubcategoryname($groupId) }}
                                                                </td>
                                                            @endif

                                                            <td colspan="2">{{ getSubcategoryDataname($checkPoint) }}
                                                            </td>

                                                            @foreach ($getoption as $option)
                                                                <td style="text-align: center;">
                                                                    @if ($option == 'Ok')
                                                                        @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Ok')
                                                                            <i class="fa-solid fa-check"
                                                                                style="color: #267709; width: 15px;"></i>
                                                                            <!-- Green check for Ok -->
                                                                        @else
                                                                            <i class="fa-solid fa-times"
                                                                                style="color: #d40a0a; width: 15px;"></i>
                                                                            <!-- Red cross if Ok is not selected -->
                                                                        @endif
                                                                    @elseif ($option == 'Not-Ok')
                                                                        @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Not-Ok')
                                                                            <i class="fa-solid fa-check"
                                                                                style="color: #267709; width: 15px;"></i>
                                                                            <!-- Red check for Not-Ok -->
                                                                        @else
                                                                            <i class="fa-solid fa-times"
                                                                                style="color: #d40a0a; width: 15px;"></i>
                                                                            <!-- Red cross if Ok is not selected -->
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                            @endforeach



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

                                <div class="row mt-3">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                    </div>
                                    <div class="row mb-2">
                                        @if (isset($weekAmbualance->verified_by))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($weekAmbualance->verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            @php
                                                $signature = GetSignature(
                                                    $weekAmbualance->verified_by,
                                                    $weekAmbualance->id,
                                                    OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                                                );
                                            @endphp
                                        @endif
                                        @if (isset($weekAmbualance->created_at))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($weekAmbualance->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (isset(Auth::user()->signature_upload))
                                            <label class="form-label"
                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                            <img src="{{ asset(Auth::user()->signature_upload) }}" alt="Signature Upload"
                                                style="width: 150px; margin-top: -10px;">
                                        @elseif(isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif
                                        @if ($weekAmbualance->approved_by)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.approved_by') }}</label>
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
                                            @php
                                                $signature = GetSignature(
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
                                            @endif
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
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
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
                                            @php
                                                $signature = GetSignature(
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
                                            @endif
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
                                        @if (isset(Auth::user()->signature_upload))
                                            <label class="form-label"
                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                            <img src="{{ asset(Auth::user()->signature_upload) }}" alt="Signature Upload"
                                                style="width: 150px; margin-top: -10px;">
                                        @elseif(isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif
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
                                        @php
                                            $signature = GetSignature(
                                                $weekAmbualance->l2_manager_verified_by,
                                                $weekAmbualance->id,
                                                OHC_TYPE_WEEKLY_AMBULANCE_CHECKLIST,
                                            );
                                        @endphp
                                        @if (isset(Auth::user()->signature_upload))
                                            <label class="form-label"
                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                            <img src="{{ asset(Auth::user()->signature_upload) }}" alt="Signature Upload"
                                                style="width: 150px; margin-top: -10px;">
                                        @elseif(isset($signature))
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"
                                                        style="display: block;">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                        style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif

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

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.status_log') }}</h4>

                                    </div>

                                    <div class="table-responsive">
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                <thead class="bg-secondary" style="color: #ffff">
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
                                                    @if(empty($statuslog) || $statuslog->isEmpty())
                                                        <tr>
                                                            <td colspan="7" class="text-center">No data is available</td>
                                                        </tr>
                                                    @else
                                                        @foreach ($statuslog as $log)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ getInspectionStatus($log->from_status) }}</td>
                                                                <td>{{ getInspectionStatus($log->to_status) }}</td>
                                                                <td>{{ $log->remarks ?? 'N/A' }}</td>
                                                                <td>{{ getUserName($log->approved_by) ?? '-' }}</td>
                                                                <td>{{ getUserName($log->created_by) ?? '-' }}</td>
                                                                <td>{{ displaydateformat($log->created_at) }}</td>
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
