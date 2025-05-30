@extends('admin.layouts.admin')
@section('title', 'Safety Petty Logbook')
@section('pageurl', admin_url('ohc/safety-petty-logbook/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>


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
                                    <x-button-back href="{{ admin_url('ohc/safety-petty-logbook/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.safety_petty_logbook') }}</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ $document_no->doc_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($document_no->issue_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ $document_no->rev_dt }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($sfty_petty_details->created_by) ? $sfty_petty_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($sfty_petty_details->created_at) ? $sfty_petty_details->created_at : '') }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Safety Petty Logbook CheckList</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.ser_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->serial_number) ? $sfty_petty_details->serial_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Name</label>
                                        <div class="view_data">
                                            {{ ($sfty_petty_details->employee_name) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->employee_code) ? $sfty_petty_details->employee_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ getDepartment($sfty_petty_details->department) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname($sfty_petty_details->unit) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->date) ? $sfty_petty_details->date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Amount</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->amount) ? $sfty_petty_details->amount : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Description</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->description) ? $sfty_petty_details->description : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Amount Given By</label>
                                        <div class="view_data">
                                            {{ getUsername($sfty_petty_details->amount_given_by) }}
                                        </div>
                                    </div>

                                    {{-- @if (isset($signature_amount_givenby) && $signature_amount_givenby)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label" style="display: block;">Signature (Amount Given By)</label>
                                                <img src="{{ admin_url( $signature_amount_givenby->file_path) }}" alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif --}}

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Amount Received By</label>
                                        <div class="view_data">
                                            {{ getUsername($sfty_petty_details->amount_received_by) }}
                                        </div>
                                    </div>
                                    {{-- @if (isset($signature_amount_receivedby) && $signature_amount_receivedby)
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label" style="display: block;">Signature (Amount Received By)</label>
                                                <img src="{{ admin_url($signature_amount_receivedby->file_path) }}" alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif --}}
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">remark</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->remark) ? $sfty_petty_details->remark : '' }}
                                        </div>
                                    </div>
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($details->created_by) ? $details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($details->created_at) ? $details->created_at : '') }}
                                        </div>
                                    </div> --}}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
