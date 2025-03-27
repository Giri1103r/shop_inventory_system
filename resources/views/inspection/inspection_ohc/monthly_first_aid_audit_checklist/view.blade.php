@extends('admin.layouts.admin')
@section('title', 'Monthly First Aid Box Audit Checklist Show')
@section('pageurl', admin_url('ohc/first-aid-box/monthly-audit/list'))

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
                                    <x-button-back href="{{ admin_url('ohc/first-aid-box/monthly-audit/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Monthly First Aid Box Audit Checklist</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($occupational_health_center->doc_no) ? $occupational_health_center->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review date</label>
                                        <div class="view_data">
                                            {{ isset($occupational_health_center->revision_date) ? $occupational_health_center->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issued Date</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($occupational_health_center->issue_date) ? $occupational_health_center->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Next Due On</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($occupational_health_center->next_due) ? $occupational_health_center->next_due : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date Of Inspection</label>
                                        <div class="view_data">
                                            {{ DisplaydateFormat(isset($occupational_health_center->date_of_inspection) ? $occupational_health_center->date_of_inspection : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShift(isset($occupational_health_center->shift) ? $occupational_health_center->shift : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Location</label>
                                        <div class="view_data">
                                            {{ getLocationname(isset($occupational_health_center->location) ? $occupational_health_center->location : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($occupational_health_center->unit) ? $occupational_health_center->unit : '') }}
                                        </div>
                                    </div>
                                    @if (!empty($requestorsignature) && !empty($requestorsignature->requestor_file_path))
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label" style="display: block;">
                                                {{ __('inspection.signature') }}
                                            </label>
                                            <img src="{{ admin_url($requestorsignature->requestor_file_path) }}"
                                                alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                        </div>
                                    </div>
                                @else
                                <div class="col-md-4 mb-2">
                                    <div class="form-group form-input">
                                        <label class="form-label" style="display: block;">
                                            {{ __('inspection.signature') }}
                                        </label>
                                        <img src="{{ admin_url($signatureview->signature_upload) }}"
                                            alt="Signature Upload" style="width: 150px; margin-top: -10px;" />
                                    </div>
                                </div>
                                @endif
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($occupational_health_center->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($occupational_health_center->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($occupational_health_center->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Monthly First Aid Box Audit Checklist</h4>
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
                                                    <th>Quantity</th>
                                                    <th colspan="3">Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $decodedData = json_decode($inspectionCkeclist->checklist, true);
                                                    $checkItems = $decodedData['check_item'] ?? [];
                                                    $statuses = $decodedData['status'] ?? [];
                                                    $remarks = $decodedData['remarks'] ?? [];
                                                    $quantity = $decodedData['quantity'] ?? [];
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
                                                                @if ($option == 'Yes')
                                                                    @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'Yes')
                                                                        <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                    @endif
                                                                @elseif ($option == 'No')
                                                                    @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'No')
                                                                        <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                    @endif
                                                                @elseif ($option == 'N/A')
                                                                    @if (isset($statuses[$checkPoint]) && $statuses[$checkPoint] == 'N/A')
                                                                        <i class="fa-solid fa-check" style="color: #267709; width: 15px;"></i>
                                                                    @else
                                                                        <i class="fa-solid fa-times" style="color: #d40a0a; width: 15px;"></i>
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endforeach

                                                        <td >
                                                            {{ $quantity[$checkPoint] ?? 'No Quantity Available' }}
                                                        </td>


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
