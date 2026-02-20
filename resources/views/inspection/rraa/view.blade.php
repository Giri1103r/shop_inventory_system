@extends('admin.layouts.admin')
@section('title', 'RRAA')
@section('pageurl', admin_url('rraa/ohc_fire_environment_compliance/list'))

@section('content')

    <style>
        .card-header-inner {
            padding: 11px;
        }
    </style>

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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('rraa/ohc_fire_environment_compliance/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">RRAA Details</h4>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ $document_no->doc_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Issue Date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($document_no->issue_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Revision & Data') }}</label>
                                        <div class="view_data">
                                            {{ $document_no->rev_dt }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($rraa_details->created_at) ? $rraa_details->created_at : '') }}
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">RRAA CheckList</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Serial Number') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->serial_number) ? $rraa_details->serial_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Category') }}</label>
                                        <div class="view_data">
                                            {{ getCategoryname($rraa_details->category) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('OHS Compliance Index(Role)') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->ohs_compliance_index) ? $rraa_details->ohs_compliance_index : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Frequency') }}</label>
                                        <div class="view_data">
                                            {{ getFrequencyname($rraa_details->frequency) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Scope') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->scope) ? $rraa_details->scope : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Responsibility') }}</label>
                                        <div class="view_data">
                                            {{ getUsername($rraa_details->responsibility) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Authority') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->authority) ? $rraa_details->authority : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Accountability') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->accountability) ? $rraa_details->accountability : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">File</label>
                                        @if ($get_rraa_file)
                                            <p>

                                                @if (in_array(strtolower($get_rraa_file->file_extension), ['pdf', 'xls', 'xlsx', 'docx', 'doc']))
                                                    <a href="{{ asset($get_rraa_file->file_path) }}" target="_blank">
                                                        <i class="fas fa-eye text-danger"></i> View
                                                    </a>
                                                @endif

                                            </p>
                                        @else
                                            <p>No file is uploaded</p>
                                        @endif
                                    </div>


                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Remark') }}</label>
                                        <div class="view_data">
                                            {{ isset($rraa_details->remark) ? $rraa_details->remark : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($rraa_details->created_by) ? $rraa_details->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('Created Date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($rraa_details->created_at) ? $rraa_details->created_at : '') }}
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
