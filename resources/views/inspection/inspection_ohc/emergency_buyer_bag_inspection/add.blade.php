@extends('admin.layouts.admin')
@section('title', 'Emergency First Aid Bag Checklist')
@section('pageurl', admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list'))


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
                                        href="{{ admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="EmergencyFirstAidBagAdd" enctype="multipart/form-data"
                                    action="{{ admin_url('ohc/emergency-buyer-first-aid-bag/checklist/add/submit') }}">
                                    @csrf

                                    <div class="basic-form">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        Date of Inspection</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="date_of_inspection"
                                                            id="date_of_inspection" class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Location First Aid Bag</label>
                                                    <input type="text" name="location_first_aid_bag"
                                                        id = "location_first_aid_bag" class="form-control"
                                                        placeholder="Enter Location">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Shift</label>
                                                    <select name="shift" id="shift" style="width: 100%"
                                                        class="form-control single-select">
                                                        <option value="">Select the option</option>
                                                        @foreach ($shift as $list)
                                                            <option value="{{ encryptId($list->id) }}">{{ $list->shift }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">
                                                        Next Due Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_due_date" id="next_due_date"
                                                            class="form-control"autocomplete="off">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit</label>
                                                    <select name="unit_id" id="unit_id" class="form-control single-select"
                                                        style="width: 100%">
                                                        <option value="">Select the unit</option>
                                                        @foreach ($unit as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Frequency</label>
                                                    <select name="frequency_id" id="frequency_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select the Frequency</option>
                                                        @foreach ($frequency as $frequency)
                                                            <option value="{{ encryptId($frequency->id) }}">
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>



                                            <div class="mb-3 mt-3">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Medicine Name</th>
                                                            <th style="text-align: center">Freeze Quantity</th>
                                                            <th style="text-align: center">Available Quantity</th>
                                                            <th style="text-align: center">Expiry Date</th>
                                                            <th style="text-align: center">Remark</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($medicines as $medicines)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                                <td class="text-center">
                                                                    {{ getMedicinename($medicines->medicine_id) }} <input
                                                                        type="hidden"
                                                                        name="medicine_id[{{ $medicines->id }}]"
                                                                        value="{{ encryptId($medicines->id) }}"></td>

                                                                <td class="text-center">{{ $medicines->freeze_quantity }}
                                                                    <input type="hidden"
                                                                        name="freeze_quantity[{{ $medicines->id }}]"
                                                                        value="{{ $medicines->freeze_quantity }}">
                                                                </td>
                                                                <td>
                                                                    <div class="form-input">
                                                                        <input class="form-control" type="number"
                                                                            min="1"
                                                                            name="available_quantity[{{ $medicines->id }}]" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-input">


                                                                        <div
                                                                            class="input-group date form-input custom-height">
                                                                            <input class="form-control expired_date"
                                                                                type="date"
                                                                                name="expired_date[{{ $medicines->id }}]" />
                                                                            <div
                                                                                class="input-group-addon input-group-text">
                                                                                <span class="fa fa-calendar"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="form-input">
                                                                        <textarea class="form-control" type="text" style="resize: none" name="remarks[{{ $medicines->id }}]"></textarea>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>


                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"> PPE'S For Visitors:-</label>
                                                    <div class="view_data">
                                                        05 Air Plugs, 05 Pairs Cotton Gloves, 02 Piars Rubber Gloves, 05
                                                        Mask, 04 specticals.
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Remark By</label>
                                                    <textarea class="form-control" name="remark_by" id="remark_by"></textarea>

                                                </div>
                                            </div>

                                            {{-- <div class="row m-2"> --}}
                                            {{-- <div class="col-md-6 form-group form-input mb-2">
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


                                            {{-- </div> --}}

                                            <div class="submit-button m-2" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class="submit"></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list') }}"></x-button-cancel>
                                            </div>

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


@stop


@push('script')
    <script type="text/javascript" nonce="projectcab">
        $(document).ready(function() {

            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
            flatpickr("#next_due_date", {
                dateFormat: "d-m-Y",
            });
            flatpickr("#date_of_inspection", {
                dateFormat: "d-m-Y",
            });
            flatpickr(".expired_date", {
                dateFormat: "d-m-Y",
            });


            $('#EmergencyFirstAidBagAdd').validate({
                rules: {

                    date_of_inspection: {
                        required: true,
                    },
                    location_first_aid_bag: {
                        required: true,
                    },
                    shift: {
                        required: true,
                    },
                    next_due_date: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    frequency_id: {
                        required: true,
                    },
                    // signature_image: {
                    //     required: true,
                    //     filesize: 15728640,
                    // },
                    remark_by: {
                        required: true,
                        minlength: 3,
                        maxlength: 300,

                    }
                },
                messages: {

                    date_of_inspection: {
                        required: "Date of Inspection is required",
                    },
                    location_first_aid_bag: {
                        required: "Location First Aid Bag is required",
                    },
                    shift: {
                        required: "Shift selection is required",
                    },
                    next_due_date: {
                        required: "Next Due Date is required",
                    },
                    unit_id: {
                        required: "Unit selection is required",
                    },
                    frequency_id: {
                        required: "Frequency  selection is required",

                    },
                    // signature_image: {
                    //     required: "Signature is required",
                    //     filesize: "File size must be less than 15MB."
                    // },
                    remark_by: {
                        required: "Please add remarks",
                        minlength: "Minimum 3 characters required",
                        maxlength: "Maximum character does not exceed 300"
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
                }
            });


            $('#EmergencyFirstAidBagAdd').on('change input',
                'input[name^="available_quantity"], input[name^="expired_date"], select[name^="emp_id"], textarea[name^="remarks"]',
                function() {
                    $(this).valid();
                });

            $(document).ready(function() {
                $('input[name^="available_quantity"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        number: true,
                        min: 1,
                        messages: {
                            required: "Available Quantity is required",
                            number: "Please enter a valid number",
                            min: "Quantity must be at least 1"
                        }
                    });
                });

                $('input[name^="expired_date"]').each(function() {
                    $(this).rules('add', {
                        required: true,

                        messages: {
                            required: "Expiry Date is required",

                        }
                    });
                });

                $('select[name^="emp_id"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        messages: {
                            required: "Employee is required",
                        }
                    });
                });

                $('textarea[name^="remarks"]').each(function() {
                    $(this).rules('add', {
                        required: true,
                        minlength: 3,
                        maxlength: 300,
                        messages: {
                            required: "Remarks is required",
                            minlength: "Minimum 3 characters required",
                            maxlength: "Maximum character does not exceed 300"
                        }
                    });
                });
            });
        });
    </script>
@endpush
