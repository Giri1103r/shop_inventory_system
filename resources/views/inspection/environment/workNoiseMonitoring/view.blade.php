@extends('admin.layouts.admin')
@section('title', 'Work Noise Monitoring Show')
@section('pageurl', admin_url('environment/work-noise/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('environment/work-noise/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body">

                                <div class="basic-form">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">Work Noise Monitoring</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Work Noise No</label>
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
                                            <div class="mt-2 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($environmentData->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mt-2 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                                <div class="view_data">
                                                    {{ displayDateformat($environmentData->created_at) }}
                                                </div>
                                            </div>
                                            <div class="mt-2 col-md-4 form-input">
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
                                                <h4 class="text-white">Work Noise Monitoring Details</h4>
                                            </div>
                                        </div>

                                        <div id="lesson_learned_block">
                                            @foreach ($workNoiseDataList as $workNoiseData)
                                                <div class="row lesson_learned_row" style="margin-top: 20px;">

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">SR NO</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->sr_no ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Location</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->location_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Unit</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->unit_name ?? '-' }}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NOISE LEVEL (dBA)</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->noise_level_dba ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workNoiseData->date_of_monitoring) ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of
                                                            Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workNoiseData->next_due_date_of_monitoring) ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">NOISE LEVEL (dBA)
                                                            ({{ $workNoiseData->noise_level_dba_dropdown == 1 ? 'Day' : 'Night' }})
                                                        </label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->noise_level_dba_no ?? '-' }}
                                                        </div>

                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Date of Monitoring</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workNoiseData->date_of_monitoring_date) ?? '-' }}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Next Due Date of Monitoring
                                                        </label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workNoiseData->next_due_date_of_monitoring_date) ?? '-' }}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Last Due Date of Monitoring
                                                        </label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($workNoiseData->last_due_date_of_monitoring_date) ?? '-' }}
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Act/Rule</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->act_rule ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Remark</label>
                                                        <div class="view_data">
                                                            {{ $workNoiseData->remark ?? '-' }}
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
