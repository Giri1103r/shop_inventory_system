@extends('admin.layouts.admin')
@section('title', 'RRAA Add')
@section('pageurl', admin_url('rraa/ohc_fire_environment_compliance/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>

    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('RRAA Add') }}</h4> --}}
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
                                    <x-button-back href="{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="rraa_Add" action="{{ admin_url('rraa/ohc_fire_environment_compliance/add/submit') }}">
                                        @csrf

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">RRAA Details</h4>
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
                                                    <label class="form-label require">Revision Date</label>
                                                    <input type="text" name ="revision_date" class="form-control"
                                                        placeholder="Revision Date" value="{{ getDocumentReviewDate('RRAA-0') }}"
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
                                                        <h4 class="text-white">RRAA CheckList</h4>
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
                                                                    value="RRAA-00001" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Category</label>
                                                                <select name="category[1]" class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Category</option>
                                                                    @foreach ($category as $item)
                                                                        <option value="{{ encryptId($item->id) }}">
                                                                            {{ $item->category_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">OHC Compliance Index</label>
                                                                <input type="text" name="ohs_compliance_index[1]"
                                                                    class="form-control" placeholder="OHC Compliance Index"
                                                                    value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Frequency</label>
                                                                <select name="frequency[1]"
                                                                    class="form-control single-select" style="width: 100%">
                                                                    <option value="">Select Frequency</option>
                                                                    @foreach ($frequency as $item)
                                                                        <option value="{{ encryptId($item->id) }}">
                                                                            {{ $item->frequency_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">scope(Unit)</label>
                                                                <input type="text" name="scope[1]"
                                                                class="form-control" placeholder="Scope"
                                                                value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Responsibility</label>
                                                                <select name="emp_id[1]" id="emp_id"
                                                                    class="form-control single-select emp-select" style="width:100%">
                                                                    <option value="">Select Responsibility</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Authority</label>
                                                                <input type="text" name="authority[1]"
                                                                class="form-control" placeholder="Authority"
                                                                value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Accountability</label>
                                                                <input type="text" name="accountability[1]"
                                                                class="form-control" placeholder="Accountability"
                                                                value="">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mt-2">
                                                            <div class="form-group form-input">
                                                                <label class="form-label require">Remark</label>
                                                                <textarea name="remark[1]" rows="3"  class="form-control"
                                                                 placeholder="Remark"></textarea>
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
                                            <x-button-cancel href="{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}"></x-button-cancel>
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

        $('#emp_id').select2({
            ajax: {
                url: '{{ admin_url('rraa/ohc_fire_environment_compliance/employeeid') }}',
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

        function initEmployeeSelect2() {
            $('.emp-select').select2({
                ajax: {
                    url: '{{ admin_url('rraa/ohc_fire_environment_compliance/employeeid') }}',
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
        }

        $('#rraa_Add').validate({
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
                'category[1]': {
                    required: true,
                },
                'ohs_compliance_index[1]': {
                    required: true,
                    noSpaces: true,
                },
                'frequency[1]': {
                    required: true,
                },
                'scope[1]': {
                    required: true,
                    noSpaces: true,
                },
                'emp_id[1]': {
                    required: true,
                },
                'authority[1]': {
                    required: true,
                    noSpaces: true,
                },
                'accountability[1]': {
                    required: true,
                    noSpaces: true,
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
                'category[1]': {
                    required: "Category is Required",
                },
                'ohs_compliance_index[1]': {
                    required: "OHS Compliance Index is Required",
                },
                'frequency[1]': {
                    required: "Frequency is Required",
                },
                'scope[1]': {
                    required: "Scope is Required",
                },
                'emp_id[1]': {
                    required: "Responsibility is Required",
                },
                'authority[1]': {
                    required: "Authority is Required",
                },
                'accountability[1]': {
                    required: "Accountability is Required",
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

        let form_set_count = 2;
        let serial_number = parseInt("{{ getRRAACount() }}", 10) + 1;
        const maxFormSets = 200;
        const minFormSets = 1;

        // $(".add-row").click(function() {
        $(document).on('click',".add-row",function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets >= maxFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum RRAA CheckList Reached',
                    text: 'You can only add up to 200 RRAA CheckList.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            let newSerialNumber = 'RRAA-' + ('0000' + serial_number).slice(-5);

            var newFormSet = `
                <div class="form-set mb-3">
                    <div class="card-header-inner">
                        <h4 class="text-white">RRAA CheckList</h4>
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
                                <label class="form-label require">Category</label>
                                <select name="category[${form_set_count}]" class="form-control single-select" style="width: 100%">
                                    <option value="">Select Category</option>
                                    @foreach ($category as $item)
                                        <option value="{{ encryptId($item->id) }}">
                                            {{ $item->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group form-input">
                                <label class="form-label require">OHC Compliance Index</label>
                                <input type="text" name="ohs_compliance_index[${form_set_count}]"
                                    class="form-control" placeholder="OHC Compliance Index"
                                    value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Frequency</label>
                                <select name="frequency[${form_set_count}]"
                                    class="form-control single-select" style="width: 100%">
                                    <option value="">Select Frequency</option>
                                    @foreach ($frequency as $item)
                                        <option value="{{ encryptId($item->id) }}">
                                            {{ $item->frequency_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">scope(Unit)</label>
                                <input type="text" name="scope[${form_set_count}]"
                                class="form-control" placeholder="Scope"
                                value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Responsibility</label>
                                <select name="emp_id[${form_set_count}]"
                                    class="form-control single-select emp-select" style="width:100%">
                                    <option value="">Select Responsibility</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Authority</label>
                                <input type="text" name="authority[${form_set_count}]"
                                class="form-control" placeholder="Authority"
                                value="">
                            </div>
                        </div>

                        <div class="col-md-4 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Accountability</label>
                                <input type="text" name="accountability[${form_set_count}]"
                                class="form-control" placeholder="Accountability"
                                value="">
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <div class="form-group form-input">
                                <label class="form-label require">Remark</label>
                                <textarea name="remark[${form_set_count}]" rows="3"  class="form-control"
                                    placeholder="Remark"></textarea>
                            </div>
                        </div>

                    </div>
                </div>`;

            $('#form-wrapper').append(newFormSet);

            serial_number++;

            $('select[name^="emp_id["]').each(function() {
                $(this).select2({
                    placeholder: "Select Responsibility",
                    width: '100%'
                });
            });

            $('select[name^="category["]').each(function() {
                $(this).select2({
                    placeholder: "Select Category",
                    width: '100%'
                });
            });

            $('select[name^="frequency["]').each(function() {
                $(this).select2({
                    placeholder: "Select Frequency",
                    width: '100%'
                });
            });

            $("select[name='category[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Category is required',
                }
            });

            $("select[name='emp_id[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Responsibility is required',
                }
            });

            $("input[name='ohs_compliance_index[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'OHS Compliance Index is required',
                    noSpaces: 'Item Code cannot be empty or only spaces'
                }
            });

            $("select[name='frequency[" + form_set_count + "]']").rules('add', {
                required: true,
                messages: {
                    required: 'Frequency is required',
                }
            });

            $("input[name='scope[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Scope is required',
                    noSpaces: 'Item Code cannot be empty or only spaces'
                }
            });

            $("input[name='authority[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Authority is required',
                    noSpaces: 'Item Code cannot be empty or only spaces'
                }
            });

            $("input[name='accountability[" + form_set_count + "]']").rules('add', {
                required: true,
                noSpaces: true,
                messages: {
                    required: 'Accountability is required',
                    noSpaces: 'Item Code cannot be empty or only spaces'
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
            initEmployeeSelect2();
        });

        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('#form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum RRAA CheckList Required',
                    text: 'At least 1 RRAA CheckList is required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();

        });

        function updatePageIndices() {
            $('#form-wrapper .form-set').each(function(index) {
                $(this).find("input[name^='serial_number']").val('RRAA-' + ('0000' + (index + 1)).slice(-5));

                $(this).find('input[name^="serial_number"]').attr('name', 'serial_number[' + (index + 1) + ']');
                $(this).find('input[name^="scope"]').attr('name', 'scope[' + (index + 1) + ']');
                $(this).find('input[name^="ohs_compliance_index"]').attr('name', 'ohs_compliance_index[' + (index + 1) + ']');
                $(this).find('select[name^="frequency"]').attr('name', 'frequency[' + (index + 1) + ']');
                $(this).find('select[name^="category"]').attr('name', 'category[' + (index + 1) + ']');
                $(this).find('select[name^="emp_id"]').attr('name', 'emp_id[' + (index + 1) + ']');
                $(this).find('input[name^="authority"]').attr('name', 'authority[' + (index + 1) + ']');
                $(this).find('input[name^="accountability"]').attr('name', 'accountability[' + (index + 1) + ']');
                $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + (index + 1) + ']');
            });
        }

        $(".submit").on('click', function() {
            if ($("#rraa_Add").valid()) {
                $("#rraa_Add").submit();
            } else {
                return false;
            }
        });
    });
</script>
@endpush
