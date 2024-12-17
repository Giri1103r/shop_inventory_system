@extends('admin.layouts.admin')
@section('title', 'Nomination Process')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')
  
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Training Schedule Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">From Date</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($training_schedule->from_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">To Date</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($training_schedule->to_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Topic</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->topic_name) ? $training_schedule->topic_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Trainer </label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->emp_name) ? $training_schedule->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->unit_name) ? $training_schedule->unit_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->department_name) ? $training_schedule->department_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Target Trainees</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->target_trainees) ? $training_schedule->target_trainees : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Venue/Location</label>
                                        <div class="view_data">
                                            {{ isset($training_schedule->name_of_the_conference_hall) ? $training_schedule->name_of_the_conference_hall : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Training Man Hours</label>
                                        <div class="view_data">
                                            {{ $training_hours ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($training_schedule->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($training_schedule->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($training_schedule->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card-body">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Nomination Process</h4>
                                    </div>
                                </div>
                               
                                {{-- <div style="cursor: pointer  !important;padding-left: 88% !important"> --}}
                                <div class="d-flex justify-content-end p-2">
                                    <x-button-import href="{{ admin_url('nomination_process/import/' . encryptId($training_schedule->id)) }}"></x-button-import>

                                </div>

                                <div class="basic-form">
                                    <form method="POST" id="nomination_processadd"
                                        action="{{ admin_url('nomination_process/add/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div clase="nominationaddmorebutton" style="padding-left: 82% !important; margin-top: -59px;">
                                            <button class="btn btn-primary addmorebutton"
                                                data-block='lesson_learned_block' data-row='lesson_learned_row'
                                                type="button" id="dynamic-add-more"
                                                style="margin:10px;width: 84px;">Add</button>

                                        </div>
                                        <div class="row">
                                           
                                            <table class="table_card" style="margin-top: 20px;">
                                                <thead>

                                                    <th class="form-label required">Employee ID</th>
                                                    <th class="form-label required">Employee Name</th>
                                                    <th class="form-label required">Email ID</th>
                                                    <th class="form-label required">Department</th>
                                                    <th class="form-label required">Employee Type</th>
                                                    <th class="form-label required">Last training attended on (Date)</th>
                                                    <th class="form-label required">Last Training Attended on (Topic)</th>
                                                    <th>Delete</th>
                                                </thead>
                                                <input type="hidden" class="form-control" name="training_schedule_id"
                                                    id="training_schedule_id"
                                                    value="{{ encryptId($training_schedule->id) }}">

                                                <tbody id="lesson_learned_block">
                                                    @if ($nominationProcessList->isEmpty())
                                                        <tr class="lesson_learned_row">
                                                            <td>
                                                                <select name="employee[1][emp_id]" id="emp_id_1"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                    <option value="">Select Employee</option>
                                                                    @foreach ($employeeList as $emp)
                                                                        <option value="{{ $emp->id }}">
                                                                            {{ $emp->emp_id }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text"
                                                                    class="form-control validate-input-required"
                                                                    name="employee[1][emp_name]" id="emp_name_1" readonly>
                                                            </td>
                                                            <td><input type="email"
                                                                    class="form-control validate-input-required"
                                                                    name="employee[1][email]" id="email_1" readonly>
                                                            </td>
                                                            <td>
                                                                <select name="employee[1][department_id]"
                                                                    id="department_1"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                </select>
                                                            </td>
                                                            <td><input type="text"
                                                                    class="form-control validate-input-required"
                                                                    name="employee[1][employee_type]" id="employee_type_1"
                                                                    readonly></td>
                                                            <td><input type="text"
                                                                    class="form-control validate-input-required"
                                                                    name="employee[1][last_training_attended_on]"
                                                                    id="last_training_attended_on_1"></td>
                                                            <td>
                                                                <select name="employee[1][topic_id]" id="topic_id_1"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                    <option value="">Select Topic</option>
                                                                    @foreach ($topicList as $topic)
                                                                        <option value="{{ encryptId($topic->id) }}">
                                                                            {{ $topic->topic_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><button class="btn btn-danger removerowdata"
                                                                    type="button" style="margin:10px;"><i
                                                                        class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @php $i = 1; @endphp
                                                        @foreach ($nominationProcessList as $nominationProcess)
                                                            <tr class="lesson_learned_row">
                                                                <input type="hidden"
                                                                    id="employee[{{ $i }}][id]"
                                                                    name="employee[{{ $i }}][id]"
                                                                    value="{{ $nominationProcess->id }}">

                                                                <td>
                                                                    <select name="employee[{{ $i }}][emp_id]"
                                                                        class="form-control single-select validate-select-required"
                                                                        id="emp_id_{{ $i }}"
                                                                        style="width: 100%">
                                                                        <option value="">Select Employee</option>
                                                                        @foreach ($employeeList as $employee)
                                                                            <option value="{{ $employee->id }}"
                                                                                {{ $nominationProcess->employee_id == $employee->id ? 'selected' : '' }}>
                                                                                {{ $employee->emp_id }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text"
                                                                        name="employee[{{ $i }}][emp_name]"
                                                                        class="form-control validate-input-required"
                                                                        id="emp_id_{{ $i }}"
                                                                        value="{{ $nominationProcess->emp_name }}"
                                                                        readonly>
                                                                </td>
                                                                <td><input type="email"
                                                                        name="employee[{{ $i }}][email]"
                                                                        class="form-control validate-input-required"
                                                                        id="emp_id_{{ $i }}"
                                                                        value="{{ $nominationProcess->email }}" readonly>
                                                                </td>
                                                                <td>
                                                                    <select
                                                                        name="employee[{{ $i }}][department_id]"
                                                                        class="form-control single-select validate-select-required"
                                                                        style="width: 100%"
                                                                        id="department_{{ $i }}">
                                                                        <option value="">Select Department</option>
                                                                        @foreach ($departmentList as $department)
                                                                            <option value="{{ $department->id }}"
                                                                                {{ $nominationProcess->department_id == $department->id ? 'selected' : '' }}>
                                                                                {{ $department->department_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><input type="text"
                                                                        name="employee[{{ $i }}][employee_type]"
                                                                        class="form-control validate-input-required"
                                                                        id="employee_type_{{ $i }}"
                                                                        value="{{ $nominationProcess->employee_type }}"
                                                                        readonly></td>
                                                                <td><input type="text"
                                                                        name="employee[{{ $i }}][last_training_attended_on]"
                                                                        class="form-control validate-input-required"
                                                                        id="last_training_attended_on_{{ $i }}"
                                                                        value="{{ $nominationProcess->last_training_attended_on }}">
                                                                </td>
                                                                <td>
                                                                    <select name="employee[{{ $i }}][topic_id]"
                                                                        class="form-control single-select validate-select-required"
                                                                        style="width: 100%"
                                                                        id="topic_id_{{ $i }}">
                                                                        <option value="">Select Topic</option>
                                                                        @foreach ($topicList as $topic)
                                                                            <option value="{{ encryptId($topic->id) }}"
                                                                                {{ $nominationProcess->topic_id == $topic->id ? 'selected' : '' }}>
                                                                                {{ $topic->topic_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td><button class="btn btn-danger removerow"
                                                                        type="button" style="margin:10px;"><i
                                                                            class="fa fa-trash"></i></button></td>
                                                            </tr>
                                                            @php $i++; @endphp
                                                        @endforeach
                                                    @endif
                                                </tbody>

                                            </table>
                                        </div>
                                        <hr>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('training_schedule/list') }}"></x-button-cancel>
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
        $(document).ready(function() {
            flatpickr("[id^='last_training_attended_on_']", {
                dateFormat: "d-m-Y",

            });
            $(document).on("change", "[name^='employee'][name$='[emp_id]']", function() {
                var empIds = [];
                var isDuplicate = false;
                var currentRow = $(this).closest("tr");
                var emp_id = $(this).val();

                $("[name^='employee'][name$='[emp_id]']").each(function() {
                    var otherEmpId = $(this).val();
                    if (otherEmpId) {
                        if (empIds.includes(otherEmpId)) {
                            isDuplicate = true;
                        }
                        empIds.push(otherEmpId);
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: "error",
                        title: "Duplicate Employee ID!",
                        text: "Each Employee ID must be unique.",
                    });
                    $(this).val("");
                    return;
                }

                if (emp_id) {
                    $.ajax({
                        url: "{{ url('nomination_process/fetchEmployeeDetails') }}/" + emp_id,
                        type: "GET",
                        success: function(data) {
                            if (data.employee) {
                                currentRow.find('input[name*="[emp_name]"]').val(data.employee
                                    .emp_name);
                                currentRow.find('input[name*="[email]"]').val(data.employee
                                    .email);
                                currentRow.find('input[name*="[employee_type]"]').val(data
                                    .employee.employee_status);

                                var departmentDropdown = currentRow.find(
                                    'select[name*="[department_id]"]');
                                departmentDropdown.empty().append(
                                    '<option value="">Select Department</option>');
                                data.departments.forEach(function(department) {
                                    var selected = data.employee.department ==
                                        department.id ? "selected" : "";
                                    departmentDropdown.append(
                                        `<option value="${department.id}" ${selected}>${department.department_name}</option>`
                                    );
                                });
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Error",
                                    text: "Employee data could not be fetched.",
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: "An error occurred while fetching employee details.",
                            });
                        }
                    });
                } else {
                    currentRow.find('input[name*="[emp_name]"]').val("");
                    currentRow.find('input[name*="[email]"]').val("");
                    currentRow.find('input[name*="[employee_type]"]').val("");
                    currentRow.find('select[name*="[department_id]"]').empty().append(
                        '<option value="">Select Department</option>');
                }
            });

            $("#dynamic-add-more").on("click", function() {
                var rowCount = $("#lesson_learned_block .lesson_learned_row").length;

                if (rowCount >= 10) {
                    Swal.fire({
                        icon: "error",
                        title: "Sorry!",
                        text: "Maximum 10 records only.",
                    });
                    return;
                }

                var newRow = $(".lesson_learned_row").first().clone();

                newRow.find("input, select").each(function() {
                    var oldName = $(this).attr("name");
                    var oldId = $(this).attr("id");

                    if (oldName) {
                        var newName = oldName.replace(/\[\d+\]/, "[" + (rowCount + 1) + "]");
                        $(this).attr("name", newName);
                    }

                    if (oldId) {
                        var newId = oldId.replace(/\d+$/, rowCount + 1);
                        $(this).attr("id", newId);
                    }
                });

                newRow.find("input").val("");
                newRow.find("select").val("");

                newRow.find(".select2-container").remove();

                newRow.find("[id^='last_training_attended_on_']").each(function() {
                    if (this._flatpickr) {
                        this._flatpickr.destroy(); // Destroy existing flatpickr instance
                    }
                });
                $("#lesson_learned_block").append(newRow);

                $("[id^='last_training_attended_on_']").each(function() {
                    flatpickr(this, {
                        dateFormat: "d-m-Y",
                    });
                });

                $(".single-select").select2();
            });

            $(document).on('click', '.removerow', function() {
                var row = $(this).closest(
                    ".lesson_learned_row");
                var rowId = row.find("input[name*='[id]']")
                    .val();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you want to delete this record?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('nomination_process/delete') }}/" +
                                rowId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}',
                                id: rowId
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    row.remove();

                                    Swal.fire(
                                        'Deleted!',
                                        response.msg,
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.msg,
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong. Please try again later.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // $('#lesson_learned_block').on('change', '[name^="employee"][name$="[emp_id]"]', function() {
            //     var rowId = $(this).attr('name').match(/\d+/)[
            //         0]; // Extract row number from name attribute
            //     var empIdField = `[name="employee[${rowId}][emp_id]"]`;

            //     $(empIdField).rules('add', {
            //         required: true,
            //         remote: {
            //             url: '{{ admin_url('training_schedule/nomination_process/unique') }}',
            //             type: 'post',
            //             data: {
            //                 _token: '{{ csrf_token() }}',
            //                 emp_id: function() {
            //                     return $(empIdField)
            //                         .val(); // Fetch Employee ID dynamically
            //                 },
            //                 training_schedule_id: function() {
            //                     return $('#training_schedule_id').val();
            //                 }
            //             }
            //         },
            //         messages: {
            //             required: "Employee ID is required.",
            //             remote: "This Employee ID is already nominated for this training."
            //         }
            //     });
            // });

            $('#nomination_processadd').validate({

                rules: {
                    'employee[1][emp_id]': {
                        required: true,
                    },
                    'employee[1][emp_name]': {
                        required: true
                    },
                    'employee[1][email]': {
                        required: true,
                        email: true
                    },
                    'employee[1][department_id]': {
                        required: true
                    },
                    'employee[1][employee_type]': {
                        required: true
                    },
                    'employee[1][last_training_attended_on]': {
                        required: true
                    },
                    'employee[1][topic_id]': {
                        required: true
                    }
                },
                messages: {
                    'employee[1][emp_id]': {
                        required: "Select an Employee ID."
                    },
                    'employee[1][emp_name]': {
                        required: "Employee Name is required."
                    },
                    'employee[1][email]': {
                        required: "Email ID is required.",
                        email: "Enter a valid Email ID."
                    },
                    'employee[1][department_id]': {
                        required: "Department is required."
                    },
                    'employee[1][employee_type]': {
                        required: "Employee Type is required."
                    },
                    'employee[1][last_training_attended_on]': {
                        required: "Last training attended on is required."
                    },
                    'employee[1][topic_id]': {
                        required: "Training Topic is required."
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    if (element.hasClass('single-select')) {
                        element.next('.select2-container').append(
                            error);
                    } else {
                        element.closest('td').append(error);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
            $(document).on('click', '.removerowdata', function() {
                $(this).closest('tr').remove();
            });

            $('#lesson_learned_block').on('change', 'input, select', function() {
                $(this).valid();
            });
        });
    </script>
@endpush
