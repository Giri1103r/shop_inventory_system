@extends('admin.layouts.admin')
@section('title', 'Medicine Receiving Edit')
@section('pageurl', admin_url('ohc/medicine-receiving-form/list'))
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
                                        href="{{ admin_url('ohc/medicine-receiving-form/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="basic-form">
                                    <form method="POST" id="ppeExemptionForm" enctype="multipart/form-data"
                                        action="{{ admin_url('ohc/medicine-receiving-form/edit/submit') }}">
                                        @csrf
                                        <input type="hidden" name="id" id="id"
                                        value="{{ encryptId($medicine_receiving->id) }}">
                                        <hr>
                                        <div class="row">

                                            <div class="col-md-4 mb-2">
                                                <label for="medicine_name" class="form-label require ">Medicine Name</label>
                                                <select name="medicine_id" id="medicine_id"
                                                    class="form-control form-control-sm" style="width: 100%">
                                                    <option value="">Select the Medicine Name</option>
                                                    @foreach ($medicine as $list)
                                                        <option value="{{ $list->id }}" @if($medicine_receiving->medicine_id == $list->id) selected @endif>{{ $list->medicine }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="hsn_id" class="form-label require ">HSN Number</label>
                                                <input type="text" name="hsn_id" id="hsn_id" class="form-control"
                                                    readonly>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="pack_id" class="form-label require ">Pack Detatils</label>
                                                <select name="pack_id" id="pack_id" class="form-control form-control-sm"
                                                    style="width: 100%">
                                                    <option value="">Select the Pack</option>
                                                    @foreach ($medicine as $list)
                                                        <option value="{{ $list->id }}"@if($medicine_receiving->pack_id == $list->id) selected @endif>{{ $list->pack }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="quantity" class="form-label require ">Quantity</label>
                                                <input type="text" name="quantity" id="quantity" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="batch_number" class="form-label require ">Batch Number</label>
                                                <input type="text" name="batch_number" id="batch_number"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="rate" class="form-label require ">Rate</label>
                                                <input type="text" name="rate" id="rate" class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="rate" class="form-label require ">Expire Date</label>
                                                <input type="text" name="expire_date" id="expire_date"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <label for="vendor_name" class="form-label require ">Vendor Name</label>
                                                <select name="vendor_id" id="vendor_id" class="form-control form-control-sm"
                                                    style="width: 100%">
                                                    <option value="">Select the Vendor Name</option>
                                                    @foreach ($vendor as $list)
                                                        <option value="{{ $list->id }}"@if($medicine_receiving->vendor_id == $list->id) selected @endif>{{ $list->vendor_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>


                                        </div>
                                        <hr>
                                        <div class="submit-button float-end">
                                            <x-button-submit class="submit" id="submit"></x-button-submit>
                                            <x-button-reset class="submit"></x-button-reset>
                                            <x-button-cancel href="{{ admin_url('ppe_exemption/list') }}"></x-button-cancel>
                                        </div>
                                    </form>
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

@push('script')
    <script>
        $(document).ready(function() {
            var fromDatepicker = flatpickr("#expire_date", {
                dateFormat: "d-m-Y",
                minDate: new Date(),

            });
        });
    </script>
@endpush
