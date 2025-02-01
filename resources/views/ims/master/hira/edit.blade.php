@extends('admin.layouts.admin')
@section('title', 'HIRA Edit')
@section('pageurl', admin_url('incident/hira-master/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('incident/hira-master Edit') }}</h4> --}}

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

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="hiraedit"
                                        action="{{ admin_url('incident/hira-master/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($hira->id) }}">

                                        <div class="row">
                                            <div class="col-md-4 mb-2 ">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Sr. No</label>
                                                    <input type="text" name ="sr_no" id="sr_no" class="form-control"
                                                        placeholder="Incident Type ID" value="{{ $hira->sr_no }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Source, Situation, Act,Activity,
                                                        Product,Services</label>
                                                    <input type="text" name="services" id="services"
                                                        class="form-control"
                                                        placeholder="Source, Situation, Act,Activity, Product,Services"
                                                        value="{{ $hira->services }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Narration</label>
                                                    <select name="narration" id="narration"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Narration</option>
                                                        <option value="1"
                                                            {{ $hira->narration && $hira->narration == 1 ? 'selected' : '' }}>
                                                            R - Routine Activity</option>
                                                        <option value="2"
                                                            {{ $hira->narration && $hira->narration == 2 ? 'selected' : '' }}>
                                                            NR - Non-routine Activity</option>
                                                        <option value="3"
                                                            {{ $hira->narration && $hira->narration == 3 ? 'selected' : '' }}>
                                                            E - Emergency</option>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Hazard Description</label>
                                                    <input type="text" name="hazard_description" id="hazard_description"
                                                        class="form-control" placeholder="Hazard Description"
                                                        value="{{ $hira->hazard_description }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Type of Hazard</label>
                                                    <select name="hazard_type" id="hazard_type"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option
                                                            value="1"{{ $hira->hazard_type && $hira->hazard_type == 1 ? 'selected' : '' }}>
                                                            P - Physical Hazard</option>
                                                        <option
                                                            value="2"{{ $hira->hazard_type && $hira->hazard_type == 2 ? 'selected' : '' }}>
                                                            C - Chemical Hazard</option>
                                                        <option
                                                            value="3"{{ $hira->hazard_type && $hira->hazard_type == 3 ? 'selected' : '' }}>
                                                            B - Behavioral Hazard</option>
                                                        <option
                                                            value="4"{{ $hira->hazard_type && $hira->hazard_type == 4 ? 'selected' : '' }}>
                                                            O - Other Hazard</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Severity</label>
                                                    <select name="severity" id="severity" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Severity</option>
                                                        <option
                                                            value="1"{{ $hira->severity && $hira->severity == 1 ? 'selected' : '' }}>
                                                            1</option>
                                                        <option
                                                            value="2"{{ $hira->severity && $hira->severity == 2 ? 'selected' : '' }}>
                                                            2</option>
                                                        <option
                                                            value="3"{{ $hira->severity && $hira->severity == 3 ? 'selected' : '' }}>
                                                            3</option>
                                                        <option
                                                            value="4"{{ $hira->severity && $hira->severity == 4 ? 'selected' : '' }}>
                                                            4</option>
                                                        <option
                                                            value="5"{{ $hira->severity && $hira->severity == 5 ? 'selected' : '' }}>
                                                            5</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Risk/Consequence</label>
                                                    <input type="text" name="risk_consequence" id="risk_consequence"
                                                        class="form-control" placeholder="Risk/Consequence"
                                                        value="{{ $hira->risk_consequence }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Likelihood</label>
                                                    <select name="likelihood" id="likelihood" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Likelihood</option>
                                                        <option
                                                        value="1"{{ $hira->likelihood && $hira->likelihood == 1 ? 'selected' : '' }}>
                                                        1</option>
                                                    <option
                                                        value="2"{{ $hira->likelihood && $hira->likelihood == 2 ? 'selected' : '' }}>
                                                        2</option>
                                                    <option
                                                        value="3"{{ $hira->likelihood && $hira->likelihood == 3 ? 'selected' : '' }}>
                                                        3</option>
                                                    <option
                                                        value="4"{{ $hira->likelihood && $hira->likelihood == 4 ? 'selected' : '' }}>
                                                        4</option>
                                                    <option
                                                        value="5"{{ $hira->likelihood && $hira->likelihood == 5 ? 'selected' : '' }}>
                                                        5</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Risk Levels</label>
                                                    <select name="risk_levels" id="risk_levels" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Risk Levels</option>
                                                        <option
                                                        value="1"{{ $hira->risk_levels && $hira->risk_levels == 1 ? 'selected' : '' }}>
                                                        1 to 9</option>
                                                    <option
                                                        value="2"{{ $hira->risk_levels && $hira->risk_levels == 2 ? 'selected' : '' }}>
                                                        10 to 16</option>
                                                    <option
                                                        value="3"{{ $hira->risk_levels && $hira->risk_levels == 3 ? 'selected' : '' }}>
                                                        17 to 25</option>
                                                    <option
                                                        value="4"{{ $hira->risk_levels && $hira->risk_levels == 4 ? 'selected' : '' }}>
                                                        Legal</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Current Controls</label>
                                                    <input type="text" name="current_controls" id="current_controls"
                                                        class="form-control" placeholder="Current Controls"
                                                        value="{{ $hira->current_controls }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Type of Controls</label>
                                                    <select name="type_controls" id="type_controls" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Type of Controls</option>
                                                        <option
                                                        value="1"{{ $hira->type_controls && $hira->type_controls == 1 ? 'selected' : '' }}>
                                                        EL - Elimination</option>
                                                    <option
                                                        value="2"{{ $hira->type_controls && $hira->type_controls == 2 ? 'selected' : '' }}>
                                                        S - Substitution</option>
                                                    <option
                                                        value="3"{{ $hira->type_controls && $hira->type_controls == 3 ? 'selected' : '' }}>
                                                        EC- Engineering control</option>
                                                    <option
                                                        value="4"{{ $hira->type_controls && $hira->type_controls == 4 ? 'selected' : '' }}>
                                                        A - Administrative Control</option>
                                                    <option
                                                        value="5"{{ $hira->type_controls && $hira->type_controls == 5 ? 'selected' : '' }}>
                                                        P - PPE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Legal Requirements</label>
                                                    <select name="legal_req" id="legal_req" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Legal Requirements</option>
                                                        <option
                                                        value="1"{{ $hira->legal_req && $hira->legal_req == 1 ? 'selected' : '' }}>
                                                        L - Applicable</option>
                                                    <option
                                                        value="2"{{ $hira->legal_req && $hira->legal_req == 2 ? 'selected' : '' }}>
                                                        NA - Not Applicable</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Risk Ratings</label>
                                                    <select name="risk_rating" id="risk_rating" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Risk Ratings</option>
                                                        <option
                                                        value="1"{{ $hira->risk_rating && $hira->risk_rating == 1 ? 'selected' : '' }}>
                                                        Low (when RPN is 1 to 9)</option>
                                                    <option
                                                        value="2"{{ $hira->risk_rating && $hira->risk_rating == 2 ? 'selected' : '' }}>
                                                        Medium (when RPN is 10 to 16)</option>
                                                    <option
                                                        value="3"{{ $hira->risk_rating && $hira->risk_rating == 3 ? 'selected' : '' }}>
                                                        High (when RPN is 17 to 25)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Additional control Measures Required</label>
                                                    <input type="text" name="additionl_control" id="additionl_control"
                                                        class="form-control"
                                                        placeholder="Additional controlMeasures Required"
                                                        value="{{ $hira->additionl_control }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Nature of Change</label>
                                                    <input type="text" name="nature_change" id="nature_change"
                                                        class="form-control" placeholder="Nature of Change"
                                                        value="{{ $hira->nature_change }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Implement of Change</label>
                                                    <input type="text" name="implement_change" id="implement_change"
                                                        class="form-control" placeholder="Implement of Change"
                                                        value="{{ $hira->implement_change }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Control of Change</label>
                                                    <input type="text" name="control_change" id="control_change"
                                                        class="form-control" placeholder="Control of Change"
                                                        value="{{ $hira->control_change }}">
                                                </div>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('incident/hira-master/list') }}"></x-button-cancel>
                                        </div>

                                    </form>
                                </div>

                            </div>
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
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(function() {
            $('#hiraedit').validate({
                rules: {
                    services: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },

                    narration: {
                        required: true,
                    },
                    hazard_description: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    hazard_type: {
                        required: true,
                    },
                    severity: {
                        required: true,
                    },
                    risk_consequence: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    likelihood: {
                        required: true,
                    },
                    risk_levels: {
                        required: true,
                    },
                    current_controls: {
                        required: true,
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    type_controls: {
                        required: true,
                    },
                    legal_req: {
                        required: true,
                    },
                    risk_rating: {
                        required: true,
                    },
                    additionl_control: {
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    nature_change: {
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    implement_change: {
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                    control_change: {
                        minlength: 2,
                        maxlength: 2000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()]+$/,
                    },
                },
                messages: {
                    services: {
                        required: "Source, Situation, Act,Activity, Product,Services is required.",
                        minlength: "Incident Short Name must be exactly 2 characters.",
                        maxlength: "Incident Short Name must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    narration: {
                        required: "Narration is required.",
                    },
                    hazard_description: {
                        required: "Hazard Description is required.",
                    },
                    hazard_type: {
                        required: "Type of Hazard is required.",
                    },
                    severity: {
                        required: "Severity is required.",
                    },
                    risk_consequence: {
                        required: "Risk/Consequence is required.",
                    },
                    likelihood: {
                        required: "Likelihood is required.",
                    },
                    risk_levels: {
                        required: "Risk Levels is required.",
                    },
                    current_controls: {
                        required: "Current Controls is required.",
                    },
                    type_controls: {
                        required: "Type of Controls is required.",
                    },
                    legal_req: {
                        required: "Legal Requirements is required.",
                    },
                    risk_rating: {
                        required: "Risk Ratings is required.",
                    },
                    additionl_control: {
                        minlength: "Additional control Measures Required must be exactly 2 characters.",
                        maxlength: "Additional control Measures Required must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    nature_change: {
                        minlength: "Nature of Change must be exactly 2 characters.",
                        maxlength: "Nature of Change must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    implement_change: {
                        minlength: "Implement of Change must be exactly 2 characters.",
                        maxlength: "Implement of Change must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
                    },
                    control_change: {
                        minlength: "Control of Change must be exactly 2 characters.",
                        maxlength: "Control of Change must be exactly 2000 characters.",
                        pattern: "Only alphanumeric characters and (-, _, ‘, “, ()) are allowed.",
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
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
        });
    </script>
@endpush
