@extends('admin.layouts.admin')
@section('title', 'Safety Petty Logbook View')
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
                                            {{ isset($sfty_petty_details->document_number) ? $sfty_petty_details->document_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->issue_date) ? $sfty_petty_details->issue_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($sfty_petty_details->revision_date) ? $sfty_petty_details->revision_date : '' }}
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

                            @foreach ($sfty_petty_checklist as $item)
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
                                                {{ isset($item->serial_number) ? $item->serial_number : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Employee Name</label>
                                            <div class="view_data">
                                                {{ isset($item->employee_name) ? $item->employee_name : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">Employee Code</label>
                                            <div class="view_data">
                                                {{ isset($item->employee_code) ? $item->employee_code : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">Department</label>
                                            <div class="view_data">
                                                {{ isset($item->department) ? $item->department : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Unit</label>
                                            <div class="view_data">
                                                {{ isset($item->unit) ? $item->unit : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Date</label>
                                            <div class="view_data">
                                                {{ isset($item->date) ? $item->date : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Amount</label>
                                            <div class="view_data">
                                                {{ isset($item->amount) ? $item->amount : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Description</label>
                                            <div class="view_data">
                                                {{ isset($item->description) ? $item->description : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Amount Given By</label>
                                            <div class="view_data">
                                                {{ isset($item->amount_given_by) ? $item->amount_given_by : '' }}
                                            </div>
                                        </div>
                                        {{-- @dd($signature_given_by) --}}
                                        @if (isset($signature_amount) && $signature_amount)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label" style="display: block;">Signature (Amount Given By)</label>
                                                    <img src="{{ admin_url( $signature_amount->file_path) }}" alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Amount Received By</label>
                                            <div class="view_data">
                                                {{ isset($item->amount_received_by) ? $item->amount_received_by : '' }}
                                            </div>
                                        </div>
                                        @if (isset($signature_amount) && $signature_amount)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label" style="display: block;">Signature (Amount Received By)</label>
                                                    <img src="{{ admin_url($signature_amount->file_path) }}" alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">remark</label>
                                            <div class="view_data">
                                                {{ isset($item->remark) ? $item->remark : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                            <div class="view_data">
                                                {{ getUsername(isset($item->created_by) ? $item->created_by : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($item->created_at) ? $item->created_at : '') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
