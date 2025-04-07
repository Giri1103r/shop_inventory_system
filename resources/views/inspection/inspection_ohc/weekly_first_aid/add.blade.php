@extends('admin.layouts.admin')
@section('title', 'Weekly First Aid Checklist')
@section('pageurl', admin_url('ohc/first-aid-box/weekly-inspection/list'))


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
                                        href="{{ admin_url('ohc/first-aid-box/weekly-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="WeeklyFirstAidAdd" enctype="multipart/form-data"
                                    action="{{ admin_url('ohc/first-aid-box/weekly-inspection/add/submit') }}">
                                    @csrf


                                    <input type="hidden" name="document_reference_id"
                                    value="{{ $document_no->id }}">
                                    
                                    <div class="basic-form">

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name="document_no" id = "document_no"
                                                        class="form-control" value="{{ $document_no->doc_no }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Issued
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="issue_date" id="issue_date"
                                                            class="form-control"autocomplete="off" value="{{ displaydateformat($document_no->issue_date) }}" readonly>
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Review
                                                        Date</label>
                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" 
                                                            name="review_date" id="review_date" class="form-control" value="{{ $document_no->rev_dt }}" readonly
                                                            autocomplete="off" readonly>

                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label for="rate" class="form-label require ">Date Of
                                                        Inspection</label>
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
                                                    <label class="form-label require">First Aid Box Number</label>
                                                    <input type="text" name="first_aid_box_no" id = "first_aid_box_no"
                                                        class="form-control">
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

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="location_id" class="form-label">
                                                        Location</label>
                                                    <select name="location_id" id="location_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Location</option>
                                                        @foreach ($location as $loc)
                                                            <option value="{{ encryptId($loc->id) }}">
                                                                {{ $loc->location_name }}</option>
                                                        @endforeach
                                                    </select>
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
                                                    <label class="form-label require">First Aider Name</label>
                                                    <select name="first_aider" id="first_aider"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select the option</option>
                                                        @foreach ($First_aid as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->certifier_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class=" mt-3">
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
                                                                        <input class="form-control" type="text"
                                                                            name="available_quantity[{{ $medicines->id }}]" />
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-input">
                                                                        <input class="form-control expired_date"
                                                                            type="date"
                                                                            name="expired_date[{{ $medicines->id }}]" />
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

                                            <div class="row m-2">
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

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Remark By</label>
                                                        <textarea class="form-control" name="remark_by" id="remark_by"></textarea>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="submit-button m-2" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class="submit"></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-cancel>
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
            flatpickr("#issue_date", {
                dateFormat: "d-m-Y",
            });
            flatpickr("#date_of_inspection", {
                dateFormat: "d-m-Y",
            });
            flatpickr(".expired_date", {
                dateFormat: "d-m-Y",
            });


            $('#WeeklyFirstAidAdd').validate({
                rules: {
                    document_no: {
                        required: true,
                    },
                    issue_date: {
                        required: true,
                    },
                    review_date: {
                        required: true,
                    },
                    date_of_inspection: {
                        required: true,
                    },
                    first_aid_box_no: {
                        required: true,
                    },
                    shift: {
                        required: true,
                    },
                    location_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    first_aider: {
                        required: true,
                    },
                    next_due: {
                        required: true,
                        date: true
                    },
                    signature_image: {
                        required: true,
                    }
                },
                messages: {
                    document_no: {
                        required: "Document Number is required",
                    },
                    issue_date: {
                        required: "Issued Date is required",
                    },
                    review_date: {
                        required: "Review Date is required",
                    },
                    date_of_inspection: {
                        required: "Date of Inspection is required",
                    },
                    first_aid_box_no: {
                        required: "First Aid Box Number is required",
                    },
                    shift: {
                        required: "Shift is required",
                    },
                    location_id: {
                        required: "Location is required",
                    },
                    unit_id: {
                        required: "Unit is required",
                    },
                    first_aider: {
                        required: "First Aider Name is required",
                    },
                    next_due: {
                        required: "Next Due Date is required",
                        date: "Please enter a valid date"
                    },
                    signature_image: {
                        required: "Signature is required",
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


            $('#WeeklyFirstAidAdd').on('change input',
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
                        required: 500,
                        messages: {
                            required: "Remarks is required"
                        }
                    });
                });
            });
        });
    </script>
@endpush
