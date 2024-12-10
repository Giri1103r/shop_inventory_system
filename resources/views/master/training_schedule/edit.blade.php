@extends('admin.layouts.admin')
@section('title', 'Training Schedule Edit')
@section('pageurl', admin_url('training_schedule/list'))


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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="training_scheduleedit"
                                        action="{{ admin_url('training_schedule/edit/submit') }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                            value="{{ encryptId($training_schedule->id) }}">

                                        <div class="row">
                                            @if (Auth::user()->role == ROLE_USER)
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label for="from_date" class="form-label require">From Date</label>
                                                        <input type="text" name ="from_date" id="from_date_datepicker"
                                                            class="form-control" placeholder="From Date"
                                                            value="{{ Displaydatetimeformat($training_schedule->from_date) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label for="to_date" class="form-label require">To Date</label>
                                                        <input type="text" name ="to_date" id="to_date_datepicker"
                                                            class="form-control" placeholder="To Date"
                                                            value="{{ Displaydatetimeformat($training_schedule->to_date) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label for="venue_id" class="form-label require">Venue/Location
                                                        </label>
                                                        <select name="venue_id" id="venue_id"
                                                            class="form-control single-select" style="width: 100%">
                                                            <option value="">Select Venue/Location </option>
                                                            @foreach ($venueList as $venue)
                                                                <option @if ($training_schedule->venue_id == $venue->id) selected @endif
                                                                    value="{{ encryptId($venue->id) }}">
                                                                    {{ $venue->name_of_the_conference_hall }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="from_date" class="form-label require">From Date</label>
                                                    <input type="text" name ="from_date" id="from_date_datepicker"
                                                        class="form-control" placeholder="From Date"
                                                        value="{{ Displaydatetimeformat($training_schedule->from_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="to_date" class="form-label require">To Date</label>
                                                    <input type="text" name ="to_date" id="to_date_datepicker"
                                                        class="form-control" placeholder="To Date"
                                                        value="{{ Displaydatetimeformat($training_schedule->to_date) }}">
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="topic_id" class="form-label require">Training Topic</label>
                                                    <select name="topic_id" id="topic_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Training Topic</option>
                                                        @foreach ($topicList as $topic)
                                                            <option @if ($training_schedule->topic_id == $topic->id) selected @endif
                                                                value="{{ encryptId($topic->id) }}">
                                                                {{ $topic->topic_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="trainer_id" class="form-label require">Trainer</label>
                                                    <select name="trainer_id" id="trainer_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Trainer</option>
                                                        @foreach ($employeeList as $employee)
                                                            <option @if ($training_schedule->trainer_id == $employee->id) selected @endif
                                                                value="{{ encryptId($employee->id) }}">
                                                                {{ $employee->emp_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Unit </label>
                                                    <select name="unit_id" id="unit_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($unitList as $unit)
                                                            <option @if ($training_schedule->unit_id == $unit->id) selected @endif
                                                                value="{{ encryptId($unit->id) }}">
                                                                {{ $unit->unit_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="department_id" class="form-label require">Department
                                                    </label>
                                                    <select name="department_id" id="department_id"
                                                        class=" form-control single-select" style="width: 100%">
                                                        <option value="">Select Department </option>
                                                        @foreach ($departmentList as $department)
                                                            <option @if ($training_schedule->department_id == $department->id) selected @endif
                                                                value="{{ encryptId($department->id) }}">
                                                                {{ $department->department_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Trainees</label>
                                                    <input type="text" name="target_trainees" id="target_trainees"
                                                        class="form-control" placeholder="Target Trainees"
                                                        value="{{ $training_schedule->target_trainees }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group form-input">
                                                    <label for="venue_id" class="form-label require">Venue/Location
                                                    </label>
                                                    <select name="venue_id" id="venue_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Venue/Location </option>
                                                        @foreach ($venueList as $venue)
                                                            <option @if ($training_schedule->venue_id == $venue->id) selected @endif
                                                                value="{{ encryptId($venue->id) }}">
                                                                {{ $venue->name_of_the_conference_hall }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
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

            var initialUnitId = $('#unit_id').val();
            var preselectedDepartmentId = "{{ encryptId($training_schedule->department_id) ?? '0' }}";

            if (initialUnitId) {
                fetchDepartments(initialUnitId, preselectedDepartmentId, function() {
                    var department_id = preselectedDepartmentId;

                });
            }

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
            });


            function fetchDepartments(unit_id, preselectedDepartmentId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('department/ajax-list/') }}" + unit_id + '/' +
                            preselectedDepartmentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#department_id').empty().append(
                                '<option value="">Select Department</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedDepartmentId) ?
                                    'selected' : '';
                                $('#department_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#department_id').empty().append('<option value="">Select Department</option>');
                }
            }




        });
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            flatpickr("#from_date_datepicker", {
                dateFormat: "d-m-Y H:i",
                minDate: "today",
                enableTime: true,
                time_24hr: true,
                onChange: function(selectedDates, dateStr, instance) {
                    const toDatePicker = document.getElementById("to_date_datepicker")._flatpickr;
                    toDatePicker.set("minDate",
                        dateStr);
                    toDatePicker.setDate(dateStr,
                        false);
                }
            });

            flatpickr("#to_date_datepicker", {
                dateFormat: "d-m-Y H:i",
                minDate: "today",
                enableTime: true,
                time_24hr: true,
            });

            $('#training_scheduleedit').validate({
                rules: {
                    from_date: {
                        required: true,
                    },
                    to_date: {
                        required: true,
                    },
                    topic_id: {
                        required: true,
                    },
                    trainer_id: {
                        required: true,
                    },
                    venue_id: {
                        required: true,
                    },
                    unit_id: {
                        required: true,
                    },
                    department_id: {
                        required: true,
                    },
                    target_trainees: {
                        required: true,
                    },

                },
                messages: {
                    from_date: {
                        required: "Select a From Date.",
                    },
                    to_date: {
                        required: "Select a To Date.",
                    },
                    topic_id: {
                        required: "Select a Training Topic.",
                    },
                    trainer_id: {
                        required: "Select a Trainer.",
                    },
                    venue_id: {
                        required: "Select a Venue/Location.",
                    },
                    unit_id: {
                        required: "Select a Unit.",
                    },
                    department_id: {
                        required: "Select a Department.",
                    },
                    target_trainees: {
                        required: "Target Trainees is Required.",
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
                    form.submit(); // Submit the form when valid
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

        });
    </script>
@endpush
