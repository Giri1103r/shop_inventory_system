@extends('admin.layouts.admin')
@section('title', 'Discard Medicine Approval')
@section('pageurl', admin_url('ohc/medicine-expire-report/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/discard/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Discard Medicine Details</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Medicine Name') }}</label>
                                        <div class="view_data">
                                            {{ getMedicinename(isset($user_discard->medicine_id) ? $user_discard->medicine_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ (isset($user_discard->quantity) ? $user_discard->quantity : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($user_discard->unit_id) ? $user_discard->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Discard Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($user_discard->expire_date) ? $user_discard->expire_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($user_discard->created_by) ? $user_discard->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($user_discard->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($user_discard->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>

                                </div>
{{-- Paramedicies Request  --}}

                                <div>
                                    <form method="POST" id="paramedicesForm"
                                        action="{{ admin_url('ohc/discard/approval/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ encryptId($user_discard->id) }}">
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">EHS Head Approval</h4>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-input">
                                                    <label for="ehs_head" class="require form-label">Approver Name</label>
                                                    <input type="text" name="approver_name" id="approver_name"
                                                        class="form-control" value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-input">
                                                    <label for="ehs_head" class="require form-label">Date</label>
                                                    <input type="text" name="date" id="date" class="form-control"
                                                        value="{{ date('d-m-Y H:i:s') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1 form-input">
                                                    <label for="remarks" class="form-label require">Remarks</label>
                                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3"></textarea>
                                                    <div class="text-danger" id="remarks_error"></div>
                                                    @error('remarks')
                                                        <span id="remark_error" class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex float-end gap-2 mx-auto">
                                            <button type="submit" name="action" value="approve"
                                                class="btn btn-success w-100">Approve</button>
                                                {{-- <button type="submit" name="action" value="reject"
                                                class="btn btn-danger w-100">Reject</button> --}}
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
