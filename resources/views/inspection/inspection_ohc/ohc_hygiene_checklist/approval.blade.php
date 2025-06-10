@extends('admin.layouts.admin')
@section('title', 'OHC HYGIENE CLEANING CHECKLIST')
@section('pageurl', admin_url('ohc/first-aid-record/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>


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
                                    <x-button-back href="{{ admin_url('ohc/first-aid-record/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">OHC HYGIENE CLEANING CHECKLIST</h4>
                                    </div>
                                </div>



                                <div class="row">


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ getShiftname(isset($inspection_details->shift_id) ? $inspection_details->shift_id : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($inspection_details->created_by) ? $inspection_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($inspection_details->created_at) ? $inspection_details->created_at : '') }}
                                        </div>
                                    </div>
                                </div>


                                <div class="table-responsive container mb-3">
                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Date
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Shift
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Description/Equipment
                                                </th>
                                                <th colspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Cleaning and Sanitization
                                                </th>
                                                <th rowspan="2"
                                                    style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                    Remarks</th>
                                                @isset($nursing_signature)
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 12px; background-color: #ccc; text-align: center;">
                                                        Nursing Officer Remarks</th>
                                                @endisset
                                            </tr>
                                            <tr>
                                                <td
                                                    style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                    YES</td>
                                                <td
                                                    style="border: 1px solid black; text-align: center; padding: 12px; background-color: #ccc;">
                                                    NO</td>
                                            </tr>

                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="border: 1px solid black; text-align: center; padding: 12px;">
                                                    {{ Displaydateformat($inspection_details->issue_date) }}
                                                </td>
                                                <td style="border: 1px solid black; padding: 12px;">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </td>
                                                <td style="border: 1px solid black; padding: 12px;">
                                                    {{ $inspection_details->inspection_question }}
                                                </td>
                                                <td colspan="2"
                                                    style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    @if ($inspection_details->inspection_value == 1)
                                                        <span style="color: green;">✅</span>
                                                    @else
                                                        <span style="color: red;">❌</span>
                                                    @endif

                                                </td>
                                                <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                    class="form-input">
                                                    {{ $inspection_details->cleaner_remarks }}
                                                </td>
                                                @if (isset($nursing_signatue))
                                                    <td style="border: 1px solid black; text-align: center; padding: 12px;"
                                                        class="form-input">
                                                        {{ $inspection_details->nursing_officer_remarks }}
                                                    </td>
                                                @endif
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                                {{-- <div class="container d-flex justify-content-between">
                                     <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Cleaner Signature') }}</label>
                                        <div class="view_data">
                                            <img src="{{ admin_url($cleaner_signature) }}" alt="Cleaner Signature"
                                                style="width:100px; height:100px;">
                                        </div>
                                    </div> 
                                    @isset($nursing_signature)
                                        <div class="container ">
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('Nursing Officer') }}</label>
                                                <div class="view_data">
                                                    <img src="{{ admin_url($cleaner_signature) }}" alt="Cleaner Signature"
                                                        style="width:100px; height:100px;">
                                                </div>
                                            </div>
                                        </div>
                                    @endisset
                                </div> --}}

                                @if ((checkUserRole(ROLE_NURSING_OFFICER) || isAdmin()) )
                                    <div class="row  mb-3">
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Nursing Officer Approval Pending</h4>
                                            </div>
                                        </div>

                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('ohc/ohc-hygiene-cleaning-checklist/verify/submit') }}"
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

                                                {{-- <div class="col-md-4 form-group form-input mb-2">
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
                                                </div> --}}

                                                <div class="col-md-12 mb-2 form-input" id="capa_remarks">
                                                    <label for="capa_remarks" class="form-label">Remarks</label>
                                                    <textarea id="capa_remarks" class="form-control" rows="3" placeholder="Please provide Remarks..."
                                                        name="capa_remarks"></textarea>
                                                </div>
                                                <div class="submit-button" style="text-align: right;">
                                                    <x-button-approve></x-button-approve>
                                                    <x-button-reject></x-button-reject>
                                                </div>
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

    @stop

    @push('script')
        <script>
            $('#forklistassessmentAdd').validate({
                rules: {
                    capa_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //    filesize: 15728640,
                    // }
                },
                messages: {
                    capa_remarks: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
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
