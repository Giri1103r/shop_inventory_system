@extends('admin.layouts.admin')
@section('title', 'Certified First Aider')
@section('pageurl', admin_url('ohc/certified-first-aider/list'))


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

                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/certified-first-aider/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.certified_first_aider_heading') }}</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($certifiedfirstaider->unit_id) ? $certifiedfirstaider->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($certifiedfirstaider->department_id) ? $certifiedfirstaider->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('common.employee_or_worker_code') }}</label>
                                        <div class="view_data">
                                            {{ isset($certifiedfirstaider->emp_id) ? $certifiedfirstaider->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.certified_first_aider') }}</label>
                                        <div class="view_data">
                                            {{ isset($certifiedfirstaider->certifier_name) ? $certifiedfirstaider->certifier_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.mobile_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($certifiedfirstaider->mobile_no) ? $certifiedfirstaider->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.address') }}</label>
                                        <div class="view_data">
                                            {{ isset($certifiedfirstaider->address) ? $certifiedfirstaider->address : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($certifiedfirstaider->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($certifiedfirstaider->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($certifiedfirstaider->status == 1)
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
        </div>
        </form>
    </div>

@stop
