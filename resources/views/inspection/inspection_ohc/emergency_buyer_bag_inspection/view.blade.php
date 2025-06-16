@extends('admin.layouts.admin')
@section('title', 'Emergency First Aid Bag Checklist')
@section('pageurl', admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list'))
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
                                        href="{{ admin_url('ohc/emergency-buyer-first-aid-bag/checklist/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">

                                    <div class="row">

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Date of Inspection</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($inspection_details->date_of_inspection) ? $inspection_details->date_of_inspection : '') }}

                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Location First Aid Bag</label>
                                                <div class="view_data">
                                                    {{ isset($inspection_details->location_first_aid_bag) ? $inspection_details->location_first_aid_bag : '' }}

                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Shift</label>
                                                <div class="view_data">
                                                    {{ getShift(isset($inspection_details->shift_id) ? $inspection_details->shift_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Due Date</label>
                                                <div class="view_data">
                                                    {{ displaydateformat(isset($inspection_details->due_date) ? $inspection_details->due_date : '') }}

                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Unit ID</label>
                                                <div class="view_data">
                                                    {{ getUnitname(isset($inspection_details->unit_id) ? $inspection_details->unit_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Frequency</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname(isset($inspection_details->frequency_id) ? $inspection_details->frequency_id : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">{{ __('common.sno') }}</th>
                                                <th style="text-align: center">{{ __('ohc_management.medicine_name') }}</th>
                                                <th style="text-align: center">{{ __('ohc_management.freeze_quantity') }}
                                                </th>
                                                <th style="text-align: center">
                                                    {{ __('ohc_management.available_quantity') }}</th>
                                                <th style="text-align: center">{{ __('ohc_management.expiry_date') }}</th>
                                                <th style="text-align: center">{{ __('common.remarks') }}</th>
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
                                                        <td class="text-center">{{ $medicines['remarks']  }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- <div class="row m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                                <img src="{{ admin_url($inspection_file) }}"
                                                alt="Signature Upload" style="width: 100px; margin-top:-10px">

                                        </div>
                                    </div> --}}

                                    <div class="col-md-4 mt-3 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label">Remark By</label>
                                            <div class="view_data">
                                                {{ isset($inspection_details->remark_by) ? $inspection_details->remark_by : '' }}
                                            </div>
                                        </div>
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
