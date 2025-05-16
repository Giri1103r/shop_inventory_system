@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation')
@section('pageurl', admin_url('safety/safety-walk-observation/list'))
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
                                        href="{{ admin_url('safety/safety-walk-observation/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Month</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->month }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">{{ __('inspection.safety_walk_taken_by') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->safety_walk_taken_by }}
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $signature = GetSafetySignature(
                                                $inspection_details->created_by,
                                                $inspection_details->id,
                                                SAFETY_WALK_OBSERVATION,
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
                                    </div>
                                    <hr>
                                    @foreach ($inspection as $details)
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Safety Walk Observation</h4>
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
                                                        <label class="form-label ">{{ __('inspection.location') }}</label>
                                                        <div class="view_data">
                                                            {{ getLocationName($details->location) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.date_of_observation') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($details->observation_date) }}
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
                                                            class="form-label ">{{ __('inspection.recomended_action') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->recomended_action }}
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.employee') }}</label>
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
                                                        <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $images = GetSafetyWalkImage($details->id);
                                                @endphp


                                                <div class="col-md-4 mb-2">
                                                    @if ($images !== false)
                                                        <label class="form-label "
                                                            style="display: block;">{{ __('inspection.image') }}</label>
                                                        <img src="{{ admin_url($images) }}" style="width: 100px;" />
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if ((checkUserRole(ROLE_EHS_OFFICER) || isAdmin()) && $inspection_details->observation_status == OBSERVATION_PENDING)
                                    <div>
                                        <div class="card-header-inner p-2">
                                            <h4 class="text-white">EHS Officer Approval </h4>
                                        </div>
                                        <form method="POST" id="forklistassessmentAdd"
                                            action="{{ admin_url('safety/safety-walk-observation/verify/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" value="{{ encryptId($inspection_details->id) }}"
                                                name="id">
                                            <div class="row">
                                                <div class="col-md-4 form-group form-input mb-2">
                                                    <label class="form-label ">{{ __('inspection.name') }}</label>
                                                    <input type="text" name="name" id = "name"
                                                        class="form-control" value="{{ getUserName(Auth::id()) }}"
                                                        readonly>
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
                    filesize: 15728640,
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
                     filesize: "File must be less than 15MB."
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
