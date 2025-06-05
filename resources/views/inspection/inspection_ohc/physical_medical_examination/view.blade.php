@extends('admin.layouts.admin')
@section('title', 'Physical Health Examination Check-up View')
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

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    @stop
