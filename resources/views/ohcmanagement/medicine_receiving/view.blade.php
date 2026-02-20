@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving')
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.medicine_receiving_form') }}</h4>
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
                                        <label class="form-label view_label">{{ __('ohc_management.hsn_number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->hsn_id) ? $medicine_receiving->hsn_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.pack') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->pack) ? $medicine->pack : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->quantity) ? $medicine_receiving->quantity : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.batch_number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->batch_number) ? $medicine_receiving->batch_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.rate') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->rate) ? $medicine_receiving->rate : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.expiry_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicine_receiving->expire_date) ? $medicine_receiving->expire_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.vendor_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($vendor->vendor_name) ? $vendor->vendor_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicine_receiving->created_by) ? $medicine_receiving->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.stock_entry_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicine_receiving->created_at) }}
                                        </div>
                                    </div>

                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.status_log') }}</h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">

                                            <thead>
                                                <th>{{ __('common.from_status') }}</th>
                                                <th>{{ __('common.to_status') }}</th>
                                                <th>{{ __('common.approved_by') }}</th>
                                                <th>{{ __('ohc_management.remarks') }}</th>
                                                <th>{{ __('common.created_date') }}</th>
                                            </thead>

                                            <tbody>
                                                @foreach ($medicineReceivingStockData as $status_log)
                                                    <tr>

                                                        <td>
                                                            @if ($status_log['from_status'] == STATUS_OHC_STOCK_REQUEST)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>Stock
                                                                    Requested</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                                    Officer Verification Pending</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_EHS_VERIFIED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Officer Verified</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_L1_EHS_VERIFIED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'> L1
                                                                    EHS Officer Verified</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_EHS_REJECTED)
                                                                <p class='badge bg-danger' style='font-size: 1.0em;'>EHS
                                                                    Officer Rejected</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_AGM_APPROVED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Head Approved</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>L1 EHS
                                                                    Officer Verification Pending</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_AGM_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head
                                                                    Approval Pending</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_OPEN)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>Open</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($status_log['to_status'] == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                                    Officer Verification Pending</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_EHS_VERIFIED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Officer Verified</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_EHS_REJECTED)
                                                                <p class='badge bg-danger' style='font-size: 1.0em;'>EHS
                                                                    Officer Rejected</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_L1_EHS_VERIFIED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'> L1
                                                                    EHS Officer Verified</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_AGM_APPROVED)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head
                                                                    Approved</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_OPEN)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>Open</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_AGM_REJECTED)
                                                                <p class='badge bg-danger' style='font-size: 1.0em;'>EHS
                                                                    Head Rejected</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>L1 EHS
                                                                    Officer Verification Pending</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_AGM_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>EHS Head
                                                                    Approval Pending</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_CLOSE)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>Closed
                                                                </p>
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
