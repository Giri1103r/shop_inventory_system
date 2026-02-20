@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Exemption Request ')
@section('pageurl', admin_url('ppe_exemption/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_exemption/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{__('ppe_management.ppe_shoe_exemption')}}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_emp_id')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->emp_id) ? $ppeexemption->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_emp_name')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->emp_name) ? $ppeexemption->emp_name : '' }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.company')}}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($ppeexemption->company) ? $ppeexemption->company : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.location')}}</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($ppeexemption->location_id) ? $ppeexemption->location_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.unit')}}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($ppeexemption->unit) ? $ppeexemption->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.department')}}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($ppeexemption->department) ? $ppeexemption->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.from_date')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->from_date) ? $ppeexemption->from_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.to_date')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->to_date) ? $ppeexemption->to_date : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.created_by')}}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($ppeexemption->created_by) ? $ppeexemption->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($ppeexemption->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($ppeexemption->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.file')}}</label>
                                        @if (isset($ppefiles) && $ppefiles->count() > 0)
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach ($ppefiles as $file)
                                                    <p>
                                                        @php
                                                            $fileExtension = strtolower(
                                                                pathinfo($file->file_path, PATHINFO_EXTENSION),
                                                            );
                                                        @endphp

                                                        @if (in_array($fileExtension, ['docx', 'pdf', 'doc']))
                                                            <a href="{{ asset('' . $file->file_path) }}" target="_blank">
                                                                <i class="fa-solid fa-eye text-danger"></i> View
                                                            </a>
                                                        @elseif (in_array($fileExtension, ['png', 'jpg', 'jpeg']))
                                                            <a href="{{ asset('' . $file->file_path) }}" target="_blank">
                                                                <img src="{{ asset('' . $file->file_path) }}"
                                                                    alt="image"
                                                                    style="max-width: 100px; max-height: 100px;">
                                                            </a>
                                                        @else
                                                            <span>{{ $file->file_path }}</span>
                                                        @endif
                                                    </p>
                                                @endforeach
                                            </div>
                                        @else
                                            <p>No files are uploaded</p>
                                        @endif
                                    </div>

                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_reason')}}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
                                        </div>
                                    </div>
                                </div>



                                <div>
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Status logs</h4>
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <div class="col-md-12">
                                            <table class="table table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>From Status</th>
                                                        <th>To Status</th>
                                                        <th>Approved By</th>
                                                        <th>Remarks</th>
                                                        <th>Date</th>

                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr>
                                                        <td> <span class='badge bg-info' style='font-size: 1.0em;'>User
                                                                Applied</span></td>
                                                        <td><span class='badge bg-info' style='font-size: 1.0em;'>EHS Head
                                                                Approval
                                                                Pending</span></td>
                                                        <td> {{ getUsername(isset($ppeexemption->created_by) ? $ppeexemption->created_by : '') }}
                                                        </td>
                                                        <td> {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
                                                        </td>
                                                        <td> {{ displaydateformat(isset($ppeexemption->created_at) ? $ppeexemption->created_at : '') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                            <td> <span class='badge bg-info' style='font-size: 1.0em;'>EHS Head Approval
                                                                Pending</span></td>
                                                                <td>
                                                                    @if (isset($ehsheadstatus['to_status']) && $ehsheadstatus['to_status'] == STATUS_EHS_APPROVED)
                                                                        <span class='badge bg-success' style='font-size: 1.0em;'>EHS Head Approved</span>
                                                                    @elseif (isset($ehsheadstatus['to_status']) && $ehsheadstatus['to_status'] == STATUS_EHS_REJECTED)
                                                                        <span class='badge bg-danger' style='font-size: 1.0em;'>EHS Head Rejected</span>
                                                                    @else
                                                                        <p>-</p>
                                                                    @endif
                                                            </td>


                                                            <td> {{ isset($ehsheadstatus->created_by) && $ehsheadstatus->created_by != '' ? getUsername($ehsheadstatus->created_by) : '-' }}
                                                            </td>
                                                            <td> {{ isset($ehsheadstatus->remarks) ? $ehsheadstatus->remarks : '-' }}
                                                            </td>
                                                            <td>  {{ isset($ehsheadstatus->created_at) && $ehsheadstatus->created_at != '' ? displaydateformat($ehsheadstatus->created_at) : '-' }}
                                                            </td>
                                                    </tr>

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
        </div>
    </div>

@stop
