@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition')
@section('pageurl', admin_url('ohc/medicine-requisition/list'))


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
                                    <x-button-back href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.medicine_requisition') }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.req_id') }}</label>
                                        <div class="view_data">
                                            {{ isset($user_medicine_requisition->req_id) ? $user_medicine_requisition->req_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($user_medicine_requisition->unit_id) ? $user_medicine_requisition->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getdepartment(isset($user_medicine_requisition->department_id) ? $user_medicine_requisition->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('ohc_management.request_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_requisition->request_date) ? $user_medicine_requisition->request_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($user_medicine_requisition->created_by) ? $user_medicine_requisition->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_requisition->created_at) ? $user_medicine_requisition->created_at : '') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">

                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>{{ __('common.sno') }}</th>
                                                    <th>{{ __('ohc_management.medicine_name') }}</th>
                                                    <th>{{ __('ohc_management.available_quantity') }}</th>
                                                    <th>{{ __('ohc_management.quantity') }}</th>
                                                    <th>{{ __('ohc_management.remarks') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @if ($medicine_requisition->isEmpty())
                                                    <tr>
                                                        <td colspan="5" class="text-center">No data available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($medicine_requisition as $data)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                            <td>{{ $data->available_quantity }}</td>
                                                            <td>{{ $data->quantity }}</td>
                                                            <td>{{ $data->remarks }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('ohc_management.ehs_head_approval_pending') }}</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <form method="POST" id="ParamedicsForm"
                                        action="{{ admin_url('ohc/medicine-requisition/approval/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id"
                                            value="{{ encryptId($user_medicine_requisition->id) }}">
                                        <div class="row">

                                            <div class="col-md-4 mb-3">
                                                <div class="form-input">
                                                    <label for="ehs_head"
                                                        class="require form-label">{{ __('ohc_management.approver_name') }}</label>
                                                    <input type="text" name="approver_name" id="approver_name"
                                                        class="form-control" value="{{ Auth::user()->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-input">
                                                    <label for="ehs_head"
                                                        class="require form-label">{{ __('common.date') }}</label>
                                                    <input type="text" name="date" id="date" class="form-control"
                                                        value="{{ date('d-m-Y H:i:s') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="mb-1 form-input">
                                                    <label for="remarks"
                                                        class="form-label require">{{ __('ohc_management.remarks') }}</label>
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

                $('#ParamedicsForm').validate({
                    rules: {
                        remarks: {
                            required: true,
                            minlength: 3,
                            maxlength: 600,

                        },
                    },
                    messages: {
                        remarks: {
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
