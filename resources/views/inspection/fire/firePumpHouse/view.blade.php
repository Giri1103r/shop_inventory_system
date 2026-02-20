@extends('admin.layouts.admin')
@section('title', 'Daily Fire Pump House Inspection')
@section('pageurl', admin_url('fire/daily-fire-pump-house-inspection/list'))

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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('fire/daily-fire-pump-house-inspection/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Daily Fire Pump House Inspection</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Inspection ID</label>
                                        <div class="view_data">
                                            {{ isset($dailyFire->inspection_id) ? $dailyFire->inspection_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Doc. No</label>
                                        <div class="view_data">
                                            {{ isset($dailyFire->document_no) ? $dailyFire->document_no : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issue Dt.</label>
                                        <div class="view_data">
                                            {{ displayDateformat($dailyFire->issuedate) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Rev. & Dt.</label>
                                        <div class="view_data">
                                            {{ $dailyFire->rev_date }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date of Inspection</label>
                                        <div class="view_data">
                                            {{ displayDateformat($dailyFire->date_of_inspection) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Unit</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($dailyFire->unit_id) ? $dailyFire->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Shift</label>
                                        <div class="view_data">
                                            {{ getShiftname(isset($dailyFire->shift_id) ? $dailyFire->shift_id : '') }}
                                        </div>
                                    </div>
                                    @php
                                        $user_response = json_decode($dailyFire->checklist, true);
                                    @endphp

                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No</th>
                                                <th colspan="2"
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Check Points</th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Pump No</th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    YES/NO</th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $srNo = 1; @endphp
                                            @foreach ($user_response as $checklistId => $data)
                                                <tr>
                                                    <td
                                                        style="border: 1px solid black; padding: 8px; font-weight: bold; text-align: center;">
                                                        {{ $srNo }}
                                                    </td>
                                                    <td colspan="2" style="border: 1px solid black; padding: 8px;">
                                                        {{ GetChecklistTypeDate($checklistId) }}
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ $data['pump_no'] ?? 'N/A' }}
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        @if ($data['response'] == 'YES')
                                                            <span style="color: green; font-size: 20px;">✓</span>
                                                        @elseif ($data['response'] == 'NO')
                                                            <span style="color: red; font-size: 20px;">X</span>
                                                        @endif
                                                    </td>
                                                    <td style="border: 1px solid black; padding: 8px; text-align: center;">
                                                        {{ !empty($data['remarks']) ? $data['remarks'] : '-' }}
                                                    </td>
                                                </tr>
                                                @php $srNo++; @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{-- <div class="mt-3 col-md-4 form-input">
                                        <label class="form-label">Signature</label>
                                        <div>
                                            <a href="{{ asset($dailyFire->file_path) }}" target="_blank">
                                                <img src="{{ asset($dailyFire->file_path) }}" alt="Signature"
                                                    style="max-width: 20%;">
                                            </a>
                                        </div>
                                    </div> --}}
                                    <div class="mt-3 col-md-4 form-input">
                                        <label class="form-label view_label">Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat($dailyFire->date) }}
                                        </div>
                                    </div>
                                    <div class="mt-3 col-md-12 form-input">
                                        <label class="form-label view_label">Note</label>
                                        <div class="view_data">
                                            {{ $dailyFire->note }}
                                        </div>
                                    </div>
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
