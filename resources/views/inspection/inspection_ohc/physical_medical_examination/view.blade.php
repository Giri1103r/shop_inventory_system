@extends('admin.layouts.admin')
@section('title', 'Physical Health Examination Check-up')
@section('pageurl', admin_url('ohc/physical-medical-examination/yearly/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>


    <div class="clearfix">
    </div>
    <div class="page-titles">
        <div class="d-flex align-items-center">

        </div>

    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/physical-medical-examination/yearly/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Employee Details</h4>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Id') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->emp_id) ? $physicalHealth->emp_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Employee Name') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->emp_name) ? $physicalHealth->emp_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Contact Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->mobile_no) ? $physicalHealth->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Age') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->age) ? $physicalHealth->age : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($physicalHealth->date) ? $physicalHealth->date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Date of Birth') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat(isset($physicalHealth->dob) ? $physicalHealth->dob : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($physicalHealth->unit_id) ? $physicalHealth->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($physicalHealth->department_id) ? $physicalHealth->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Gender') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->gender) ? $physicalHealth->gender : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Blood Group') }}</label>
                                        <div class="view_data">
                                            {{ getBloodGroupname(isset($physicalHealth->blood_group)) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Heigth') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->height) ? $physicalHealth->height : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Weigth') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->weight) ? $physicalHealth->weight : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('BMI') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->bmi) ? $physicalHealth->bmi : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Address') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->address) ? $physicalHealth->address : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($physicalHealth->created_by) ? $physicalHealth->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($physicalHealth->created_at) ? $physicalHealth->created_at : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Past History') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->past_history) ? $physicalHealth->past_history : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-8 form-input">
                                        <label class="form-label view_label">{{ __('Present Complaints') }}</label>
                                        <div class="view_data">
                                            {{ isset($physicalHealth->present_complaint) ? $physicalHealth->present_complaint : '' }}
                                        </div>
                                    </div>
                                </div>
                                {{-- clinical details --}}
                                @php
                                    $clinicalDetails = json_decode($physicalHealth->personal_details, true);
                                    $statusArray = $clinicalDetails['status'] ?? [];
                                @endphp

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Clinical Details</h4>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Details Of Personal Habits</th>
                                                <th style="text-align: center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($statusArray as $key => $value)
                                                <tr>
                                                    <td style="text-align: center">{{ $loop->iteration }}</td>
                                                    <td style="text-align: center">{{ getClinicalDetails($key) }}</td>
                                                    <td style="text-align: center">
                                                        @if ($value == '1')
                                                            <span style="color: green; font-size: 20px;">✓</span>
                                                        @else
                                                            <span style="color: red; font-size: 20px;">✗</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Family History --}}
                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Famiy History</h4>
                                    </div>

                                    <div class="mb-2">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="text-align: center">Sr. No.</th>
                                                    <th style="text-align: center">Details Of Personal Habits</th>
                                                    <th style="text-align: center">Status</th>
                                                    <th style="text-align: center">Remarks</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $FamilyHistory = json_decode($physicalHealth->family_history, true);
                                                $statusArray = $FamilyHistory['status'] ?? [];
                                                $remarksArray = $FamilyHistory['remarks'] ?? [];
                                            @endphp

                                            <tbody>
                                                @foreach ($familyHistory as $item)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>

                                                        <td class="text-center">
                                                            {{ $item->family_history }}
                                                            <input type="hidden" name="id[{{ $item->id }}]"
                                                                value="{{ encryptId($item->id) }}">
                                                        </td>

                                                        <td class="text-center">
                                                            @php
                                                                $status = $statusArray[$item->id] ?? null;
                                                            @endphp

                                                            @if ($status === '1')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($status === '0')
                                                                <span style="color: red; font-size: 20px;">✗</span>
                                                            @else
                                                                <span style="color: gray; font-size: 16px;">N/A</span>
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $remarksArray[$item->id] ?? 'No remarks' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>
                                    </div>



                                </div>
                                {{-- Vital check points --}}


                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Vital Check Points</h4>
                                    </div>

                                    <div class="mb-2">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="text-align: center">Sr. No.</th>
                                                    <th style="text-align: center">Check Points</th>
                                                    <th style="text-align: center">Reading Value</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $vital_checkpoints = json_decode(
                                                    $physicalHealth->vital_checkpoints,
                                                    true,
                                                );
                                                $reading_value = $vital_checkpoints['reading_value'] ?? [];
                                                $serial = 1;
                                            @endphp

                                            <tbody>
                                                @foreach ($check_points as $label => $items)
                                                    @foreach ($items as $point)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>

                                                            <td class="text-center">
                                                                {{ $point->name }}
                                                                <input type="hidden" name="id[{{ $point->id }}]"
                                                                    value="{{ encryptId($point->id) }}">
                                                            </td>

                                                            <td class="text-center">
                                                                {{ $reading_value[$point->id] ?? 'No Value' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach


                                            </tbody>

                                        </table>
                                    </div>
                                </div>

                                {{-- Eye check points --}}

                                <div class="row mt-2">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EYE Check Up</h4>
                                    </div>

                                    <div class="mb-2">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th style="text-align: center">Vision</th>
                                                    <th style="text-align: center">Without Glasses(Right)</th>
                                                    <th style="text-align: center">With Glasses(Left)</th>
                                                    <th style="text-align: center">Color Blindness</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Distance</td>
                                                    <td>
                                                        <div class="view_data">
                                                            {{ isset($physicalHealth->distance_without_glass) ? $physicalHealth->distance_without_glass : '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="view_data">
                                                            {{ isset($physicalHealth->distance_with_glass) ? $physicalHealth->distance_with_glass : '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="view_data">
                                                            @if ($physicalHealth->distance_without_glass_yes === '1')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($physicalHealth->distance_without_glass_yes === '0')
                                                                <span style="color: red; font-size: 20px;">✗</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Near</td>
                                                    <td>
                                                        <div class="view_data">
                                                            {{ isset($physicalHealth->near_without_glass) ? $physicalHealth->near_without_glass : '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="view_data">
                                                            {{ isset($physicalHealth->near_with_glass) ? $physicalHealth->near_with_glass : '' }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="view_data">
                                                            @if ($physicalHealth->near_without_glass_yes === '1')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($physicalHealth->near_without_glass_yes === '0')
                                                                <span style="color: red; font-size: 20px;">✗</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>



                                </div>


                                <div class="mb-3 col-md-8 form-input">
                                    <label class="form-label view_label">{{ __('Remarks by Medical Officer') }}</label>
                                    <div class="view_data">
                                        {{ isset($physicalHealth->remarks) ? $physicalHealth->remarks : '' }}
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @stop
