@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving Approval')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))


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
                                    <x-button-back
                                        href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medicine Receving Form</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->medicine) ? $medicine->medicine : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('HSN Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->hsn) ? $medicine->hsn : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Pack Details') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine->pack) ? $medicine->pack : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->quantity) ? $medicine_receiving->quantity : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Batch Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->batch_number) ? $medicine_receiving->batch_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Rate') }}</label>
                                        <div class="view_data">
                                            {{ isset($medicine_receiving->rate) ? $medicine_receiving->rate : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Expire Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($medicine_receiving->expire_date) ? $medicine_receiving->expire_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Vendor Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($vendor->vendor_name) ? $vendor->vendor_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($medicine_receiving->created_by) ? $medicine_receiving->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($medicine_receiving->created_at) }}
                                        </div>
                                    </div>

                                </div>
                                @if ($medicine_receiving->approve_status == STATUS_OHC_EHS_VERIFICATION_PENDING)
                                    <div class="row">
                                        <form method="POST" id="paramedicesForm"
                                            action="{{ admin_url('ohc/medicine-receiving-form/requestapproval/submit') }}">
                                            @csrf
                                            <input type="hidden" name="id"
                                                value="{{ encryptId($medicine_receiving->id) }}">
                                            <div class="row">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">EHS Officer Verification</h4>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-input">
                                                        <label for="ehs_head" class="require form-label">Approver
                                                            Name</label>
                                                        <input type="text" name="approver_name" id="approver_name"
                                                            class="form-control" value="{{ Auth::user()->name }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-input">
                                                        <label for="ehs_head" class="require form-label">Date</label>
                                                        <input type="text" name="date" id="date"
                                                            class="form-control" value="{{ date('d-m-Y H:i:s') }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="mb-1 form-input">
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
                                            <div class="d-flex float-end gap-2 mx-auto">
                                                <button type="submit" name="action" value="approve"
                                                    class="btn btn-success w-100">Forward</button>
                                                {{-- <button type="submit" name="action" value="reject"
                                            class="btn btn-danger w-100">reject</button> --}}
                                            </div>
                                        </form>
                                    </div>
                            </div>
                            @endif

                            {{-- View of the EHS Verification --}}
                            @if ($medicine_receiving->approve_status == STATUS_OHC_L1_EHS_VERIFICATION_PENDING)
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Verification</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                            <div class="view_data">
                                                {{ getUsername(isset($ehsverify->created_by) ? $ehsverify->created_by : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($ehsverify->created_at) ? $ehsverify->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                            <div class="view_data">
                                                {{ displaytimeformat(isset($ehsverify->created_at) ? $ehsverify->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12 form-input">
                                            <label class="form-label view_label">{{ __('Remarks') }}</label>
                                            <div class="view_data">
                                                {{ isset($ehsverify->remarks) ? $ehsverify->remarks : '' }}
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

@stop
@push('script')
    <script>
        $(function() {
            // Add custom regex rule
            $.validator.addMethod(
                "regex",
                function(value, element, regex) {
                    return this.optional(element) || regex.test(value);
                },
                "Invalid format."
            );

            $('#paramedicesForm').validate({
                rules: {
                    remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,

                    },
                },
                messages: {
                    medicine: {
                        required: "Remarks is required",
                        minlength: "Minimum 3 characters are needed",
                        maxlength: "Maximum Characters should not be exceed more than 600",
                    },
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-input').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    console.log("Form has " + errors + " invalid fields.");
                },
            });
        });
    </script>
@endpush
