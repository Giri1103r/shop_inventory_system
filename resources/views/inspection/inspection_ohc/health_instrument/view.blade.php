@extends('admin.layouts.admin')
@section('title', 'Health Instrument Calibration')
@section('pageurl', admin_url('ohc/first-aider/list'))

@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center"></div>
    </div>

    <div class="content-body default-height">
        <div class="container-fluid main-content">
            <div class="row">
                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('ohc/health-instrument/calibration-track-sheet/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">
                              
                                    @if ($health_instrument_calibration_details->isNotEmpty())
                                        @php
                                            $health_details = $health_instrument_calibration_details->first();
                                        @endphp
                                    
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white">Health Instrument Calibration</h4>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Health Instrument ID</label>
                                                    <div class="view_data">
                                                        {{ isset($health_details->health_auto_id) ? $health_details->health_auto_id : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Unit ID</label>
                                                    <div class="view_data">
                                                        {{ getUnitname(isset($health_details->unit_id) ? $health_details->unit_id : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label"> Document No</label>
                                                    <div class="view_data">
                                                        {{ isset($document_no->doc_no) ? $document_no->doc_no : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                    
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Issue Date</label>
                                                    <div class="view_data">
                                                        {{ displaydateformat(isset($document_no->issue_date) ? $document_no->issue_date : '') }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label">Revision Date</label>
                                                    <div class="view_data">
                                                        {{ isset($document_no->rev_dt) ? $document_no->rev_dt : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <div class="row">
                                            <div class="card-header-inner">
                                                <h4 class="text-white"> Health Instrument Calibration Details</h4>
                                            </div>
                                        </div>
                                    
                                        @foreach ($health_instrument_calibration_details as $health_details)
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label"> Serial No</label>
                                                        <div class="view_data">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Instrument Name</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->instrument_name) ? $health_details->instrument_name : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Resource Code</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->resource_code) ? $health_details->resource_code : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Exact Location</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->exact_location) ? $health_details->exact_location : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                               
                                    
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Instrument Serial No</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->instrument_serial_no) ? $health_details->instrument_serial_no : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Make</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->make) ? $health_details->make : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Model</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->model) ? $health_details->model : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Instrument Range</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->instrument_range) ? $health_details->instrument_range : '' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Calibration Frequency</label>
                                                        <div class="view_data">
                                                            {{ getFrequencyname(isset($health_details->calibration_frequency) ? $health_details->calibration_frequency : '') }}
                                                        </div>
                                                    </div>
                                                </div>
                                    
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Date of Calibration</label>
                                                        <div class="view_data">
                                                            {{ displaydateformat(isset($health_details->date_of_calibration) ? $health_details->date_of_calibration : '') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Next Due Date</label>
                                                        <div class="view_data">
                                                            {{ displaydateformat(isset($health_details->due_date_of_calibration) ? $health_details->due_date_of_calibration : '') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label class="form-label">Remarks</label>
                                                        <div class="view_data">
                                                            {{ isset($health_details->remarks) ? $health_details->remarks : '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                        @endforeach
                                    @endif

                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop