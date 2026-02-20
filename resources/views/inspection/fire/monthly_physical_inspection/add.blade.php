    @extends('admin.layouts.admin')
    @section('title', 'Fire Equipment Monthly Physical Inspection')
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
                                    <div class="align-back-btc d-flex justify-content-end align-items-center">
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
                                                            class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                        <input type="text" name="doc_no" id = "doc_no"
                                                            class="form-control" placeholder="Enter the Document Number"
                                                            value="{{ $document_no->doc_no }}" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.issue_date') }}</label>

                                                        <div class="input-group date form-input custom-height">
                                                            <input type="text" name="issue_date" id = ""
                                                                class="form-control" placeholder="Issued Date"
                                                                value="{{ displaydateformat($document_no->issue_date) }}"
                                                                readonly>
                                                            <div class="input-group-addon input-group-text">
                                                                <span class="fa fa-calendar"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                        <input type="text" name="rev_date" id = "rev_date"
                                                            class="form-control" value="{{ $document_no->rev_dt }}"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.inspection_date') }}</label>


                                                        <div class="input-group date form-input custom-height">
                                                            <input type="text" name="inspection_date"
                                                                id = "inspection_date" class="form-control inspection_date">
                                                            <div class="input-group-addon input-group-text">
                                                                <span class="fa fa-calendar"></span>
                                                            </div>
                                                        </div>
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
                                                        <label
                                                            class="form-label require">{{ __('inspection.unit') }}</label>
                                                        <select name="unit_id" id="unit_id"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>

                                                        </select>
                                                    </div>
                                                    @error('unit_id')
                                                        <div class="error">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <hr>
                                                <input type="hidden" name="document_reference_id"
                                                    value="{{ encryptId($document_no->id) }}">
                                                <div class="">
                                                    <table class="table table-bordered table-striped">
                                                        <thead class="table-secondary">
                                                            <tr>
                                                                <th style="text-align: center">Sr. No.</th>
                                                                <th style="text-align: center">Name of Equipment</th>
                                                                <th style="text-align: center">Frequency</th>
                                                                <th style="text-align: center">Status</th>
                                                                <th style="text-align: center">Remark</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($equipment_list as $medicines)
                                                                <tr>z
                                                                    <td class="text-center">{{ $loop->iteration }}</td>
                                                                    <td class="text-center">
                                                                        {{ $medicines->equipment_name }}
                                                                        <input type="hidden"
                                                                            name="id[{{ $medicines->id }}]"
                                                                            value="{{ encryptId($medicines->id) }}">
                                                                    </td>
                                                                    <td>
                                                                        <div class="form-group form-input">
                                                                            <select name="frequency[{{ $medicines->id }}]"
                                                                                id="frequency_{{ $loop->iteration }}"
                                                                                class=" form-control single-select"
                                                                                style="width: 100%">
                                                                                <option value="">Select
                                                                                    {{ __('inspection.frequency') }}
                                                                                </option>
                                                                                @foreach ($frequencies as $frequency)
                                                                                    <option
                                                                                        value="{{ encryptId($frequency->id) }}">
                                                                                        {{ $frequency->frequency_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
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
                                                        <input type="file" name="equipment[{{ $equipment->id }}][1]"
                                                            id="signature_upload_{{ $index }}"
                                                            class="form-control form-control-sm" accept="image/*"
                                                            placeholder="Upload {{ $equipment->name }} image">
                                                        <small>Allowed file types: jpg, jpeg, png</small>
                                                        <div id="signature_upload_{{ $index }}"
                                                            class="text-danger">
                                                        </div>
                                                    </div>

                                                    <div class="form-input col-md-4 mb-2">
                                                        <label class="form-label require">Image - 2</label>
                                                        <input type="file" name="equipment[{{ $equipment->id }}][2]"
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
                                                    href="{{ admin_url('fire/equipment-monthly-physical-inspection/list') }}"></x-button-cancel>
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

                    $(document).on('change', '#location_id', function() {
                        var locationId = $(this).val();
                        if (locationId) {
                            $.ajax({
                                url: "{{ admin_url('unit/ajax-list') }}/" + locationId + "/0",
                                type: 'GET',
                                dataType: 'json',
                                success: function(data) {
                                    $('#unit_id').empty().append(
                                        '<option value="">Select unit</option>');
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
                            $('#unit_id').empty().append('<option value="">Select unit</option>');
                            $('#unit_id').trigger('change.');
                        }
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
                            unit_id: {
                                required: true,
                            },
                            location_id: {
                                required: true,
                            },
                            frequency: {
                                required: true,
                            }

                        },
                        messages: {
                            inspection_date: {
                                required: "Inspection Date is required",
                            },
                            unit_id: {
                                required: "Unit is Required",
                            },
                            location_id: {
                                required: "Location is required",
                            },
                            frequency: {
                                required: "Frequency is required",
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
                        $('input[type="file"][name^="equipment"]').each(function() {
                            $(this).rules('add', {
                                required: true,
                                accept: "image/jpeg, image/png, image/jpg",
                                 filesize: 15728640,
                                messages: {
                                    required: "Please upload an image.",
                                    accept: "Only JPG, JPEG, and PNG files are allowed.",
                                     filesize: "File size should not exceed 15MB",
                                }
                            });
                        });

                        $('input[name^="status"]').each(function() {
                            $(this).rules('add', {
                                required: true,
                                messages: {
                                    required: "Status is required",
                                }
                            });
                        });

                        $('textarea[name^="remarks"]').each(function() {
                            $(this).rules('add', {
                                required: true,
                                minlength: 3,
                                messages: {
                                    required: "Remarks are required",
                                    minlength: "Remarks must be at least 3 characters long."
                                }
                            });
                        });
                    });
                });
            </script>
        @endpush
