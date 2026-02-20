@extends('admin.layouts.admin')
@section('title', 'Discard medicine Show')
@section('pageurl', admin_url('ohc/discard/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/discard/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Discard Details</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            {{ getMedicinename(isset($user_discard->medicine_id) ? $user_discard->medicine_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($user_discard->quantity) ? $user_discard->quantity : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Discard Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($user_discard->expire_date) ? $user_discard->expire_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($user_discard->created_by) ? $user_discard->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($user_discard->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($user_discard->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

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
                                                @if (!$logData || count($logData) == 0)
                                                    <tr>
                                                        <td colspan="5" class="text-center">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($logData as $log)
                                                        <tr>
                                                            <td>{{ getDiscardStatus($log['from_status'] ?? '-') }}</td>
                                                            <td>{{ getDiscardStatus($log['to_status'] ?? '-') }}</td>
                                                            <td>{{ $log['remarks'] ?? '-' }}</td>
                                                            <td>{{ isset($log['created_by']) ? getUsername($log['created_by']) : '-' }}</td>
                                                            <td>{{ isset($log['created_at']) ? Displaydateformat($log['created_at']) : '-' }}</td>
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
    </div>
@stop
