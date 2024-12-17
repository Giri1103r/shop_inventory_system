@extends('admin.layouts.admin')
@section('title', 'calendar')
@section('pageurl', admin_url('company/list'))


@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="d-flex justify-content-end p-2">
                <x-button-filter dataId="" class="search me-1" href=""></x-button-filter>
            </div>
            <div id="search" class="collapse">
                <form action="" id="formsearch">
                    <div class="card-body">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3 mb-3 form-input">
                                    <label for="topic_id" class="form-label ">Training Topic</label>
                                    <select name="topic_id" id="topic_id" class=" form-control single-select"
                                        style="width: 100%">
                                        <option value="">Select Training Topic</option>
                                        @foreach ($topicList as $topic)
                                            <option value="{{ encryptId($topic->id) }}">
                                                {{ $topic->topic_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 form-input">
                                    <label for="trainer_id" class="form-label ">Trainer</label>
                                    <select name="trainer_id" id="trainer_id" class=" form-control single-select"
                                        style="width: 100%">
                                        <option value="">Select Trainer</option>
                                        @foreach ($employeeList as $employee)
                                            <option value="{{ encryptId($employee->id) }}">
                                                {{ $employee->emp_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end gap-2">
                                    <x-button-search class="me-2"></x-button-search>
                                    <x-button-reset class="ms-1"></x-button-reset>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <hr>
            </div>

            <div id="calendar"></div>
        </div>
    </div>


@stop


@push('script')
    <!--calender-->
    {{-- <script src="{{ public_plugins('fullcalendar/locales-all.min.js') }}"></script> --}}
    <script src="{{ public_plugins('fullcalendar/main.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay',
                },
                editable: false,
                events: {
                    url: '{{ url('training_calendar/fetch/schedule') }}',
                    method: 'GET',
                    extraParams: function() {
                        return {
                            topic_id: $('#topic_id').val(),
                            trainer_id: $('#trainer_id').val(),
                        };
                    },
                    failure: function() {
                        alert('Failed to fetch events!');
                    }
                },
                eventDidMount: function(info) {
                    const tooltip = document.createElement('div');
                    tooltip.classList.add('custom-tooltip');
                    tooltip.innerHTML = `
            <b>Trainer:</b> ${info.event.extendedProps.trainer_name || 'N/A'}<br>
            <b>Venue:</b> ${info.event.extendedProps.venue_name || 'N/A'}`;
                    info.el.style.position = 'relative';
                    info.el.appendChild(tooltip);

                    info.el.addEventListener('mouseenter', function() {
                        tooltip.style.display = 'block';
                    });
                    info.el.addEventListener('mouseleave', function() {
                        tooltip.style.display = 'none';
                    });
                },
                eventClick: function(info) {
                    const editUrl = `{{ url('training_schedule/edit/') }}/${info.event.id}`;
                    window.location.href = editUrl;
                },

            });

            calendar.render();

            $(document).on('click', '#searchform', function() {
                calendar.refetchEvents();
            });

            $(document).on('click', '#resetform', function() {
                $('#formsearch .single-select').val('').trigger('change');
                calendar.refetchEvents();
            });
        });
    </script>
@endpush
