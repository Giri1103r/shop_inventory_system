@extends('admin.layouts.admin')
@section('title', 'Medicine Requisition Show')
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-requisition/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medicine Requisition</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Requisition ID') }}</label>
                                        <div class="view_data">
                                            {{ isset($user_medicine_requisition->req_id) ? $user_medicine_requisition->req_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($user_medicine_requisition->unit_id) ? $user_medicine_requisition->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($user_medicine_requisition->department_id) ? $user_medicine_requisition->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Request date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_requisition->request_date) ? $user_medicine_requisition->request_date : '') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table view_card ">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Medicine Name</th>
                                                <th>Quantity</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($medicine_requisition->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center">No data is available</td>
                                                </tr>
                                            @else
                                                @foreach ($medicine_requisition as $data)
                                                    <tr>
                                                        <td>{{$data->id}}</td>
                                                        <td>{{$data->medicine}}</td>
                                                        <td>{{$data->quantity}}</td>
                                                        <td>{{$data->remarks}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
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
