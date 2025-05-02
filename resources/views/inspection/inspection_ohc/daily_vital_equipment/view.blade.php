@extends('admin.layouts.admin')
@section('title', 'Ohc Daily Vital Equipment')
@section('pageurl', admin_url('ohc/daily-vital-equipment/list'))


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
                                        href="{{ admin_url('ohc/daily-vital-equipment/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Ohc Daily Vital Equipment</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                            <div class="view_data">
                                                {{ $document_no->doc_no }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                            <div class="view_data">
                                                {{ Displaydateformat($document_no->issue_date) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                            <div class="view_data">
                                                {{ $document_no->rev_dt }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                            <div class="view_data">
                                                {{ displaydateformat(isset($daily_vital->date_of_inspection) ? $daily_vital->date_of_inspection : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">shift</label>
                                            <div class="view_data">
                                                {{ getShift(isset($daily_vital->shift) ? $daily_vital->shift : '') }}
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-4 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">{{ __('inspection.unit') }}</label>
                                            <div class="view_data">
                                                {{ getUnitname(isset($daily_vital->unit) ? $daily_vital->unit : '') }}
                                            </div>
                                        </div>
                                    </div>
                                    @php
                                        $signature = GetOHCSignature(
                                            $daily_vital->created_by,
                                            $daily_vital->id,
                                            OHC_TYPE_DAILY_VITAL_EQUIPMENT_CHECKLIST,
                                        );
                                    @endphp
                                    @if (isset($signature))
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label"
                                                    style="display: block;">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($signature) }}" alt="Signature Upload"
                                                    style="width: 100px; margin-top: -10px;" />
                                            </div>
                                        </div>
                                    @endif

                                    @php
                                        $user_response = json_decode($daily_vital->responses, true);
                                        $srNo = 1;
                                    @endphp

                                    <table class="container p-5">
                                        <thead>
                                            <tr>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Sr. No
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Check Points
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Response
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Quantity
                                                </th>
                                                <th
                                                    style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center;">
                                                    Remarks
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user_response as $subcategory => $questions)
                                                @foreach ($questions as $questionId => $answer)
                                                    <tr>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                                            {{ $srNo }}
                                                        </td>
                                                        <td style="border: 1px solid black; padding: 8px;">
                                                            {{ GetChecklistTypeDate($questionId) }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            @php
                                                                $responseText = $answer['response'] ?? '-';
                                                            @endphp
                                                            @if ($responseText == 'YES')
                                                                <span style="color: green; font-size: 20px;">✓</span>
                                                            @elseif ($responseText == 'NO' || $responseText == 'N/A')
                                                                <span style="color: red; font-size: 20px;">X</span>
                                                            @else
                                                                {{ $responseText }}
                                                            @endif
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            {{ $answer['quantity'] ?? '-' }}
                                                        </td>
                                                        <td
                                                            style="border: 1px solid black; padding: 8px; text-align: center;">
                                                            {{ $answer['remark'] ?? '-' }}
                                                        </td>
                                                    </tr>
                                                    @php $srNo++; @endphp
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

        @stop
