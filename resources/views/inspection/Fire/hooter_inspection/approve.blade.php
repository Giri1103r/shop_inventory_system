@extends('admin.layouts.admin')
@section('title', 'Hooter Inspection Approve')
@section('pageurl', admin_url('fire/hooter-inspection/list'))
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
                                    <x-button-back href="{{ admin_url('fire/hooter-inspection/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="basic-form mx-3">


                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.doc_no') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection->doc_no }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.issue_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->issue_date) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.rev_date') }}</label>
                                                <div class="view_data">
                                                    {{ $inspection->revision_data }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.inspection_date') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->date_of_inspection) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.location') }}</label>
                                                <div class="view_data">
                                                    {{ getLocationname($inspection->location) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">Shift</label>
                                                <div class="view_data">
                                                    {{ getShiftName($inspection->shift) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.next_due') }}</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($inspection->next_due) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.unit') }}</label>
                                                <div class="view_data">
                                                    {{ getUnitname($inspection->unit) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label class="form-label require">{{ __('inspection.frequency') }}</label>
                                                <div class="view_data">
                                                    {{ getFrequencyname($inspection->frequency) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-group form-input">
                                                <label
                                                    class="form-label require">{{ __('inspection.upload_image') }}</label>
                                                <div class="view_data">
                                                    <img src="{{ admin_url($inspection_image) }}"
                                                        style="width:50px; height:50px;" alt="" srcset="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-observation">
                                        <div class="row mt-4 form-obs">
                                            <div class="card-header-inner p-2">
                                                <h4 class="text-white">Hooter Inspection Observation</h4>
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-group form-input">
                                                    <label class="form-label require">{{ __('inspection.obs') }}</label>
                                                    <div class="view_data">
                                                        {{ $inspection->observation }}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                    @foreach ($inspection_details as $details)
                                        <div class="form-wrapper">
                                            <div class="row mt-4 form-set">
                                                <div class="card-header-inner p-2">
                                                    <h4 class="text-white">Hooter Inspection Checklist</h4>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.sr_no') }}</label>
                                                        <div class="view_data">
                                                            {{ $details->sr_no }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.resource_code') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->resource_code }}
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.department') }}</label>
                                                            <div class="view_data">
                                                                {{ GetDeptName($details->department) }}
                                                            </div>
                                                        
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.check_items') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->condition_of_hooter }}
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.quantity') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->quantity }}
                                                            </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.remarks') }}</label>
                                                            <div class="view_data">
                                                                {{ $details->remarks }}
                                                            </div>

                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-group form-input">
                                                        <label
                                                            class="form-label require">{{ __('inspection.checked_obs') }}</label>
                                                        <div class="mt-1">
                                                            <div class="form-check form-check-inline">
                                                                <div class="view_data">
                                                                    @if($details->blinking_light == 1)
                                                                        <span style="color: green;">&#10004;</span>
                                                                    @else
                                                                        <span style="color: red;">&#10060;</span>
                                                                    @endif
                                                                </div>
                                                                <label class="form-check-label"
                                                                    for="blinking_light">Blinking
                                                                    Light</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <div class="view_data">
                                                                    @if($details->connection == 1)
                                                                        <span style="color: green;">&#10004;</span>
                                                                    @else
                                                                        <span style="color: red;">&#10060;</span>
                                                                    @endif
                                                                </div>
                                                                <label class="form-check-label"
                                                                    for="connection">Connection</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <div class="view_data">
                                                                    @if($details->audiobility == 1)
                                                                        <span style="color: green;">&#10004;</span>
                                                                    @else
                                                                        <span style="color: red;">&#10060;</span>
                                                                    @endif
                                                                </div>
                                                                <label class="form-check-label"
                                                                    for="auditbility">Audibility</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                


                                            </div>
                                        </div>
                                    @endforeach
                                    <hr>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    @stop
