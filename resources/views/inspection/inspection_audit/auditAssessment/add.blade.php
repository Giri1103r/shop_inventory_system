@extends('admin.layouts.admin')
@section('title', '6S Audit Assessment Add')
@section('pageurl', admin_url('audit/assessment/list'))


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
                                    <x-button-back href="{{ admin_url('audit/assessment/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="auditAssessmentAdd"
                                        action="{{ admin_url('audit/assessment/add/submit') }}" autocomplete="off"
                                        enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Audit ID</label>
                                                    <input type="text" name="audit_id" id = "audit_id"
                                                        class="form-control" readonly
                                                        value="{{ getSequence('audit_assessment') }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Name Of The Shop Floor</label>
                                                    <input type="text" name="floor_name" id = "floor_name"
                                                        class="form-control" placeholder="Name Of The Shop Floor">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Date Of Audit</label>
                                                    <input type="text" name="audit_date" id = "audit_date"
                                                        class="form-control">
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
                                                    <label class="form-label require">Floor Executive on Duty</label>
                                                    <select name="floor_executive" id="floor_executive" style="width: 100%"
                                                        class="form-control floor_executive">
                                                        <option value="">Select Name</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <table class="container p-5">
                                                <thead>
                                                    <tr>


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
                                                                    {{-- <td style="border: 1px solid black; padding: 8px; text-align: center;"
                                                                        class="form-input">
                                                                        <input type="radio"
                                                                            name="checklist[{{ $checklist->subcategory_name }}][{{ $checklist->checklist_name }}]"
                                                                            value="{{ trim($option) }}" class="validate-radio-required">
                                                                    </td> --}}

                                                                    <td class="form-input"
                                                                    style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                    <input type="radio"
                                                                        name="checklist[{{ $checklist->checklist_sub_type_id }}][{{ $checklist->id }}][selected_option]"
                                                                        value="{{ trim($option) }}"
                                                                        class="validate-radio-required">
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

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('audit/assessment/list') }}"></x-button-cancel>
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
                flatpickr("#audit_date", {
                    dateFormat: "d-m-Y",
                });
                $('.floor_executive').select2({
                    ajax: {
                        url: "{{ admin_url('audit/assessment/employeeName') }}",
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
                    selectionCssClass: 'form-control',

                });
            });
            $(function() {
                $('#auditAssessmentAdd').validate({
                    rules: {
                        floor_name: {
                            required: true,
                            minlength: 3,
                            maxlength: 100,
                        },
                        audit_date: {
                            required: true,
                        },
                        shift_id: {
                            required: true,
                        },
                        floor_executive: {
                            required: true,
                        },

                    },
                    messages: {
                        floor_name: {
                            required: "{{ __('Name Of The Shop Floor  is Required') }}",
                            minlength: "Minimum Characters should be 3",
                            maxlength: "Maximum Characters should not exceed 100",
                        },
                        audit_date: {
                            required: "{{ __('Date Of Audit is required') }}",
                        },
                        shift_id: {
                            required: "Shift is required",
                        },
                        floor_executive: {
                            required: "Floor Executive on Duty is required",
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
