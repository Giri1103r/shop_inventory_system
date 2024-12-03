@extends('admin.layouts.admin')
@section('title', 'PPE Exemption')
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
                                        <h4 class="text-white">PPE Exemption </h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="requestApprovalForm" action="{{ admin_url('ppe_exemption/approvereject/submit') }}">
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
                                                    <input type="text" class="form-control form-control-sm" id="date" name="date" readonly value="{{ today() }}">
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
            $('#requestApprovalForm').on('submit', function(e) {
                let valid = true;

                if (!validateRemarks()) valid = false;

                if (!valid) {
                    e.preventDefault();
                } else {
                    $('#submit').prop('disabled', true);
                }
            });

            function validateRemarks() {
                var remarks = $('#remarks').val();

                if (remarks === "") {
                    $('#remarks_error').text('Remarks cannot be empty.');
                    return false;
                } else if (remarks.length < 3 || remarks.length > 40 || !/^[a-zA-Z\s]+$/.test(remarks)) {
                    $('#remarks_error').text('Remarks must contain only letters and be between 3 and 40 characters long.');
                    return false;
                }
                $('#remarks_error').text('');
                return true;
            }
        });
    </script>
@endpush

