@extends('admin.layouts.admin')
@section('title', 'Monthly First Aid Box Audit Checklist Show')
@section('pageurl', admin_url('ohc/first-aid-box/monthly-audit/list'))

@push('style')
    <style>
        .view_label {
            display: block;

        }

        .image-wrapper {
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endpush


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/first-aid-box/monthly-audit/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Monthly First Aid Box Audit Checklist</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($monthly_first_aid->doc_no) ? $monthly_first_aid->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review date</label>
                                        <div class="view_data">
                                            {{ isset($monthly_first_aid->revision_date) ? $monthly_first_aid->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issued Date</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($monthly_first_aid->issue_date) ? $monthly_first_aid->issue_date : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Inspection</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($monthly_first_aid->date_of_inspection) ? $monthly_first_aid->date_of_inspection : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShift(isset($monthly_first_aid->shift) ? $monthly_first_aid->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Frequency</label>
                                        <div class="view_data">
                                            {{ getFrequencyname(isset($monthly_first_aid->frequency) ? $monthly_first_aid->frequency : '') }}
                                        </div>
                                    </div>

                                    @if (!empty($requestorsignature) && !empty($requestorsignature->requestor_file_path))
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label" style="display: block;">
                                                {{ __('inspection.signature') }}
                                            </label>
                                            <img src="{{ admin_url($requestorsignature->requestor_file_path) }}"
                                                alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                        </div>
                                    </div>
                                @else
                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label" style="display: block;">
                                            {{ __('inspection.signature') }}
                                        </label>
                                        <img src="{{ admin_url($signatureview->signature_upload) }}"
                                            alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                    </div>
                                </div>
                                @endif
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($monthly_first_aid->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($monthly_first_aid->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($monthly_first_aid->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Monthly First Aid Box Audit Checklist</h4>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>S.NO</th>
                                                    <th>Unit</th>
                                                            <th>Department</th>
                                                            <th>First Aid Box Number</th>
                                                            <th>Does the first-aid register is being properly maintened as &
                                                                when require.</th>
                                                            <th>Does the first-aid box is bieng inspect as per periodicity.</th>
                                                            <th>Does the First- aid box inspection Checklist is being filled as per periodicity.</th>
                                                            <th>Does the First-aid box is being maintained as per the freeze
                                                                quantity.</th>
                                                            <th>Does the medical requisition slip record is being
                                                                maintained.</th>
                                                            <th>Does the first-aid box is clean.</th>
                                                            <th>Does the first-aid box sticker available.</th>
                                                            <th>Does the First aid material index is available.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (empty($monthly_first_aid_audit_checklist) || $monthly_first_aid_audit_checklist->isEmpty())
                                                    <tr>
                                                        <td colspan="12" class="text-center">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($monthly_first_aid_audit_checklist as $index => $log)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ getUnitname($log->unit_id) }}</td>
                                                            <td>{{getDepartment( $log->department_id) }}</td>
                                                            <td>{{ $log->first_aid_box_no }}</td>
                                                            <td>
                                                                @if ($log->first_aid_register_maintained == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_box_inspect_periodicity == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_box_checklist_periodicity == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_box_freeze_quantity == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->medicine_requisition_slip_record == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_box_clean == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_box_sticker == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($log->first_aid_material_index == 1)
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
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
