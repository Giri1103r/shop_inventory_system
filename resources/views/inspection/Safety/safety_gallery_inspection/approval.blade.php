@extends('admin.layouts.admin')
@section('title', 'Safety Gallery Inspection')
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.safety_gallery_inspection') }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">
                                                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->date_of_inspection) ? $inspection_details->date_of_inspection : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.location') }}</label>
                                            <div class="view_data">
                                                {{ getLocationname(isset($inspection_details->location) ? $inspection_details->location : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.exact_location') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->excat_location) ? $inspection_details->excat_location : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.resource_code') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->resource_code) ? $inspection_details->resource_code : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    {{-- @dd($inspection_details); --}}


                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">
                                                {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
                                            </div>
                                        </div>
                                    </div>




                                    {{-- @php
                                        $signature = GetSafetySignature(
                                            $inspection_details->created_by,
                                            $inspection_details->id,
                                            SAFETY_GALLERY_INSPECTION,
                                        );
                                    @endphp
                                    @if (isset($signature))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label"
                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                    style="width: 100px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif --}}
                                    @php
                                        $user_response = json_decode($inspection_details->responses, true);
                                    @endphp
                                    <table class="container p-5" style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No
                                                </th>
                                                <th colspan="3"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Description
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Status
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $srNo = 1; @endphp
                                            @foreach ($user_response as $index => $item)
                                                <tr>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $srNo++ }}
                                                    </td>
                                                    <td colspan="3" style="border: 1px solid black; padding: 8px;">
                                                        {{ GetChecklistTypeDate($index) }}
                                                    </td>
                                                    <td
                                                        style="border: 1px solid black; padding: 8px; text-align: center; color:
                                                {{ strtoupper($item['answer']) == 'YES' ? 'green' : 'red' }};">
                                                        @if (strtoupper($item['answer']) == 'YES')
                                                            ✔
                                                        @else
                                                            ❌
                                                        @endif
                                                    </td>

                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $item['remarks'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if (
                                    $inspection_details->inspection_status == SAFETY_L2_MANAGER_APPROVAL_PENDING &&
                                        (checkUserRole(ROLE_L2_MANAGER) || isAdmin()))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.l2_manager_verify') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/safety-gallery-inspection/ehsofficer/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="name" id = "name" class="form-control"
                                                    value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="date" id = "date" class="form-control"
                                                    value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="remarks">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea id="remarks" class="form-control" rows="3" placeholder="Please Enter Remarks" name="remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <button class="btn btn-success" name="approved"
                                                    value="1">Approve</button>
                                                <button class="btn btn-warning" name="rejected"
                                                    value="2">Reject</button>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                                @if (
                                    ($inspection_details->inspection_status == SAFETY_EHS_HEAD_APPROVAL_PENDING ||
                                        $inspection_details->inspection_status == SAFETY_L2_MANAGER_REJECTED) &&
                                        (checkUserRole(ROLE_EHS_HEAD) || isAdmin()))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.l2_manager_verify') }}</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Approver Name</label>
                                                <div class="view_data">
                                                    {{ getUsername(isset($inspection_details->verified_by) ? $inspection_details->verified_by : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Approved Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($inspection_details->verified_date) ? $inspection_details->verified_date : '') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Remarks</label>
                                                <div class="view_data">
                                                    {{ (isset($inspection_details->remarks) ? $inspection_details->remarks : '') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (
                                    $inspection_details->inspection_status == SAFETY_EHS_HEAD_APPROVAL_PENDING &&
                                        (checkUserRole(ROLE_EHS_HEAD) || isAdmin()))
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_head_approval') }}</h4>
                                        </div>
                                    </div>
                                    <form method="POST" id="safetyGalleryAprpoval"
                                        action="{{ admin_url('safety/safety-gallery-inspection/ehshead/verify/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                            name="id">
                                        <div class="row">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.name') }}</label>
                                                <input type="text" name="ehs_name" id = "ehs_name"
                                                    class="form-control" value="{{ getUserName(Auth::id()) }}" readonly>
                                            </div>
                                            <div class="col-md-4 form-group form-input mb-2">
                                                <label class="form-label ">{{ __('inspection.date') }}</label>
                                                <input type="text" name="ehs_date" id = "ehs_date"
                                                    class="form-control" value="{{ todayDate() }}" readonly>
                                            </div>
                                            <div class="col-md-12 mb-2 form-input" id="remarks">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea id="ehs_remarks" class="form-control" rows="3" placeholder="Please Enter Remarks"
                                                    name="ehs_remarks"></textarea>
                                            </div>
                                            <div class="submit-button" style="text-align: right;">
                                                <button class="btn btn-success" name="approved"
                                                    value="1">Approve</button>
                                                <button class="btn btn-warning" name="rejected"
                                                    value="2">Reject</button>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/safety-gallery-inspection/list') }}"></x-button-cancel>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @stop
        @push('script')
            <script>
                $('#forklistassessmentAdd').validate({
                    rules: {
                        remarks: {
                            required: true,
                            minlength: 3,
                            maxlength: 600,
                            noSpaces: true,
                        },
                        // signature_image: {
                        //     required: true,
                        //     filesize: 15728640,
                        // }
                    },
                    messages: {
                        remarks: {
                            required: "Remarks is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 600",
                        },
                        // signature_image: {
                        //     required: "Signature is Required",
                        //     filesize: "File size should not exceed 15MB",
                        // }
                    },
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                        $(element).closest('.form-input').find('.invalid-feedback').remove();
                    },
                    submitHandler: function(form) {
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        validator.errorList.forEach(function(error) {});
                    }
                });

                $.validator.addMethod("noSpaces", function(value) {
                    return value.trim().length > 0;
                }, "Spaces are not allowed");



                $('#safetyGalleryAprpoval').validate({
                    rules: {
                        ehs_remarks: {
                            required: true,
                            minlength: 3,
                            maxlength: 600,
                            noSpaces: true,
                        },
                        // signature_image: {
                        //     required: true,
                        //     filesize: 15728640,
                        // }
                    },
                    messages: {
                        ehs_remarks: {
                            required: "Remarks is Required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 600",
                        },
                        // signature_image: {
                        //     required: "Signature is Required",
                        //     filesize: "File size must be less than 15MB."
                        // }
                    },
                    errorElement: 'div',
                    errorPlacement: function(error, element) {
                        error.addClass('invalid-feedback');
                        element.closest('.form-input').append(error);
                    },
                    highlight: function(element) {
                        $(element).addClass('is-invalid');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('is-invalid');
                        $(element).closest('.form-input').find('.invalid-feedback').remove();
                    },
                    submitHandler: function(form) {
                        form.submit();
                    },
                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        validator.errorList.forEach(function(error) {});
                    }
                });
            </script>
        @endpush
