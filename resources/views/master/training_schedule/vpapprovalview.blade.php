@extends('admin.layouts.admin')
@section('title', 'Training Schedule Show')
@section('pageurl', admin_url('training_schedule/list'))


@section('content')

    <style>
        .table_card {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        .table_card th,
        .table_card td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .table_card th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        .table_card tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table_card tr:hover {
            background-color: #f1f1f1;
        }

        .table_card td {
            text-align: center;
        }

        .table-container {
            padding: 20px;
        }
    </style>

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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back href="{{ admin_url('training_schedule/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="tab-content">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Training Schedule</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">From Date</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($training_schedule->from_date) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">To Date</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($training_schedule->to_date) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Start Time</label>
                                                <div class="view_data">
                                                    {{ Displaytimeformat($training_schedule->start_time) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">End Time</label>
                                                <div class="view_data">
                                                    {{ Displaytimeformat($training_schedule->end_time) }}
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
                                                    {{ $training_schedule->training_man_hours ?? '-' }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Training Status</label>
                                                @if (
                                                    $training_schedule->training_status == 1 ||
                                                        $training_schedule->training_status == 2 ||
                                                        $training_schedule->training_status == 4 ||
                                                        $training_schedule->training_status == 5)
                                                    <div class="view_data"
                                                        style="background-color: #FFA500; width: 40%;padding: 1px 9px; border: 1px solid #FFA500; color: black;">
                                                        Training Pending
                                                    </div> <!-- Orange -->
                                                @elseif ($training_schedule->training_status == 8)
                                                    <div class="view_data"
                                                        style="background-color: #008000; width: 40%;padding: 1px 9px; border: 1px solid #008000; color: rgb(246, 244, 244);">
                                                        Training Completed
                                                    </div> <!-- Green -->
                                                @elseif ($training_schedule->training_status == 6 || $training_schedule->training_status == 7)
                                                    <div class="view_data"
                                                        style="background-color: #FFFF00; width: 40%;padding: 1px 9px; border: 1px solid #FFFF00; color: black;">
                                                        Training in Progress
                                                    </div> <!-- Yellow -->
                                                @elseif ($training_schedule->training_status == 3)
                                                    <div class="view_data"
                                                        style="background-color: #FFFF00; width: 40%;padding: 1px 9px; border: 1px solid #FFFF00; color: black;">
                                                        Training Rejected
                                                    </div> <!-- red -->
                                                @endif
                                            </div>

                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($training_schedule->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label
                                                    class="form-label view_label">{{ __('common.created_date') }}</label>
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
                                        @if (isset($rejectedlog) && $rejectedlog->isNotEmpty())

                                            <div class="row">
                                                <div class="card-header-inner">
                                                    <h4 class="text-white">Training Rejection Log List</h4>
                                                </div>
                                            </div>
                                            <div class="basic-form">
                                                <div class="row">
                                                    <table class="table_card" style="margin-top: 20px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="form-label ">Date</th>
                                                                <th class="form-label ">Remark</th>

                                                            </tr>
                                                        </thead>

                                                        <tbody id="lesson_learned_block">
                                                            @foreach ($rejectedlog as $log)
                                                                <tr class="lesson_learned_row">
                                                                    <td>
                                                                        {{ Displaydateformat($log->created_at) ?? '' }}
                                                                    </td>
                                                                    <td>
                                                                        {{ isset($log->remarks) ? $log->remarks : '' }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <hr>
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">EHS Head Approval</h4>
                                            </div>
                                        </div>
                                        @if (isset($training_schedule->approver_name))
                                            <div class="row">

                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Approver Name</label>
                                                    <div class="view_data">
                                                        {{ $training_schedule->approver_name }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Date</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($training_schedule->date) }}
                                                    </div>
                                                </div>
                                                <div class="mb-3 col-md-4 form-input">
                                                    <label class="form-label view_label">Remark</label>
                                                    <div class="view_data">
                                                        {{ isset($training_schedule->remark) ? $training_schedule->remark : '' }}
                                                    </div>
                                                </div>

                                            </div>
                                        @else
                                            <p>No data available.</p>
                                        @endif
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
      
    </script>
@endpush
