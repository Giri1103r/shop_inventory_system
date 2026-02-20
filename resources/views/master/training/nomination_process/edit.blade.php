@extends('admin.layouts.admin')
@section('title', 'Nomination Process Edit')
@section('pageurl', admin_url('nomination_process/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Edit') }}</h4> --}}

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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('nomination_process/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="nomination_processedit"
                                        action="{{ admin_url('nomination_process/edit/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf


                                        <div class="row">
                                            <button class="btn btn-primary addmorebutton" data-block='lesson_learned_block'
                                                data-row='lesson_learned_row' type="button" id="dynamic-add-more"
                                                style="margin:10px;float:right;width: 84px;">Add</button>
                                            <table class="table_card" style="margin-top: 20px;">
                                                <thead>
                                                    <tr>
                                                        <th class="form-label required">Employee ID</th>
                                                        <th class="form-label required">Employee Name</th>
                                                        <th class="form-label required">Email ID</th>
                                                        <th class="form-label required">Department</th>
                                                        <th class="form-label required">Employee Type</th>
                                                        <th class="form-label required">Last Training Attended On (Date)
                                                        </th>
                                                        <th class="form-label required">Last Training Attended On (Topic)
                                                        </th>
                                                        <th>Delete</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="lesson_learned_block">
                                                    @php $i = 1; @endphp
                                                    @foreach ($nominationProcessList as $nominationProcess)
                                                        <tr class="lesson_learned_row">
                                                            <input type="hidden" name="employee[{{ $i }}][id]"
                                                                value="{{ $nominationProcess->id }}">

                                                            <td>
                                                                <select name="employee[{{ $i }}][emp_id]"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                    <option value="">Select Employee</option>
                                                                    @foreach ($employeeList as $employee)
                                                                        <option value="{{ $employee->id }}"
                                                                            {{ $nominationProcess->emp_id == $employee->id ? 'selected' : '' }}>
                                                                            {{ $employee->emp_id }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text"
                                                                    name="employee[{{ $i }}][emp_name]"
                                                                    class="form-control validate-input-required"
                                                                    value="{{ $nominationProcess->emp_name }}" readonly>
                                                            </td>
                                                            <td><input type="email"
                                                                    name="employee[{{ $i }}][email]"
                                                                    class="form-control validate-input-required"
                                                                    value="{{ $nominationProcess->email }}" readonly></td>
                                                            <td>
                                                                <select name="employee[{{ $i }}][department]"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                    @foreach ($departmentList as $department)
                                                                        <option value="{{ $department->id }}"
                                                                            {{ $nominationProcess->department == $department->id ? 'selected' : '' }}>
                                                                            {{ $department->department_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><input type="text"
                                                                    name="employee[{{ $i }}][employee_type]"
                                                                    class="form-control validate-input-required"
                                                                    value="{{ $nominationProcess->employee_type }}"
                                                                    readonly></td>
                                                            <td><input type="date"
                                                                    name="employee[{{ $i }}][last_training_attended_on]"
                                                                    class="form-control validate-input-required"
                                                                    value="{{ $nominationProcess->last_training_attended_on }}">
                                                            </td>
                                                            <td>
                                                                <select name="employee[{{ $i }}][topic_id]"
                                                                    class="form-control single-select validate-select-required"
                                                                    style="width: 100%">
                                                                    <option value="">Select Topic</option>
                                                                    @foreach ($topicList as $topic)
                                                                        <option value="{{ encryptId($topic->id) }}"
                                                                            {{ $nominationProcess->topic_id == $topic->id ? 'selected' : '' }}>
                                                                            {{ $topic->topic_name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><button class="btn btn-danger removerow" type="button"
                                                                    style="margin:10px;"><i
                                                                        class="fa fa-trash"></i></button></td>
                                                        </tr>
                                                        @php $i++; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="submit-button" style="text-align: right;">
                                            <x-button-submit class="submit"></x-button-submit>
                                            <x-button-reset class=""></x-button-reset>
                                            <x-button-cancel
                                                href="{{ admin_url('nomination_process/list') }}"></x-button-cancel>
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
        flatpickr("#last_training_attended_on", {
            dateFormat: "d-m-Y",
        });

        $(document).ready(function() {
            $(document).on("change", "[id^=\"emp_id_\"]", function() {
                var emp_id = $(this).val();
                var currentRow = $(this).closest("tr");
                var departmentDropdown = currentRow.find(
                    'select[name*="[department_id]"]');

                if (emp_id) {
                    $.ajax({
                        url: "{{ url('nomination_process/fetchEmployeeDetails') }}/" + emp_id,
                        type: "GET",
                        success: function(data) {
                            if (data.employee) {
                                // Populate employee details in the current row
                                currentRow.find('input[name*="[emp_name]"]').val(data.employee
                                    .emp_name);
                                currentRow.find('input[name*="[email]"]').val(data.employee
                                    .email);
                                currentRow.find('input[name*="[employee_type]"]').val(data
                                    .employee.employee_status);

                                // Populate the department dropdown
                                departmentDropdown.empty(); // Clear existing options
                                departmentDropdown.append(
                                    '<option value="">Select Department</option>'
                                ); // Default option

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
                        },
                    });
                } else {
                    // Clear the current row inputs if no employee is selected
                    currentRow.find('input[name*="[emp_name]"]').val("");
                    currentRow.find('input[name*="[email]"]').val("");
                    currentRow.find('input[name*="[employee_type]"]').val("");
                    departmentDropdown.empty();
                    departmentDropdown.append('<option value="">Select Department</option>');
                }
            });

            // Event listener for dynamic row addition
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

                // Clear inputs and reset classes
                newRow.find(".form-input").removeClass("selecterror");
                newRow.find(".form-control").removeClass("is-invalid");
                newRow.find(".invalid-feedback").remove();

                var newIndex = rowCount + 1; // Update index for the new row

                // Reset all input values and attributes for the new row
                newRow.find("input").val("");
                newRow.find("select").val("");

                newRow.find("input, select").each(function() {
                    var oldName = $(this).attr("name");
                    var oldId = $(this).attr("id");

                    if (oldName) {
                        var newName = oldName.replace(/\[\d+\]/, "[" + newIndex + "]");
                        $(this).attr("name", newName);
                    }

                    if (oldId) {
                        var newId = oldId.replace(/\d+$/, newIndex);
                        $(this).attr("id", newId);
                    }
                });

                // Remove the existing select2 container to prevent duplicate initialization
                newRow.find(".select2-container").remove();

                // Append the new row to the table
                $("#lesson_learned_block").append(newRow);

                // Reinitialize select2 for all dropdowns
                $(".single-select").select2();

                // Disable Add button if row count reaches the maximum limit
                $("#dynamic-add-more").attr("disabled", rowCount + 1 >= 10);
            });


            // Event listener for dynamic row removal
            $(document).on('click', '.removerow', function() {
                var rowCount = $("#lesson_learned_block .lesson_learned_row").length;
                if (rowCount > 1) {
                    $(this).closest(".lesson_learned_row").remove();

                    // Re-indexing remaining rows
                    $("#lesson_learned_block .lesson_learned_row").each(function(index) {
                        var newIndex = index + 1;
                        $(this).find("input, select").each(function() {
                            var oldName = $(this).attr("name");
                            var oldId = $(this).attr("id");

                            if (oldName) {
                                var newName = oldName.replace(/\[\d+\]/, '[' + newIndex +
                                    ']');
                                $(this).attr("name", newName);
                            }

                            if (oldId) {
                                var newId = oldId.replace(/\d+$/, newIndex);
                                $(this).attr("id", newId);
                            }
                        });

                        // Reinitialize select2 for each row
                        $(this).find(".select2-container").remove();
                        $(this).find(".select2").select2();
                    });

                    $('#dynamic-add-more').attr("disabled", rowCount - 1 >= 10);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Sorry!',
                        text: 'At least one record is required.',
                    });
                }
            });

            // AJAX call when emp_id is selected


            // Form validation rules
            $('#nomination_processadd').validate({
                rules: {
                    'emp_id[]': {
                        required: true
                    },
                    'emp_name[]': {
                        required: true
                    },
                    'email[]': {
                        required: true,
                        email: true
                    },
                    'department_id[]': {
                        required: true
                    },
                    'employee_type[]': {
                        required: true
                    },
                    'last_training_attended_on[]': {
                        required: true
                    },
                    'topic_id[]': {
                        required: true
                    }
                },
                messages: {
                    'emp_id[]': {
                        required: "Select an Employee ID."
                    },
                    'emp_name[]': {
                        required: "Employee Name is required."
                    },
                    'email[]': {
                        required: "Email ID is required."
                    },
                    'department_id[]': {
                        required: "Department is required."
                    },
                    'employee_type[]': {
                        required: "Employee Type is required."
                    },
                    'last_training_attended_on[]': {
                        required: "Last training attended on is required."
                    },
                    'topic_id[]': {
                        required: "Training Topic is required."
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
            });
        });
    </script>
@endpush
