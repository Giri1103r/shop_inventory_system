@extends('admin.layouts.admin')
@section('title', 'User View')
@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto">

                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('') }}"><i class="mdi mdi-home-outline"></i></a> </li>
                                    <li class="breadcrumb-item" aria-current="page">Tables</li>
                                    <li class="breadcrumb-item active" aria-current="page">Data Tables</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">

                    <div class="col-12">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">User View</h4>
                                    <div>
                                        <x-button-back href="{{ admin_url('admin/master/user/list') }}"></x-button-back>
                                    </div>
                                </div>

                                <div class="card-body ">

                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4>Genearl Details</h4>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="mb-3 col-md-4 form-input">
                                            <label  class="form-label view_label">{{ __('administration.employee_no') }}</label>
                                            <div class="view_data">
                                                {{isset($employee->employee_no)?$employee->employee_no:''}}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label  class="form-label view_label">{{ __('administration.first_name') }}</label>
                                            <div class="view_data">
                                                {{isset($employee->first_name)?$employee->first_name:''}}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label  class="form-label view_label">{{ __('administration.last_name') }}</label>
                                            <div class="view_data">
                                                {{isset($employee->last_name)?$employee->last_name:''}}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label  class="form-label view_label">{{ __('administration.joining_date') }}</label>
                                            <div class="view_data">
                                                {{isset($employee->joining_date)?Displaydateformat($employee->joining_date):''}}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label  class="form-label view_label">{{ __('administration.employee_type') }}</label>
                                            <div class="view_data">
                                                {{isset($employee->employee_type)?getEmpType($employee->employee_type):''}}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('administration.date_contract_end') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->contract_end_date)?Displaydateformat($employee->contract_end_date):''}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('administration.designation') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->designation_id)?getUserdesignation($employee->designation_id):'';}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('administration.department') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->department_id)?getDepartmentName($employee->department_id):'';}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('User Role') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->designation_id)?getUserRoleName($employee->designation_id):'';}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('administration.reporting_manager') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->reporting_manager_id)?getUsername($employee->reporting_manager_id):'';}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label">{{ __('administration.factory') }}</label>
                                                <div class="view_data">
                                                    {{isset($employee->factory_id)?getFactoryNames($employee->factory_id):'';}}
                                                </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('common.created_by') }}</label>
                                            <div class="view_data">
                                                {{ getusername($employee->created_by) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('common.created_date') }}</label>
                                            <div class="view_data">
                                                {{ displayDateformat($employee->created_at ) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('common.status') }}</label>
                                            <div class="view_data">
                                                @if ($employee->status == 1)
                                                    {{ __('common.active') }}
                                                @else
                                                {{ __('common.inactive') }}
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>


@endsection


@push('scripts')
@endpush

@push('script')
    <script type="text/javascript" nonce="projectcab">

    </script>
@endpush
