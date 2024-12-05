@extends('admin.layouts.admin')
@section('title', 'PPE Exemption Show')
@section('pageurl', admin_url('ppe_exemption/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_exemption/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">PPE Exemption </h4>
                                    </div>
                                </div>

                              <div class="row">
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->emp_id) ? $ppeexemption->emp_id : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->emp_name) ? $ppeexemption->emp_name : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Department') }}</label>
                                    <div class="view_data">
                                        {{ getDepartment(isset($ppeexemption->department) ? $ppeexemption->department : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Unit') }}</label>
                                    <div class="view_data">
                                        {{ getUnitname(isset($ppeexemption->unit) ? $ppeexemption->unit : '' )}}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('From Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->from_date) ? $ppeexemption->from_date : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('To Date') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->to_date) ? $ppeexemption->to_date : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('EHS Officer Approve Status') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->approve_status) ? $ppeexemption->approve_status : '' }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Created By') }}</label>
                                    <div class="view_data">
                                        {{ getUsername(isset($ppeexemption->created_by) ? $ppeexemption->created_by : '') }}
                                    </div>
                                </div>
                                <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                    <div class="view_data">
                                        {{ displayDateformat($ppeexemption->created_at) }}
                                    </div>
                                </div>
                                 <div class="mb-3 col-md-4 form-input">
                                    <label class="form-label view_label">{{ __('Status') }}</label>
                                    <div class="view_data">
                                        @if ($ppeexemption->status == 1)
                                            {{ __('common.active') }}
                                        @else
                                            {{ __('common.inactive') }}
                                        @endif

                                    </div>
                                </div>
                                <div class="mb-3 col-md-12 form-input">
                                    <label class="form-label view_label">{{ __('Reason') }}</label>
                                    <div class="view_data">
                                        {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
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


