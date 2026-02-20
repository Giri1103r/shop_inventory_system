@extends('admin.layouts.admin')
@section('title', 'Weekly First Aid  Checklist')
@section('pageurl', admin_url('ohc/first-aid-box/weekly-inspection/list'))
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
                                        href="{{ admin_url('ohc/first-aid-box/weekly-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">


                                    <div class="row">

                                        <div class="col-md-4 mb-2">
                                            <label
                                                class="form-label view_label">Documnet Number</label>
                                            <div class="view_data">
                                                {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                            </div>
                                        </div>



                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label ">Issue Date</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">Revision Date</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label "> Date of Inspection   </label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection_details->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Location </label>
                                                <div class="view_data">
                                                    {{ getLocationname(isset($inspection_details->location) ? $inspection_details->location : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Unit </label>
                                                <div class="view_data">
                                                    {{ getUnitname(isset($inspection_details->unit) ? $inspection_details->unit : '') }}
                                                </div>
                                            </div>
                                        </div>

                                         <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">First Aid Box No</label>
                                                <div class="view_data">
                                                    {{ isset($inspection_details->first_aid_box_no) ? $inspection_details->first_aid_box_no : '' }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">First Aider Name</label>
                                                <div class="view_data">
                                                    {{ getFirstAider(isset($inspection_details->first_aider) ? $inspection_details->first_aider : '') }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label">Shift</label>
                                                <div class="view_data">
                                                    {{ getShift(isset($inspection_details->shift) ? $inspection_details->shift : '') }}
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Medicine Name</th>
                                                <th style="text-align: center">Available Quantity</th>
                                                <th style="text-align: center">Expiry Date</th>
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
                                                        <td class="text-center">{{ $medicines['remarks'] }}
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

                                    <div class="col-md-12 mt-2">
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


    @stop
