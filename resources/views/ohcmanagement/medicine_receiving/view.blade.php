@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving Show')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))


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
                                    <x-button-back
                                        href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medicine Receving Form</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->medicine) ? $medicine->medicine : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('HSN Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->hsn) ? $medicine->hsn : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Pack Details') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->pack) ? $medicine->pack : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->quantity) ? $medicine_receiving->quantity : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Batch Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->batch_number) ? $medicine_receiving->batch_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Rate') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->rate) ? $medicine_receiving->rate : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Expire Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicine_receiving->expire_date) ? $medicine_receiving->expire_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Vendor Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($vendor->vendor_name) ? $vendor->vendor_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicine_receiving->created_by) ? $medicine_receiving->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicine_receiving->created_at) }}
                                        </div>
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
                                                @foreach ($medicineReceivingStockData as $status_log )

                                                <tr>

                                                    <td>
                                                        @if( ($status_log['from_status']) == STATUS_OHC_STOCK_REQUEST)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Stock Requested</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>EHS Officer Verification Pending</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_VERIFIED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>EHS Officer Verified</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_L1_EHS_VERIFIED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'> L1 EHS Officer Verified</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_EHS_REJECTED)
                                                            <p class='badge bg-danger' style='font-size: 1.0em;'>EHS Officer Rejected</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_AGM_APPROVED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>L1 EHS Officer Verification Pending</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_AGM_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_OPEN)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Open</p>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(($status_log['to_status']) == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>EHS Officer Verification Pending</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_EHS_VERIFIED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>EHS Officer Verified</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_EHS_REJECTED)
                                                            <p class='badge bg-danger' style='font-size: 1.0em;'>EHS Officer Rejected</p>
                                                        @elseif( ($status_log['to_status']) == STATUS_OHC_L1_EHS_VERIFIED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'> L1 EHS Officer Verified</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_AGM_APPROVED)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approved</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_OPEN)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Open</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_AGM_REJECTED)
                                                            <p class='badge bg-danger' style='font-size: 1.0em;'>EHS Head Rejected</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>L1 EHS Officer Verification Pending</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_AGM_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</p>
                                                        @elseif(($status_log['to_status']) == STATUS_OHC_CLOSE)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>Closed</p>
                                                        @endif
                                                    </td>

                                                    <td>{{ isset($status_log['created_by']) ? getUsername($status_log['created_by']) : '-' }}
                                                    </td>
                                                    <td>{{ isset($status_log['remarks']) ? $status_log['remarks'] : '-' }}
                                                    </td>
                                                    <td>{{ null !== Displaydateformat($status_log['created_at']) ? Displaydateformat($status_log['created_at']) : '-' }}
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
