@extends('admin.layouts.admin')
@section('title', 'Monthly OHC First-Aid Medicine Inspection Checklist')
@section('pageurl', admin_url('ohc/first-aid/opd-medicine-inspection/list'))
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
                                        href="{{ admin_url('ohc/first-aid/opd-medicine-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">
                                    <form method="POST" id="safetygalleryAdd"
                                        action="{{ admin_url('ohc/first-aid/opd-medicine-inspection/add/submit') }}"
                                        autocomplete="off" enctype="multipart/form-data">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                    <input type="text" name="inspection_date" id = "inspection_date"
                                                        class="form-control inspection_date"
                                                        value="{{ old('inspection_date') }}">
                                                </div>
                                                @error('inspection_date')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label require">{{ __('inspection.next_due') }}</label>
                                                    <input type="text" name="next_due" id = "next_due"
                                                        class="form-control next_due" value="{{ old('next_due') }}">
                                                </div>
                                                @error('next_due')
                                                    <div class="error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-secondary">
                                                        <tr>
                                                            <th style="text-align: center">Sr. No.</th>
                                                            <th style="text-align: center">Name Of Inspection</th>
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
                                                                    {{ getMedicinename($medicines->medicine_id) }} <input
                                                                        type="hidden" name="id[{{ $medicines->id }}]"
                                                                        value="{{ encryptId($medicines->id) }}"></td>
                                                                <td>
                                                                    <div class="form-input">
                                                                        <input class="form-control" type="number" min="1"
                                                                            name="available_quantity[{{ $medicines->id }}]"
                                                                            value="{{ old('available_quantity.' . $loop->iteration) }}" />
                                                                    </div>
                                                                    @error('available_quantity.' . $loop->iteration)
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <div class="form-input">
                                                                        <input class="form-control expired_date"
                                                                            type="date"
                                                                            name="expired_date[{{ $medicines->id }}]"
                                                                            value="{{ old('expired_date.' . $loop->iteration) }}" />
                                                                    </div>
                                                                    @error('expired_date.' . $loop->iteration)
                                                                        <div class="error">{{ $message }}</div>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <div class="form-input">
                                                                        <select name="emp_id[{{ $medicines->id }}]"
                                                                            id="emp_id[{{ $loop->iteration }}]"
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
                                                                        <textarea class="form-control" type="text" style="resize: none" name="remarks[{{ $medicines->id }}]">{{ old('remarks.' . $loop->iteration) }}</textarea>
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
                                                    href="{{ admin_url('ohc/first-aid/opd-medicine-inspection/list') }}"></x-button-cancel>
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
                        url: '{{ admin_url('ohc/safety-petty-logbook/employeeid') }}',
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
                            filesize: 10485760,
                        }
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
                            filesize: "File size must be less than 10MB."
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
                            minlength:3,
                            maxlength:300,
                            messages: {
                                required: "Remarks is required",
                                minlength:"Minimum 3 characters required",
                                maxlength:"Maximum character does not exceed 300"
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
