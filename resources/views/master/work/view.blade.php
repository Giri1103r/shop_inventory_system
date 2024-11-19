@extends('admin.layouts.admin')
@section('title', 'Worker')
@section('pageurl', admin_url('work/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h4 class="text-black">{{ __('Worker Show') }}</h4>

        </div>
        {{-- <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#009999"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_4') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_8') }}</a></li>
        </ol> --}}
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
                                        <label class="form-label view_label">{{ __('Nationality') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->nationality) ? $work->nationality : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('biometric Code') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->biometric_code) ? $work->biometric_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('DOI') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->doi) ? $work->doi : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Exit Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($work->exit_date) ? $work->exit_date : '' }}
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
