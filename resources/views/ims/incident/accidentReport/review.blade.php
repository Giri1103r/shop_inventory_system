@extends('admin.layouts.admin')
@section('title', 'Accident Report Review')
@section('pageurl', admin_url('accidentReport/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
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
                                    <x-button-back href="{{ admin_url('accidentReport/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Accident Report Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Sr. No</label>
                                        <div class="view_data">
                                            {{ $accident_report->accident_report_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date and Time</label>
                                        <div class="view_data">
                                            {{ Displaydatetimeformat($accident_report->date_and_time) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Employee Code</label>
                                        <div class="view_data">
                                            {{ $accident_report->emp_code }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ $accident_report->unit_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Designation</label>
                                        <div class="view_data">
                                            {{ $accident_report->designation }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Department</label>
                                        <div class="view_data">
                                            {{ $accident_report->department_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ $accident_report->shift }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Accident Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->location_name }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Exact Location</label>
                                        <div class="view_data">
                                            {{ $accident_report->exact_location }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Address of the injured person</label>
                                        <div class="view_data">
                                            {{ $accident_report->address_of_the_injuredperson }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($accident_report->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($accident_report->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($accident_report->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">EHS Head Review</h4>
                                    </div>
                                </div>

                                @if ($accident_report->accident_status == STATUS_ACCIDENT_REPORT)
                                    <div class="basic-form">
                                        <form method="POST" id="ehs_head_review"
                                            action="{{ admin_url('accidentReport/ehs_head_review/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="accident_report_id"
                                                    id="accident_report_id" value="{{ encryptId($accident_report->id) }}">

                                                <input type="hidden" name="reviewer_emp_id" id="reviewer_emp_id"
                                                    class="form-control" value="{{ Auth::user()->employee_id ?? '' }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Reviewer Name</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="date" id="date_datepicker"
                                                            class="form-control" placeholder="Date" readonly
                                                            value="{{ todaydate() }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="team_id" class="form-label require">Assign Team
                                                            members</label>
                                                        <select name="team_member[]" id="team_id"
                                                            class="form-control team_name" multiple="multiple"
                                                            style="width: 100%">
                                                            <option value="">Select Team Members</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
                                            </div>
                                        </form>

                                    </div>
                                @else
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Reviewer Name</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->reviewer_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getEHSReview->date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="team_id" class="form-label">Assign Team
                                                members</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->team_member_names }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Remark</label>
                                            <div class="view_data">
                                                {{ $getEHSReview->remark }}
                                            </div>
                                        </div>

                                    </div>
                                @endif

                            </div>


                            @if ($accident_report->accident_status >= STATUS_EHSVERIFY_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Injured Person Details</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Injury Person Type</th>
                                                    <th>Injury Person Name</th>
                                                    <th>Injury Person Employee ID</th>
                                                    <th>Injury Person Designation</th>
                                                    <th>Injury Person Department</th>
                                                    <th>Injury Body Parts</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($accident_investigation_injury as $injury)
                                                    <tr>
                                                        <td>
                                                            {{ $injury->injury_person_type == 1 ? 'Employee' : ($injury->injury_person_type == 2 ? 'Worker' : 'Others') }}
                                                        </td>
                                                        <td>
                                                            @if ($injury->injury_person_type == 1 || $injury->injury_person_type == 2)
                                                                {{ $injury->emp_name }}
                                                            @else
                                                                {{ $injury->injury_person_name }}
                                                            @endif
                                                        </td>
                                                        <td>{{ $injury->emp_id }}</td>
                                                        <td>{{ $injury->injury_person_designation }}</td>
                                                        <td>
                                                            {{-- @if ($injury->injury_person_type == 1 || $injury->injury_person_type == 2)
                                                                {{ $injury->department_name }}
                                                            @else --}}
                                                                {{ $injury->injury_person_department_id }}
                                                            {{-- @endif --}}
                                                        </td>
                                                        <td>
                                                            @if ($injury->body_part_image)
                                                                <a href="{{ admin_url('storage/app/public/uploads/' . $injury->body_part_image) }}"
                                                                    target="_blank">
                                                                    <img src="{{ admin_url('storage/app/public/uploads/' . $injury->body_part_image) }}"
                                                                        alt="Body Parts Image"
                                                                        style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                                </a>
                                                            @endif
                                                        </td>


                                                        <td>
                                                            @php
                                                                $imgMapDataDecoded = json_decode(
                                                                    $injury->imgMapdata,
                                                                    true,
                                                                );
                                                            @endphp
                                                            @if ($imgMapDataDecoded)
                                                                <ul>
                                                                    @foreach ($imgMapDataDecoded['map']['total'] as $key => $value)
                                                                        <li>{{ ucfirst($key) }}: {{ $value }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </div>
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Investigation</h4>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Name of the
                                                Witness</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->witness_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Was anything
                                                damaged?</label>
                                            <div class="view_data">

                                                @if ($getInvestigation->anything_damaged == 1)
                                                    Man
                                                @elseif($getInvestigation->anything_damaged == 2)
                                                    Machine
                                                @elseif($getInvestigation->anything_damaged == 3)
                                                    Materials
                                                @else
                                                    NA
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">HIRA</label>
                                            <div class="view_data">
                                                @if (!empty($getInvestigation->hira_moc[0]['hira_name']))
                                                    {{ $getInvestigation->hira_moc[0]['hira_name'] }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">MOC</label>
                                            <div class="view_data">
                                                @if (!empty($getInvestigation->hira_moc[0]['moc_name']))
                                                    {{ $getInvestigation->hira_moc[0]['moc_name'] }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Possible Root
                                                Cause
                                                Analysis (PRCA)</label>
                                            <div class="view_data">
                                                @if ($getInvestigation->root_cause_analysis == 1)
                                                    Why - Why Analysis
                                                @elseif($getInvestigation->root_cause_analysis == 2)
                                                    Fish Bone Analysis
                                                @else
                                                    NA
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Immediate action taken
                                                (If any)</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->action_taken }}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Was the injured person receiving any
                                                treatment
                                                at present?</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->is_treatment == 1 ? 'Yes' : 'No' }}
                                            </div>
                                        </div>

                                        @if ($getInvestigation->is_treatment == 1)
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Details</label>
                                                <div class="view_data">
                                                    {{ $getInvestigation->details }}
                                                </div>
                                            </div>
                                        @endif


                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Recommended Corrective & Preventive
                                                Action</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->corrective_preventive_action }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Responsible Person</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->responsible_person }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Target Date</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getInvestigation->target_date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Remarks (If Any)</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->remark }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Risk Analysis</label>
                                            <div class="view_data">
                                                {{ $getInvestigation->risk_analysis == 1 ? 'Yes':'No' }}
                                            </div>
                                        </div>
                                        @if ($getInvestigation->risk_analysis == 2)
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">Risk Analysis Remark</label>
                                                <div class="view_data">
                                                    {{ $getInvestigation->risk_analysis_remark }}
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                    @if ($getInvestigation->root_cause_analysis == 1)
                                        <div class="row mt-3 whywhy">
                                            <div class="card p-3">
                                                <div
                                                    class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                                                    <h4 class="text-dark mb-0">Why Why Analysis</h4>
                                                </div>

                                                <!-- Table -->
                                                <div class="table-responsive">
                                                    <table class="table table-bordered whywhyanalysis text-center">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>Why 1</th>
                                                                <th></th>
                                                                <th>Why 2</th>
                                                                <th></th>
                                                                <th>Why 3</th>
                                                                <th></th>
                                                                <th>Why 4</th>
                                                                <th></th>
                                                                <th>Why 5</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="whywhyanalysisBody">
                                                            @foreach ($getwhywhy as $index => $item)
                                                                <tr id="RowwhywhyanalysisView{{ $index }}">
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_first]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_1 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_second]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_2 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_third]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_3 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_forth]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_4 }}" readonly>
                                                                    </td>
                                                                    <td><i class="fas fa-arrow-right text-primary"></i>
                                                                    </td>
                                                                    <td><input type="text"
                                                                            name="whywhyanalysis[{{ $index }}][whywhyanalysis_fifth]"
                                                                            class="form-control"
                                                                            value="{{ $item->why_5 }}" readonly>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($getInvestigation->root_cause_analysis == 2)
                                        <div class="row m-5 p-3 fishbone">
                                            <div class="fishbone-container" style="text-align: center;">
                                                <!-- First Cause -->
                                                <div class="cause">
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[first][root_cause]"
                                                            value="{{ $fishboneData['first']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['first']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[first][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Second Cause -->
                                                <div class="cause">
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[second][root_cause]"
                                                            value="{{ $fishboneData['second']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['second']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[second][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>

                                                <!-- Third Cause -->
                                                <div class="cause">
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fourth][root_cause]"
                                                            value="{{ $fishboneData['fourth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                    <div class="subcause">
                                                        @foreach ($fishboneData['third']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[third][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                </div>
                                                <div class="line"></div>
                                                <!-- Fourth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['fourth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[fourth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause blue">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fourth][root_cause]"
                                                            value="{{ $fishboneData['fourth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- Fifth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['fifth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[fifth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause green">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[fifth][root_cause]"
                                                            value="{{ $fishboneData['fifth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- Sixth Cause -->
                                                <div class="cause">

                                                    <div class="subcause">
                                                        @foreach ($fishboneData['sixth']['sub'] as $key => $value)
                                                            <div class="stat">
                                                                <input type="text" class="form-control sub-stat"
                                                                    placeholder="Enter value"
                                                                    name="fishbone[sixth][sub][{{ $key }}]"
                                                                    value="{{ $value }}" readonly>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="rootcause yellow">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[sixth][root_cause]"
                                                            value="{{ $fishboneData['sixth']['root_cause'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="defect-spacer-top"></div>

                                                <!-- Root Cause -->
                                                <div class="defect">
                                                    <div class="defect-text">
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter value" name="fishbone[root_cause][main]"
                                                            value="{{ $fishboneData['root_cause']['main'] ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="defect-spacer-bottom"></div>
                                            </div>
                                        </div>

                                    @endif

                                </div>

                            @endif

                            @if ($accident_report->accident_status >= STATUS_EHSVERIFY_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">UAUC</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">UAUC</label>
                                            <div class="view_data">
                                                @if ($accident_report->ua_uc_yes_no == 1)
                                                    Yes
                                                @else
                                                    No
                                                @endif
                                            </div>
                                        </div>

                                        @if ($accident_report->ua_uc_yes_no == 1)
                                            <div class="mb-3 col-md-4 form-input">
                                                <label for="name" class="form-label">UA/UC</label>
                                                <div class="view_data">
                                                    @php
                                                        $ua_uc_values = explode(',', $accident_report->ua_or_uc);
                                                    @endphp

                                                    <span>UA: {!! in_array('1', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                    <br>
                                                    <span>UC: {!! in_array('2', $ua_uc_values)
                                                        ? '<i class="fas fa-check text-success"></i>'
                                                        : '<i class="fas fa-times text-danger"></i>' !!}</span>
                                                </div>


                                            </div>
                                            <div class="mb-3 col-md-12 form-input">
                                                <label class="form-label">Description of UAUC</label>
                                                <div class="view_data">
                                                    {{ $accident_report->description_uauc }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if ($accident_report->accident_status >= STATUS_EHSVERIFY_PENDING)
                                <div class="card-body">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Risk Level</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-12 form-input">
                                            <label for="name" class="form-label">Risk Level</label>
                                            <div class="view_data">
                                                @if ($getrisklevel->risk_level == 1)
                                                    Low
                                                @elseif($getrisklevel->risk_level == 2)
                                                    Medium
                                                @else
                                                    High
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Description of CA</label>
                                            <div class="view_data">
                                                {{ $getrisklevel->description_ca }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            @if (
                                $accident_report->accident_status == STATUS_EHSVERIFY_PENDING ||
                                    $accident_report->accident_status == STATUS_EHSAPPROVAL_REJECTED)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Head Verify</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="ehs_head_verify"
                                            action="{{ admin_url('accidentReport/ehs_head_verify/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="accident_report_id"
                                                    id="accident_report_id"
                                                    value="{{ encryptId($accident_report->id) }}">

                                                <input type="hidden" name="reviewer_emp_id" id="reviewer_emp_id"
                                                    class="form-control" value="{{ Auth::user()->employee_id ?? '' }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Verifier Name</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="date" id="date_datepicker"
                                                            class="form-control" placeholder="Date" readonly
                                                            value="{{ todaydate() }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="team_id" class="form-label require">Choose
                                                            Assignee</label>
                                                        <select name="team_member[]" id="team_id"
                                                            class="form-control team_name" style="width: 100%">
                                                            <option value="">Select Assignee</option>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Target Date</label>
                                                        <input type="text" name="target_date" id="target_date"
                                                            class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @elseif($getEHSVerify != null)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Head Verify</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Verifier Name</label>
                                            <div class="view_data">
                                                {{ $getEHSVerify->reviewer_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($getEHSVerify->date) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="team_id" class="form-label">Choose Assignee</label>
                                            <div class="view_data">
                                                {{ $getEHSVerify->team_member_names }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Remark</label>
                                            <div class="view_data">
                                                {{ $getEHSVerify->remark }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            @if ($accident_report->accident_status == STATUS_ACTION_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Action submission</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="action_submission"
                                            action="{{ admin_url('accidentReport/actiontaken/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="accident_id"
                                                    id="accident_id" value="{{ encryptId($accident_report->id) }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Submission By</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="action_submission_date"
                                                            id="" class="form-control" placeholder="Date"
                                                            readonly value="{{ todaydate() }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Action Taken</label>
                                                        <textarea name="action_submission_description" id="action_submission_description" class="form-control"
                                                            rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="submit-button" style="text-align: right;">
                                                <x-button-submit class="submit"></x-button-submit>
                                                <x-button-reset class=""></x-button-reset>
                                                <x-button-cancel
                                                    href="{{ admin_url('accidentReport/list') }}"></x-button-cancel>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @elseif(
                                $accident_report->accident_status > STATUS_ACTION_PENDING &&
                                    $accident_report->accident_status != STATUS_EHSAPPROVAL_REJECTED)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">Action submission</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label for="name" class="form-label">Submission By</label>
                                            <div class="view_data">
                                                {{ getUsername($accident_report->action_submission_by) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($accident_report->action_submission_date) }}
                                            </div>
                                        </div>

                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label">Action Taken</label>
                                            <div class="view_data">
                                                {{ $accident_report->action_submission_description }}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif
                            @if ($accident_report->accident_status == STATUS_EHSAPPROVAL_PENDING)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">EHS Approval</h4>
                                        </div>
                                    </div>
                                    <div class="basic-form">
                                        <form method="POST" id="ehs_approval"
                                            action="{{ admin_url('accidentReport/ehApproval/submit') }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <input type="hidden" class="form-control" name="accident_report_id"
                                                    id="accident_report_id"
                                                    value="{{ encryptId($accident_report->id) }}">
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="name" class="form-label">Approval By</label>
                                                        <input type="text" name="reviewer_name" id="reviewer_name"
                                                            class="form-control" value="{{ Auth::user()->name ?? '' }}"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="form-group form-input">
                                                        <label for="date" class="form-label require">Date</label>
                                                        <input type="text" name ="date" id=""
                                                            class="form-control" placeholder="Date" readonly
                                                            value="{{ todaydate() }}">
                                                    </div>
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <div class="form-group form-input">
                                                        <label class="form-label require">Remark</label>
                                                        <textarea name="remark" id="remark" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="d-flex float-end gap-2 mx-auto">
                                                <button type="submit" name="approve" value="approve"
                                                    class="btn btn-success w-100">Approve</button>
                                                <button type="submit" name="reject" value="reject"
                                                    class="btn btn-danger w-100">Reject</button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            @endif

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
            flatpickr("#target_date", {
                dateFormat: "d-m-Y",
                minDate: "today" // Allows only future dates
            });
            $('#team_id').select2({
                placeholder: "Select Team Members",
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: "{{ url('accidentReport/getemployeename') }}",
                    type: "GET",
                    dataType: "json",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true,
                    error: function(xhr, textStatus, errorThrown) {
                        console.log("Error in AJAX request:", textStatus, errorThrown);
                    }
                },
                minimumInputLength: 1,
                width: '100%',
            });


            $('#ehs_head_review').validate({
                rules: {
                    "team_member[]": {
                        required: true,
                    },
                    remark: {
                        required: true,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/
                    }
                },
                messages: {
                    "team_member[]": {
                        required: "Please select a team member.",
                    },
                    remark: {
                        required: "Please provide a remark.",
                        maxlength: "Remark cannot exceed 1000 characters."
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });

            $('#ehs_head_verify').validate({
                rules: {
                    "team_member[]": {
                        required: true,
                    },
                    target_date: {
                        required: true,
                    },
                    remark: {
                        required: true,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/
                    }
                },
                messages: {
                    "team_member[]": {
                        required: "Please select a Assignee.",
                    },
                    target_date: {
                        required: "Please provide Target Date.",

                    },
                    remark: {
                        required: "Please provide a remark.",
                        maxlength: "Remark cannot exceed 1000 characters.",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
            $('#action_submission').validate({
                rules: {

                    action_submission_description: {
                        required: true,
                        maxlength: 1000
                    }
                },
                messages: {

                    action_submission_description: {
                        required: "Please provide Action Taken.",
                        maxlength: "Action Taken cannot exceed 1000 characters."
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
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
            $('#ehs_approval').validate({
                rules: {

                    remark: {
                        required: true,
                        maxlength: 1000,
                        pattern: /^[a-zA-Z0-9\s\-_'"()\n\r,.”.;]+$/
                    }
                },
                messages: {

                    remark: {
                        required: "Please provide remark.",
                        maxlength: "Remark cannot exceed 1000 characters.",
                        pattern: "Only alphanumeric characters and - _ ' \" ( ) are allowed.",
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
                submitHandler: function(form) {
                    if ($('#vp_approval').data('conflict') === true) {
                        return false;
                    } else {
                        form.submit(); // Submit the form when valid
                    }
                },
                invalidHandler: function(event, validator) {
                    var errors = validator.numberOfInvalids();
                    validator.errorList.forEach(function(error) {

                    });
                }
            });
        });
    </script>
@endpush
