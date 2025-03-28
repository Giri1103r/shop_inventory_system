@extends('admin.layouts.admin')
@section('title', 'Work Zone Air Monitoring Show')
@section('pageurl', admin_url('environment/work-zone/air/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('environment/work-zone/air/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body">

                                <div class="basic-form">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">Work Zone Air Monitoring</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Work Zone Air Monitoring No</label>
                                                <div class="view_data">
                                                    {{ isset($environmentData->environment_no) ? $environmentData->environment_no : '' }}
                                                </div>

                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <div class="view_data">
                                                    {{ isset($staticDocno->doc_no) ? $staticDocno->doc_no : '' }}
                                                </div>
                                            </div>

                                            <div class="col-md-4 form-input">
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
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($environmentData->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                                <div class="view_data">
                                                    {{ displayDateformat($environmentData->created_at) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.status') }}</label>
                                                <div class="view_data">
                                                    @if ($environmentData->status == 1)
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
                                                <h4 class="text-white">Work Zone Air Monitoring Details</h4>
                                            </div>
                                        </div>

                                        <div id="lesson_learned_block">
                                            @foreach ($workZoneAirDataList as $workZoneAirData)
                                                <div class="row lesson_learned_row" style="margin-top: 20px;">

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">SR NO</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->sr_no ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Location</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->location_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Unit</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->unit_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workZoneAirData->date_of_monitoring) ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of
                                                            Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workZoneAirData->next_due_date_of_monitoring) ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SPM (Session 1)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->spm ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SO2 (Session 1)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->so2 ?? '-' }}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NO2 (Session 1)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->no2 ?? '-' }}
                                                        </div>

                                                    </div>
                                                    
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workZoneAirData->date_of_monitoring2) ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of
                                                            Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workZoneAirData->next_due_date_of_monitoring2) ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SPM (Session 2)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->spm_session2 ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">SO2 (Session 1)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->so2_session2 ?? '-' }}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NO2 (Session 1)</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->no2_session2 ?? '-' }}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Act/Rule</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->act_rule ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Remark</label>
                                                        <div class="view_data">
                                                            {{ $workZoneAirData->remark ?? '-' }}
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
