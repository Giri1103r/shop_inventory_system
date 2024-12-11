@extends('admin.layouts.admin')
@section('title', 'PPE Shoe  Exemption Show')
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
                                            {{ getUnitname(isset($ppeexemption->unit) ? $ppeexemption->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Company') }}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($ppeexemption->company) ? $ppeexemption->company : '') }}
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


                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status log</h4>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Approved By</th>
                                                <th>Remarks</th>
                                                <th>Date</th>

                                            </tr>
                                        </thead>

                                        <tbody>
                                            @if ($ppestatuslog->isEmpty())
                                            <tr>
                                                <td class="text-center" colspan="5">No data is available</td>
                                            </tr>
                                        @else
                                            @foreach ($ppestatuslog as $log)
                                                <tr class="hover-row">
                                                    <td>{{ removeUnderScore(getStatus($log['to_status'])) }}</td>
                                                    <td>{{getUsername($log['created_by'] ) }}</td>
                                                  <td>{{$log['remarks']}}</td>
                                                  <td>{{displaydateformat($log['created_at'])}}</td>

                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
