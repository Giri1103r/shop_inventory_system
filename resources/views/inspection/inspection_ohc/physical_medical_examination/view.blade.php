@extends('admin.layouts.admin')
@section('title', 'Physical Health Examination Check-up View')
@section('pageurl', admin_url('ohc/physical-medical-examination/yearly/list'))

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
                                        href="{{ admin_url('ohc/physical-medical-examination/yearly/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">OHC HYGIENE CLEANING CHECKLIST</h4>
                                    </div>
                                </div>



                                <div class="row">


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ getShiftname(isset($inspection_details->shift_id) ? $inspection_details->shift_id : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($inspection_details->created_by) ? $inspection_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($inspection_details->created_at) ? $inspection_details->created_at : '') }}
                                        </div>
                                    </div>
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
                                                Cleaner Signature</th>
                                            <th rowspan="2"
                                                style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                Nursing Signature</th>
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
                                                <img src="{{ admin_url($cleaner_signature) }}" alt="Cleaner Signature"
                                                    style="width:100px; height:100px;">
                                            </td>
                                            <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                class="form-input">
                                                @if ($nursing_signature)
                                                    <img src="{{ admin_url($cleaner_signature) }}" alt="Cleaner Signature"
                                                        style="width:100px; height:100px;">
                                                @else
                                                <p>Inspection has not been Verified Yet</p>
                                                @endif
                                            </td>
                                            <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                class="form-input">
                                                {{ $inspection_details->cleaner_remarks }}
                                            </td>
                                            @if ($inspection_details->updated_by)
                                                <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    {{ isset($inspection_details->nursing_officer_remarks) ? $inspection_details->nursing_officer_remarks : '-' }}
                                                </td>
                                            @endif
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @stop
