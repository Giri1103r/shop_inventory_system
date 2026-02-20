@extends('admin.layouts.admin')
@section('title', 'HIRA Add')
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

                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('incident/hira-master/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="hiraAdd"
                                        action="{{ admin_url('incident/hira-master/add/submit') }}">
                                        @csrf
                                        <input type="hidden" name="incident_id" value="{{ $incident_id }}">
                                        <input type="hidden" name="accident_id" value="{{ $accident_id }}">
                                        <input type="hidden" name="fire_id" value="{{ $fire_id }}">
                                        <input type="hidden" name="hiramoc_id" value="{{ $hiramoc_id }}">
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Sr. No</label>
                                                    <input type="text" name="sr_no" id="sr_no" class="form-control"
                                                        placeholder="" value = "{{ getsequence('hira') }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Source, Situation, Act,Activity,
                                                        Product,Services</label>
                                                    <input type="text" name="services" id="services"
                                                        class="form-control"
                                                        placeholder="Source, Situation, Act,Activity, Product,Services">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Narration</label>
                                                    <select name="narration" id="narration" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Narration</option>
                                                        <option value="1">R - Routine Activity</option>
                                                        <option value="2">NR - Non-routine Activity</option>
                                                        <option value="3">E - Emergency</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Hazard Description</label>
                                                    <input type="text" name="hazard_description" id="hazard_description"
                                                        class="form-control" placeholder="Hazard Description">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Type of Hazard</label>
                                                    <select name="hazard_type" id="hazard_type" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Type of Hazard</option>
                                                        <option value="1">P - Physical Hazard</option>
                                                        <option value="2">C - Chemical Hazard</option>
                                                        <option value="3">B - Behavioral Hazard</option>
                                                        <option value="4">O - Other Hazard</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Severity</label>
                                                    <select name="severity" id="severity" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Severity</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Risk/Consequence</label>
                                                    <input type="text" name="risk_consequence" id="risk_consequence"
                                                        class="form-control" placeholder="Risk/Consequence">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Likelihood</label>
                                                    <select name="likelihood" id="likelihood" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Likelihood</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="status" class="form-label require">Risk Levels</label>
                                                    <select name="risk_levels" id="risk_levels" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Risk Levels</option>
                                                        <option value="1">1 to 9</option>
                                                        <option value="2">10 to 16</option>
                                                        <option value="3">17 to 25</option>
                                                        <option value="4">Legal</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Current Controls</label>
                                                    <input type="text" name="current_controls" id="current_controls"
                                                        class="form-control" placeholder="Current Controls">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Type of Controls</label>
                                                    <select name="type_controls" id="type_controls" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Type of Controls</option>
                                                        <option value="1">EL - Elimination</option>
                                                        <option value="2">S - Substitution</option>
                                                        <option value="3">EC- Engineering control</option>
                                                        <option value="4">A - Administrative Control</option>
                                                        <option value="5">P - PPE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Legal Requirements</label>
                                                    <select name="legal_req" id="legal_req" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Legal Requirements</option>
                                                        <option value="1">L - Applicable</option>
                                                        <option value="2">NA - Not Applicable</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Risk Ratings</label>
                                                    <select name="risk_rating" id="risk_rating" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select Risk Ratings</option>
                                                        <option value="1">Low (when RPN is 1 to 9)</option>
                                                        <option value="2">Medium (when RPN is 10 to 16)</option>
                                                        <option value="3">High (when RPN is 17 to 25)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Additional control Measures Required</label>
                                                    <input type="text" name="additionl_control" id="additionl_control"
                                                        class="form-control"
                                                        placeholder="Additional controlMeasures Required">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Nature of Change</label>
                                                    <input type="text" name="nature_change" id="nature_change"
                                                        class="form-control" placeholder="Nature of Change">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Implement of Change</label>
                                                    <input type="text" name="implement_change" id="implement_change"
                                                        class="form-control" placeholder="Implement of Change">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Control of Change</label>
                                                    <input type="text" name="control_change" id="control_change"
                                                        class="form-control" placeholder="Control of Change">
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
            $('#hiraAdd').validate({
                rules: {
                    services: {
                        required: true,
                        minlength: 2,
                        maxlength: 1000,
                       pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
                    },

                    narration: {
                        required: true,
                    },
                    hazard_description: {
                        required: true,
                        minlength: 2,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
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
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
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
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
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
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
                    },
                    nature_change: {
                        minlength: 2,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
                    },
                    implement_change: {
                        minlength: 2,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
                    },
                    control_change: {
                        minlength: 2,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9,\-%+_\/!\\()"\s]+$/,
                    },
                },
                messages: {
                    services: {
                        required: "Source, Situation, Act,Activity, Product,Services is required.",
                        minlength: "Incident Short Name must be exactly 2 characters.",
                        maxlength: "Incident Short Name must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
                    },
                    narration: {
                        required: "Narration is required.",
                    },
                    hazard_description: {
                        required: "Hazard Description is required.",
                        minlength: "Hazard Description must be exactly 2 characters.",
                        maxlength: "Hazard Description must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
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
                        maxlength: "Additional control Measures Required must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
                    },
                    nature_change: {
                        minlength: "Nature of Change must be exactly 2 characters.",
                        maxlength: "Nature of Change must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
                    },
                    implement_change: {
                        minlength: "Implement of Change must be exactly 2 characters.",
                        maxlength: "Implement of Change must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
                    },
                    control_change: {
                        minlength: "Control of Change must be exactly 2 characters.",
                        maxlength: "Control of Change must be exactly 1000 characters.",
                        pattern: "Only alphanumeric characters and (” %+-_/!\ -, _, ‘, “, ()) are allowed.",
                    },
                },

                errorElement: 'span',
                errorPlacement: function(error, element) {
                    // Add the 'invalid-feedback' class to the error element
                    error.addClass('invalid-feedback');
                    // Append the error message to the closest '.form-input' container
                    element.closest('.form-input').append(error);
                },
                highlight: function(element) {
                    // Add the 'is-invalid' class to the invalid input
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    // Remove the 'is-invalid' class when the input becomes valid
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    // Submit the form when all validations pass
                    form.submit();
                },
                invalidHandler: function(event, validator) {
                    // Handle invalid form submissions
                    var errors = validator.numberOfInvalids();
                    if (errors) {
                        console.log(`There are ${errors} validation errors.`);
                        validator.errorList.forEach(function(error) {
                            console.log(
                                `Field: ${error.element.name}, Error: ${error.message}`);
                        });
                    }
                },
            });
        });
    </script>
@endpush
