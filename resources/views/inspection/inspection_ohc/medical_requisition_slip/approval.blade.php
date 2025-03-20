@extends('admin.layouts.admin')
@section('title', ' Medical Requisition Slip-Floor Approval')
@section('pageurl', admin_url('ohc/medical-requisition-slip/list'))

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
                                        href="{{ admin_url('ohc/medical-requisition-slip/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medical Requisition Slip- Floor </h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($medicinerequisition->doc_no) ? $medicinerequisition->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issue Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicinerequisition->issue_date) ? $medicinerequisition->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review Date</label>
                                        <div class="view_data">
                                            {{ isset($medicinerequisition->revision_date) ? $medicinerequisition->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label"> Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicinerequisition->date) ? $medicinerequisition->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($medicinerequisition->unit) ? $medicinerequisition->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($medicinerequisition->department) ? $medicinerequisition->department : '') }}
                                        </div>
                                    </div>

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
                                                    <th>S.No</th>
                                                    <th>Medicine Name</th>
                                                    <th>Freeze Quantity</th>
                                                    <th>Quantity</th>
                                                    <th>Remarks</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($medicine_requisition_floor_checklist->isEmpty())
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($medicine_requisition_floor_checklist as $data)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                            <td>{{ $data->quantity }}</td>
                                                            <td>{{ $data->freeze_quantity }}</td>
                                                            <td>{{ $data->remarks }}</td>

                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @if ($medicinerequisition->approve_status == FLOOR_MANAGER_APPROVAL_PENDING)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Floor Manager Approval Pending</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="basic-form">
                                            <form method="POST" id="requestApprovalForm"
                                                action="{{ admin_url('ohc/medical-requisition-slip/floormanagerapproval/submit') }}">
                                                @csrf
                                                <input type="hidden" name="id"
                                                    value="{{ encryptId($medicinerequisition->id) }}">
                                                <div class="">
                                                    <div class="mb-3 row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="approver_name" class="form-label require">Approver
                                                                Name</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="approver_name" readonly
                                                                value="{{ Auth::user()->name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="date" class="form-label require">Date</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="date" name="date" readonly
                                                                value="{{ date('d-m-Y H:i:s') }}">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <div class="mb-1">
                                                                <label for="remarks"
                                                                    class="form-label require">Remarks</label>
                                                                <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"></textarea>
                                                                <div class="text-danger" id="remarks_error"></div>
                                                                @error('remarks')
                                                                    <span id="remark_error"
                                                                        class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex float-end gap-2 mx-auto">
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-success w-100">Approve</button>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                {{-- <div class="row">
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                            <div class="view_data">
                                                {{ getUsername(isset($doctorapprovalview->created_by) ? $doctorapprovalview->created_by : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($doctorapprovalview->created_at) ? $doctorapprovalview->created_at : '') }}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                            <div class="view_data">
                                                {{ displaytimeformat(isset($doctorapprovalview->created_at) ? $doctorapprovalview->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">{{ __('Remarks') }}</label>
                                            <div class="view_data">
                                                {{ isset($doctorapprovalview->remarks) ? $doctorapprovalview->remarks : '' }}
                                            </div>
                                        </div>

                                    </div>
                                </div> --}}
                                @if ($medicinerequisition->approve_status == SAFETY_OFFICER_APPROVAL_PENDING)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Safety Officer Approval Pending</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="basic-form">
                                            <form method="POST" id="requestApprovalForm"
                                                action="{{ admin_url('ohc/medical-requisition-slip/safetyofficerapproval/submit') }}">
                                                @csrf
                                                <input type="hidden" name="id"
                                                    value="{{ encryptId($medicinerequisition->id) }}">
                                                <div class="">
                                                    <div class="mb-3 row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="approver_name" class="form-label require">Approver
                                                                Name</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="approver_name" readonly
                                                                value="{{ Auth::user()->name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="date" class="form-label require">Date</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="date" name="date" readonly
                                                                value="{{ date('d-m-Y H:i:s') }}">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <div class="mb-1">
                                                                <label for="remarks"
                                                                    class="form-label require">Remarks</label>
                                                                <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"></textarea>
                                                                <div class="text-danger" id="remarks_error"></div>
                                                                @error('remarks')
                                                                    <span id="remark_error"
                                                                        class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex float-end gap-2 mx-auto">
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-success w-100">Approve</button>

                                                </div>
                                            </form>
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
