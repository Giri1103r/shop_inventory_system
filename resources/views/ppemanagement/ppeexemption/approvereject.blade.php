@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Exemption Request')
@section('pageurl', admin_url('ppe_exemption/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_exemption/list') }}"></x-button-back>

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
                                            {{ isset($ppeexemption->emp_id) ? $ppeexemption->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->emp_name) ? $ppeexemption->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($ppeexemption->department) ? $ppeexemption->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($ppeexemption->unit) ? $ppeexemption->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Company') }}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($ppeexemption->company) ? $ppeexemption->company : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('From Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->from_date) ? $ppeexemption->from_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('To Date') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->to_date) ? $ppeexemption->to_date : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
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
                                        <label class="form-label view_label">Files</label>
                                        @if (isset($ppefiles) && $ppefiles->count() > 0)
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($ppefiles as $file)
                                            <p>
                                                @php
                                                    $fileExtension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION));
                                                @endphp

                                                @if (in_array($fileExtension, ['docx', 'pdf', 'doc']))
                                                    <a href="{{ asset('' . $file->file_path) }}" target="_blank">
                                                        <i class="fa-solid fa-eye text-danger"></i> View
                                                    </a>
                                                @elseif (in_array($fileExtension, ['png', 'jpg', 'jpeg']))

                                                    <a href="{{ asset('' . $file->file_path) }}" target="_blank">
                                                        <img src="{{ asset('' . $file->file_path) }}" alt="image" style="max-width: 100px; max-height: 100px;">
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

                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{ __('Reason') }}</label>
                                        <div class="view_data">
                                            {{ isset($ppeexemption->reason) ? $ppeexemption->reason : '' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Approval</h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="requestApprovalForm"
                                        action="{{ admin_url('ppe_exemption/approvereject/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <div class="">
                                            <div class="mb-3 row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="approver_name" class="form-label require">Approver Name</label>
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
                                            <button type="submit" name="action" value="reject"
                                                class="btn btn-danger w-100">Reject</button>
                                        </div>
                                    </form>
                                </div>


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
