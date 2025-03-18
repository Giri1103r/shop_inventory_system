@extends('admin.layouts.admin')
@section('title', 'RRAA View')
@section('pageurl', admin_url('rraa/ohc_fire_environment_compliance/list'))

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
                                <x-button-back href="{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}"></x-button-back>

                            </div>
                        </div>

                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">RRAA Details</h4>
                                </div>
                            </div>

                            <div class="row">

                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">Document Number</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->document_number) ? $rraa_details->document_number : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Issue Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->issue_date) ? $rraa_details->issue_date : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Revision Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($rraa_details->revision_date) ? $rraa_details->revision_date : '' }}
                                    </div>
                                </div>
                                
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created at') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created Date') }}</label>
                                    <div class="view_data">
                                        {{ displaydateformat(isset($rraa_details->created_at) ? $rraa_details->created_at : '') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                        
                        @foreach ($rraa_checkList as $item)
                        <div class="card-body ">
                            <div class="row">
                                <div class="card-header-inner">
                                    <h4 class="text-white">RRAA CheckList</h4>
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
                                    <label class="form-label view_label">{{ __('category') }}</label>
                                    <div class="view_data">
                                        {{ getCategoryname($item->id) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('OHS Compliance Index') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->ohs_compliance_index) ? $item->ohs_compliance_index : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Frequency') }}</label>
                                    <div class="view_data">
                                        {{ getFrequencyname($item->id) }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Scope') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->scope) ? $item->scope : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Responsibility') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->responsibility) ? $item->responsibility : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Authority') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->authority) ? $item->authority : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Accountability') }}</label>
                                    <div class="view_data">
                                        {{ isset($item->accountability) ? $item->accountability : '' }}
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
