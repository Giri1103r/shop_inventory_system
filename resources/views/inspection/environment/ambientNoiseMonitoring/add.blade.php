@extends('admin.layouts.admin')
@section('title', 'Ambient Noise Monitoring Add')
@section('pageurl', admin_url('environment/ambient-noise/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('environment/ambient-noise/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form">
                                    <form method="POST" id="addchecklist"
                                        action="{{ admin_url('environment/ambient-noise/add/submit') }}">
                                        @csrf
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="card-header-inner d-flex justify-content-between">
                                                    <a class="text-white card-link">Ambient Noise Monitoring </a>
                                                    <div class="btn btn-warning btn-sm addMoreInjuryDetails">Add</div>
                                                </div>
                                            </div>

                                            <div class="injury-details-templat">
                                                <div class="row injury-append" style="margin-top: 20px;">

                                                    <div class="col-md-4 form-input" id="injuryPersonTextContainer_0">
                                                        <label class="form-label require">SR NO</label>
                                                        <input type="text" class="form-control require"
                                                            name="monitoring[0][ambient_noise_id]" alt="0"
                                                            id="ambient_noise_id_0" readonly>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label require">Location</label>
                                                        <select class="form-control require single-select"
                                                            name="monitoring[0][location_id]" alt="0"
                                                            style="width: 100%" id="location_id_0">
                                                            <option value="">Select Location</option>
                                                            @foreach ($locationList as $list)
                                                                <option value="{{ encryptId($list->id) }}">
                                                                    {{ $list->location_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label require">Unit</label>
                                                        <select class="form-control require single-select"
                                                            name="monitoring[0][unit_id]" alt="0"
                                                            style="width: 100%" id="unit_id_0">
                                                            <option value="">Select Unit</option>
                                                           
                                                        </select>
                                                    </div>
                                                   
                                                
                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label require">NOISE LEVEL (dBA)</label>
                                                        <input type="text" alt="0"
                                                            name="monitoring[0][noise_level_dba]"
                                                            class="form-control" id="noise_level_dba_0">
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label require">Date of Monitoring</label>
                                                        <input type="text" alt="0"
                                                            name="monitoring[0][date_of_monitoring]"
                                                            class="form-control" id="date_of_monitoring_0">
                                                    </div>

                                                    <div class="col-md-2 text-right">
                                                        <button type="button"
                                                            class="btn btn-danger btn-sm removeInjuryDetails"
                                                            style="margin-top: 35px;">Remove</button>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('environment/ambient-noise/list') }}"></x-button-cancel>
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
                <td><input type="text" name="checklist[${checklistIndex}][name]" class="form-control"></td>
                <td><textarea name="checklist[${checklistIndex}][description]" class="form-control"></textarea></td>
                <td>
                    <button type="button" class="btn btn-sm  removeChecklistRow">
                    <i class="fa-solid fa-trash text-danger"></i>
                    </button>
                </td>
            </tr>`;

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
                            maxlength: 100,
                            pattern: /^[a-zA-Z0-9\s\-_'"()]*$/,

                            // remote: {
                            //     url: '{{ admin_url('environment/ambient-noise/unique') }}',
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
    </script>
@endpush
