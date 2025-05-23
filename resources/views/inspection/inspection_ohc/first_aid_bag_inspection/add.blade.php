@extends('admin.layouts.admin')
@section('title', ' FIRST AID BAG INSPECTION CHECKLIST')
@section('pageurl', admin_url('ohc/emergency-floor-first-aid-bag/checklist/list'))
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
                                        href="{{ admin_url('ohc/emergency-floor-first-aid-bag/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetygalleryAdd"
                                        action="{{ admin_url('ohc/emergency-floor-first-aid-bag/checklist/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="inspection_date" id = "inspection_date"
                                                            class="form-control inspection_date"
                                                            value="{{ old('inspection_date') }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('inspection_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>

                                                    <div class="input-group date form-input custom-height">
                                                        <input type="text" name="next_due" id = "next_due"
                                                            class="form-control next_due" value="{{ old('next_due') }}">
                                                        <div class="input-group-addon input-group-text">
                                                            <span class="fa fa-calendar"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                @error('next_due')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
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
                                                            <option value="{{ encryptId($location->id) }}"
                                                                {{ old('location_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                {{ $location->location_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('location_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>

                                                    </select>
                                                </div>
                                                @error('unit_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.shifts') }}</label>
                                                    <select name="shift_id" id="shift_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select {{ __('inspection.shifts') }}
                                                        </option>
                                                        @foreach ($shifts as $location)
                                                            <option value="{{ encryptId($location->id) }}"
                                                                {{ old('shift_id') == encryptId($location->id) ? 'selected' : '' }}>
                                                                {{ $location->shift }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('shift_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
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
                                                            <option value="{{ encryptId($frequency->id) }}"
                                                                {{ old('frequency_id') == encryptId($frequency->id) ? 'selected' : '' }}>
                                                                {{ $frequency->frequency_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('frequency_id')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Name Of Inspection</th>
                                                            <th style="text-align: center">Freeze Quantity</th>
                                                            <th style="text-align: center">Available Quantity</th>
                                                            <th style="text-align: center">Expiry Date</th>
                                                            <th style="text-align: center">Inspected By</th>
                                                            <th style="text-align: center">Remark</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($medicines as $medicines)
                                                            <tr>
                                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                                <td class="text-center">
                                                                    {{ getMedicinename($medicines->medicine_id) }}
                                                                    <input type="hidden" name="id[{{ $medicines->id }}]"
                                                                        value="{{ encryptId($medicines->id) }}">
                                                                </td>

                                                                <td class="text-center">
                                                                    {{ $medicines->freeze_quantity }}
                                                                    <input type="hidden"
                                                                        name="freeze_quantity[{{ $medicines->id }}]"
                                                                        value="{{ $medicines->freeze_quantity }}">
                                                                </td>

                                                                <td>
                                                                    <div class="form-input">
                                                                        <input class="form-control" type="number"
                                                                            min="1"
                                                                            name="available_quantity[{{ $medicines->id }}]"
                                                                            value="{{ old('available_quantity.' . $loop->iteration) }}" />
                                                                    </div>
                                                                    @error('available_quantity.' . $loop->iteration)
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </td>

                                                                <td>
                                                                    <div class="form-input">

                                                                        <div
                                                                            class="input-group date form-input custom-height">
                                                                            <input class="form-control expired_date"
                                                                                type="date"
                                                                                name="expired_date[{{ $medicines->id }}]"
                                                                                value="{{ old('expired_date.' . $loop->iteration) }}" />
                                                                            <div
                                                                                class="input-group-addon input-group-text">
                                                                                <span class="fa fa-calendar"></span>
                                                                            </div>
                                                                        </div>
                                                                        @error('expired_date.' . $loop->iteration)
                                                                            <div class="error">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="form-input">
                                                                        <select name="emp_id[{{ $medicines->id }}]"
                                                                            id="emp_id[{{ $loop->iteration }}]"
                                                                            style="width: 100%"
                                                                            class="form-select single-select emp_id">
                                                                            <option value="">Select Employee Name
                                                                            </option>
                                                                        </select>
                                                                        @error('emp_id.' . $loop->iteration)
                                                                            <div class="error">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </td>

                                                                <td>
                                                                    <div class="form-input">
                                                                        <textarea class="form-control" style="resize: none" name="remarks[{{ $medicines->id }}]">{{ old('remarks.' . $loop->iteration) }}</textarea>
                                                                    </div>
                                                                    @error('remarks.' . $loop->iteration)
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
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
                                            </div>



                                            <div class="submit-button m-2" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class="submit"></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('ohc/emergency-floor-first-aid-bag/checklist/list') }}"></x-button-cancel>
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
            $(document).on('change', '#location_id', function() {
                var locationId = $(this).val();
                if (locationId) {
                    $.ajax({
                        url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#unit_id').empty().append(
                                '<option value="">Select Unit</option>');
                            $.each(data, function(key, value) {
                                $('#unit_id').append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            $('#unit_id').trigger('change.');
                        },
                        error: function(xhr) {
                            alert('Error fetching unit. Please try again.');
                        }
                    });
                } else {
                    $('#unit_id').empty().append('<option value="">Select Unit</option>');
                    $('#unit_id').trigger('change.');
                }
            });
            $(document).ready(function() {
                $('#resetform').on('click', function(e) {
                    e.preventDefault();
                    location.reload();
                });
                flatpickr(".expired_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr(".inspection_date", {
                    dateFormat: "d-m-Y",
                });
                flatpickr(".next_due", {
                    dateFormat: "d-m-Y",
                });


                $('.emp_id').select2({
                    ajax: {
                        url: '{{ admin_url('ohc/prescribe-to-patient/employeename') }}',
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
                        }
                    },
                    minimumInputLength: 1,
                    dropdownCssClass: 'form-control',
                    selectionCssClass: 'form-control'
                });


                $('#safetygalleryAdd').validate({
                    rules: {
                        inspection_date: {
                            required: true,
                        },
                        next_due: {
                            required: true,
                        },
                        signature_image: {
                            required: true,
                            filesize: 15728640,
                        },
                        frequency_id: {
                            required: true,
                        },
                        location_id: {
                            required: true,
                        },
                        unit_id: {
                            required: true,
                        },
                        shift_id: {
                            required: true,
                        },
                    },
                    messages: {
                        inspection_date: {
                            required: "Inspection Date is required",
                        },
                        next_due: {
                            required: "Next Due Date is required",
                        },
                        signature_image: {
                            required: "Signature is required",
                            filesize: "File size must be less than 15MB."
                        },
                        frequency_id: {
                            required: "Frequency is required",
                        },
                        location_id: {
                            required: "Location is required",
                        },
                        unit_id: {
                            required: "Unit is required",
                        },
                        shift_id: {
                            required: "Shift is required",
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
                    }
                });

                $('#safetygalleryAdd').on('change input',
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
