@extends('admin.layouts.admin')
@section('title', 'Safety Equipment')
@section('pageurl', admin_url('safety/fire-safety-equipment/list'))
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('safety/fire-safety-equipment/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-wrapper">
                                        <div class="row mt-4 form-set">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">{{ __('inspection.fire_safety_equipment') }}</h4>
                                            </div>
                                            {{-- <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.sr_no') }}</label>
                                                        <div class="view_data">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                    </div>
                                                </div> --}}
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.equipment_name') }}</label>
                                                    <div class="view_data">
                                                        {{ getEquipmentName($details->equipment_id) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.item_code') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->item_code }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.standard_norms') }}</label>
                                                    <div class="view_data">
                                                        @if ($details->standard_norms == STANDARD)
                                                            Standard
                                                        @elseif($details->standard_norms == NORMS)
                                                            Norms
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.equipment_category') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->equipment_category }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.unit_of_measurement') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->measurement_unit }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.minimum_order_value') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->minimum_order_level }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.economic_order_quantity') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->economic_order_quantity }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.observation_status') }}</label>
                                                    <div class="view_data">
                                                        @if ($details->observation_status == 1)
                                                            Active
                                                        @elseif($details->observation_status == 0)
                                                            Deactive
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $details->remark }}
                                                    </div>
                                                </div>
                                            </div>

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
