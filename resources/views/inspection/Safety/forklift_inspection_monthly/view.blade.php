@extends('admin.layouts.admin')
@section('title', 'Monthly ForkLift Inspection')
@section('pageurl', admin_url('ohc/medicine-requisition/list'))


@section('content')
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
                                        href="{{ admin_url('safety/forklift-inspection/monthly/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.monthly_forklift_inspection') }}</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->doc_no) ? $inspection_details->doc_no : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->issue_date) ? $inspection_details->issue_date : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->revision_data) ? $inspection_details->revision_data : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->date_of_inspection) ? $inspection_details->date_of_inspection : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.location') }}</label>
                                            <div class="view_data">
                                                {{ getLocationname(isset($inspection_details->location) ? $inspection_details->location : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">Shift</label>
                                            <div class="view_data">
                                                {{ getShift(isset($inspection_details->shift) ? $inspection_details->shift : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.next_due') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($inspection_details->next_due) ? $inspection_details->next_due : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">
                                                {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.frequency') }}</label>
                                            <div class="view_data">
                                                {{ GetFrequency(isset($inspection_details->frequency) ? $inspection_details->frequency : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.identification_no') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->identification_no) ? $inspection_details->identification_no : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.forklift_type') }}</label>
                                            <div class="view_data">
                                                {{ GetForkLiftType(isset($inspection_details->forklift_type) ? $inspection_details->forklift_type : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.capacity') }}</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->capacity) ? $inspection_details->capacity : '' }}
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $user_response = json_decode($inspection_details->responses, true);
                                    @endphp
                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No</th>
                                                <th colspan="3"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Check Points</th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Reports</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $srNo = 1; @endphp
                                            @foreach ($user_response as $subcategory => $questions)
                                                @php
                                                    $rowCount = count($questions);
                                                    $firstRow = true;
                                                @endphp
                                                @foreach ($questions as $questionId => $answer)
                                                    <tr>
                                                        @if ($firstRow)
                                                            <td rowspan="{{ $rowCount }}"
                                                                style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                                                {{ $srNo }}</td>
                                                            <td rowspan="{{ $rowCount }}"
                                                                style="border: 1px solid black; padding: 8px; font-weight: bold;">
                                                                {{ GetSubChecklistTypeName($subcategory) }}
                                                            </td>
                                                            @php
                                                                $srNo++;
                                                                $firstRow = false;
                                                            @endphp
                                                        @endif
                                                        <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                                            {{ GetChecklistTypeDate($questionId) }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            @if ($answer == 'YES')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($answer == 'NO' || $answer == 'N/A')
                                                                <span style="color: red; font-size: 20px;">X</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="row mt-3">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_verify') }}</h4>
                                        </div>
                                        <div class="row mb-2">
                                            @if (isset($inspection_details->verified_by))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->verified_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($inspection_details->created_at))
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            @if (isset($inspection_details->approved_by))
                                                @if ($inspection_details->verified_by == $inspection_details->approved_by)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label
                                                                class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                            <div class="view_data">
                                                                {{ getUsername($inspection_details->approved_by) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            @if (isset($inspection_details->capa_recomendation))
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label">{{ __('inspection.capa_recomendation') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->capa_recomendation }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">{{ __('inspection.remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @if (isset($inspection_details->capa_remarks))
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.fire_associate_action') }}</h4>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.name') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->created_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.capa_action_remarks') }}</label>
                                                        <div class="view_data">
                                                            {{ $inspection_details->capa_remarks }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($inspection_details->capa_ehs_remarks)
                                        <div class="card-header-inner">
                                            <h4 class="text-white">{{ __('inspection.ehs_officer_reverification') }}
                                            </h4>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.verified_by') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.capa_reverifcation_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->capa_ehs_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (isset($inspection_details->level_one_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.level_one_manager_action') }}
                                                </h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->l1_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->level_one_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($inspection_details->level_two_manager_remarks))
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">{{ __('inspection.level_two_manager_action') }}
                                                </h4>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_one_manager') }}</label>
                                                    <div class="view_data">
                                                        {{ getUserName($inspection_details->l2_manager_verified_by) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.date') }}</label>
                                                    <div class="view_data">
                                                        {{ Displaydateformat($inspection_details->created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                            @if ($inspection_details->approved_by)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.approved_by') }}</label>
                                                        <div class="view_data">
                                                            {{ getUsername($inspection_details->approved_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label
                                                        class="form-label ">{{ __('inspection.level_two_manager_remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->level_two_manager_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @stop
