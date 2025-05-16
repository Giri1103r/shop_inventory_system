@extends('admin.layouts.admin')
@section('title', 'First Aid Record')
@section('pageurl', admin_url('ohc/first-aid-record/list'))

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
                                    <x-button-back href="{{ admin_url('ohc/first-aid-record/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">First Aid Record</h4>
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
                                        <label class="form-label view_label">Month</label>
                                        <div class="view_data">
                                            {{ $first_aid_details->month }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Year</label>
                                        <div class="view_data">
                                            {{ $first_aid_details->year }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($first_aid_details->created_by) ? $first_aid_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($first_aid_details->created_at) ? $first_aid_details->created_at : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Overall Total Number of First Aid</label>
                                        <div class="view_data">
                                            {{ isset($first_aid_details->overall_total_number_of_first_aid) ? $first_aid_details->overall_total_number_of_first_aid : '' }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            @foreach ($first_aid_checklist as $detail)
                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">First Aid Record CheckList</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.ser_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($detail->serial_number) ? $detail->serial_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ getDepartment($detail->department) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname($detail->unit) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">First Aid Station Number</label>
                                        <div class="view_data">
                                            {{ isset($detail->first_aid_station_number) ? $detail->first_aid_station_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">First Aid Box Number</label>
                                        <div class="view_data">
                                            {{ isset($detail->first_aid_box_number) ? $detail->first_aid_box_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Total Number of First Aid</label>
                                        <div class="view_data">
                                            {{ isset($detail->total_number_of_first_aid) ? $detail->total_number_of_first_aid : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Remark</label>
                                        <div class="view_data">
                                            {{ isset($detail->remark) ? $detail->remark : '' }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($detail->created_by) ? $detail->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($detail->created_at) ? $detail->created_at : '') }}
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
