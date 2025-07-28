@extends('admin.layouts.admin')
@section('title', 'EHS Audit Calendar')
@section('pageurl', admin_url('audit/monthly-audit/audit-plan/list'))

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
                                        href="{{ admin_url('audit/monthly-audit/audit-plan/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <form method="POST" id="monthlyAuditTask" enctype="multipart/form-data"
                                    action="{{ admin_url('audit/monthly-audit/audit-plan/add/submit') }}">
                                    @csrf

                                    <div class="basic-form">

                                        <div class="form-wrapper">

                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">EHS Audit Calendar</h4>
                                                <button class="btn btn-primary add-row mb-2 " type="button" id="add-row"
                                                    style="margin-left: 10px;  margin-right: 10px; width: 84px;">
                                                    Add
                                                </button>
                                            </div>


                                            <div class="row mt-4   form-set">




                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Auditee Name</label>
                                                        <input type="text" name="auditee_name[1]" id = "auditee_name[1]"
                                                            class="form-control" placeholder="Enter Auditee Name">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">
                                                            Unit</label>
                                                        <select name="unit_id[1]" id="unit_id[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($unitList as $unit)
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">
                                                            Task Name</label>
                                                        <select name="task_name[1]" id="task_name[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Task</option>
                                                            @foreach ($audit_task as $task)
                                                                <option value="{{ encryptId($task->id) }}">
                                                                    {{ $task->task_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">Compliance
                                                            Category
                                                        </label>
                                                        <select name="compliance_category[1]" id="compliance_category[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Category</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ encryptId($category->id) }}">
                                                                    {{ $category->compliance_category }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Reference Doc.No
                                                        </label>
                                                        <input type="text" name="reference_doc_no[1]"
                                                            id = "reference_doc_no[1]" class="form-control"
                                                            placeholder="Enter Reference Doc.No">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Frequency</label>
                                                        <select name="frequency_id[1]" id="frequency_id[1]"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select the Frequency</option>
                                                            @foreach ($frequency as $freq)
                                                                <option value="{{ encryptId($freq->id) }}">
                                                                    {{ $freq->frequency_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require"> Direct / In-Direct
                                                        </label>
                                                        <select name="direct_in_direct[1]" id="direct_in_direct[1]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Category</option>
                                                            <option value="1">Direct</option>
                                                            <option value="0">In-Direct</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label ">Status (Yes/No)</label>
                                                    <div class="mb-3 form-input">
                                                        <input type="radio" name="status[1]" value="1">
                                                        <label for="yes">YES</label>

                                                        <input type="radio" name="status[1]" value="0">
                                                        <label for="no">NO</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Points
                                                        </label>
                                                        <input type="number" name="points[1]" id = "points[1]"
                                                            class="form-control" placeholder="Enter Points">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Auditor Name</label>
                                                        <input type="text" name="auditor_name[1]"
                                                            id = "auditor_name[1]" class="form-control"
                                                            placeholder="Enter Auditor Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Audit Date</label>
                                                        <input type="text" name="audit_date[1]" id = "audit_date_1"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Audit Time</label>
                                                        <input type="text" name="audit_time[1]" id = "audit_time_1"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2 mt-2">
                                                    <div class="form-group form-input require">
                                                        <label class="form-label">Remark</label>
                                                        <textarea class="form-control" name="remark[1]" id="remark[1]"></textarea>

                                                    </div>
                                                </div>

                                                <div class="col-md-2 text-right mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>


                                                <hr>


                                            </div>


                                        </div>

                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('audit/monthly-audit/audit-plan/list') }}"></x-button-cancel>
                                        </div>

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

            flatpickr("#audit_date_1", {
                dateFormat: "d-m-Y",
                minDate: "today",
            });
            flatpickr("#audit_time_1", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultMinute: 0,
            });
        });

        $(document).ready(function() {
            $("#monthlyAuditTask").validate({
                rules: {
                    "auditee_name[1]": {
                        required: true,
                    },
                    "unit_id[1]": {
                        required: true,
                    },
                    "task_name[1]": {
                        required: true,
                    },
                    "compliance_category[1]": {
                        required: true,
                    },
                    "reference_doc_no[1]": {
                        required: true,
                    },
                    "frequency_id[1]": {
                        required: true,
                    },
                    "direct_in_direct[1]": {
                        required: true,
                    },
                    "status[1]": {
                        required: true,
                    },
                    "auditor_name[1]": {
                        required: true,
                    },
                    "audit_date[1]": {
                        required: true,
                    },
                    "audit_time[1]": {
                        required: true,
                    },
                    "points[1]": {
                        required: true,
                        number: true,
                        min: 1
                    },
                    "remark[1]": {
                        required: true,
                        minlength: 3
                    }
                },
                messages: {
                    "auditee_name[1]": "Please enter Auditee Name",
                    "unit_id[1]": "Please select a Unit",
                    "task_name[1]": "Please select a Task",
                    "compliance_category[1]": "Please select a Compliance Category",
                    "reference_doc_no[1]": "Please enter a Reference Doc No",
                    "frequency_id[1]": "Please select Frequency",
                    "direct_in_direct[1]": "Please choose Direct or In-Direct",
                    "status[1]": "Please select Yes or No",
                    "auditor_name[1]": "Please Enter the Auditor Name",
                    "audit_date[1]": "Please Select the Audit Date",
                    "audit_time[1]": "Please Select the Audit Time",
                    "points[1]": {
                        required: "Please enter Points",
                        number: "Please enter a valid number",
                        min: "Points must be at least 1"
                    },
                    "remark[1]": {
                        required: "Please enter a Remark",
                        minlength: "Remark must be at least 3 characters"
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
        });


        let form_set_count = 2;
        let formIndex = 1;
        const minFormSets = 1;
        const maxFormSets = 200;
        let serial_number = 2;
        const maxObsSets = 5;

        $(document).ready(function() {
            $(document).on('click', '#add-row', function() {
                let currentFormSets = $('.form-wrapper .form-set').length;



                if (currentFormSets >= maxFormSets) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Maximum Monthly Audit Plan Inspection  Reached',
                        text: 'You can only add up to 200 Monthly Audit Plan.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                let newSerialNumber = 'HTR-' + ('00000' + serial_number).slice(-5);

                var newFormSet = `
                        <div class="row mt-4 mb-2 form-set">

                                                <div class="d-flex justify-content-end align-items-center gap-2 m-2">



                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Auditee Name</label>
                                                        <input type="text" name="auditee_name[${form_set_count}]"
                                                            id = "auditee_name[${form_set_count}]" class="form-control"
                                                            placeholder="Enter Auditee Name">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">
                                                            Unit</label>
                                                        <select name="unit_id[${form_set_count}]" id="unit_id[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Unit</option>
                                                            @foreach ($unitList as $unit)
                                                                <option value="{{ encryptId($unit->id) }}">
                                                                    {{ $unit->unit_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">
                                                            Task Name</label>
                                                        <select name="task_name[${form_set_count}]" id="task_name[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Task</option>
                                                            @foreach ($audit_task as $task)
                                                                <option value="{{ encryptId($task->id) }}">
                                                                    {{ $task->task_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">Compliance
                                                            Category
                                                        </label>
                                                        <select name="compliance_category[${form_set_count}]"
                                                            id="compliance_category[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select Category</option>
                                                            <option value="{{ encryptId(FIRE) }}">Fire</option>
                                                            <option value="{{ encryptId(HEALTH) }}">Health</option>
                                                            <option value="{{ encryptId(SAFETY) }}">Saftey</option>
                                                            <option value="{{ encryptId(MIS) }}">MIS</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Reference Doc.No
                                                        </label>
                                                        <input type="text" name="reference_doc_no[${form_set_count}]"
                                                            id = "reference_doc_no[${form_set_count}]" class="form-control"
                                                            placeholder="Enter Reference Doc.No">
                                                    </div>
                                                </div>

                                               <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="unit_id" class="form-label require">
                                                           Frequency </label>
                                                        <select name="frequency_id[${form_set_count}]" id="frequency_id[${form_set_count}]"
                                                            class=" form-control single-select" style="width: 100%">
                                                            <option value="">Select the Frequency</option>
                                                          @foreach ($frequency as $item)
                                                                <option value="{{ encryptId($item->id) }}">
                                                                    {{ $item->frequency_name }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require"> Direct / In-Direct
                                                        </label>
                                                        <select name="direct_in_direct[${form_set_count}]"
                                                            id="direct_in_direct[${form_set_count}]" class=" form-control single-select"
                                                            style="width: 100%">
                                                            <option value="">Select Category</option>
                                                            <option value="1">Direct</option>
                                                            <option value="0">In-Direct</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <label class="form-label " for="status[${form_set_count}]">Status (Yes/No)</label>
                                                    <div class="mb-3 form-input">
                                                        <input type="radio" name="status[${form_set_count}]" 
                                                            value="1">
                                                        <label for="yes">YES</label>

                                                        <input type="radio" name="status[${form_set_count}]"
                                                            value="0">
                                                        <label for="no">NO</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Points
                                                        </label>
                                                        <input type="number" name="points[${form_set_count}]"
                                                            id = "points[${form_set_count}]" class="form-control"
                                                            placeholder="Enter Points">
                                                    </div>
                                                </div>
                                                  <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Auditor Name</label>
                                                        <input type="text" name="auditor_name[${form_set_count}]"
                                                            id = "auditor_name[${form_set_count}]" class="form-control"
                                                            placeholder="Enter Auditor Name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Audit Date</label>
                                                        <input type="text" name="audit_date[${form_set_count}]" id = "audit_date_${form_set_count}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Audit Time</label>
                                                        <input type="text" name="audit_time[${form_set_count}]" id = "audit_time_${form_set_count}"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2  mt-2">
                                                    <div class="form-group form-input require">
                                                        <label class="form-label">Remark</label>
                                                        <textarea class="form-control" name="remark[${form_set_count}]" id="remark[${form_set_count}]"></textarea>

                                                    </div>
                                                </div>
                                                   <div class="col-md-2 text-right  mt-4">
                                                    <button class="btn btn-danger remove-row" type="button"
                                                        style="margin:10px;"><i class="fa fa-trash"></i></button>

                                                </div>
 <hr>
                                            </div>

                    `;

                let newFormSetElement = $(newFormSet);

                $('.form-wrapper').append(newFormSetElement);

                flatpickr(`#audit_date_${form_set_count}`, {
                    dateFormat: "d-m-Y",
                    minDate: "today",
                });

                flatpickr(`#audit_time_${form_set_count}`, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    defaultMinute: 0,
                });

                $("input[name='auditee_name[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please add the Auditee Name',
                    }
                });

                $("select[name='unit_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Unit',
                    }
                });

                $("select[name='task_name[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Task Name',
                    }
                });

                $("select[name='compliance_category[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Compliance Category',
                    }
                });

                $("input[name='reference_doc_no[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Reference Doc No',
                    }
                });

                $("select[name='frequency_id[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Frequency',
                    }
                });

                $("select[name='direct_in_direct[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select this Field',
                    }
                });

                $("input[name='status[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Status',
                    }
                });

                $("input[name='auditor_name[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Enter the Auditor Name',
                    }
                });

                $("input[name='audit_date[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Audit Date',
                    }
                });

                $("input[name='audit_time[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please Select the Audit Time',
                    }
                });

                $("textarea[name='remark[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please add the remarks',
                    }
                });

                $("input[name='points[" + form_set_count + "]']").rules('add', {
                    required: true,
                    messages: {
                        required: 'Please add the points',
                    }
                });


                serial_number++;
                form_set_count++;
                updatePageIndices();

            });


        });

        function updatePageIndices() {
            $('.form-wrapper .form-set').each(function(index) {
                let idx = index + 1;

                $(this).find('input[name^="auditee_name"]').attr('name', 'auditee_name[' + idx + ']');
                $(this).find('select[name^="unit_id"]').attr('name', 'unit_id[' + idx + ']');
                $(this).find('select[name^="task_name"]').attr('name', 'task_name[' + idx + ']');
                $(this).find('select[name^="compliance_category"]').attr('name', 'compliance_category[' + idx +
                    ']');
                $(this).find('input[name^="reference_doc_no"]').attr('name', 'reference_doc_no[' + idx + ']');
                $(this).find('select[name^="frequency_id"]').attr('name', 'frequency_id[' + idx + ']');
                $(this).find('select[name^="direct_in_direct"]').attr('name', 'direct_in_direct[' + idx + ']');
                $(this).find('input[name^="status"]').attr('name', 'status[' + idx + ']');
                $(this).find('input[name^="points"]').attr('name', 'points[' + idx + ']');
                $(this).find('textarea[name^="remark"]').attr('name', 'remark[' + idx + ']');

                $(this).find('select').select2();
            });
        }


        $(document).on('click', '.remove-row', function() {
            let currentFormSets = $('.form-wrapper .form-set').length;

            if (currentFormSets <= minFormSets) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Minimum One CheckList Required',
                    text: 'At least Monthly Audit Plan Required.',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }
            $(this).closest('.form-set').remove();
            updatePageIndices();

        });
    </script>
@endpush
