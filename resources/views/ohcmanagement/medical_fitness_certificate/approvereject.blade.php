@extends('admin.layouts.admin')
@section('title', 'Medical Fitness Certificate Approval')
@section('pageurl', admin_url('ohc/medical-fitness/list'))


@section('content')
    <div class="clearfix"></div>
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
                                        <h4 class="text-white">User Details</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->emp_id) ? $medicalfitness->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->emp_name) ? $medicalfitness->emp_name : '' }}
                                        </div>
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicalfitness->date) ? $medicalfitness->date : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">File</label>
                                        @if (isset($medicalfitness) && $medicalfitness && $medicalfitness->file)
                                            <p>
                                                @php
                                                    $fileExtension = pathinfo($medicalfitness->file, PATHINFO_EXTENSION);
                                                @endphp
                                                @if (in_array($fileExtension, ['pdf', 'doc', 'docx']))
                                                    <a href="{{ asset('public/' . $medicalfitness->file) }}" target="_blank" >
                                                        <i class="fas fa-eye text-danger"></i> View
                                                    </a>
                                                @else
                                                    <a href="{{ asset('public/' . $medicalfitness->file) }}" target="_blank">
                                                        <img src="{{ asset('public/' . $medicalfitness->file) }}" style="width: 100px" alt="image">
                                                    </a>
                                                @endif
                                            </p>
                                        @else
                                            <p>No file is uploaded</p>
                                        @endif
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicalfitness->created_by) ?$medicalfitness->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicalfitness->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{ __('Remarks') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicalfitness->remarks) ? $medicalfitness->remarks : '' }}
                                        </div>
                                    </div>
                                </div>
                                @if (
                                    $medicalfitness->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING &&
                                        CheckUserrole(ROLE_DOCTOR) || ((  $medicalfitness->approve_status == STATUS_OHC_MEDICAL_DOCTOR_APPROVAL_PENDING) &&
                                        CheckUserrole(ROLE_SUPERADMIN)))
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Doctor Approval Pending</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="requestApprovalForm"
                                            action="{{ admin_url('ohc/medical-fitness/approvereject/submit') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ encryptId($medicalfitness->id) }}">
                                            <div class="">
                                                <div class="mb-3 row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="approver_name" class="form-label require">Approver
                                                            Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="approver_name" readonly value="{{ Auth::user()->name }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="date" name="date" readonly
                                                            value="{{ date('d-m-Y H:i:s') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <div class="mb-1">
                                                            <label for="remarks" class="form-label require">Remarks</label>
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

                                            </div>
                                        </form>
                                    </div>
                                @endif

                                {{-- view of doctor aapproval --}}

                                @if (
                                     $medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING )
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Doctor Approval Pending</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($doctorapprovalview->created_by) ? $doctorapprovalview->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($doctorapprovalview->created_at) ? $doctorapprovalview->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($doctorapprovalview->created_at) ? $doctorapprovalview->created_at : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-12 form-input">
                                                <label class="form-label view_label">{{ __('Remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($doctorapprovalview->remarks) ? $doctorapprovalview->remarks : '' }}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                @endif

                                @if(
                                    ($medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING && CheckUserrole(ROLE_EHS_HEAD)) ||
                                    ($medicalfitness->approve_status == STATUS_OHC_MEDICAL_EHS_HEAD_APPROVAL_PENDING && CheckUserrole(ROLE_SUPERADMIN))
                                )
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Head Approval</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="requestApprovalForm"
                                            action="{{ admin_url('ohc/medical-fitness/ehsheadapprove/submit') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ encryptId($medicalfitness->id) }}">
                                            <div class="">
                                                <div class="mb-3 row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="approver_name" class="form-label require">Approver
                                                            Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="approver_name" readonly value="{{ Auth::user()->name }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="date" name="date" readonly
                                                            value="{{ date('d-m-Y H:i:s') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <div class="mb-1">
                                                            <label for="remarks" class="form-label require">Remarks</label>
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


                                            </div>
                                        </form>
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
            $('#requestApprovalForm').validate({
                rules: {
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,


                    },
                },
                messages: {

                    remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 600 characters.",
                        maxlength: "Remarks must contain between 3 and 600 characters.",
                    },
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
