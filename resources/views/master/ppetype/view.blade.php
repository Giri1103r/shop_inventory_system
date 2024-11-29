@extends('admin.layouts.admin')
@section('title', 'PPE Type Master Show')
@section('pageurl', admin_url('ppe_type/list'))


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
                                <x-button-back href="{{ admin_url('ppe_type/list') }}"></x-button-back>

                            </div>
                        </div>


                        <div class="card-body ">

                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">PPE Type  Details</h4>
                                </div>
                            </div>
                            <div class="row">

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Item Code') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppetype->ppe_id) ? $ppetype->ppe_id : '' }}
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppetype->ppe_type) ? $ppetype->ppe_type : '' }}
                                    </div>
                                </div>

                             

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                    <div class="view_data">
                                        {{ getusername($ppetype->created_by) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                    <div class="view_data">
                                        {{ displayDateformat($ppetype->created_at) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.status') }}</label>
                                    <div class="view_data">
                                        @if ($ppetype->status == 1)
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
</div>

@stop

