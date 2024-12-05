@extends('admin.layouts.admin')
@section('title', 'PPE Request Show')
@section('pageurl', admin_url('ppe_request/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">PPE Request </h4>
                                    </div>
                                </div>

                              <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                    <div class="view_data">
                                        {{ isset($pperequest->emp_id) ? $pperequest->emp_id : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($pperequest->emp_name) ? $pperequest->emp_name : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Department') }}</label>
                                    <div class="view_data">
                                        {{ getDepartment(isset($pperequest->department) ? $pperequest->department : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('PPE Name') }}</label>
                                    <div class="view_data">
                                        {{ getPpename(isset($pperequest->ppe_name) ? $pperequest->ppe_name : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                    <div class="view_data">
                                        {{ getPpeType(isset($pperequest->ppe_type) ? $pperequest->ppe_type : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                    <div class="view_data">
                                        {{ getPpeType(isset($pperequest->ppe_type) ? $pperequest->ppe_type : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('HOD Approve Status') }}</label>
                                    <div class="view_data">
                                        {{isset($pperequest->approve_status) ? $pperequest->approve_status : ''}}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('EHS Officer Approve Status') }}</label>
                                    <div class="view_data">
                                        {{isset($pperequest->ehs_approve_status) ? $pperequest->ehs_approve_status : ''}}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created By') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($pperequest->created_by) ? $pperequest->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                    <div class="view_data">
                                        {{ displayDateformat($pperequest->created_at) }}
                                    </div>
                                </div>
                                 <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Status') }}</label>
                                    <div class="view_data">
                                        @if ($pperequest->status == 1)
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


