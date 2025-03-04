@extends('admin.layouts.admin')
@section('title', 'HIRA Show')
@section('pageurl', admin_url('incident/hira-master/list'))


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
                                    <x-button-back href="{{ admin_url('incident/hira-master/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">HIRA Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Sr. No') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->sr_no) ? $hira->sr_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('Source, Situation, Act,Activity,Product,Services') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->services) ? $hira->services : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Narration') }}</label>
                                        <div class="view_data">
                                            @if ($hira->narration == 1)
                                                {{ 'R - Routine Activity' }}
                                            @elseif($hira->narration == 2)
                                                {{ 'NR - Non-routine Activity' }}
                                            @elseif($hira->narration == 3)
                                                {{ 'E - Emergency' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Hazard Description') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->hazard_description) ? $hira->hazard_description : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Type of Hazard') }}</label>
                                        <div class="view_data">
                                            @if ($hira->hazard_type == 1)
                                                {{ 'P - Physical Hazard' }}
                                            @elseif($hira->hazard_type == 2)
                                                {{ 'C - Chemical Hazard' }}
                                            @elseif($hira->hazard_type == 3)
                                                {{ 'B - Behavioral Hazard' }}
                                            @elseif($hira->hazard_type == 4)
                                                {{ 'O - Other Hazard' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Severity') }}</label>
                                        <div class="view_data">
                                            @if ($hira->severity == 1)
                                                {{ '1' }}
                                            @elseif($hira->severity == 2)
                                                {{ '2' }}
                                            @elseif($hira->severity == 3)
                                                {{ '3' }}
                                            @elseif($hira->severity == 4)
                                                {{ '4' }}
                                            @elseif($hira->severity == 5)
                                                {{ '5' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Risk/Consequence') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->risk_consequence) ? $hira->risk_consequence : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Likelihood') }}</label>
                                        <div class="view_data">
                                            @if ($hira->likelihood == 1)
                                                {{ '1' }}
                                            @elseif($hira->likelihood == 2)
                                                {{ '2' }}
                                            @elseif($hira->likelihood == 3)
                                                {{ '3' }}
                                            @elseif($hira->likelihood == 4)
                                                {{ '4' }}
                                            @elseif($hira->likelihood == 5)
                                                {{ '5' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Risk Levels') }}</label>
                                        <div class="view_data">
                                            @if ($hira->risk_levels == 1)
                                                {{ '1 to 9' }}
                                            @elseif($hira->risk_levels == 2)
                                                {{ '10 to 16' }}
                                            @elseif($hira->risk_levels == 3)
                                                {{ '17 to 25' }}
                                            @elseif($hira->risk_levels == 4)
                                                {{ 'Legal' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Current Controls') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->current_controls) ? $hira->current_controls : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Type of Controls') }}</label>
                                        <div class="view_data">

                                            @if ($hira->type_controls == 1)
                                                {{ 'EL - Elimination' }}
                                            @elseif($hira->type_controls == 2)
                                                {{ 'S - Substitution' }}
                                            @elseif($hira->type_controls == 3)
                                                {{ 'EC- Engineering control' }}
                                            @elseif($hira->type_controls == 4)
                                                {{ 'A - Administrative Control' }}
                                            @elseif($hira->type_controls == 5)
                                                {{ 'P - PPE' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Legal Requirements') }}</label>
                                        <div class="view_data">

                                            @if ($hira->legal_req == 1)
                                                {{ 'L - Applicable' }}
                                            @elseif($hira->legal_req == 2)
                                                {{ 'NA - Not Applicable' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Risk Ratings') }}</label>
                                        <div class="view_data">

                                            @if ($hira->risk_rating == 1)
                                                {{ 'Low (when RPN is 1 to 9)' }}
                                            @elseif($hira->risk_rating == 2)
                                                {{ 'Medium (when RPN is 10 to 16)' }}
                                            @elseif($hira->risk_rating == 3)
                                                {{ 'High (when RPN is 17 to 25)' }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('Additional control Measures Required') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->additionl_control) ? $hira->additionl_control : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Nature of Change') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->nature_change) ? $hira->nature_change : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Implement of Change') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->implement_change) ? $hira->implement_change : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Implement of Change') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->implement_change) ? $hira->implement_change : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Control of Change') }}</label>
                                        <div class="view_data">
                                            {{ isset($hira->control_change) ? $hira->control_change : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($hira->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($hira->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($hira->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($hira->hira_status == 1 && CheckUserRole(ROLE_EHS_HEAD))
                                <div class="card-body ">

                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Head Approval</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="ehs_approval"
                                            action="{{ admin_url('incident/hira-master/ehsapproval/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="hira_id" id="hira_id"
                                                    value="{{ encryptId($hira->id) }}">

                                                <input type="hidden" name="reviewer_emp_id" id="reviewer_emp_id"
                                                    class="form-control" value="{{ Auth::user()->employee_id ?? '' }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Reviewer Name</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="date" id="date_datepicker"
                                                            class="form-control" placeholder="Date" readonly
                                                            value="{{ todaydate() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="d-flex float-end gap-2 mx-auto">
                                                <button type="submit" name="approve" value="approve"
                                                    class="btn btn-success w-100">Approve</button>
                                                <button type="submit" name="reject" value="reject"
                                                    class="btn btn-danger w-100">Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

@stop
@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {
            $('#ehs_approval').validate({
                rules: {
                    remark: {
                        required: true,
                        maxlength: 1000
                    }
                },
                messages: {
                    remark: {
                        required: "Please provide a remark.",
                        maxlength: "Remark cannot exceed 1000 characters."
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
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });

        });
    </script>
@endpush
