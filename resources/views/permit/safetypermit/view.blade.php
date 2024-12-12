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
                                    <x-button-back href="{{ admin_url('safetypermit/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Safety Permit</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Work Permit No') }}</label>
                                        <div class="view_data">
                                            {{ isset($safetypermit->permit_id) ? $safetypermit->permit_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($safetypermit->date) ? Displaydateformat($safetypermit->date) : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time(From)') }}</label>
                                        <div class="view_data">
                                            {{ isset($safetypermit->time_from) ? $safetypermit->time_from : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time(To)') }}</label>
                                        <div class="view_data">
                                            {{isset($safetypermit->time_to) ? $safetypermit->time_to : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($safetypermit->unit_id) ? $safetypermit->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Exact location of job') }}</label>
                                        <div class="view_data">
                                            {{ isset($safetypermit->exact_location_job) ? $safetypermit->exact_location_job : ''}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Job Location & Area') }}</label>
                                        <div class="view_data">
                                            {{isset($safetypermit->job_location_area) ? $safetypermit->job_location_area : ''}}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($safetypermit->created_by) ? $safetypermit->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($safetypermit->created_at) }}
                                        </div>
                                    </div>
                                
                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Type of Job</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Work Permit No') }}</label>
                                        <div class="view_data">
                                            {{ isset($safetypermit->permit_id) ? $safetypermit->permit_id : '' }}
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
