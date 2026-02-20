@extends('admin.layouts.admin')
@section('title', 'PPE Stock Inventory View')
@section('pageurl', admin_url('ppe_stock_inventory/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ppe_stock_inventory/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{__('ppe_management.ppe_stock_inventory')}}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.org') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->org) ? $ppestock->org : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.item_code')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->item_code) ? $ppestock->item_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.inven_item_id') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->inventory_item_id) ? $ppestock->inventory_item_id : ''}}
                                        </div>

                                    </div>
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item name') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->item_name) ? $ppestock->item_name : ''}}
                                        </div>
                                    </div> --}}
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->ppe_name) ? $ppestock->ppe_name : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_sub') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->sub) ? $ppestock->sub : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_uom') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->uom) ? $ppestock->uom : ''}}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_qunatity') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->quantity) ? $ppestock->quantity : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.item_description') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->item_description) ? $ppestock->item_description : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($ppestock->created_at) }}
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
