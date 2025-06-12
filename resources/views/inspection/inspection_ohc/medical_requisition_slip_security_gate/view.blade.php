@extends('admin.layouts.admin')
@section('title', ' Medical Requisition Slip- Fdo & Security Gate ')
@section('pageurl', admin_url('ohc/medical-requisition-slip/fdo-security-gate/list'))

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
                                        href="{{ admin_url('ohc/medical-requisition-slip/fdo-security-gate/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">
                                            {{ __('ohc_management.medicine_requisition_slip_security_gate_fdo') }}</h4>
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
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($medicinerequisition->unit) ? $medicinerequisition->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($medicinerequisition->department) ? $medicinerequisition->department : '') }}
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
                                    @endif --}}
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($medicinerequisition->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicinerequisition->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($medicinerequisition->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">

                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>{{ __('common.sno') }}</th>
                                                    <th>{{ __('ohc_management.medicine_name') }}</th>
                                                    <th>{{ __('ohc_management.quantity') }}</th>
                                                    <th>{{ __('ohc_management.remarks') }}</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($medicine_requisition_fdo_checklist->isEmpty())
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($medicine_requisition_fdo_checklist as $data)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                            <td>{{ $data->quantity }}</td>
                                                            <td>{{ $data->remarks ?? '-' }}</td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @if (
                                    $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVED ||
                                        $medicinerequisition->approve_status == SAFETY_OFFICER_REJECTED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('ohc_management.safety_officer_approval') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approver_name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($safetyofficer->approved_by) ? $safetyofficer->approved_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($safetyofficer->created_at) ? $safetyofficer->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($safetyofficer->created_at) ? $safetyofficer->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-12 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($safetyofficer->remarks) ? $safetyofficer->remarks : '' }}
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
                                                        <th>{{ __('common.sno') }}</th>
                                                        <th>{{ __('common.from_status') }}</th>
                                                        <th>{{ __('common.to_status') }}</th>
                                                        <th>{{ __('ohc_management.remarks') }}</th>
                                                        <th>{{ __('common.approved_by') }}</th>
                                                        <th>{{ __('common.created_by') }}</th>
                                                        <th>{{ __('common.created_date') }}</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($statuslog as $log)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getohcrequisitionfloorstatus($log->from_status) }}</td>
                                                            <td>{{ getohcrequisitionfloorstatus($log->to_status) }}</td>
                                                            <td>{{ $log->remarks ?? 'N/A' }}</td>
                                                            <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                                            <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}
                                                            </td>
                                                            <td>{{ displaydateformat($log->created_at) }}</td>
                                                        </tr>
                                                    @endforeach


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
