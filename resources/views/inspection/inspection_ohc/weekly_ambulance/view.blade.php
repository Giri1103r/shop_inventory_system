@extends('admin.layouts.admin')
@section('title', 'Weekly Ambulance Inspection Checklist Show')
@section('pageurl', admin_url('ohc/weekly-ambulance/inspection/checklist/list'))

@push('style')
    <style>
        .view_label {
            display: block;

        }

        .image-wrapper {
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endpush


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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/weekly-ambulance/inspection/checklist/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Weeky Ambulance Details</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($weekAmbualance->doc_no) ? $weekAmbualance->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review date</label>
                                        <div class="view_data">
                                            {{ isset($weekAmbualance->revision_date) ? $weekAmbualance->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issued Date</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->issue_date) ? $weekAmbualance->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Next Due On</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->next_due) ? $weekAmbualance->next_due : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Inspection</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($weekAmbualance->date_of_inspection) ? $weekAmbualance->date_of_inspection : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShift(isset($weekAmbualance->shift) ? $weekAmbualance->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Location</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($weekAmbualance->location) ? $weekAmbualance->location : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($weekAmbualance->unit) ? $weekAmbualance->unit : '') }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($weekAmbualance->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($weekAmbualance->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($weekAmbualance->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Weeky Ambulance Inspection Checklist</h4>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead class="bg-secondary text-white">
                                                <tr>
                                                    <th colspan="3">Check Points</th>
                                                    @foreach ($getoption as $option)
                                                        <th>{{ $option }}</th>
                                                    @endforeach
                                                    <th colspan="3">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $decodedData = json_decode($inspectionCkeclist->checklist, true);
                                                    $checkItems = $decodedData['check_item'] ?? [];
                                                    $statuses = $decodedData['status'] ?? [];
                                                    $remarks = $decodedData['remarks'] ?? [];
                                                @endphp

                                                @foreach ($checkItems as $groupId => $checkPoints)
                                                    @php $rowCount = count($checkPoints); @endphp

                                                    @foreach ($checkPoints as $index => $checkPoint)
                                                        <tr>
                                                            @if ($index == 0)
                                                                <td rowspan="{{ $rowCount }}">
                                                                    {{ getSubcategoryname($groupId) }}
                                                                </td>
                                                            @endif

                                                            <td colspan="2">{{ getSubcategoryDataname($checkPoint) }}
                                                            </td>

                                                            @foreach ($getoption as $option)
                                                            <td style="text-align: center;">
                                                                @if ($option == 'Ok')
                                                                    @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Ok')
                                                                        <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i> <!-- Green check for Ok -->
                                                                    @else
                                                                        <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i> <!-- Red cross if Ok is not selected -->
                                                                    @endif
                                                                @elseif ($option == 'Not-Ok')
                                                                    @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Not-Ok')
                                                                    <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i> <!-- Red check for Not-Ok -->
                                                                    @else
                                                                    <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i> <!-- Red cross if Ok is not selected -->
                                                                @endif
                                                                @endif
                                                            </td>
                                                        @endforeach



                                                            <td colspan="3">
                                                                {{ $remarks[$checkPoint] ?? 'No Remarks' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>



                                        </table>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    @stop
