@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate')
@section('pageurl', admin_url('ohc/medical-fitness/list'))


@section('content')
    <div class="clearfix">
    </div>
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{__('ohc_management.medical_fitness_certificate_details')}}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('common.employee_or_worker_code') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->emp_id) ? $medicalfitness->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('common.employee_or_worker_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->emp_name) ? $medicalfitness->emp_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.company') }}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($medicalfitness->company_id) ? $medicalfitness->company_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($medicalfitness->unit_id) ? $medicalfitness->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($medicalfitness->department_id) ? $medicalfitness->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicalfitness->date) ? $medicalfitness->date : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.file') }}</label>
                                        @if (isset($medicalfitness) && $medicalfitness && $medicalfitness->file)
                                            <p>
                                                @php
                                                    $fileExtension = pathinfo(
                                                        $medicalfitness->file,
                                                        PATHINFO_EXTENSION,
                                                    );
                                                @endphp
                                                @if (in_array($fileExtension, ['pdf', 'doc', 'docx']))
                                                    <a href="{{ asset('public/' . $medicalfitness->file) }}"
                                                        target="_blank">
                                                        <i class="fas fa-eye text-danger"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ asset('public/' . $medicalfitness->file) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('public/' . $medicalfitness->file) }}"
                                                            style="width: 100px" alt="image">
                                                    </a>
                                                @endif
                                            </p>
                                        @else
                                            <p>No file is uploaded</p>
                                        @endif
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicalfitness->created_by) ? $medicalfitness->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicalfitness->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->remarks) ? $medicalfitness->remarks : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label
                                            class="form-label view_label">{{ __('ohc_management.chief_complaint') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->cheif_complaint) ? $medicalfitness->cheif_complaint : '' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- view of doctor approval --}}

                                @if (
                                    $medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING ||
                                        $medicalfitness->approve_status == STATUS_OHC_MEDICAL_DOCTOR_REJECTED ||
                                        $medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('ohc_management.doctor_approval') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approver_name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($doctorapprovallog->created_by) ? $doctorapprovallog->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($doctorapprovallog->created_at) ? $doctorapprovallog->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($doctorapprovallog->created_at) ? $doctorapprovallog->created_at : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-12 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($doctorapprovallog->remarks) ? $doctorapprovallog->remarks : '' }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif


                                @if (
                                    $medicalfitness->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVED ||
                                        $medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('ohc_management.ehs_head_approval') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approver_name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($ehsheadlog->created_by) ? $ehsheadlog->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($ehsheadlog->created_at) ? $ehsheadlog->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($ehsheadlog->created_at) ? $ehsheadlog->created_at : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-12 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($ehsheadlog->remarks) ? $ehsheadlog->remarks : '' }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Status Log</h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-hover">

                                            <thead>
                                                <th>From Status</th>
                                                <th>To Status</th>
                                                <th>Approved By</th>
                                                <th>Remarks</th>
                                                <th>Created Date</th>
                                            </thead>

                                            <tbody>
                                                @foreach ($medicalfitnesslog as $status_log)
                                                    <tr>

                                                        <td>
                                                            @if ($status_log['from_status'] == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>
                                                                    Paramedics request the fitness Approval</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>Doctor
                                                                    Approval Pending</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>
                                                                    Doctor
                                                                    Approved</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'> EHS
                                                                    Head
                                                                    Approval Pending</p>
                                                            @elseif($status_log['from_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Head Approved</p>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($status_log['to_status'] == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>
                                                                    Paramedics request the fitness Approval</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'>Doctor
                                                                    Approval Pending</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>
                                                                    Doctor
                                                                    Approved</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                                <p class='badge bg-info' style='font-size: 1.0em;'> EHS
                                                                    Head
                                                                    Approval Pending</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                                <p class='badge bg-success' style='font-size: 1.0em;'>EHS
                                                                    Head Approved</p>
                                                            @elseif($status_log['to_status'] == STATUS_OHC_MEDICAL_DOCTOR_REJECTED)
                                                                <p class='badge bg-danger' style='font-size: 1.0em;'>
                                                                    Doctor Rejected
                                                                </p>
                                                            @endif
                                                        </td>

                                                        <td>{{ isset($status_log['created_by']) ? getUsername($status_log['created_by']) : '-' }}
                                                        </td>
                                                        <td>{{ isset($status_log['remarks']) ? $status_log['remarks'] : '-' }}
                                                        </td>
                                                        <td>{{ null !== Displaydateformat($status_log['created_at']) ? Displaydateformat($status_log['created_at']) : '-' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @stop
