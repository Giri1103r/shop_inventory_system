@extends('admin.layouts.admin')
@section('title', 'MSDS View')
@section('pageurl', admin_url('msds/list'))

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
                                    <x-button-back href="{{ admin_url('msds/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.msds') }}</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($msdsDetails->document_number) ? $msdsDetails->document_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($msdsDetails->issue_date) ? $msdsDetails->issue_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($msdsDetails->revision_date) ? $msdsDetails->revision_date : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($msdsDetails->created_by) ? $msdsDetails->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($msdsDetails->created_at) ? $msdsDetails->created_at : '') }}
                                        </div>
                                    </div>
                                    @php
                                        $signature = GetSignature(
                                            $inspection_details->created_by,
                                            $inspection_details->id,
                                            MSDS_INSPECTION,
                                        );
                                    @endphp
                                    @if (isset($signature))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label"
                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                    style="width: 150px; margin-top: -10px;">
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            @foreach ($msdsCheckList as $item)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">MSDS CheckList</h4>
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
                                            <label class="form-label view_label">{{ __('inspection.item_code') }}</label>
                                            <div class="view_data">
                                                {{ isset($item->item_code) ? $item->item_code : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('inspection.name_of_chemical') }}</label>
                                            <div class="view_data">
                                                {{ isset($item->name_of_chemical) ? $item->name_of_chemical : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('inspection.msds_avl_sts') }}</label>
                                            <div class="view_data">
                                                {{ isset($item->msds_availability_status) ? $item->msds_availability_status : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('inspection.remarks') }}</label>
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
