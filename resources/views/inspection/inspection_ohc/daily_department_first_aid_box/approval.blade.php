@extends('admin.layouts.admin')
@section('title', ' Daily Departmental First Aid Box')
@section('pageurl', admin_url('ohc/first-aid-box/daily-departmental/list'))

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
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/first-aid-box/daily-departmental/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Daily Departmental First Aid Box</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issue Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review Date</label>
                                        <div class="view_data">
                                            {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label"> Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($medicinerequisition->date) ? $medicinerequisition->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($medicinerequisition->unit) ? $medicinerequisition->unit : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($medicinerequisition->department) ? $medicinerequisition->department : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShift(isset($medicinerequisition->shift) ? $medicinerequisition->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">First Aid Box No</label>
                                        <div class="view_data">
                                            {{ isset($medicinerequisition->first_aid_box_no) ? $medicinerequisition->first_aid_box_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">First Aider</label>
                                        <div class="view_data">
                                            {{ getFirstAider(isset($medicinerequisition->first_aider) ? $medicinerequisition->first_aider : '') }}
                                        </div>
                                    </div>
                                    {{-- @php
                                        $signature = GetOHCSignature(
                                            $medicinerequisition->created_by,
                                            $medicinerequisition->id,
                                            OHC_TYPE_DAILY_DEPARTMENT_FIRST_AID_BOX,
                                        );
                                    @endphp
                                    @if (!empty($signature) && !empty($signature))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label" style="display: block;">
                                                    {{ __('inspection.signature') }}
                                                </label>
                                                <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                    style="width: 150px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif --}}
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
                                        <div class="table-responsive">
                                            <div class="col-md-12">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <th style="text-align: center">Sr. No.</th>
                                                        <th style="text-align: center">Medicine Name</th>
                                                        <th style="text-align: center">Available Quantity</th>
                                                        <th style="text-align: center">Expiry Date</th>
                                                        <th style="text-align: center">Remark</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($inspection_data as $medicines)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                                <td class="text-center">
                                                                    {{ getMedicinename($medicines['medicine_id']) }}
                                                                <td class="text-center">
                                                                    {{ $medicines['available_quantity'] }}
                                                                <td class="text-center">
                                                                    {{ Displaydateformat($medicines['expired_date']) }}
                                                                <td class="text-center">{{ $medicines['remarks'] }}
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (
                                    (checkUserRole(ROLE_FLOOR_MANAGER) && $medicinerequisition->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING) ||
                                        (checkUserRole(ROLE_SUPERADMIN) &&
                                            $medicinerequisition->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING) ||
                                        (checkUserRole(ROLE_MEDICAL_ASSISTANT) &&
                                            $medicinerequisition->approve_status == MEDICAL_ASSISTANT_APPROVAL_PENDING))
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Floor Manager /Medical Assistant Approval Pending</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="basic-form">
                                            <form method="POST" id="FloorApprovalForm" enctype="multipart/form-data"
                                                action="{{ admin_url('ohc/first-aid-box/daily-departmental/floormanagerapproval/submit') }}">
                                                @csrf
                                                <input type="hidden" name="id"
                                                    value="{{ encryptId($medicinerequisition->id) }}">
                                                <div class="">
                                                    <div class="mb-3 row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="approver_name" class="form-label require">Approver
                                                                Name</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="floor_approver_name" readonly
                                                                value="{{ Auth::user()->name }}">
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="date" class="form-label require">Date</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="floor_date" name="floor_date" readonly
                                                                value="{{ date('d-m-Y H:i:s') }}">
                                                        </div>
                                                        {{-- <div class="col-md-4 form-group form-input mb-2">
                                                            @if (isset(Auth::user()->signature_upload))
                                                                <label class="form-label"
                                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                                <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                                    alt="Signature Upload"
                                                                    style="width: 150px; margin-top:-10px">
                                                            @else
                                                                <div class="form-input col-md-12 mb-2">
                                                                    <label class="form-label require">Signature</label>
                                                                    <input type="file" name="signature_image"
                                                                        id="signature_upload"
                                                                        class="form-control form-control-sm"
                                                                        accept="image/*" placeholder="Enter the image">
                                                                    <small>Allowed file types: jpg, jpeg, png</small>
                                                                    <div id="signature_upload" class="text-danger"></div>
                                                                </div>
                                                            @endif
                                                        </div> --}}
                                                        <div class="col-md-12 mb-3">
                                                            <div class="mb-1">
                                                                <label for="remarks"
                                                                    class="form-label require">Remarks</label>
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
                                    $medicinerequisition->approve_status == MEDICAL_ASSISTANT_APPROVED ||
                                        $medicinerequisition->approve_status == MEDICAL_ASSISTANT_REJECTED)
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Floor Manager Approval </h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="row">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approver Name') }}</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($floormanger->created_by) ? $floormanger->created_by : '') }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approved Date') }}</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Approved Time') }}</label>
                                                <div class="view_data">
                                                    {{ displaytimeformat(isset($floormanger->created_at) ? $floormanger->created_at : '') }}
                                                </div>
                                            </div>

                                            {{-- @if (isset($floormanagersignature))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($floormanagersignature->file_path) }}"
                                                            alt="Signature Upload"
                                                            style="width: 150px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @elseif(!empty($floorapproversignatureview) && !empty($floorapproversignatureview->signature_upload))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"
                                                            style="display: block;">{{ __('inspection.signature') }}</label>
                                                        <img src="{{ admin_url($floorapproversignatureview->signature_upload) }}"
                                                            alt="Approver Signature"
                                                            style="width: 150px; margin-top: -10px;" />

                                                    </div>
                                                </div>
                                            @endif --}}
                                            <div class="mb-3 col-md-12 form-input">
                                                <label class="form-label view_label">{{ __('Remarks') }}</label>
                                                <div class="view_data">
                                                    {{ isset($floormanger->remarks) ? $floormanger->remarks : '' }}
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
