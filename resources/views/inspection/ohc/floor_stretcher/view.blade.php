@extends('admin.layouts.admin')
@section('title', 'Ohc Floor Stretcher Checklist')
@section('pageurl', admin_url('ohc/floor_stretcher/checklist/list'))
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
                                    <x-button-back
                                        href="{{ admin_url('ohc/floor_stretcher/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">


                                    <div class="row">

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Shift</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection_details->frequency) }}
                                                </div>
                                            </div>
                                        </div>

                                        @php
                                            $inspection_details = json_decode($inspection_details->responses, true);
                                            $srNo = 1;
                                            $checkpoint_keys = [
                                                'fs_first',
                                                'fs_second',
                                                'fs_third',
                                                'fs_fourth',
                                                'fs_fifth',
                                                'fs_sixth',
                                            ];
                                        @endphp

                                        <table class="container p-5 table-responsive">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;">
                                                        {{ __('inspection.sr_no') }}
                                                    </th>
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:12%;">
                                                        {{ __('inspection.resource_code') }}
                                                    </th>
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:12%;">
                                                        {{ __('inspection.dept/location') }}
                                                    </th>
                                                    <th colspan="6"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:48%;">
                                                        {{ __('inspection.checkpoints') }}
                                                    </th>
                                                    <th rowspan="2"
                                                        style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:20%;">
                                                        {{ __('inspection.remarks') }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    @foreach ($checkpoint_keys as $key)
                                                        <th
                                                            style="border: 1px solid black; padding: 8px; background-color: #ccc; text-align: center; width:8%;">
                                                            {{ __('inspection.' . $key) }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($inspection_details['resource_code'] as $subTypeId => $resource)
                                                    @foreach ($resource as $checklistId => $resourceCode)
                                                        <tr>
                                                            <td
                                                                style="border: 1px solid black; padding: 8px; text-align: center; font-weight: bold;">
                                                                {{ $srNo }}
                                                            </td>
                                                            <td
                                                                style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                {{ $resourceCode }}
                                                            </td>
                                                            <td
                                                                style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                {{ GetChecklistTypeDate($checklistId) }}
                                                            </td>
                                                            @php
                                                                $responses =
                                                                    $inspection_details['response'][$subTypeId][
                                                                        $checklistId
                                                                    ] ?? [];
                                                            @endphp
                                                            @foreach ($checkpoint_keys as $key)
                                                                <td
                                                                    style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                    @php $responseText = $responses[$key] ?? '-'; @endphp
                                                                    @if ($responseText == 'YES')
                                                                        <span
                                                                            style="color: green; font-size: 20px;">✔</span>
                                                                    @elseif ($responseText == 'NO')
                                                                        <span style="color: red; font-size: 20px;">✖</span>
                                                                    @else
                                                                        {{ $responseText }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                            <td
                                                                style="border: 1px solid black; padding: 8px; text-align: center;">
                                                                {{ $inspection_details['remarks'][$subTypeId][$checklistId] ?? '-' }}
                                                            </td>
                                                        </tr>
                                                        @php $srNo++; @endphp
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>


                                    </div>
                                    <div class="row m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                    style="display: block; ">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($inspection_file->file_path) }}"
                                                    alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop
