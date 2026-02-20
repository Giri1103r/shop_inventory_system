@extends('admin.layouts.admin')
@section('title', 'Road Side First Aid Show')
@section('pageurl', admin_url('ohc/roadside-first-aid/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/roadside-first-aid/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Road Side First Aid</h4>
                                    </div>
                                </div>
                                <div class="row">


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Injured Person Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_roadside_first_aid->name) ? $opd_roadside_first_aid->name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date of Incident') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opd_roadside_first_aid->date_of_incident) ? $opd_roadside_first_aid->date_of_incident : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Time of Incident') }}</label>
                                        <div class="view_data">
                                            {{ (isset($opd_roadside_first_aid->time_of_incident) ? $opd_roadside_first_aid->time_of_incident : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Location of Incident') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_roadside_first_aid->location_of_incident) ? $opd_roadside_first_aid->location_of_incident : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Person Condition') }}</label>
                                        <div class="view_data">
                                            {{ getPersonalCondition(isset($opd_roadside_first_aid->person_condtion) ? $opd_roadside_first_aid->person_condtion : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Firstaid Provided') }}</label>
                                        <div class="view_data">
                                            {{ isset($opd_roadside_first_aid->first_aid_provided) ? $opd_roadside_first_aid->first_aid_provided : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('First Aider Name') }}</label>
                                        <div class="view_data">
                                            {{ (isset($opd_roadside_first_aid->first_aider_name) ? $opd_roadside_first_aid->first_aider_name : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Transport Medical Facility') }}</label>
                                        <div class="view_data">
                                            @if ($opd_roadside_first_aid->transport_to_medical_facility == 1)
                                                {{ __('Yes') }}
                                            @else
                                                {{ __('No') }}
                                            @endif

                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Hospital Name') }}</label>
                                        <div class="view_data">
                                            {{ getHospitalname(isset($opd_roadside_first_aid->hospital_id) ? $opd_roadside_first_aid->hospital_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Transport Method') }}</label>
                                        <div class="view_data">
                                            {{ getReferedVechicle(isset($opd_roadside_first_aid->transport_method) ? $opd_roadside_first_aid->transport_method : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Incident Report Filled') }}</label>
                                        <div class="view_data">
                                            @if ($opd_roadside_first_aid->incident_report_filled == 1)
                                                {{ __('Yes') }}
                                            @else
                                                {{ __('No') }}
                                            @endif

                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Remarks') }}</label>
                                        <div class="view_data">
                                            {{ (isset($opd_roadside_first_aid->remarks) ? $opd_roadside_first_aid->remarks : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Status') }}</label>
                                        <div class="view_data">
                                            @if ($opd_roadside_first_aid->status == 1)
                                                {{ __('Active') }}
                                            @else
                                                {{ __('In-active') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($opd_roadside_first_aid->created_by) ? $opd_roadside_first_aid->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created At') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($opd_roadside_first_aid->created_at) ? $opd_roadside_first_aid->created_at : '') }}
                                        </div>
                                    </div>
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
