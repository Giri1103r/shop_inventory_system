@extends('admin.layouts.admin')
@section('title', 'Hooter Inspection')
@section('pageurl', admin_url('fire/hooter-inspection/list'))
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center"></div>
    </div>

    <div class="content-body default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="align-back-btc">
                                <x-button-back href="{{ admin_url('fire/hooter-inspection/list') }}"></x-button-back>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="basic-form mx-3">
                                {{-- Basic Information Section --}}
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">{{ $document_no->doc_no }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">{{ Displaydateformat($document_no->issue_date) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">{{ $document_no->rev_dt }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">{{ Displaydateformat($inspection->date_of_inspection) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.location') }}</label>
                                            <div class="view_data">{{ getLocationname($inspection->location) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">Shift</label>
                                            <div class="view_data">{{ getShiftName($inspection->shift) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.next_due') }}</label>
                                            <div class="view_data">{{ Displaydateformat($inspection->next_due) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">{{ getUnitname($inspection->unit) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.frequency') }}</label>
                                            <div class="view_data">{{ getFrequencyname($inspection->frequency) }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">{{ __('inspection.upload_image') }}</label>
                                            <div class="view_data">
                                                <img src="{{ admin_url($inspection_image) }}" class="img-thumbnail" style="width:100px; height:100px; object-fit:cover;" alt="Inspection Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Observation Section --}}
                                <div class="form-observation">
                                    <div class="row mt-4">
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">Hooter Inspection Observation</h4>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Observation</label>
                                                <div class="view_data">
                                                    <span class="{{ $inspection->observation_needed == '1' ? 'text-success' : 'text-danger' }}">
                                                        {{ $inspection->observation_needed == '1' ? 'YES' : 'NO' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                {{-- Inspection Details Section --}}
                                @foreach ($inspection_details as $details)
                                    <div class="form-wrapper">
                                        <div class="row mt-4">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Hooter Inspection Checklist</h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.sr_no') }}</label>
                                                    <div class="view_data">{{ $details->sr_no }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.resource_code') }}</label>
                                                    <div class="view_data">{{ $details->resource_code }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.department') }}</label>
                                                    <div class="view_data">{{ GetDeptName($details->department) }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Condition Of The Hooter</label>
                                                    <div class="view_data">{{ $details->condition_of_hooter }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.quantity') }}</label>
                                                    <div class="view_data">{{ $details->quantity }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-8 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">{{ $details->remarks }}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Check If Observations Needed</label>
                                                    <div class="mt-2">
                                                        <div class="d-flex gap-4">
                                                            <div class="observation-item">
                                                                <span class="me-2">Blinking Light:</span>
                                                                <i class="fas fa-{{ $details->blinking_light == 1 ? 'check text-success' : 'times text-danger' }}"></i>
                                                            </div>
                                                            <div class="observation-item">
                                                                <span class="me-2">Connection:</span>
                                                                <i class="fas fa-{{ $details->connection == 1 ? 'check text-success' : 'times text-danger' }}"></i>
                                                            </div>
                                                            <div class="observation-item">
                                                                <span class="me-2">Audibility:</span>
                                                                <i class="fas fa-{{ $details->audiobility == 1 ? 'check text-success' : 'times text-danger' }}"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <hr>

                                {{-- Status Log Section --}}
                                <div class="row mt-4">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.status_log') }}</h4>
                                    </div>
                                    <div class="card">
                                        @if (isset($status_log) && $status_log->isNotEmpty())
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>S.NO</th>
                                                                <th>From Status</th>
                                                                <th>To Status</th>
                                                                <th>Remarks</th>
                                                                <th>Approved By</th>
                                                                <th>Created By</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($status_log as $log)
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ getInspectionStatus($log->from_status) }}</td>
                                                                    <td>{{ getInspectionStatus($log->to_status) }}</td>
                                                                    <td>{{ $log->remarks ?? 'N/A' }}</td>
                                                                    <td>{{ getUserName($log->approved_by) ?: '-' }}</td>
                                                                    <td>{{ getUserName($log->created_by) ?: '-' }}</td>
                                                                    <td>{{ displaydateformat($log->created_at) }}</td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card-body">
                                                <p class="text-muted">{{ __('No status logs available.') }}</p>
                                            </div>
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
@stop
