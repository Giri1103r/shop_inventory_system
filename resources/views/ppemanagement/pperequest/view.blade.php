@extends('admin.layouts.admin')
@section('title', 'PPE Request ')
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
                                        <h4 class="text-white">PPE Request </h4>
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
                                            {{ (isset($pperequest->ppe_name) ? $pperequest->ppe_name : '') }}
                                        </div>
                                    </div>
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                        <div class="view_data">
                                            {{ getPpeType(isset($pperequest->ppe_type) ? $pperequest->ppe_type : '') }}
                                        </div>
                                    </div> --}}

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
                                    @if ($pperequest->ppe_image != '')
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Image</label>
                                            @if (isset($pperequest) && $pperequest && $pperequest->ppe_image)
                                                <p>
                                                    <a href="{{ asset('public/' . $pperequest->ppe_image) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('public/' . $pperequest->ppe_image) }}"
                                                            style="width: 100px" alt="image">
                                                    </a>
                                                </p>
                                            @else
                                                <p>No image is uploaded</p>
                                            @endif

                                        </div>
                                    @endif

                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{ __('Reason') }}</label>
                                        <div class="view_data">
                                            {{ !empty($pperequest->employee_reason) ? $pperequest->employee_reason : $pperequest->employee_remarks }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Previous History</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Employee Name</th>
                                                    <th>Employee ID</th>
                                                    <th>Previous Applied Date</th>
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
                                                            <td>
                                                                @if ($data->approve_status == STATUS_HOD_APPROVAL_PENDING)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>HOD Approval
                                                                        Pending</span>
                                                                @elseif ($data->approve_status == STATUS_HOD_APPROVED)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>HOD Approved</span>
                                                                @elseif ($data->approve_status == STATUS_USER_APPLIED)
                                                                    <span class='badge bg-primary'
                                                                        style='font-size: 1.0em;'>User Applied</span>
                                                                @elseif ($data->approve_status == STATUS_HOD_REJECTED)
                                                                    <span class='badge bg-danger'
                                                                        style='font-size: 1.0em;'>HOD Rejected</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_APPROVAL_PENDING)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>EHS Officer Approval
                                                                        Pending</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_APPROVED)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>EHS Officer
                                                                        Approved</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_REJECTED)
                                                                    <span class='badge bg-danger'
                                                                        style='font-size: 1.0em;'>EHS Officer
                                                                        Rejected</span>
                                                                @elseif ($data->approve_status == STATUS_ISSUED)
                                                                    <span class='badge bg-success'
                                                                        style='font-size: 1.0em;'>Issued</span>
                                                                @endif
                                                            </td>

                                                            {{-- <td>{{ removeUnderScore(getStatus($data['ehs_approve_status'])) }} --}}
                                                            </td>
                                                            <td>{{ $data['remarks'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>


                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status Logs</h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">

                                            <thead>
                                                <th>From Status</th>
                                                <th>To Status</th>
                                                <th>Approved By</th>
                                                <th>Remarks</th>
                                                <th>Created Date</th>
                                            </thead>

                                            <tbody>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>User
                                                            Applied</span></td>
                                                    <td><span class='badge bg-info' style='font-size: 1.0em;'>HOD Approval
                                                            Pending</span></td>
                                                    <td> {{ getUsername(isset($pperequest->created_by) ? $pperequest->created_by : '') }}
                                                    </td>
                                                    <td> {{ isset($pperequest->employee_reason) ? $pperequest->employee_reason : '' }}
                                                    </td>
                                                    <td> {{ displaydateformat(isset($pperequest->created_at) ? $pperequest->created_at : '') }}
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>HOD Approval
                                                            Pending</span></td>
                                                    <td>
                                                        @if (isset($hodstatuslog['to_status']) && $hodstatuslog['to_status'] == STATUS_HOD_APPROVED)
                                                            <span class='badge bg-success' style='font-size: 1.0em;'>HOD
                                                                Approved</span>
                                                        @elseif (isset($hodstatuslog['to_status']) && $hodstatuslog['to_status'] == STATUS_HOD_REJECTED)
                                                            <span class='badge bg-danger' style='font-size: 1.0em;'>HOD
                                                                Rejected</span>
                                                        @else
                                                            <p>-</p>
                                                        @endif
                                                    </td>


                                                    <td> {{ isset($hodstatuslog->created_by) && $hodstatuslog->created_by != '' ? getUsername($hodstatuslog->created_by) : '-' }}
                                                    </td>
                                                    <td> {{ isset($hodstatuslog->remarks) ? $hodstatuslog->remarks : '-' }}
                                                    </td>
                                                    <td> {{ isset($hodstatuslog->created_at) && $hodstatuslog->created_at != '' ? displaydateformat($hodstatuslog->created_at) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>EHS Offcer
                                                            Approval Pending</span></td>
                                                    <td>
                                                        @if (isset($ehsstatuslog['to_status']) && $ehsstatuslog['to_status'] == STATUS_EHS_APPROVED)
                                                            <span class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                Offcer Approved</span>
                                                    </td>
                                                @elseif (isset($ehsstatuslog['to_status']) && $ehsstatuslog['to_status'] == STATUS_EHS_REJECTED)
                                                    <span class='badge bg-danger' style='font-size: 1.0em;'>EHS Officer
                                                        Rejected</span>
                                                @else
                                                    <p> - </p>
                                                    @endif
                                                    </td>
                                                    <td> {{ isset($ehsstatuslog->created_by) && $ehsstatuslog->created_by != '' ? getUsername($ehsstatuslog->created_by) : '-' }}
                                                    </td>
                                                    <td> {{ isset($ehsstatuslog->remarks) ? $ehsstatuslog->remarks : '-' }}
                                                    </td>
                                                    <td> {{ isset($ehsstatuslog->created_at) && $ehsstatuslog->created_at != '' ? displaydateformat($ehsstatuslog->created_at) : '-' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>Store manager
                                                            Issue Pending</span></td>
                                                    <td>
                                                        @if (isset($smStatuslog['to_status']) && $smStatuslog['to_status'] == STATUS_ISSUED)
                                                            <span class='badge bg-success'
                                                                style='font-size: 1.0em;'>Issued</span>
                                                    </td>
                                                @else
                                                    <p>-</p>
                                                    @endif
                                                    </td>
                                                    <td> {{ isset($smStatuslog->created_by) && $smStatuslog->created_by != '' ? getUsername($smStatuslog->created_by) : '-' }}
                                                    </td>
                                                    <td> {{ isset($smStatuslog->remarks) ? $smStatuslog->remarks : '-' }}
                                                    </td>
                                                    <td> {{ isset($smStatuslog->created_at) && $smStatuslog->created_at != '' ? displaydateformat($smStatuslog->created_at) : '-' }}
                                                    </td>
                                                </tr>
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
