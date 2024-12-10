@extends('admin.layouts.admin')
@section('title', 'PPE Request EHS Approval')
@section('pageurl', admin_url('ppe_request/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">PPE Request </h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="requestApprovalForm" action="{{ admin_url('ppe_request/ehsapprovereject/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <div class="">
                                            <div class="mb-3 row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="approver_name" class="form-label">Approver Name</label>
                                                    <input type="text" class="form-control form-control-sm" id="approver_name" readonly value="{{ Auth::user()->name }}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="date" class="form-label">Date</label>
                                                    <input type="text" class="form-control form-control-sm" id="date" name="date" readonly value="{{ date('d-m-Y H:i:s') }}">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="mb-1">
                                                        <label for="remarks" class="form-label">Remarks</label>
                                                        <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"></textarea>
                                                        <div class="text-danger" id="remarks_error"></div>
                                                        @error('remarks')
                                                            <span id="remark_error" class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="d-flex float-end gap-2 mx-auto">
                                            <button type="submit" name="action" value="approve" class="btn btn-success w-100">Approve</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger w-100">Reject</button>
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
                        maxlength: 255,
                        regex: /^[a-zA-Z0-9\s]+$/
                    },
                },
                messages: {

                    remarks: {
                        required: " Remarks cannot be empty.",
                        minlength: "Remarks  must contain between 3 and 255 characters.",
                        maxlength: "Remarks must contain between 3 and 255 characters.",
                        regex: "Remarks must contain only letters and numbers."
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

