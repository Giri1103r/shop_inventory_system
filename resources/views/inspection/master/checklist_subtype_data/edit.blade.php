@extends('admin.layouts.admin')
@section('title', 'Checklist Sub Type Data Edit')
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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-sub-type-data/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="editchecklist"
                                        action="{{ admin_url('inspection/master/checklist-sub-type-data/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($checklistSubTypeDataList->id) }}">

                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Checklist Type Name</label>
                                                    <select name="checklist_type_id" id="checklist_type_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Checklist Type Name</option>
                                                        @foreach ($checklistTypeList as $list)
                                                            <option @if ($checklistSubTypeDataList->checklist_type_id == $list->id) selected @endif
                                                                value="{{ encryptId($list->id) }}">
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
                                                            @php $i = 1; @endphp
                                                            @foreach ($checklistSubTypeDataNameList as $dataNameList)
                                                                <tr id="RowchecklistView0">
                                                                    <td>
                                                                        <input type="hidden"
                                                                            name="checklist[{{ $i }}][subTypeDataNameId]"
                                                                            value="{{ $dataNameList->id }}">
                                                                        <input type="text"
                                                                            name="checklist[{{ $i }}][name]"
                                                                            class="form-control"
                                                                            value="{{ $dataNameList->name }}">
                                                                    </td>
                                                                    <td>
                                                                        <textarea name="checklist[{{ $i }}][description]" class="form-control">{{ $dataNameList->description }}</textarea>
                                                                    </td>
                                                                    <td><button type="button"
                                                                            class="btn btn-sm  removeeditrow"> <i
                                                                                class="fa-solid fa-trash text-danger"></i></button>
                                                                    </td>
                                                                </tr>
                                                                @php $i++; @endphp
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
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
        $(document).ready(function() {

            var initialChecklistTypeId = $('#checklist_type_id').val();
            var preselectedSubTypeId = "{{ encryptId($checklistSubTypeDataList->checklist_sub_type_id) ?? '0' }}";

            if (initialChecklistTypeId) {
                fetchSubType(initialChecklistTypeId, preselectedSubTypeId);
            }

            $('#checklist_type_id').on('change', function() {
                var checklist_type_id = $(this).val();
                fetchSubType(checklist_type_id, preselectedSubTypeId, function() {
                    $('#checklist_sub_type_id').trigger('change');
                });
            });

            function fetchSubType(checklist_type_id, preselectedSubTypeId, callback) {
                if (checklist_type_id) {
                    $.ajax({
                        url: "{{ admin_url('inspection/master/checklist-sub-type/ajax-list/') }}" +
                            checklist_type_id + '/' +
                            preselectedSubTypeId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#checklist_sub_type_id').empty().append(
                                '<option value="">Select Checklist Sub Type Name</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedSubTypeId) ?
                                    'selected' : '';
                                $('#checklist_sub_type_id').append('<option value="' + value
                                    .id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#checklist_sub_type_id').empty().append(
                        '<option value="">Select Checklist Sub Type Name</option>');
                }
            }


        });
        $(document).ready(function() {
            let checklistIndex = $("#checklistBody tr").length + 1; // Set initial index

            $(".addchecklistBody").on("click", function() {
                let rowCount = $("#checklistBody tr").length;
                checklistIndex = rowCount + 1; // Ensure checklistIndex is updated dynamically

                const newRow = `
            <tr id="RowchecklistView${checklistIndex}">
                <td> <input type="hidden" name="checklist[${checklistIndex}][subTypeDataNameId]" value="">
                    <input type="text" name="checklist[${checklistIndex}][name]" class="form-control"></td>
                <td><textarea name="checklist[${checklistIndex}][description]" class="form-control"></textarea></td>
                <td>
                    <button type="button" class="btn btn-sm removeChecklistRow">
                    <i class="fa-solid fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>`;

                $("#checklistBody").append(newRow);
                addchecklistValidation(checklistIndex);
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
                $(`input[name="checklist[${checklistIndex}][name]"]`).rules("add", {
                    required: true,
                    minlength: 3,
                    maxlength: 100,
                    pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,
                    messages: {
                        required: "Checklist Sub-Type Data Name is Required",
                        minlength: "{{ __('common.validate_min_length') }}",
                        maxlength: "Maximum Characters should not exceed 100",
                        pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",
                    }
                });


            }
            $(function() {
                $(document).on('click', '#resetform', function() {
                    $('#editchecklist .single-select').val('');
                    $('#editchecklist .single-select').trigger('change');
                    setTimeout(function() {
                        table.draw();
                    }, 150);
                });

                $('#editchecklist').validate({
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
                            maxlength: 100,
                            pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,

                            // remote: {
                            //     url: '{{ admin_url('inspection/master/checklist-sub-type-data/unique') }}',
                            //     type: 'post',
                            //     data: {

                            //         checklist_type_id: function() {
                            //             return $('#checklist_type_id').val();
                            //         },
                            //         checklist_sub_type_id: function() {
                            //             return $('#checklist_sub_type_id').val();
                            //         },
                            //         unit_name: function() {
                            //             return $('#unit_name').val();
                            //         },
                            //     }
                            // }
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
                            maxlength: "Maximum Characters should not exceed 100",
                            pattern: "Only alphanumeric characters and -, _, ', \", () are allowed",
                            // remote: "Checklist Sub-Type Data Name should be unique"
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
                        console.log('test');
                        form.submit();

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
        $(document).on("click", ".removeeditrow", function() {
            var row = $(this).closest("tr"); // Select the closest row
            var rowId = row.find("input[name*='[subTypeDataNameId]']").val();
            var totalRows = $("#checklistBody tr").length; // Count total rows

            if (totalRows <= 1) {
                Swal.fire("Warning!", "At least one row is required!", "error");
                return; // Prevent deletion if it's the last row
            }

            if (rowId) {
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
                        $.ajax({
                            url: "{{ url('inspection/master/checklist-sub-type-data/deleteChecklist') }}/" +
                                rowId,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE', // Laravel requires _method for DELETE requests
                                id: rowId
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    row.remove(); // Remove the row on success
                                    Swal.fire("Deleted!", response.msg, "success");
                                    updateAddMoreButton();
                                } else {
                                    Swal.fire("Error!", response.msg, "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error!",
                                    "Something went wrong. Please try again later.", "error"
                                    );
                            }
                        });
                    }
                });
            } else {
                row.remove(); // Directly remove the row if it has no ID (unsaved row)
                updateAddMoreButton();
            }
        });
    </script>
@endpush
