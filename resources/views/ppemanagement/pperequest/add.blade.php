@extends('admin.layouts.admin')
@section('title', 'PPE Shoe Request Add')
@section('pageurl', admin_url('ppe_request/list'))


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
                                    <x-button-back href="{{ admin_url('ppe_request/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="pperequestadd"
                                        action="{{ admin_url('ppe_request/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="emp_name" class="form-label require">Employee Name</label>
                                                    <input type="text" name="emp_name"
                                                        class="form-control form-control-sm " id="emp_name"
                                                        value="{{ $employee->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="emp_id" class="form-label require">Employee ID</label>
                                                    <input type="text" name="emp_id"
                                                        class="form-control form-control-sm "id="emp_id"
                                                        value="{{ $employee->employee_id }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="department" class="form-label require">Department</label>
                                                    <input type="text" name="department" id="department"
                                                        class="form-control form-control-sm"
                                                        value="{{ getDepartment($employee->department_id) }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Item Code</label>
                                                    <select name="item_code" id="item_code" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the Item Code</option>
                                                        @foreach ($ppetypemaster as $itemcode)
                                                            <option value="{{ $itemcode->item_code }}">
                                                                {{ $itemcode->item_code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Type</label>
                                                    <select name="ppe_type" id="ppe_type" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE type</option>
                                                    </select>
                                                    @error('ppe_type')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_type_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">PPE Name</label>
                                                    <select name="ppe_name" id="ppe_name" style="width: 100%"
                                                        class="form-select form-select-sm single-select ">
                                                        <option value="">Select the PPE name</option>
                                                    </select>
                                                    @error('ppe_name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                    <div class="text-danger" id="ppe_name_error"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <label for="reason" class="form-label require">Reason</label>
                                                <textarea name="reason" id="reason" cols="3" rows="4" class="form-control form-control-sm"
                                                    placeholder="Enter the Reason"></textarea>
                                                @error('reason')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                                <div class="text-danger" id="reason_error"></div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_type/list') }}"></x-button-cancel>
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
    <script>
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });
        });
        $(document).on('change', '#item_code', function() {
            let PPEtypeId = $(this).val();
            console.log(PPEtypeId);

            if (PPEtypeId) {
                $.ajax({
                    url: "{{ admin_url('ppe_ppetype_master/ajax-list') }}",
                    type: 'GET',
                    data: {
                        id: PPEtypeId,
                        _ts: new Date().getTime()
                    },
                    success: function(data) {
                        console.log(data);
                        $('#ppe_type').empty().append('<option value="">Select PPE type</option>');
                        $.each(data, function(key, value) {
                            $('#ppe_type').append('<option value="' + value.id + '">' + value
                                .ppe_type + '</option>');
                        });
                        $('#ppe_type').trigger('change');
                    },
                    error: function(xhr) {
                        alert('Error fetching PPE Types. Please try again.');
                    }
                });
            } else {
                $('#ppe_type').empty().append('<option value="">Select PPE Type</option>');
                $('#ppe_type').trigger('change');
            }
        });

        $(document).on('change', '#ppe_type', function() {
            let PPEnameId = $(this).val();
            console.log(PPEnameId);

            if (PPEnameId) {
                $.ajax({
                    url: "{{ admin_url('ppe_ppetype_master/ajax-ppename') }}",
                    type: 'GET',
                    data: {
                        id: PPEnameId,
                        _ts: new Date().getTime()
                    },
                    success: function(data) {
                        console.log(data);
                        $('#ppe_name').empty().append('<option value="">Select PPE Name</option>');
                        $.each(data, function(key, value) {
                            $('#ppe_name').append('<option value="' + value.id + '">' + value
                                .ppe_name + '</option>');
                        });
                        $('#ppe_name').trigger('change');
                    },
                    error: function(xhr) {
                        alert('Error fetching PPE names. Please try again.');
                    }
                });
            } else {
                $('#ppe_name').empty().append('<option value="">Select PPE Name</option>');
                $('#ppe_name').trigger('change');
            }
        });



        $(document).ready(function() {
            $('#pperequestadd').validate({
                rules: {
                    item_code: {
                        required: true,
                    },
                    ppe_name: {
                        required: true,
                    },
                    ppe_type: {
                        required: true,
                    },
                    reason: {
                        required: true,
                        minlength: 3,
                        maxlength: 255,
                        regex:/^[a-zA-Z\s][a-zA-Z\s.]*$/

                    },
                },
                messages: {

                    item_code: {
                        required: "Please Select the Item Code.",
                    },

                    ppe_name: {
                        required: "Please Select the PPE Name.",
                    },
                    ppe_type: {
                        required: "Please Select the PPE Type.",
                    },
                    reason: {
                        required: "Reason cannot be empty.",
                        minlength: "Reason must contain between 3 and 255 characters.",
                        maxlength: "Reason must contain between 3 and 255 characters.",
                        regex: "Reason must contain only letters and numbers."
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    var errorDiv = element.siblings('div.text-danger');
                    errorDiv.html(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form) {
                    $('#submit').prop('disabled', true);
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

            $.validator.addMethod("regex", function(value, element, regexp) {
                return this.optional(element) || regexp.test(value);
            }, "Please check your input.");
        });
    </script>
@endpush
