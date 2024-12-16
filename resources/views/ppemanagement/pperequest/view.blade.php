@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Request View')
@section('pageurl', admin_url('ppe_request/list'))


@section('content')
    <div class="clearfix"></div>
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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>
                                    {{-- <x-button-statuslog href="{{ admin_url('ppe_request/statuslog') }}"></x-button-statuslog> --}}
                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">PPE Shoe Request </h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_id) ? $pperequest->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_name) ? $pperequest->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($pperequest->department) ? $pperequest->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item Code') }}</label>
                                        <div class="view_data">
                                            {{ getItemCode(isset($pperequest->item_code) ? $pperequest->item_code : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Name') }}</label>
                                        <div class="view_data">
                                            {{ getPpename(isset($pperequest->ppe_name) ? $pperequest->ppe_name : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                        <div class="view_data">
                                            {{ getPpeType(isset($pperequest->ppe_type) ? $pperequest->ppe_type : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($pperequest->created_by) ? $pperequest->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($pperequest->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{ __('Reason') }}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->employee_reason) ? $pperequest->employee_reason : '' }}


                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Previous History</h4>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Employee Id</th>
                                                <th>Previous applied Date</th>
                                                <th>Approval Status</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($userdata->isEmpty())
                                                <tr>
                                                    <td class="text-center" colspan="6">No data is available</td>
                                                </tr>
                                            @else
                                                @foreach ($userdata as $data)
                                                    <tr class="hover-row">
                                                        <td>{{ $data['emp_name'] }}</td>
                                                        <td>{{ $data['emp_id'] }}</td>
                                                        <td>{{ displaydateformat($data['created_at']) }}</td>
                                                        <td>{{ removeUnderScore(getStatus($data['approve_status'])) }}</td>
                                                        {{-- <td>{{ removeUnderScore(getStatus($data['ehs_approve_status'])) }} --}}
                                                        </td>
                                                        <td>{{ $data['remarks'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>

                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status log</h4>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Approved By</th>
                                                <th>Remarks</th>
                                                <th>Date</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if ($ppestatuslog->isEmpty())
                                                <tr>
                                                    <td class="text-center" colspan="5">No data is available</td>
                                                </tr>
                                            @else
                                                @foreach ($ppestatuslog as $log)
                                                    <tr class="hover-row">
                                                        <td>
                                                            @if ($log['to_status'] == STATUS_HOD_APPROVAL_PENDING)
                                                                <span class='badge bg-info'>HOD Approval Pending</span>
                                                            @elseif ($log['to_status'] == STATUS_HOD_APPROVED)
                                                                <span class='badge bg-success'>HOD Approved</span>
                                                            @elseif ($log['to_status'] == STATUS_USER_APPLIED)
                                                                <span class='badge bg-primary'>User Applied</span>
                                                            @elseif ($log['to_status'] == STATUS_HOD_REJECTED)
                                                                <span class='badge bg-danger'>HOD Rejected</span>
                                                            @elseif ($log['to_status'] == STATUS_EHS_APPROVAL_PENDING)
                                                                <span class='badge bg-info'>EHS Approval Pending</span>
                                                            @elseif ($log['to_status'] == STATUS_EHS_APPROVED)
                                                                <span class='badge bg-success'>EHS Approved</span>
                                                            @elseif ($log['to_status'] == STATUS_EHS_REJECTED)
                                                                <span class='badge bg-danger'>EHS Rejected</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ getUsername($log['created_by']) }}</td>
                                                        <td>{{ $log['remarks'] }}</td>
                                                        <td>{{ displaydateformat($log['created_at']) }}</td>

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

@stop
