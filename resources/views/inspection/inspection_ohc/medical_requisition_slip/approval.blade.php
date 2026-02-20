@extends('admin.layouts.admin')
@section('title', ' Medical Requisition Slip-Floor')
@section('pageurl', admin_url('ohc/medical-requisition-slip/list'))

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
                                    <x-button-back
                                        href="{{ admin_url('ohc/medical-requisition-slip/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.medicine_requisition_slip_floor') }}
                                        </h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicinerequisition->date) ? $medicinerequisition->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($medicinerequisition->unit) ? $medicinerequisition->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($medicinerequisition->department) ? $medicinerequisition->department : '') }}
                                        </div>
                                    </div>
                                  
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($medicinerequisition->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicinerequisition->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($medicinerequisition->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">{{ __('common.sno') }}</th>
                                                <th style="text-align: center">{{ __('ohc_management.medicine_name') }}
                                                </th>
                                                <th style="text-align: center">{{ __('ohc_management.freeze_quantity') }}
                                                </th>
                                                <th style="text-align: center">{{ __('ohc_management.quantity') }}</th>
                                                <th style="text-align: center">{{ __('ohc_management.remarks') }}</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($inspection_data as $medicines)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            {{ getMedicinename($medicines['medicine_id']) }}
                                                        <td class="text-center">
                                                            {{ $medicines['freeze_quantity'] }}
                                                        <td class="text-center">{{ $medicines['quantity'] }}

                                                        <td class="text-center">{{ $medicines['remarks'] }}
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                @if (
                                    (checkUserRole(ROLE_FLOOR_MANAGER) && $medicinerequisition->approve_status == FLOOR_MANAGER_APPROVAL_PENDING) ||
                                        (checkUserRole(ROLE_SUPERADMIN) && $medicinerequisition->approve_status == FLOOR_MANAGER_APPROVAL_PENDING))
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">
                                                {{ __('ohc_management.floor_manager_approval_pending') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="basic-form">
                                            <form method="POST" id="FloorApprovalForm" enctype="multipart/form-data"
                                                action="{{ admin_url('ohc/medical-requisition-slip/floormanagerapproval/submit') }}">
                                                @csrf
                                                <input type="hidden" name="id"
                                                    value="{{ encryptId($medicinerequisition->id) }}">
                                                <div class="">
                                                    <div class="mb-3 row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="approver_name"
                                                                class="form-label require">{{ __('ohc_management.approver_name') }}</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="floor_approver_name" readonly
                                                                value="{{ Auth::user()->name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="date"
                                                                class="form-label require">{{ __('common.date') }}</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="floor_date" name="floor_date" readonly
                                                                value="{{ date('d-m-Y H:i:s') }}">
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <div class="mb-1">
                                                                <label for="remarks"
                                                                    class="form-label require">{{ __('ohc_management.remarks') }}</label>
                                                                <textarea class="form-control @error('remarks') is-invalid @enderror" id="floor_remarks" name="floor_remarks"
                                                                    rows="3"></textarea>
                                                                <div class="text-danger" id="remarks_error"></div>
                                                                @error('remarks')
                                                                    <span id="remark_error"
                                                                        class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex float-end gap-2 mx-auto">
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-success w-100">Approve</button>
                                                    <button type="submit" name="action" value="reject"
                                                        class="btn btn-danger w-100">Reject</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                                @if (
                                    $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVAL_PENDING ||
                                        $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVED ||
                                        $medicinerequisition->approve_status == SAFETY_OFFICER_REJECTED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('ohc_management.floor_manager_approval') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approver_name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($floormanger->created_by) ? $floormanger->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                                                </div>
                                            </div>


                                            <div class="mb-3 col-md-12 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($floormanger->remarks) ? $floormanger->remarks : '' }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif


                                @if (
                                    (checkUserRole(ROLE_SAFETY_OFFICER) && $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) ||
                                        (checkUserRole(ROLE_SUPERADMIN) && $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVAL_PENDING) ||
                                        (checkUserRole(ROLE_MEDICAL_ASSISTANT) &&
                                            $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVAL_PENDING))
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">
                                                {{ __('ohc_management.safety_officer_approval_pending') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="basic-form">
                                            <form method="POST" id="safetyofficerApprovalForm"
                                                enctype="multipart/form-data"
                                                action="{{ admin_url('ohc/medical-requisition-slip/safetyofficerapproval/submit') }}">
                                                @csrf
                                                <input type="hidden" name="id"
                                                    value="{{ encryptId($medicinerequisition->id) }}">
                                                <div class="">
                                                    <div class="mb-3 row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="approver_name"
                                                                class="form-label require">{{ __('ohc_management.approver_name') }}</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="approver_name" readonly
                                                                value="{{ Auth::user()->name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="date"
                                                                class="form-label require">{{ __('common.date') }}</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="date" name="date" readonly
                                                                value="{{ date('d-m-Y H:i:s') }}">
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <div class="mb-1">
                                                                <label for="remarks"
                                                                    class="form-label require">{{ __('ohc_management.remarks') }}</label>
                                                                <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"></textarea>
                                                                <div class="text-danger" id="remarks_error"></div>
                                                                @error('remarks')
                                                                    <span id="remark_error"
                                                                        class="text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                                <div class="d-flex float-end gap-2 mx-auto">
                                                    <button type="submit" name="action" value="approve"
                                                        class="btn btn-success w-100">Approve</button>
                                                    <button type="submit" name="action" value="reject"
                                                        class="btn btn-danger w-100">Reject</button>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif

                                @if (
                                    $medicinerequisition->approve_status == SAFETY_OFFICER_APPROVED ||
                                        $medicinerequisition->approve_status == SAFETY_OFFICER_REJECTED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('ohc_management.safety_officer_approval') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approver_name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($safetyofficer->created_by) ? $safetyofficer->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($safetyofficer->created_at) ? $safetyofficer->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.approved_time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($safetyofficer->created_at) ? $safetyofficer->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-12 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('ohc_management.remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($safetyofficer->remarks) ? $safetyofficer->remarks : '' }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@stop
@push('script')
    <script>
        $(document).ready(function() {
            $('#safetyofficerApprovalForm').validate({
                rules: {
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,


                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {

                    remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 600 characters.",
                        maxlength: "Remarks must contain between 3 and 600 characters.",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File size must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
        // floor manager validation

        $(document).ready(function() {
            $('#FloorApprovalForm').validate({
                rules: {
                    floor_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,


                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // }
                },
                messages: {

                    remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 600 characters.",
                        maxlength: "Remarks must contain between 3 and 600 characters.",
                    },
                    // signature_image: {
                    //     required: "Signature is Required",
                    //     filesize: "File size must be less than 15MB."
                    // }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log(errors + " field(s) are invalid");
                    validator.errorList.forEach(function(error) {
                        console.log("Field: " + error.element.name + ", Error: " + error
                            .message);
                    });
                }
            });

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
