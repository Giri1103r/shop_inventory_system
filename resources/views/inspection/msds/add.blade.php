@extends('admin.layouts.admin')
@section('title', 'MSDS Add')
@section('pageurl', admin_url('msds/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>

    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('MSDS Add') }}</h4> --}}
        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('msds/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="msdsAdd" action="{{ admin_url('msds/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">MSDS Details</h4>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Document Number</label>
                                                    <input type="text" name ="document_number" class="form-control"
                                                        placeholder="Document Number" value="">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Issue Date</label>
                                                    <input type="text" name ="issue_date" id="issue_date"
                                                        class="form-control" placeholder="Issue Date" value="">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Revision Data</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ getDocumentReviewDate('MSDS-0') }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-4">
                                            <div class="row mt-2">
                                                <div
                                                    class="d-flex justify-content-end align-items-center me-2 mb-3 button-container">
                                                   
                                                </div>
                                            </div>

                                            <div id="form-wrapper">
                                                <div class="form-set mb-3">
                                                    <div class="card-header-inner">
                                                        <h4 class="text-white">MSDS CheckList</h4>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button class="btn btn-primary add-row me-3" type="button"
                                                        id="add-row" style="width: 84px;">
                                                        Add
                                                    </button>
                                                        <button type="button" class="btn btn-danger remove-row">
                                                            <i class="fa-solid fa-trash"></i> Remove
                                                        </button>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Serial Number</label>
                                                                <input type="text" name="serial_number[1]"
                                                                    class="form-control" placeholder="Serial Number"
                                                                    value="MSDS-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Item Code</label>
                                                                <input type="text" name="item_code[1]"
                                                                    class="form-control" placeholder="Item Code"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Name of Chemical</label>
                                                                <input type="text" name="name_of_chemical[1]"
                                                                    class="form-control" placeholder="Name of Chemical"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">MSDS Availability
                                                                    Status</label>
                                                                <select name="msds_availability_status[1]"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select MSDS Availability Status
                                                                    </option>
                                                                    <option value="Yes">Yes</option>
                                                                    <option value="No">No</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[1]" class="form-control" placeholder="Remark" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="submit-button" style="text-align: right;">

                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('msds/list') }}"></x-button-cancel>
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

        var fromDatepicker = flatpickr("#issue_date", {
            dateFormat: "d-m-Y",
            minDate: new Date(),
        });

        $.validator.addMethod("noSpaces", function(value, element) {
            return this.optional(element) || value.trim().length > 0;
        }, "This field cannot contain only spaces");

        $('#msdsAdd').validate({
            rules: {
                document_number: {
                    required: true,
                    noSpaces: true,
                },
                issue_date: {
                    required: true,
                },
                revision_date: {
                    required: true,
                },
                'item_code[1]': {
                    required: true,
                    uniqueItemCode: true,
                    noSpaces: true,
                },
                'name_of_chemical[1]': {
                    required: true,
                    noSpaces: true,
                },
                'msds_availability_status[1]': {
                    required: true,
                },
                'remark[1]': {
                    required: true,
                    noSpaces: true,
                },
            },
            messages: {
                document_number: {
                    required: "Document Number is Required",
                },
                issue_date: {
                    required: "Please Select Issue Date",
                },
                revision_date: {
                    required: "Please Select Revision Date",
                },
                'item_code[1]': {
                    required: "Item Code is Required",
                    uniqueItemCode: "Item Code must be unique",
                },
                'name_of_chemical[1]': {
                    required: "Name of Chemical is Required",
                },
                'msds_availability_status[1]': {
                    required: "MSDS Availability Status is Required",
                },
                'remark[1]': {
                    required: "Remark is Required",
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
                validator.errorList.forEach(function(error) {});
            }
        });


        $.validator.addMethod("uniqueItemCode", function(value, element) {
            var itemCodes = [];
            
            $("input[name^='item_code']").each(function() {
                var itemCodeValue = $(this).val();
                if (itemCodeValue) {
                    itemCodes.push(itemCodeValue);  
                }
            });
           
            return itemCodes.indexOf(value) === itemCodes.lastIndexOf(value);
        }, "Item Code must be unique");

        let form_set_count = 2;
        let serial_number = parseInt("{{ getMSDSCount() }}", 10) + 1;
        const maxFormSets = 200;
        const minFormSets = 1;

        // $(".add-row").click(function() {
        $(document).on('click',".add-row",function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets >= maxFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum MSDS CheckList Reached',
                    text: 'You can only add up to 200 MSDS CheckList.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let newSerialNumber = 'MSDS-' + ('0000' + serial_number).slice(-5);

            var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">MSDS CheckList</h4>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary add-row me-3" type="button"
                            id="add-row" style="width: 84px;">
                            Add
                        </button>
                        <button type="button" class="btn btn-danger remove-row">
                            <i class="fa-solid fa-trash"></i> Remove
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Serial Number</label>
                                <input type="text" name="serial_number[${form_set_count}]" class="form-control" placeholder="Serial Number" value="${newSerialNumber}" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Item Code</label>
                                <input type="text" name="item_code[${form_set_count}]" class="form-control" placeholder="Item Code" value="">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">Name of Chemical</label>
                                <input type="text" name="name_of_chemical[${form_set_count}]" class="form-control" placeholder="Name of Chemical" value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">MSDS Availability Status</label>
                                <select name="msds_availability_status[${form_set_count}]" class="form-control single-select" style="width: 100%">
                                    <option value="">Select MSDS Availability Status</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Remark</label>
                                <textarea name="remark[${form_set_count}]" class="form-control" placeholder="Remark" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>`;

            $('#form-wrapper').append(newFormSet);

            serial_number++;

            $('select[name^="msds_availability_status["]').each(function() {
                $(this).select2({
                    placeholder: "Select MSDS Availability Status",
                    width: '100%'
                });
            });

            $("input[name='item_code[" + form_set_count + "]']").rules('add', {
                required: true,
                uniqueItemCode: true,
                noSpaces: true,
                messages: {
                    required: 'Item Code is required',
                    uniqueItemCode: 'Item Code must be unique',
                    noSpaces: 'Item Code cannot be empty or only spaces'
                }
            });

            $("input[name='name_of_chemical[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true, 
                messages: {
                    required: 'Name of Chemical is required',
                    noSpaces: 'Item Code cannot be empty or only spaces'
                }
            });

            $("select[name='msds_availability_status[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'MSDS Availability Status is required',
                }
            });

            $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Remark is required',
                    noSpaces: 'Remark cannot be empty or only spaces'
                }
            }); 
            form_set_count++;
            updatePageIndices(); 
        });

        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum MSDS CheckList Required',
                    text: 'At least 1 MSDS CheckList is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();
        });

        function updatePageIndices() {
            $('#form-wrapper .form-set').each(function(index) {
                $(this).find("input[name^='serial_number']").val('MSDS-' + ('0000' + (index + 1)).slice(-5));

                $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index + 1) + ']'); 
                $(this).find('input[name^="item_code"]').attr('name', 'item_code[' + (index + 1) + ']'); 
                $(this).find('input[name^="name_of_chemical"]').attr('name', 'name_of_chemical[' + (index + 1) + ']');
                $(this).find('select[name^="msds_availability_status"]').attr('name', 'msds_availability_status[' + (index + 1) + ']'); 
                $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
            });
        }

        $(".submit").on('click', function() {
            if ($("#msdsAdd").valid()) {
                $("#msdsAdd").submit();
            } else {
                return false;
            }
        });
    });
</script>
@endpush

