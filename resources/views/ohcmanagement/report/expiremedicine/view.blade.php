@extends('admin.layouts.admin')
@section('title', 'Expire Medicine Show')
@section('pageurl', admin_url('ohc/medicine-expire-report/list'))


@section('content')
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-expire-report/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Expire Medicine Details</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            {{ getMedicinename(isset($medicine->medicine_id) ? $medicine->medicine_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Batch Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->batch_no) ? $medicine->batch_no : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->quantity) ? $medicine->quantity : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Expire Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicine->expiry_date) ? $medicine->expiry_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Remarks') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->remarks) ? $medicine->remarks : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicine->created_by) ? $medicine->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicine->created_at) }}
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status Logs</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>From Status</th>
                                                    <th>To Status</th>
                                                    <th>Remarks</th>
                                                    <th>Approver Name</th>
                                                    <th>Approver Date</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($logData as $log)
                                                <tr>
                                                    <td>{{getDiscardStatus( isset($log['from_status']) ? $log['from_status'] : '-') }}</td>
                                                    <td>{{ getDiscardStatus(isset($log['to_status']) ? $log['to_status'] : '-' )}}</td>
                                                    <td>{{ isset($log['remarks']) ? $log['remarks'] : '-' }}</td>
                                                    <td>{{ isset($log['created_by']) ? getUsername($log['created_by']) : '-' }}</td>
                                                    <td>{{ isset($log['created_at']) ? Displaydateformat($log['created_at']) : '-' }}</td>
                                                </tr>
                                                @endforeach
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
    </div>

@stop

