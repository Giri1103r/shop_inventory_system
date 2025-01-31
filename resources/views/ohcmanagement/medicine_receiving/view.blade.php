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
                                <div>
                                    {{-- View of the EHS Verification --}}
                                    @if (
                                        $medicine_receiving->approve_status == STATUS_OHC_L1_EHS_VERIFICATION_PENDING ||
                                            $medicine_receiving->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING ||
                                            $medicine_receiving->approve_status == STATUS_OHC_OPEN ||
                                            $medicine_receiving->approve_status == STATUS_OHC_CLOSE)
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">EHS Verification</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername(isset($ehsverify->created_by) ? $ehsverify->created_by : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($ehsverify->created_at) ? $ehsverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                                    <div class="view_data">
                                                        {{ displaytimeformat(isset($ehsverify->created_at) ? $ehsverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-12 form-input">
                                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ isset($ehsverify->remarks) ? $ehsverify->remarks : '' }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if (
                                        $medicine_receiving->approve_status == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING ||
                                            $medicine_receiving->approve_status == STATUS_OHC_OPEN ||
                                            $medicine_receiving->approve_status == STATUS_OHC_CLOSE)
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">L1 EHS Approval</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername(isset($l1ehsverify->created_by) ? $l1ehsverify->created_by : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($l1ehsverify->created_at) ? $l1ehsverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                                    <div class="view_data">
                                                        {{ displaytimeformat(isset($l1ehsverify->created_at) ? $l1ehsverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-12 form-input">
                                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ isset($l1ehsverify->remarks) ? $l1ehsverify->remarks : '' }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    @if ($medicine_receiving->approve_status == STATUS_OHC_OPEN || $medicine_receiving->approve_status == STATUS_OHC_CLOSE)
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">EHS Head Approved</h4>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row">
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                                    <div class="view_data">
                                                        {{ getUsername(isset($ehsheadverify->created_by) ? $ehsheadverify->created_by : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($ehsheadverify->created_at) ? $ehsheadverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                                    <div class="view_data">
                                                        {{ displaytimeformat(isset($ehsheadverify->created_at) ? $ehsheadverify->created_at : '') }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-12 form-input">
                                                    <label class="form-label view_label">{{ __('Remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ isset($ehsheadverify->remarks) ? $ehsheadverify->remarks : '' }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    @endif
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
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>Stock
                                                            Request</span></td>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                            Verification Pending</span></td>
                                                    <td> {{ getUsername(isset($medicine_receiving->created_by) ? $medicine_receiving->created_by : '') }}
                                                    </td>
                                                    <td>
                                                        <p>-</p>
                                                    </td>
                                                    <td> {{ Displaydateformat(isset($medicine_receiving->created_at) ? $medicine_receiving->created_at : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>EHS
                                                        Verification Pending</span></td>
                                                        <td>
                                                            @if (isset($ehsverify['to_status']) && $ehsverify['to_status'] == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                                                <span class='badge bg-info' style='font-size: 1.0em;'>L1 EHS officer Approval Pending</span>

                                                            @else
                                                                <p>-</p>
                                                            @endif
                                                        </td>

                                                    <td> {{ getUsername(isset($ehsverify->created_by) ? $ehsverify->created_by : '') }}
                                                    </td>
                                                    <td>{{ isset($ehsverify->remarks) ? $ehsverify->remarks : '' }}</td>
                                                    <td> {{ Displaydateformat(isset($ehsverify->created_at) ? $ehsverify->created_at : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>L1 EHS
                                                        Verification Pending</span></td>
                                                        <td>
                                                            @if (isset($l1ehsverify['to_status']) && $l1ehsverify['to_status'] == STATUS_OHC_EHS_HEAD_APPROVAL_PENDING)
                                                                <span class='badge bg-info' style='font-size: 1.0em;'>EHS HEAD Approval Pending</span>

                                                            @else
                                                                <p>-</p>
                                                            @endif
                                                        </td>

                                                    <td> {{ getUsername(isset($l1ehsverify->created_by) ? $l1ehsverify->created_by : '') }}
                                                    </td>
                                                    <td>{{ isset($l1ehsverify->remarks) ? $l1ehsverify->remarks : '' }}</td>
                                                    <td> {{ Displaydateformat(isset($l1ehsverify->created_at) ? $l1ehsverify->created_at : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval Pending</span></td>
                                                        <td>
                                                            @if (isset($ehsheadverify['to_status']) && $ehsheadverify['to_status'] == STATUS_OHC_OPEN)
                                                                <span class='badge bg-success' style='font-size: 1.0em;'>Open </span>

                                                            @else
                                                                <p>-</p>
                                                            @endif
                                                        </td>

                                                    <td> {{ getUsername(isset($ehsheadverify->created_by) ? $ehsheadverify->created_by : '') }}
                                                    </td>
                                                    <td>{{ isset($ehsheadverify->remarks) ? $ehsheadverify->remarks : '' }}</td>
                                                    <td> {{ Displaydateformat(isset($ehsheadverify->created_at) ? $ehsheadverify->created_at : '') }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> <span class='badge bg-success' style='font-size: 1.0em;'>Open</span></td>
                                                        <td>
                                                            @if (isset($stockopen['to_status']) && $stockopen['to_status'] == STATUS_OHC_CLOSE)
                                                                <span class='badge bg-info' style='font-size: 1.0em;'>Closed</span>

                                                            @else
                                                                <p>-</p>
                                                            @endif
                                                        </td>

                                                    <td> {{ getUsername(isset($stockopen->created_by) ? $stockopen->created_by : '') }}
                                                    </td>
                                                    <td>{{ isset($stockopen->remarks) ? $stockopen->remarks : '' }}</td>
                                                    <td> {{ Displaydateformat(isset($stockopen->created_at) ? $stockopen->created_at : '') }}
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
