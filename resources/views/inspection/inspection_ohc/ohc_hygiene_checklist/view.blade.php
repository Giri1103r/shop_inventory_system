@extends('admin.layouts.admin')
@section('title', 'OHC HYGIENE CLEANING CHECKLIST')
@section('pageurl', admin_url('ohc/ohc-hygiene-cleaning-checklist/list'))

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
                                    <x-button-back
                                        href="{{ admin_url('ohc/ohc-hygiene-cleaning-checklist/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">OHC HYGIENE CLEANING CHECKLIST</h4>
                                    </div>
                                </div>
                                <div class="table-responsive container mb-3">
                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Date
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Shift
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Description/Equipment
                                                </th>
                                                <th colspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Cleaning and Sanitization
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Cleaner</th>
                                                {{-- <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Nursing</th> --}}
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Remarks</th>
                                                @isset($nursing_signature)
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                        Nursing Officer Remarks</th>
                                                @endisset
                                            </tr>
                                            <tr>
                                                <td
                                                    style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                    YES</td>
                                                <td
                                                    style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                    NO</td>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="border: 1px solid black; text-align: center; padding: 12px;">
                                                    {{ Displaydateformat($inspection_details->issue_date) }}
                                                </td>
                                                <td style="border: 1px solid black; padding: 12px;">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </td>
                                                <td style="border: 1px solid black; padding: 12px;">
                                                    {{ $inspection_details->inspection_question }}
                                                </td>
                                                <td colspan="2"
                                                    style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    @if ($inspection_details->inspection_value == 1)
                                                        <span style="color: green;">✅</span>
                                                    @else
                                                        <span style="color: red;">❌</span>
                                                    @endif

                                                </td>

                                                <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    <p>{{ getUsername($inspection_details->created_by) }}</p>
                                                </td>
                                                {{-- <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    @if ($nursing_signature)
                                                        <p>{{ getUsername($inspection_details->updated_by) }}</p>
                                                    @else
                                                        <p>Inspection has not been Verified Yet</p>
                                                    @endif
                                                </td> --}}
                                                <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    {{ $inspection_details->cleaner_remarks }}
                                                </td>
                                                {{-- @if ($inspection_details->updated_by)
                                                    <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                        class="form-input">
                                                        {{ isset($inspection_details->nursing_officer_remarks) ? $inspection_details->nursing_officer_remarks : '-' }}
                                                    </td>
                                                @endif --}}
                                            </tr>
                                        </tbody>
                                    </table>


                                </div>

                                {{-- nursing officer remarks --}}

                                @if (isset($inspection_details->updated_by))
                                    <div class="row">
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">{{ __('ohc_management.nursing_officer_approval') }}</h4>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->updated_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->updated_at) }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if (isset($inspection_details->checklist_status))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{__('ohc_management.checklist_status')}}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->checklist_status == 2 ? 'Approved' : 'Rejected' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->nursing_officer_remarks }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (isset($status_log) && $status_log->isNotEmpty())
                                    <div class="row mt-4">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.status_log') }}</h4>

                                        </div>
                                        <div class="card">
                                            @if (isset($status_log) && $status_log->isNotEmpty())
                                                <div class="card-body">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>S.NO</th>
                                                                <th>From Status</th>
                                                                <th>To Status</th>
                                                                <th>Remarks</th>
                                                                <th>Approved By</th>
                                                                <th>Created By</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($status_log as $log)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ getOhcHygieneCleaningStatus($log->from_status) }}
                                                                    </td>
                                                                    <td>{{ getOhcHygieneCleaningStatus($log->to_status) }}
                                                                    </td>
                                                                    <td>{{ $log->remarks ? $log->remarks : '-' }}</td>
                                                                    <td>{{ getUserName($log->approved_by) ? getUserName($log->approved_by) : '-' }}
                                                                    </td>
                                                                    <td>{{ getUserName($log->created_by) ? getUserName($log->created_by) : '-' }}
                                                                    </td>
                                                                    <td>{{ displaydateformat($log->created_at) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="card-body">
                                                    <p class="text-white">No status logs available.</p>
                                                </div>
                                            @endif
                                        </div>

                                    </div>
                                @endif
                                {{-- status-log --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @stop
