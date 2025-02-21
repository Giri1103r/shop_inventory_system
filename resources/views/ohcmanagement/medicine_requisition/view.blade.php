@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition Show')
@section('pageurl', admin_url('ohc/medicine-requisition/list'))


@section('content')
    <div class="clearfix">
    </div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medicine Requisition</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Requisition ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($user_medicine_requisition->req_id) ? $user_medicine_requisition->req_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($user_medicine_requisition->unit_id) ? $user_medicine_requisition->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getdepartment(isset($user_medicine_requisition->department_id) ? $user_medicine_requisition->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Request date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_requisition->request_date) ? $user_medicine_requisition->request_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created at') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($user_medicine_requisition->created_by) ? $user_medicine_requisition->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_requisition->created_at) ? $user_medicine_requisition->created_at : '') }}
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
                                                    <th>Available Quantity</th>
                                                    <th>Quantity</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @if ($medicine_requisition->isEmpty())
                                                    <tr>
                                                        <td colspan="5" class="text-center">No data available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($medicine_requisition as $data)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                            <td>{{ $data->available_quantity }}</td>
                                                            <td>{{ $data->quantity }}</td>
                                                            <td>{{ $data->remarks }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status Logs</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>From Status</th>
                                                    <th>To Status</th>
                                                    <th>Remarks</th>
                                                    <th>Approver Name</th>
                                                    <th>Approver Date</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($logdata as $log)
                                                <tr>
                                                    <td>
                                                        @if($log['from_status'] == STATUS_OHC_STOCK_REQUEST)
                                                            <span class='badge bg-info' style='font-size: 1.0em;'>Stock Requested</span>
                                                            @elseif($log['from_status'] == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                                                            <span class='badge bg-info' style='font-size: 1.0em;'>Paramedics Approval Pending</span>
                                                        @elseif($log['from_status'] == STATUS_OHC_PARAMEDICS_APPROVED)
                                                            <span class='badge bg-success' style='font-size: 1.0em;'>Paramedics Approved</span>
                                                        @elseif($log['from_status'] == STATUS_OHC_PARAMEDICS_REJECTED)
                                                            <span class='badge bg-info' style='font-size: 1.0em;'>Paramedics Approval Pending</span>
                                                        @elseif($log['from_status'] == STATUS_OHC_CLOSE)
                                                            <span class='badge bg-info' style='font-size: 1.0em;'>Paramedics Approved</span>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        @if($log['to_status'] == STATUS_OHC_PARAMEDICS_APPROVAL_PENDING)
                                                            <span class='badge bg-info' style='font-size: 1.0em;'>Paramedics Approval Pending</span>
                                                        @elseif($log['to_status'] == STATUS_OHC_PARAMEDICS_APPROVED)
                                                            <span class='badge bg-success' style='font-size: 1.0em;'>Paramedics Approved</span>
                                                        @elseif($log['to_status'] == STATUS_OHC_PARAMEDICS_REJECTED)
                                                            <span class='badge bg-danger' style='font-size: 1.0em;'>Paramedics Rejected</span>
                                                        @elseif($log['to_status'] == STATUS_OHC_CLOSE)
                                                            <span class='badge bg-success' style='font-size: 1.0em;'>Close</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ isset($log['remarks']) ? $log['remarks'] : '-' }}</td>
                                                    <td>{{ isset($log['created_by']) ? getUsername($log['created_by']) : '-' }}</td>
                                                    <td>{{ isset($log['created_at']) ? Displaydateformat($log['created_at']) : '-' }}</td>
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

    @stop
