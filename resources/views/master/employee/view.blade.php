@extends('admin.layouts.admin')
@section('title', 'Employee')
@section('pageurl', admin_url('employee/list'))


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
                                    <x-button-back href="{{ admin_url('employee/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4>Employee Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->emp_id) ? $employee->emp_id : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->emp_name) ? $employee->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Gender') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->gender) ? $employee->gender : '' }}
                                        </div>
                                    </div>
                                    
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Nationality') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->nationality) ? $employee->nationality : '' }}
                                        </div>
                                    </div> --}}
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Email') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->email) ? $employee->email : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('User Role') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->role_name) ? $employee->role_name : '' }}
                                        </div>
                                    </div>
                                  
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Joining Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat(isset($employee->joining_date) ? $employee->joining_date : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Status') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->employee_status) ? $employee->employee_status : '' }}
                                        </div>
                                    </div>
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Company Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->company_name) ? $employee->company_name : '' }}
                                        </div>
                                    </div>                                  
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Location Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->location_name) ? $employee->location_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->unit_name) ? $employee->unit_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->department_name) ? $employee->department_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('SubDepartment Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->subdepartment) ? $employee->subdepartment : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Designation') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->designation) ? $employee->designation : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('WFEmptype') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->wfemptype) ? $employee->wfemptype : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Skill') }}</label>
                                        <div class="view_data">
                                            {{ isset($employee->skill) ? $employee->skill : '' }}
                                        </div>
                                    </div> --}}
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
