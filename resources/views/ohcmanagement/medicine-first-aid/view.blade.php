
@extends('admin.layouts.admin')
@section('title', 'Medicine First Aid Show')
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
                                    <x-button-back href="{{ admin_url('ohc/medicine-first-aid/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Medicine Issuance</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname(isset($user_medicine_first_aid->unit_id) ? $user_medicine_first_aid->unit_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment(isset($user_medicine_first_aid->department_id) ? $user_medicine_first_aid->department_id : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Issued date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_first_aid->issue_date) ? $user_medicine_first_aid->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created at') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($user_medicine_first_aid->created_by) ? $user_medicine_first_aid->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($user_medicine_first_aid->created_at) ? $user_medicine_first_aid->created_at : '') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">

                                            <thead class="bg-secondary" style="color: #ffff">
                                            <tr>
                                                <th>S.No</th>
                                                <th>Medicine Name</th>
                                                <th>Available Quantity</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($medicine_first_aid->isEmpty())
                                                <tr>
                                                    <td colspan="4" class="text-center">No data is available</td>
                                                </tr>
                                            @else
                                                @foreach ($medicine_first_aid as $data)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getMedicinename($data->medicine_id) }}</td>
                                                        <td>{{$data->available_quantity}}</td>
                                                        <td>{{$data->quantity}}</td>
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
