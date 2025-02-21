@extends('admin.layouts.admin')
@section('title', 'Monthly Inventory Report')
@section('pageurl', admin_url('ohc/monthly-inventory/list'))
@section('content')
    @push('style')
        <style>
            .table-responsive {
                overflow-x: auto;
                width: 100%
            }
        </style>
    @endpush
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <h4 class="card-title"></h4>
                    <div class="d-flex justify-content-end p-2 me-2">


                    </div>



                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3 form-input">
                                <label for="medicine_name" class="form-label ">Unit Name</label>
                                <select name="unit_id" id="unit_id" class="form-control single-select form-control-sm"
                                    style="width: 100%">
                                    <option value="">Select the Medicine Name</option>
                                    @foreach ($unit as $list)
                                        <option value="{{ $list->id }}">{{ $list->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3 form-input">
                                <label for="emp_name" class="form-label ">Year</label>
                                <div class="input-group date form-input custom-height">
                                    <input type="text" class="form-control " name="year" id="year"
                                        autocomplete="off">
                                    <div class="input-group-addon input-group-text">
                                        <span class="fa fa-calendar"></span>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-3 mb-3 form-input">
                                <label for="emp_name" class="form-label ">Month</label>
                                <div class="input-group date form-input  custom-height">
                                    <input type="text" class="form-control " name="month" id="month"
                                        autocomplete="off">
                                    <div class="input-group-addon input-group-text">
                                        <span class="fa fa-calendar"></span>
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
