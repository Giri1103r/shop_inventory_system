@extends('admin.layouts.admin')
@section('title', 'Ohc Floor Stretcher Checklist ')
@section('pageurl', admin_url('ohc/floor_stretcher/checklist/list'))
@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

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
                                        href="{{ admin_url('ohc/floor_stretcher/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetygalleryAdd"
                                        action="{{ admin_url('ohc/floor_stretcher/checklist/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shifts as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.frequency') }}</label>
                                                    <select name="frequency_id" id="frequency_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Frequency</option>
                                                        @foreach ($frequency as $frequency)
                                                            <option value="{{ encryptId($frequency->id) }}">
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <table class="container p-5 table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.sr_no') }}
                                                        </th>
                                                        <th rowspan="2"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:12%;"
                                                            class="require">
                                                            {{ __('inspection.resource_code') }}
                                                        </th>
                                                        <th rowspan="2"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:12%;"
                                                            class="require">
                                                            {{ __('inspection.dept/location') }}
                                                        </th>
                                                        <th colspan="6"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:48%;"
                                                            class="require">
                                                            {{ __('inspection.checkpoints') }}
                                                        </th>
                                                        <th rowspan="2"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:20%;"
                                                            class="require">
                                                            {{ __('inspection.remarks') }}
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_first') }}
                                                        </th>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_second') }}
                                                        </th>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_third') }}
                                                        </th>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_fourth') }}
                                                        </th>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_fifth') }}
                                                        </th>
                                                        <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;"
                                                            class="require">
                                                            {{ __('inspection.fs_sixth') }}
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($checklist_details as $key => $details)
                                                        @foreach ($details as $index => $checklist)
                                                            <tr>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    {{ $loop->iteration }}
                                                                </td>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <input type="text"
                                                                        name="resource_code[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]"
                                                                        class="form-control">
                                                                </td>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    {{ $checklist->checklist_name }}
                                                                </td>
                                                                <td style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;"
                                                                    class="form-input">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_first]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_first]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_second]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_second]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_third]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_third]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_fourth]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_fourth]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_fifth]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_fifth]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold; text-align: center;">
                                                                    <div
                                                                        style="display: flex; flex-direction: column; gap: 5px;">
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_sixth]"
                                                                                value="YES"
                                                                                class="validate-radio-required"> Yes
                                                                        </label>
                                                                        <label>
                                                                            <input type="radio"
                                                                                name="response[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}][fs_sixth]"
                                                                                value="NO"
                                                                                class="validate-radio-required"> No
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; text-align: center;">
                                                                    <textarea class="form-control" name="remarks[{{ $checklist->sub_type_id }}][{{ $checklist->checklist_id }}]"
                                                                        rows="4" style="resize:none;"></textarea>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>


                                        </div>
                                        {{-- <div class="row m-2">
                                            <div class="col-md-4 form-group form-input mb-2">
                                                @if (isset(Auth::user()->signature_upload))
                                                    <label class="form-label"
                                                        style="display: block; ">{{ __('inspection.signature') }}</label>
                                                    <img src="{{ admin_url(Auth::user()->signature_upload) }}"
                                                        alt="Signature Upload" style="width: 150px; margin-top:-10px">
                                                @else
                                                    <div class="form-input col-md-12 mb-2">
                                                        <label class="form-label require">Signature</label>
                                                        <input type="file" name="signature_image"
                                                            id="signature_upload" class="form-control form-control-sm"
                                                            accept="image/*" placeholder="Enter the image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload" class="text-danger"></div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div> --}}



                                        <div class="submit-button m-2" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
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
        <script type="text/javascript" nonce="projectcab">
            $(document).ready(function() {
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });
                flatpickr("#issue_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr("#inspection_date", {
                    dateFormat: "d-m-Y",
                });
            });
            $(function() {


                $('#safetygalleryAdd').validate({
                    rules: {

                        issue_date: {
                            required: true,
                        },
                        inspection_date: {
                            required: true,
                        },
                        shift: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        resource_code: {
                            required: true,
                            minlength: 3,
                            maxlength: 30,
                        },
                        frequency_id: {
                            required: true,
                        },
                        "remarks[*][*]": {
                            required: true,
                            minlength: 3,
                            maxlength: 300,
                        },
                        signature_image:{
                            required: true,
                            filesize: 15728640,
                        }

                    },
                    messages: {

                        issue_date: {
                            required: "Date Of Audit is required",
                        },
                        inspection_date: {
                            required: "Inspeciton Date is required",
                        },
                        shift: {
                            required: "Shift is required",
                        },
                        unit_id: {
                            required: "Unit is required",
                        },
                        frequency_id: {
                            required: "Frequency is required",
                        },
                        signature_image:{
                            required: "Signature Image is required",
                            filesize: "File size should not exceed 15 MB",
                        }
                        resource_code: {
                            required: 'Recource Code is requried',
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 30",
                        },
                        "remarks[*][*]": {
                            required: "Remarks is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 300",
                        }

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
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " +
                                error
                                .message);
                        });
                    }
                });

                $('textarea[name^="remarks"]').each(function() {
                    $(this).rules("add", {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        messages: {
                            required: "Remarks is required",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 255",
                        }
                    });
                });
            });
        </script>
    @endpush
