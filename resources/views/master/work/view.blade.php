@extends('admin.layouts.admin')
@section('title', 'Worker')
@section('pageurl', admin_url('work/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div>
                                    <x-button-back href="{{ admin_url('work/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4>Worker Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Worker ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->emp_id) ? $work->emp_id : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Worker Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->emp_name) ? $work->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Gender') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->gender) ? $work->gender : '' }}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Nationality') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->nationality) ? $work->nationality : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Biometric Code') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->biometric_code) ? $work->biometric_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('DOI') }}</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat(isset($work->doi) ? $work->doi : '' )}}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Exit Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat(isset($work->exit_date) ? $work->exit_date : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Mobile No') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->mobile_no) ? $work->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Company Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->company_name) ? $work->company_name : '' }}
                                        </div>
                                    </div>                                  
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Location Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->location_name) ? $work->location_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->unit_name) ? $work->unit_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->department_name) ? $work->department_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('SubDepartment Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->subdepartment) ? $work->subdepartment : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Designation') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->designation) ? $work->designation : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('WFEmptype') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->wfemptype) ? $work->wfemptype : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Skill') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->skill) ? $work->skill : '' }}
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
