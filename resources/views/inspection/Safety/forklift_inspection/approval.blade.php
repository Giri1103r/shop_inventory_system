@extends('admin.layouts.admin')
@section('title', 'Forklift Inspection Report')
@section('pageurl', admin_url('safety/forklift-inspection/list'))
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
                                        href="{{ admin_url('safety/forklift-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_data') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->rev_data }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $signature = GetSafetySignature(
                                                $inspection_details->created_by,
                                                $inspection_details->id,
                                                FORKLIFT_INSPECTION,
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
                                        @endif
                                        <hr>
                                        @foreach ($inspection as $details)
                                            <div class="form-wrapper">
                                                <div class="row mt-4 form-set">
                                                    <div class="card-header-inner p-2">
                                                        <h4 class="text-white">Forklift Inspection Report</h4>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label ">{{ __('inspection.sr_no') }}</label>
                                                            <div class="view_data">
                                                                {{ $loop->iteration }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.department') }}</label>
                                                            <div class="view_data">
                                                                {{ getDepartment($details->department_id) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.identification_no') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->identification_no }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.observation') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->observation }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.corrective_action') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->correction_preventive_action }}
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.employee') }}</label>
                                                            <div class="view_data">
                                                                {{ getUsername($details->responsibility) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.date_of_compliance') }}</label>
                                                            <div class="view_data">
                                                                {{ Displaydateformat($details->date_of_compliance) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.observation_status') }}</label>
                                                            <div class="view_data">
                                                                @if ($details->observation_status == 1)
                                                                    Active
                                                                @elseif($details->observation_status == 0)
                                                                    Inactive
                                                                @else
                                                                    Unknown
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->remarks }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">APPROVAL</h4>

                                        </div>
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('safety/forklift-inspection/verify/submit') }}"
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
                                                    <input type="text" name="date" id = "date"
                                                        class="form-control" value="{{ todayDate() }}" readonly>
                                                </div>

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
            $('#forklistassessmentAdd').validate({
                rules: {
                    capa_remarks: {
                        required: true,
                        minlength: 3,
                        maxlength: 100,
                        noSpaces: true,
                    },
                    signature_image: {
                        required: true,
                    }
                },
                messages: {
                    capa_remarks: {
                        required: "Remarks is Required",
                        minlength: "Minimum Characters should be 3",
                        maxlength: "Maximum Characters should not exceed 100",
                    },
                    signature_image: {
                        required: "Signature is Required",
                    }
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
