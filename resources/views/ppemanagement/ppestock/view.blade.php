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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ppe_stock_inventory/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">PPE Stock Inventory </h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Org') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->org) ? $ppestock->org : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item Code') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->item_code) ? $ppestock->item_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Inventory Item Id') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->inventory_item_id) ? $ppestock->inventory_item_id : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('SUB') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->sub) ? $ppestock->sub : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('UOM') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->uom) ? $ppestock->uom : ''}}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppestock->quantity) ? $ppestock->quantity : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item Description') }}</label>
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
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Status') }}</label>
                                        <div class="view_data">
                                            @if ($ppestock->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

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
