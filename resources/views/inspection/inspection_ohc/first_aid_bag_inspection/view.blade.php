@extends('admin.layouts.admin')
@section('title', ' FIRST AID BAG INSPECTION CHECKLIST')
@section('pageurl', admin_url('ohc/first-aid/opd-medicine-inspection/list'))
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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('ohc/emergency-floor-first-aid-bag/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">


                                    <div class="row">

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->next_due) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname($inspection_details->location) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.shifts') }}</label>
                                                <div class="view_data">
                                                    {{ getShiftname($inspection_details->shift_id) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection_details->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection_details->frequency) }}
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Name Of Inspection</th>
                                                <th style="text-align: center">Freeze Quantity</th>
                                                <th style="text-align: center">Available Quantity</th>
                                                <th style="text-align: center">Expiry Date</th>
                                                <th style="text-align: center">Inspected By</th>
                                                <th style="text-align: center">Remark</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($inspection_data as $medicines)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            {{ getMedicinename($medicines['medicine_id']) }}</td>
                                                        <td class="text-center">{{ $medicines['freeze_quantity'] }}</td>
                                                        <td class="text-center">{{ $medicines['available_quantity'] }}</td>
                                                        <td class="text-center">
                                                            {{ Displaydateformat($medicines['expired_date']) }}</td>
                                                        <td class="text-center">{{ ($medicines['emp_id']) }}
                                                        </td>
                                                        <td class="text-center">{{ $medicines['remarks'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- <div class="row m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                            <img src="{{ admin_url($signature) }}"
                                                alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                        </div>
                                    </div> --}}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop
