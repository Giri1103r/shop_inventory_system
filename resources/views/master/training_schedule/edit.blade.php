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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
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

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="from_date" class="form-label require">From Date</label>
                                                    <input type="text" name ="from_date" id="from_date_datepicker"
                                                        class="form-control" placeholder="From Date"
                                                        value="{{ Displaydateformat($training_schedule->from_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="to_date" class="form-label require">To Date</label>
                                                    <input type="text" name ="to_date" id="to_date_datepicker"
                                                        class="form-control" placeholder="To Date"
                                                        value="{{ Displaydateformat($training_schedule->to_date) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="start_time" class="form-label require">Start Time</label>
                                                    <input type="text" name ="start_time" id="start_timepicker"
                                                        class="form-control"
                                                        value="{{ Displaytimeformat($training_schedule->start_time) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="end_time" class="form-label require">End Time</label>
                                                    <input type="text" name ="end_time" id="end_timepicker"
                                                        class="form-control"
                                                        value="{{ Displaytimeformat($training_schedule->end_time) }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
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
                                            <div class="col-md-4 mb-3">
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

                                            <div class="col-md-4 mb-3">
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
                                            <div class="col-md-4 mb-3">
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
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">Target Trainees</label>
                                                    <input type="text" name="target_trainees" id="target_trainees"
                                                        class="form-control" placeholder="Target Trainees"
                                                        value="{{ $training_schedule->target_trainees }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group form-input">
                                                    <label for="venue_id" class="form-label require">Venue/Location
                                                    </label>
                                                    <select name="venue_id" id="venue_id"
                                                        class="form-control single-select" style="width: 100%">
                                                        <option value="">Select Venue/Location </option>

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
        // $(document).ready(function() {
        //     function checkSelections() {
        //         var fromDate = $('#from_date_datepicker').val();
        //         var toDate = $('#to_date_datepicker').val();
        //         var topicId = $('#topic_id').val();
        //         var trainerId = $('#trainer_id').val();
        //         var unitId = $('#unit_id').val();
        //         var departmentId = $('#department_id').val();
        //         var venueId = $('#venue_id').val();
        //         var id = $('#id').val(); // Corrected variable name to `id`

        //         if (fromDate && toDate && topicId && trainerId && unitId && departmentId && venueId) {
        //             $.ajax({
        //                 url: "{{ url('training_schedule/topic/ajax-list') }}",
        //                 type: 'GET',
        //                 dataType: 'json',
        //                 data: {
        //                     from_date: fromDate,
        //                     to_date: toDate,
        //                     topicId: topicId,
        //                     trainerId: trainerId,
        //                     unitId: unitId,
        //                     departmentId: departmentId,
        //                     venueId: venueId,
        //                     id: id // Corrected variable name to `id`
        //                 },
        //                 success: function(response) {
        //                     $('.text-danger').remove();
        //                     var conflicts = response.conflicts;

        //                     if (Object.keys(conflicts).length > 0) {
        //                         for (var key in conflicts) {
        //                             if (conflicts.hasOwnProperty(key)) {
        //                                 $('#' + key).closest('.form-group').append(
        //                                     '<div><span class="text-danger">' + conflicts[key] +
        //                                     '</span></div>'
        //                                 );
        //                             }
        //                         }
        //                         $('#training_scheduleadd').data('conflict', true);
        //                     } else {
        //                         $('#training_scheduleadd').data('conflict', false);
        //                     }
        //                 },
        //                 error: function(xhr) {
        //                     alert('Error fetching data. Please try again.');
        //                 }
        //             });
        //         } else {
        //             $('.text-danger').remove();
        //             $('#training_scheduleadd').data('conflict', false);
        //         }
        //     }

        //     $('#from_date_datepicker').on('change', checkSelections);
        //     $('#to_date_datepicker').on('change', checkSelections);
        //     $('#topic_id').on('change', checkSelections);
        //     $('#trainer_id').on('change', checkSelections);
        //     $('#unit_id').on('change', checkSelections);
        //     $('#department_id').on('change', checkSelections);
        //     $('#venue_id').on('change', checkSelections);
        // });

        $(document).ready(function() {

            var initialUnitId = $('#unit_id').val();
            var preselectedDepartmentId = "{{ encryptId($training_schedule->department_id) ?? '0' }}";
            var preselectedVenueId = "{{ encryptId($training_schedule->venue_id) ?? '0' }}";

            if (initialUnitId) {
                fetchDepartments(initialUnitId, preselectedDepartmentId, function() {
                    var department_id = preselectedDepartmentId;

                });
                fetchVenues(initialUnitId, preselectedVenueId, function() {
                    var venue_id = preselectedVenueId;

                });
            }

            $('#unit_id').on('change', function() {
                var unit_id = $(this).val();
                fetchDepartments(unit_id, preselectedDepartmentId, function() {
                    $('#department_id').trigger('change');
                });
                fetchVenues(unit_id, preselectedVenueId, function() {
                    $('venue_id').trigger('change');
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

            function fetchVenues(unit_id, preselectedVenueId, callback) {
                if (unit_id) {
                    $.ajax({
                        url: "{{ admin_url('venue/ajax-list/') }}" + unit_id + '/' +
                            preselectedVenueId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#venue_id').empty().append(
                                '<option value="">Select Venue/Location</option>');
                            $.each(data, function(key, value) {
                                var selected = (value.id == preselectedVenueId) ?
                                    'selected' : '';
                                $('#venue_id').append('<option value="' + value.id + '" ' +
                                    selected + '>' + value.name + '</option>');
                            });
                            if (callback) callback();
                        }
                    });
                } else {
                    $('#venue_id').empty().append('<option value="">Select Venue/Location</option>');
                }
            }




        });
        $(document).ready(function() {
            $('#resetform').on('click', function(e) {
                e.preventDefault();
                location.reload();
            });

            const toDatePicker = flatpickr("#to_date_datepicker", {
                dateFormat: "d-m-Y",
                minDate: "today",
            });

            flatpickr("#from_date_datepicker", {
                dateFormat: "d-m-Y",
                minDate: "today",
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const fromDate = selectedDates[0];
                        const toDate = new Date(fromDate);

                        if (toDatePicker) {
                            toDatePicker.set("minDate", dateStr);
                            toDatePicker.setDate(toDate, false);
                        }
                    }
                },
            });
            const startTimePicker = flatpickr("#start_timepicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: 9, // Default 9:00 AM
                defaultMinute: 0,
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        const startTime = selectedDates[0];
                        const endTimePicker = flatpickr("#end_timepicker", {
                            enableTime: true,
                            noCalendar: true,
                            dateFormat: "H:i",
                            time_24hr: true,
                            defaultHour: 18, // Default 6:00 PM
                            defaultMinute: 0,
                            minTime: dateStr, // Set minimum time to the selected start time
                        });
                    }
                },
            });

            // Initialize End Time Picker
            flatpickr("#end_timepicker", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultHour: 18, // Default 6:00 PM
                defaultMinute: 0,
            });

            $('#training_scheduleedit').validate({
                rules: {
                    from_date: {
                        required: true,
                    },
                    to_date: {
                        required: true,
                    },
                    start_time: {
                        required: true
                    },
                    end_time: {
                        required: true
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
                        digits: true,
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
                    start_time: {
                        required: "Enter Start Time"
                    },
                    end_time: {
                        required: "Enter End Time"
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
                        digits: "Please enter only numeric values for Target Trainees."
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
