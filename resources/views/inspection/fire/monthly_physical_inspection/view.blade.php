@extends('admin.layouts.admin')
@section('title', 'Fire Equipment Monthly Physical Inspection')
@section('pageurl', admin_url('fire/equipment-monthly-physical-inspection/list'))
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
                                        href="{{ admin_url('fire/equipment-monthly-physical-inspection/list') }}"></x-button-back>
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
                                                    {{ Displaydateformat($inspection_details->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($document_no->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label ">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $document_no->rev_dt }}
                                                </div>
                                            </div>
                                        </div>


                                        <table class="table table-bordered table-striped mb-3">
                                            <thead class="table-secondary">
                                                <th style="text-align: center">Sr. No.</th>
                                                <th style="text-align: center">Equipment Name</th>
                                                <th style="text-align: center">Frequency</th>
                                                <th style="text-align: center">Status</th>
                                                <th style="text-align: center">Remarks</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($inspection_data as $medicines)
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration }}</td>
                                                        <td class="text-center">
                                                            {{ getMonthlyInspectionEquipmentname($medicines['id']) }}</td>
                                                        <td class="text-center">{{ getFrequencyname($medicines['frequency']) }}</td>
                                                        <td class="text-center">{{ $medicines['status'] }}</td>
                                                        <td class="text-center">{{ $medicines['remarks'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        @foreach ($images as $index => $image)
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">{{ getMonthlyInspectionEquipmentname($index) }}
                                                </h4>
                                            </div>
                                            @foreach ($image as $img)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">

                                                        <img src="{{ admin_url($img->file_path) }}" alt=" Upload"
                                                            style="width: 100px; margin-top: -10px;" />
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


    @stop
