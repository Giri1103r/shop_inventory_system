@extends('admin.layouts.admin')
@section('title', 'Safety Walk Observation')
@section('pageurl', admin_url('safety/ohc-plant-summary/list'))
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
                                    <x-button-back href="{{ admin_url('safety/ohc-plant-summary/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form mx-3">
                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->revision_data }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.updated_frequency') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection_details->updated_frequency }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        @php
                                            $rowcount = count($units);
                                        @endphp

                                        <div class="table-responsive">
                                            <table id="dataTable" class="table table-bordered text-center"
                                                style="border-collapse: collapse;">
                                                <thead>
                                                    <tr>
                                                        <th rowspan="2"
                                                            style="border: 1px solid #000; vertical-align:middle; text-align:center;">
                                                            Sr. No.</th>
                                                        <th rowspan="2"
                                                            style="border: 1px solid #000; vertical-align:middle; text-align:center ;">
                                                            Description</th>
                                                        <th colspan="{{ $rowcount }}"
                                                            style="border: 1px solid #000; text-align: center;">
                                                            Quantity (in Nos/m²)
                                                        </th>
                                                        <th rowspan="2"
                                                            style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                            Total Quantity
                                                            (in
                                                            Nos/m²)</th>
                                                    </tr>
                                                    <tr>
                                                        @foreach ($units as $unit)
                                                            <th
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                {{ $unit->unit_name }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($quantity_details as $index => $quantity_detail)
                                                        <tr>
                                                            <td style="border: 1px solid #000;">{{ $index }}</td>
                                                            <td style="border: 1px solid #000;">
                                                                {{ $quantity_detail['description'] }}</td>
                                                            @foreach ($units as $index => $unit)
                                                                <td style="border: 1px solid #000;">
                                                                    {{ isset($quantity_detail['unit - ' . $index + 1]) ? $quantity_detail['unit - ' . $index + 1] : '' }}
                                                                </td>
                                                            @endforeach

                                                            <td style="border: 1px solid #000;">
                                                                {{ $quantity_detail['total_quantity'] }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Fire Water Pump House Details</h4>
                                            </div>
                                            <div class="table-responsive">
                                                <table id="dataTable" class="table table-bordered text-center"
                                                    style="border-collapse: collapse;">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center;">
                                                                Sr. No.</th>
                                                            <th rowspan="2"
                                                                style="border: 1px solid #000; vertical-align:middle; text-align:center ;">
                                                                Water
                                                                Pump & Water Storage Tank</th>
                                                            <th colspan="{{ $rowcount }}"
                                                                style="border: 1px solid #000; text-align: center;">
                                                                Capacity
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            @foreach ($units as $unit)
                                                                <th
                                                                    style="border: 1px solid #000; vertical-align:middle; text-align:center">
                                                                    {{ $unit->unit_name }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($fire_water_pump_details as $index => $quantity_detail)
                                                            <tr>
                                                                <td style="border: 1px solid #000;">{{ $index }}
                                                                </td>
                                                                <td style="border: 1px solid #000;">
                                                                    {{ $quantity_detail['fire_pump_details'] }}</td>
                                                                @foreach ($units as $index => $unit)
                                                                    <td style="border: 1px solid #000;">
                                                                        {{ isset($quantity_detail['fire_pump_details_unit_' . $index + 1]) ? $quantity_detail['fire_pump_details_unit_' . $index + 1] : '' }}
                                                                    </td>
                                                                @endforeach
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

                </div>
            </div>

        @stop
        @push('script')
        @endpush
