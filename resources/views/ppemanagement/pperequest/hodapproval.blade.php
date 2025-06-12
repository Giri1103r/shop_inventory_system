@extends('admin.layouts.admin')
@section('title', 'PPE Request')
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
                                        <h4 class="text-white">{{__('ppe_management.ppe_request_user_details')}}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_emp_id')}}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_id) ? $pperequest->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_emp_name')}}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_name) ? $pperequest->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.company')}}</label>
                                        <div class="view_data">
                                            {{ getCompanyname(isset($pperequest->company_id) ? $pperequest->company_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.location')}}</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($pperequest->location_id) ? $pperequest->location_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.unit')}}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($pperequest->unit_id) ? $pperequest->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.department')}}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($pperequest->department) ? $pperequest->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.item_code')}}</label>
                                        <div class="view_data">
                                            {{ getItemCode(isset($pperequest->item_code) ? $pperequest->item_code : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_name')}}</label>
                                        <div class="view_data">
                                            {{ (isset($pperequest->ppe_name) ? $pperequest->ppe_name : '') }}
                                        </div>
                                    </div>
                                    {{-- <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Type') }}</label>
                                        <div class="view_data">
                                            {{ getPpeType(isset($pperequest->ppe_type) ? $pperequest->ppe_type : '') }}
                                        </div>
                                    </div> --}}

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('common.created_by')}}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($pperequest->created_by) ? $pperequest->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($pperequest->created_at) }}
                                        </div>
                                    </div>
                                    @if ($pperequest->ppe_image != '')
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.image')}}</label>
                                        @if (isset($pperequest) && $pperequest && $pperequest->ppe_image)
                                            <p>
                                                <a href="{{ asset( $pperequest->ppe_image) }}"
                                                    target="_blank">
                                                    <img src="{{ asset( $pperequest->ppe_image) }}"
                                                        style="width: 100px" alt="image">
                                                </a>
                                            </p>
                                        @else
                                            <p>No image is uploaded</p>
                                        @endif

                                    </div>
                                @endif
                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.ppe_reason')}}</label>
                                        <div class="view_data">
                                            {{ !empty($pperequest->employee_reason) ? $pperequest->employee_reason : $pperequest->employee_remarks }}

                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{__('ppe_management.ppe_request_stock_inventory_details')}}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.org') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->org) ? $stockdata->org : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.inven_item_id') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->inventory_item_id) ? $stockdata->inventory_item_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{__('ppe_management.item_code')}}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->item_code) ? $stockdata->item_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->ppe_name) ? $stockdata->ppe_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_uom') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->uom) ? $stockdata->uom : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ppe_management.ppe_qunatity') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->quantity) ? $stockdata->quantity : '' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{__('ppe_management.ppe_hod_approval')}}</h4>
                                    </div>
                                </div>
                                <div class="basic-form">
                                    <form method="POST" id="requestApprovalForm" action="{{ admin_url('ppe_request/hodapprovereject/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $encryptid }}">
                                        <div class="">
                                            <div class="mb-3 row">
                                                <div class="col-md-4 mb-3">
                                                    <label for="approver_name" class="form-label require">{{__('ppe_management.ppe_approver_name')}}</label>
                                                    <input type="text" class="form-control form-control-sm" id="approver_name" readonly value="{{ Auth::user()->name }}">
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="date" class="form-label require">{{__('common.date')}}</label>
                                                    <input type="text" class="form-control form-control-sm" id="date" name="date" readonly value="{{ date('d-m-Y H:i:s') }}">
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="mb-1">
                                                        <label for="remarks" class="form-label require">{{__('common.remarks')}}</label>
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

