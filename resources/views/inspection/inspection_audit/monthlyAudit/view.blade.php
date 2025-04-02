@extends('admin.layouts.admin')
@section('title', 'Monthly Audit Plan view')
@section('pageurl', admin_url('audit/monthly-audit/audit-plan/list'))
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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('audit/monthly-audit/audit-plan/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Monthly Audit Task Details</h4>
                                        </div>
                                    </div>

                                    <div class="row">



                                        <div class="col-md-4 mb-2">
                                            <label
                                                class="form-label view_label">Auditee Name</label>
                                            <div class="view_data">
                                                {{ isset($monthly_audit_plan->auditee_name) ? $monthly_audit_plan->auditee_name : '' }}
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Unit</label>
                                                <div class="view_data">
                                                    {{ getUnitname(isset($monthly_audit_plan->unit_id) ? $monthly_audit_plan->unit_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Task</label>
                                                <div class="view_data">
                                                    {{ getTaskName(isset($monthly_audit_plan->task_id) ? $monthly_audit_plan->task_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Category</label>
                                                <div class="view_data">
                                                    {{ getCategoryType(isset($monthly_audit_plan->compliance_category_id) ? $monthly_audit_plan->compliance_category_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label
                                                class="form-label view_label">Reference Doc No</label>
                                            <div class="view_data">
                                                {{ isset($monthly_audit_plan->reference_doc_no) ? $monthly_audit_plan->reference_doc_no : '' }}
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Frequency</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname(isset($monthly_audit_plan->frequency_id) ? $monthly_audit_plan->frequency_id : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Audit Plan Status</label>
                                            <div class="view_data">
                                                {{ $monthly_audit_plan->audit_plan_status == 1 ? 'Yes' : 'No' }}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Direct / In-Direct</label>
                                            <div class="view_data">
                                                {{ $monthly_audit_plan->direct_in_direct == 1 ? 'Direct' : 'In-Direct' }}
                                            </div>
                                        </div>

                                        
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Points</label>
                                            <div class="view_data">
                                                {{ isset($monthly_audit_plan->points) ? $monthly_audit_plan->points : '' }}
                                            </div>
                                        </div>
                                        

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Remarks</label>
                                            <div class="view_data">
                                                {{ isset($monthly_audit_plan->remarks) ? $monthly_audit_plan->remarks : '' }}
                                            </div>
                                        </div>


                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                            <div class="view_data">
                                                {{ getusername($monthly_audit_plan->created_by) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                            <div class="view_data">
                                                {{ displayDateformat($monthly_audit_plan->created_at) }}
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
