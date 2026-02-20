@extends('admin.layouts.admin')
@section('title', 'Checklist Sub Type Data')
@section('pageurl', admin_url('inspection/master/checklist-sub-type-data/list'))


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
                                <h4 class="card-title"></h4>
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-sub-type-data/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addchecklist"
                                        action="{{ admin_url('inspection/master/checklist-sub-type-data/add/submit') }}">
                                        @csrf

                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Type Name</label>
                                                    <select name="checklist_type_id" id="checklist_type_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Checklist Type Name</option>
                                                        @foreach ($checklistTypeList as $list)
                                                            <option value="{{ encryptId($list->id) }}">
                                                                {{ $list->category_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Sub Type Name</label>
                                                    <select name="checklist_sub_type_id" id="checklist_sub_type_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Checklist Sub Type Name</option>

                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mt-3">
                                            <div class="card p-3">
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                                    <h4 class="text-dark mb-0"></h4>
                                                    <button type="button" class="btn btn-sm btn-success addchecklistBody">
                                                        Add
                                                    </button>
                                                </div>

                                                <!-- Table -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered text-center">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>Checklist Sub-Type Data Name</th>
                                                                <th>Description</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="checklistBody">
                                                            <tr id="RowchecklistView0">
                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <input type="text" name="checklist[0][name]"
                                                                            class="form-control data_name"
                                                                            id="sub_type_data_name_0">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group form-input">
                                                                        <textarea name="checklist[0][description]" class="form-control"></textarea>
                                                                    </div>
                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('inspection/master/checklist-sub-type-data/list') }}"></x-button-cancel>
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
        });

        $(document).on('change', '#checklist_type_id', function() {
            let checklistTypeId = $(this).val();

            if (checklistTypeId) {
                $.ajax({
                    url: "{{ admin_url('inspection/master/checklist-sub-type/ajax-list') }}/" +
                        checklistTypeId + "/0",
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#checklist_sub_type_id').empty().append(
                            '<option value="">Select Checklist Sub Type Name</option>');
                        $.each(data, function(key, value) {
                            $('#checklist_sub_type_id').append('<option value="' + value.id +
                                '">' + value
                                .name + '</option>');
                        });
                        $('#checklist_sub_type_id').trigger('change.');
                    },
                    error: function(xhr) {
                        alert('Error fetching Checklist Sub Type Name. Please try again.');
                    }
                });
            } else {
                $('#checklist_sub_type_id').empty().append(
                    '<option value="">Select Checklist Sub Type Name</option>');
                $('#checklist_sub_type_id').trigger('change.');
            }
        });
        $(document).ready(function() {

            let checklistIndex = 1;
            $(".addchecklistBody").on("click", function() {
                let rowCount = $("#checklistBody tr").length;

                // if (rowCount >= 5) {
                //     Swal.fire({
                //         icon: "warning",
                //         title: "Limit Reached",
                //         text: "Maximum of 5 rows can be added.",
                //         confirmButtonColor: "#d33"
                //     });
                //     return;
                // }
                const newRow = `
                                <tr id="RowchecklistView${checklistIndex}">
                        <td>
                            <div class="form-group form-input">
                                <input type="text" name="checklist[${checklistIndex}][name]" id="sub_type_data_name_${checklistIndex}" class="form-control data_name">
                            </div>
                        </td>
                        <td>
                            <div class="form-group form-input">
                                <textarea name="checklist[${checklistIndex}][description]" class="form-control"></textarea>
                            </div>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm removeChecklistRow">
                                <i class="fa-solid fa-trash text-danger"></i>
                            </button>
                        </td>
                    </tr>
                    `


                $("#checklistBody").append(newRow);
                addchecklistValidation(checklistIndex);
                checklistIndex++;

            });

            $(document).on("click", ".removeChecklistRow", function() {
                const rowCount = $("#checklistBody tr").length;
                if (rowCount > 1) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to delete this row?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "Cancel"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).closest("tr").fadeOut(300, function() {
                                $(this).remove();
                            });

                            Swal.fire("Deleted!", "The row has been deleted.", "success");
                        }
                    });
                } else {
                    Swal.fire("Warning!", "At least one row is required!", "error");
                }
            });



            function addchecklistValidation(checklistIndex) {

                let dataName = $(`#sub_type_data_name_${checklistIndex}`);

                dataName.rules('add', {
                    required: true,
                    minlength: 3,
                    maxlength: 200,

                    remote: {
                        url: '{{ admin_url('inspection/master/checklist-sub-type-data/unique') }}',
                        type: 'post',
                        data: {
                            checklist_type_id: function() {
                                return $('#checklist_type_id').val();
                            },
                            checklist_sub_type_id: function() {
                                return $('#checklist_sub_type_id').val();
                            },
                            subcategory_name: function() {
                                return dataName.val();
                            }
                        }
                    },
                    messages: {
                        required: "Checklist Sub-Type Data Name is Required",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 200",

                        remote: "Checklist Sub-Type Data Name should be unique"
                    }
                });



            }
            $(function() {
                $(document).on('click', '#resetform', function() {
                    $('#addchecklist .single-select').val('');
                    $('#addchecklist .single-select').trigger('change');
                    setTimeout(function() {
                        table.draw();
                    }, 150);
                });

                $('#addchecklist').validate({
                    rules: {
                        checklist_type_id: {
                            required: true,
                        },
                        checklist_sub_type_id: {
                            required: true,
                        },
                        'checklist[0][name]': {
                            required: true,
                            minlength: 3,
                            maxlength: 200,

                            remote: {
                                url: '{{ admin_url('inspection/master/checklist-sub-type-data/unique') }}',
                                type: 'post',
                                data: {
                                    checklist_type_id: function() {
                                        return $('#checklist_type_id').val();
                                    },
                                    checklist_sub_type_id: function() {
                                        return $('#checklist_sub_type_id').val();
                                    },
                                    subcategory_name: function() {
                                        return $('#sub_type_data_name_0').val();
                                    },
                                }
                            }
                        },


                    },
                    messages: {
                        checklist_type_id: {
                            required: "Checklist Type Name is Required",
                        },
                        checklist_sub_type_id: {
                            required: "Checklist Sub Type Name is Required",
                        },
                        'checklist[0][name]': {
                            required: "Checklist Sub-Type Data Name is Required",
                            minlength: "{{ __('common.validate_min_length') }}",
                            maxlength: "Maximum Characters should not exceed 200",

                            remote: "Checklist Sub-Type Data Name should be unique"
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
                        let names = [];
                        let isDuplicate = false;

                        $(".data_name").each(function() {
                            let val = $(this).val().trim().toLowerCase();
                            if (val !== "") {
                                if (names.includes(val)) {
                                    isDuplicate = true;
                                    return false; // break loop
                                }
                                names.push(val);
                            }
                        });

                        if (isDuplicate) {
                            Swal.fire({
                                icon: "warning",
                                title: "Duplicate Entry",
                                text: "Checklist Sub-Type Data Name should be unique across all rows.",
                                confirmButtonColor: "#d33"
                            });
                            return false; // prevent form submission
                        }

                        form.submit(); // allow form submission if all good
                    },

                    invalidHandler: function(event, validator) {
                        var errors = validator.numberOfInvalids();
                        console.log(errors + " field(s) are invalid");
                        validator.errorList.forEach(function(error) {
                            console.log("Field: " + error.element.name + ", Error: " +
                                error
                                .message);
                        });
                    }
                });
            });
        });
    </script>
@endpush
