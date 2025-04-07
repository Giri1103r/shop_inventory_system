    @extends('admin.layouts.admin')
    @section('title', 'Medicine Store Inspection Checklist')
    @section('pageurl', admin_url('fire/equipment-monthly-physical-inspection/list'))
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
                                            href="{{ admin_url('fire/equipment-monthly-physical-inspection/list') }}"></x-button-back>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="basic-form mx-3">
                                        <form method="POST" id="safetygalleryAdd"
                                            action="{{ admin_url('fire/equipment-monthly-physical-inspection/add/submit') }}"
                                            autocomplete="off" enctype="multipart/form-data">
                                            @csrf

                                            <div class="row">

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                        <input type="text" name="inspection_date" id = "inspection_date"
                                                            class="form-control inspection_date">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.next_due') }}</label>
                                                        <input type="text" name="next_due" id = "next_due"
                                                            class="form-control next_due">
                                                    </div>
                                                </div>
                                                <div class="">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th style="text-align: center">Sr. No.</th>
                                                                <th style="text-align: center">Name of Equipment</th>
                                                                <th style="text-align: center">Status</th>
                                                                <th style="text-align: center">Remark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($equipment_list as $medicines)
                                                                <tr>
                                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                                    <td class="text-center">{{ $medicines->equipment_name }}
                                                                        <input type="hidden"
                                                                            name="id[{{ $medicines->id }}]"
                                                                            value="{{ encryptId($medicines->id) }}">
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-input">
                                                                            <input class="form-control" type="text"
                                                                                name="status[{{ $medicines->id }}]" />
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

                                            </div>
                                            @foreach ($equipment_list as $index => $equipment)
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">{{ $equipment->equipment_name }}</h4>
                                                </div>
                                                <div class="row justify-content-between">
                                                    <div class="form-input col-md-4 mb-2">
                                                        <label class="form-label require">Image - 1
                                                        </label>
                                                        <input type="file" name="equipment[{{ $index + 1 }}][1]"
                                                            id="signature_upload_{{ $index }}"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Upload {{ $equipment->name }} image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload_{{ $index }}" class="text-danger">
                                                        </div>
                                                    </div>

                                                    <div class="form-input col-md-4 mb-2">
                                                        <label class="form-label require">Image - 2</label>
                                                        <input type="file" name="equipment[{{ $index + 1 }}][2]"
                                                            id="signature_upload_signature_{{ $index }}"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Upload signature for {{ $equipment->name }}">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload_signature_{{ $index }}"
                                                            class="text-danger"></div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- <div class="row m-2">
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
                                            </div> --}}

                                            <div class="submit-button m-2" style="text-align: right;">
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
