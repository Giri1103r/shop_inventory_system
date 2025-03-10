
@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate Show')
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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/medical-fitness/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Employee details</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee code') }}</label>
                                        <div class="view_data">
                                            {{ (isset($medicalfitness->emp_id) ? $medicalfitness->emp_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ (isset($medicalfitness->emp_name) ? $medicalfitness->emp_name : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicalfitness->date) ? $medicalfitness->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medical Fitness Certificate') }}</label>
                                        <div class="view_data">
                                            {{ (isset($medicalfitness->file) ? $medicalfitness->file : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created at') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicalfitness->created_by) ? $medicalfitness->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicalfitness->created_at) ? $medicalfitness->created_at : '') }}
                                        </div>
                                    </div>
                                </div>
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
                                                @foreach ($medicalfitnesslog as $status_log )

                                                <tr>

                                                    <td>
                                                        @if( ($status_log['from_status']) == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Paramedics request the fitness Approval</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Doctor Approval Pending</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>Doctor Approved</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'> EHS Head Approval Pending</p>
                                                        @elseif( ($status_log['from_status']) == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</p>
                                                        @endif
                                                    </td>
                                                    <td>
                                                         @if( ($status_log['to_status']) == STATUS_OHC_MEDICAL_PARAMEDICS_REQUEST)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Paramedics request the fitness Approval</p>
                                                        @elseif( ($status_log['to_status']) == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'>Doctor Approval Pending</p>
                                                        @elseif( ($status_log['to_status']) == STATUS_OHC_MEDICAL_DOCTOR_APPROVED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>Doctor Approved</p>
                                                        @elseif( ($status_log['to_status']) == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING)
                                                            <p class='badge bg-info' style='font-size: 1.0em;'> EHS Head Approval Pending</p>
                                                        @elseif( ($status_log['to_status']) == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVED)
                                                            <p class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</p>
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
