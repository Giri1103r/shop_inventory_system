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
                                        <h4 class="text-white">User Details</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_id) ? $pperequest->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($pperequest->emp_name) ? $pperequest->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($pperequest->department) ? $pperequest->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item Code') }}</label>
                                        <div class="view_data">
                                            {{ getItemCode(isset($pperequest->item_code) ? $pperequest->item_code : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Name') }}</label>
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
                                        <label class="form-label view_label">{{ __('Created By') }}</label>
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
                                        <label class="form-label view_label">Image</label>
                                        @if (isset($pperequest) && $pperequest && $pperequest->ppe_image)
                                            <p>
                                                <a href="{{ asset('public/' . $pperequest->ppe_image) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('public/' . $pperequest->ppe_image) }}"
                                                        style="width: 100px" alt="image">
                                                </a>
                                            </p>
                                        @else
                                            <p>No image is uploaded</p>
                                        @endif

                                    </div>
                                @endif
                                    <div class="mb-3 col-md-12 form-input">
                                        <label class="form-label view_label">{{ __('Reason') }}</label>
                                        <div class="view_data">
                                            {{ !empty($pperequest->employee_reason) ? $pperequest->employee_reason : $pperequest->employee_remarks }}


                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Stock Inventory Details</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ORG') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->org) ? $stockdata->org : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Inventory Item ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->inventory_item_id) ? $stockdata->inventory_item_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Item Code') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->item_code) ? $stockdata->item_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('PPE Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->ppe_name) ? $stockdata->ppe_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('UOM') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->uom) ? $stockdata->uom : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Quantity') }}</label>
                                        <div class="view_data">
                                            {{ isset($stockdata->quantity) ? $stockdata->quantity : '' }}
                                        </div>
                                    </div>


                                </div>

                                @if (!checkUserRole(ROLE_STORE_MANAGER))
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Previous History</h4>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Employee Name</th>
                                                    <th>Employee ID</th>
                                                    <th>Previous Applied Date</th>
                                                    <th>Approval Status</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($userdata->isEmpty())
                                                    <tr>
                                                        <td class="text-center" colspan="6">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($userdata as $data)
                                                        <tr class="hover-row">
                                                            <td>{{ $data->emp_name }}</td>
                                                            <td>{{ $data->emp_id }}</td>
                                                            <td>{{ displaydateformat($data->created_at) }}</td>
                                                            <td>
                                                                @if ($data->approve_status == STATUS_HOD_APPROVAL_PENDING)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>HOD
                                                                        Approval Pending</span>
                                                                @elseif ($data->approve_status == STATUS_HOD_APPROVED)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>HOD
                                                                        Approved</span>
                                                                @elseif ($data->approve_status == STATUS_USER_APPLIED)
                                                                    <span class='badge bg-primary'
                                                                        style='font-size: 1.0em;'>User Applied</span>
                                                                @elseif ($data->approve_status == STATUS_HOD_REJECTED)
                                                                    <span class='badge bg-danger'
                                                                        style='font-size: 1.0em;'>HOD
                                                                        Rejected</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_APPROVAL_PENDING)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>EHS
                                                                        Officer Approval Pending</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_APPROVED)
                                                                    <span class='badge bg-info'
                                                                        style='font-size: 1.0em;'>EHS
                                                                        Officer Approved</span>
                                                                @elseif ($data->approve_status == STATUS_EHS_REJECTED)
                                                                    <span class='badge bg-danger'
                                                                        style='font-size: 1.0em;'>EHS
                                                                        Officer Rejected</span>
                                                                @elseif ($data->approve_status == STATUS_ISSUED)
                                                                    <span class='badge bg-success'
                                                                        style='font-size: 1.0em;'>Issued</span>
                                                                @endif
                                                            </td>
                                                            {{-- <td>{{ removeUnderScore(getStatus($data['ehs_approve_status'])) }} --}}

                                                            <td>{{ $data->remarks }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>

                                    </div>
                                @endif



                                @if ($pperequest->approve_status != STATUS_EHS_APPROVED && $pperequest->approve_status != STATUS_ISSUED )
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Approval </h4>
                                        </div>
                                    </div>
                                    <div class="basic-form mb-3">
                                        <form method="POST" id="requestApprovalForm"
                                            action="{{ admin_url('ppe_request/ehsapprovereject/submit') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $encryptid }}">
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
                                                <button type="submit" name="action" value="reject"
                                                    class="btn btn-danger w-100">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- list for hod approval  --}}

                                @if ( $pperequest->approve_status == STATUS_EHS_APPROVED  )

                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">HOD Approval</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved By') }}</label>
                                            <div class="view_data">
                                                {{ getUsername(isset($statuslog->created_by) ? $statuslog->created_by : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($statuslog->created_at) ? $statuslog->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                            <div class="view_data">
                                                {{ displaytimeformat(isset($statuslog->created_at) ? $statuslog->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Remarks') }}</label>
                                            <div class="view_data">
                                                {{ isset($statuslog->remarks) ? $statuslog->remarks : '' }}
                                            </div>
                                        </div>



                                    </div>
                                @endif

                                {{-- list for ehs approval --}}

                                @if ($pperequest->approve_status == STATUS_EHS_APPROVED )
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Approval</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved By') }}</label>
                                            <div class="view_data">
                                                {{ getUsername(isset($ehslogdata->created_by) ? $ehslogdata->created_by : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($ehslogdata->created_at) ? $ehslogdata->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                            <div class="view_data">
                                                {{ displaytimeformat(isset($ehslogdata->created_at) ? $ehslogdata->created_at : '') }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Remarks') }}</label>
                                            <div class="view_data">
                                                {{ isset($ehslogdata->remarks) ? $ehslogdata->remarks : '' }}
                                            </div>
                                        </div>



                                    </div>
                                @endif

                                {{-- @dd($pperequest) --}}
                                {{-- @if ($pperequest->approve_status == STATUS_EHS_APPROVED && checkUserRole(ROLE_STORE_MANAGER)) --}}
                                @if (checkUserRole(ROLE_STORE_MANAGER)  || checkUserRole(ROLE_SUPERADMIN) && $pperequest->approve_status == STATUS_EHS_APPROVED )
                                    <div class="row mt-2">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Store manager</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="StorerequestApprovalForm"
                                            action="{{ admin_url('ppe_request/storemanger/issued') }}">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $encryptid }}">
                                            <div class="">
                                                <div class="mb-3 row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="approver_name" class="form-label require">Approver
                                                            Name</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="store_approver_name" readonly
                                                            value="{{ Auth::user()->name }}">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" class="form-control form-control-sm"
                                                            id="store_date" name="date" readonly
                                                            value="{{ date('d-m-Y H:i:s') }}">
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <div class="mb-1">
                                                            <label for="remarks"
                                                                class="form-label require">Remarks</label>
                                                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="store_remarks" name="store_remarks"
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
                                                <button type="submit" name="action" value="issue"
                                                    class="btn btn-success w-100">Issued</button>
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

        $(document).ready(function() {
            $('#StorerequestApprovalForm').validate({
                rules: {
                    store_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 600,


                    },
                },
                messages: {

                    store_remarks: {
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
