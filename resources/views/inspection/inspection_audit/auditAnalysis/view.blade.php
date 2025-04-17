@extends('admin.layouts.admin')
@section('title', '6S Audit Analysis View')
@section('pageurl', admin_url('audit/6s-analysis/list'))

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
                                    <x-button-back href="{{ admin_url('audit/6s-analysis/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">6S Audit Analysis</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">6S Audit Analysis No</label>
                                                <div class="view_data">
                                                    {{ isset($auditData->audit_analysis_id) ? $auditData->audit_analysis_id : '' }}
                                                </div>

                                            </div>

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">6S Audit Analysis Report</label>
                                                <div class="view_data">
                                                    {{ isset($auditData->audit_analysis) ? $auditData->audit_analysis : '' }}
                                                </div>

                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <div class="view_data">
                                                    {{ isset($staticDocno->doc_no) ? $staticDocno->doc_no : '' }}
                                                </div>
                                            </div>

                                            <div class="col-md-4 form-input mt-2">
                                                <label class="form-label">Issue Dt.</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($staticDocno->issue_date) }}
                                                </div>

                                            </div>

                                            <div class="col-md-4 form-input mt-2">
                                                <label class="form-label">Rev. & Dt.</label>
                                                <div class="view_data">
                                                    {{ isset($staticDocno->rev_dt) ? $staticDocno->rev_dt : '' }}
                                                </div>

                                            </div>
                                            <div class="mb-3 col-md-4 form-input mt-2">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($auditData->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                                <div class="view_data">
                                                    {{ displayDateformat($auditData->created_at) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.status') }}</label>
                                                <div class="view_data">
                                                    @if ($auditData->status == 1)
                                                        {{ __('common.active') }}
                                                    @else
                                                        {{ __('common.inactive') }}
                                                    @endif

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">6S Audit Analysis Details</h4>
                                            </div>
                                        </div>

                                        <div id="lesson_learned_block">
                                            @foreach ($auditAnalysisData as $analysisData)
                                                <div class="row lesson_learned_row" style="margin-top: 20px;">

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">SR NO</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->serial_number ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Department Name </label>
                                                        <div class="view_data">
                                                            {{ $analysisData->department_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Unit</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->unit_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Marks Obtained (Monthly)</label>
                                                        <div class="view_data">
                                                            @php
                                                                $marks = json_decode($analysisData->marks ?? '{}', true);
                                                                $monthWithMark = collect($marks)->filter(function ($value) {
                                                                    return $value != 0;
                                                                })->first();
                                                    
                                                                $monthName = collect($marks)->filter(function ($value) {
                                                                    return $value != 0;
                                                                })->keys()->first();
                                                            @endphp
                                                    
                                                            {{ $monthName ? ucfirst($monthName) . ' - ' . $monthWithMark : '-' }}
                                                        </div>
                                                    </div>
                                                    

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Total No's of Audit</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->no_of_audit ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Total Marks</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->total_marks ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Total Marks
                                                            Obtained</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->marks_obtained ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">%</label>
                                                        <div class="view_data">
                                                            {{ $analysisData->percentage ?? '-' }}
                                                        </div>
                                                    </div>
                                                   

                                                </div>
                                                <hr>
                                            @endforeach
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
