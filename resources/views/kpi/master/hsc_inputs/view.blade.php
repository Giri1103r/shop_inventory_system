@extends('admin.layouts.admin')
@section('title', 'HSE Inputs')
@section('pageurl', admin_url('kpi/hse-inputs/list'))


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
                                    <x-button-back href="{{ admin_url('kpi/hse-inputs/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('common.hsc_inputs') }}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.company') }}</label>
                                        <div class="view_data">
                                            {{ getCompanyname($hsc_inputs->company_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.location') }}</label>
                                        <div class="view_data">
                                            {{ getLocationname($hsc_inputs->location_id) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname($hsc_inputs->unit_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment($hsc_inputs->department_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.month') }}</label>
                                        <div class="view_data">
                                            {{ \Carbon\Carbon::create()->month((int) $hsc_inputs->month)->format('F') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.year') }}</label>
                                        <div class="view_data">
                                            {{ $hsc_inputs->calendar_year }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($hsc_inputs->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($hsc_inputs->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($hsc_inputs->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('common.leading') }}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($leadings as $leading)
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ getLeadingName($leading->leading_id) }}</label>
                                            <div class="view_data">
                                                {{ $leading->value }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('common.lagging') }}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach ($laggings as $lagging)
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ getLaggingName($lagging->lagging_id) }}</label>
                                            <div class="view_data">
                                                {{ $lagging->value }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
