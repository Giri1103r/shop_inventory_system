@extends('admin.layouts.admin')
@section('title', 'Forklift Inspection Add')
@section('pageurl', admin_url('safety/forklift-inspection/monthly/list'))


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
                                        href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="forklistassessmentAdd"
                                        action="{{ admin_url('safety/forklift-inspection/monthly/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                    <input type="text" name="doc_no" id = "doc_no" class="form-control"
                                                        placeholder="Enter the Document Number">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                    <input type="text" name="issue_date" id = "issue_date"
                                                        class="form-control" placeholder="Issued Date">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                    <input type="text" name="rev_date" id = "rev_date"
                                                        class="form-control" value="{{ getDocumentReviewDate('SAF-0') }}"
                                                        readonly>
                                                </div>
                                            </div>
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
                                                    <label
                                                        class="form-label require">{{ __('inspection.location') }}</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select {{ __('inspection.location') }}
                                                        </option>
                                                        @foreach ($locations as $location)
                                                            <option value="{{ encryptId($location->id) }}">
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Shift</option>
                                                        @foreach ($shift as $shift)
                                                            <option value="{{ encryptId($shift->id) }}">
                                                                {{ $shift->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control">
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
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.identification_no') }}</label>
                                                    <input type="text" name="identification_no" id = "identification_no"
                                                        class="form-control" placeholder="Enter Identification No">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.forklift_type') }}</label>
                                                    <select name="forklift_type" id="forklift_type"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Forklift Type</option>
                                                        @foreach ($forklifts as $forklifts)
                                                            <option value="{{ encryptId($forklifts->id) }}">
                                                                {{ $forklifts->forklift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.capacity') }}</label>
                                                    <input type="text" name="capacity" id = "capacity"
                                                        class="form-control" placeholder="Enter Capacity">
                                                </div>
                                            </div>

                                            {{-- <div class=""> --}}
                                            <table class="container p-5">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Sr. No
                                                        </th>

                                                        <th colspan="3"
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                            Check Points
                                                        </th>

                                                        @foreach ($getoption as $option)
                                                            <th style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;"
                                                                class="require">
                                                                {{ $option }}
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $i = 1;

                                                    @endphp
                                                    @foreach ($checklist_details as $key => $details)
                                                        @php
                                                            $rowCount = count($details);
                                                        @endphp
                                                        @foreach ($details as $index => $checklist)
                                                            <tr>
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                    {{ $i }}
                                                                </td>
                                                                @if ($index == 0)
                                                                    <td rowspan="{{ $rowCount }}"
                                                                        style="border: 1px solid black; padding: 8px; background-color: #f5f5f5; font-weight: bold;">
                                                                        {{ $checklist->subcategory_name }}
                                                                    </td>
                                                                @endif
                                                                <td colspan="2"
                                                                    style="border: 1px solid black; padding: 8px;">
                                                                    {{ $checklist->checklist_name }}
                                                                </td>
                                                                @foreach ($getoption as $option)
                                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;"
                                                                        class="">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->checklist_id }}]"
                                                                            value="{{ trim($option) }}">
                                                                    </td>
                                                                @endforeach
                                                                @php
                                                                    $i++;
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        {{-- </div> --}}

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
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
                flatpickr("#next_due", {
                    dateFormat: "d-m-Y",
                });
                $('.floor_executive').select2({
                    ajax: {
                        url: "{{ admin_url('safety/forklift-inspection/monthly/employeeName') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text
                                    };
                                })
                            };
                        },
                        error: function(xhr, textStatus, errorThrown) {
                            console.log("Error in AJAX request:", textStatus, errorThrown);
                        }
                    },
                    minimumInputLength: 3,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });
            });
            $(function() {
                $('#forklistassessmentAdd').validate({
                    rules: {
                        checklist_category: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                            noSpaces: true,
                        },
                        questionary_id: {
                            required: true,
                        },

                    },
                    messages: {
                        checklist_category: {
                            required: "{{ __('Name is Required') }}",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                            // remote: "{{ __('Name should be unique') }}",
                        },
                        questionary_id: {
                            required: "{{ __('inspection.questionary_required') }}",
                        },
                        checklist_file: {
                            extension: "Only .jpg files are allowed. Please upload a valid image file.",
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
                        console.log('test');
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
            });
        </script>
    @endpush
