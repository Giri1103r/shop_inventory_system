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
                                    <h4 class="text-white">MSDS Details</h4>
                                </div>
                            </div>

                            <div class="row">

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">Document Number</label>
                                    <div class="view_data">
                                        {{ isset($msdsDetails->document_number) ? $msdsDetails->document_number : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Issue Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($msdsDetails->issue_date) ? $msdsDetails->issue_date : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Revision Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($msdsDetails->revision_date) ? $msdsDetails->revision_date : '' }}
                                    </div>
                                </div>
                                
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created at') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($msdsDetails->created_by) ? $msdsDetails->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created Date') }}</label>
                                    <div class="view_data">
                                        {{ displaydateformat(isset($msdsDetails->created_at) ? $msdsDetails->created_at : '') }}
                                    </div>
                                </div>
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
                                    <label class="form-label view_label">{{ __('Serial Number') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->serial_number) ? $item->serial_number : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Item Code') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->item_code) ? $item->item_code : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Name of Chemical') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->name_of_chemical) ? $item->name_of_chemical : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('MSDS Availability Status') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->msds_availability_status) ? $item->msds_availability_status : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Remark') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->remark) ? $item->remark : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created at') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($item->created_by) ? $item->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created Date') }}</label>
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
