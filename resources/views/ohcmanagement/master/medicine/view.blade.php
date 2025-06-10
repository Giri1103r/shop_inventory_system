@extends('admin.layouts.admin')
@section('title', 'Medicine')
@section('pageurl', admin_url('ohc/medicine/list'))

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
                                    <x-button-back href="{{ admin_url('ohc/medicine/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.medicine_details') }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.medicine_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->medicine) ? $medicine->medicine : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.threshold_limit') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->threshold_limit) ? $medicine->threshold_limit : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.pack') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->pack) ? $medicine->pack : '' }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicine->created_by) ? $medicine->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicine->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($medicine->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                    @if ($medicine->remarks != null)
                                        <div class="mb-3 col-md-8 form-input">
                                            <label class="form-label view_label">{{ __('Remarks') }}</label>
                                            <div class="view_data">
                                                {{ isset($medicine->remarks) ? $medicine->remarks : '' }}
                                            </div>
                                        </div>
                                    @endif

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
                                                            @if ($log['from_status'] == STATUS_OHC_MEDICINE_REQUEST)
                                                                <span class='badge bg-info'
                                                                    style='font-size: 1.0em;'>Medicine Request</span>
                                                            @elseif($log['from_status'] == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)
                                                                <span class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                                    Head Apporval Pending</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            @if ($log['to_status'] == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)
                                                                <span class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                                    Head Apporval Pending</span>
                                                            @elseif($log['to_status'] == STATUS_OHC_EHS_HEAD_APPROVED)
                                                                <span class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Head Approved</span>
                                                            @endif
                                                        </td>

                                                        <td>{{ isset($log['remarks']) ? $log['remarks'] : '-' }}</td>
                                                        <td>{{ isset($log['created_by']) ? getUsername($log['created_by']) : '-' }}
                                                        </td>
                                                        <td>{{ isset($log['created_at']) ? Displaydateformat($log['created_at']) : '-' }}
                                                        </td>
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
