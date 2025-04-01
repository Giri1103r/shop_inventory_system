@extends('admin.layouts.admin')
@section('title', 'Monthly OHC First-Aid Medicine Inspection Checklist')
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
                                <div class="align-back-btc">
                                    <x-button-back
                                        href="{{ admin_url('ohc/first-aid/opd-medicine-inspection/list') }}"></x-button-back>
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
                                                    {{ Displaydateformat($inspection_details->inspection_date) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->next_due) }}
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Name Of Inspection</th>
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
                                                            {{ getMedicinename($medicines['medicine_id']) }}
                                                        <td class="text-center">{{ $medicines['available_quantity'] }}
                                                        <td class="text-center">
                                                            {{ Displaydateformat($medicines['expired_date']) }}
                                                        <td class="text-center">{{ getUsername($medicines['emp_id']) }}
                                                        <td class="text-center">{{ $medicines['remarks'] }}
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row m-2">
                                        <div class="col-md-4 form-group form-input mb-2">
                                            <label class="form-label"
                                                style="display: block; ">{{ __('inspection.signature') }}</label>
                                            <img src="{{ admin_url($inspection_file) }}"
                                                alt="Signature Upload" style="width: 100px; margin-top:-10px">
                                        </div>
                                    </div>
                                    <div class="row">
                                        @if (isset($inspection_details->approval_remarks))
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Approval</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label ">{{ __('inspection.name') }}</label>
                                                        <div class="view_data">
                                                            {{ getUserName($inspection_details->updated_by) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label ">{{ __('inspection.date') }}</label>
                                                        <div class="view_data">
                                                            {{ Displaydateformat($inspection_details->created_at) }}
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="col-md-4 mb-2">
                                                        <div class="form-group form-input">
                                                            <label class="form-label"
                                                                style="display: block;">{{ __('inspection.signature') }}</label>
                                                            <img src="{{ admin_url($verified_by) }}" alt="Signature Upload"
                                                                style="width: 100px; margin-top: -10px;" />
                                                        </div>
                                                    </div>

                                                <div class="form-group form-input">
                                                    <label class="form-label ">{{ __('inspection.remarks') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection_details->approval_remarks }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop
