@extends('admin.layouts.admin')
@section('title', 'MSDS ')
@section('pageurl', admin_url('msds/list'))

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
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('msds/list') }}"></x-button-back>

                                </div>
                            </div>

                            <div class="card-body ">
                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('inspection.msds') }}</h4>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.doc_no') }}</label>
                                        <div class="view_data">
                                            {{ $document_no->doc_no }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.issue_date') }}</label>
                                        <div class="view_data">
                                            {{ Displaydateformat($document_no->issue_date) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('inspection.rev_date') }}</label>
                                        <div class="view_data">
                                            {{ $document_no->rev_dt }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.location') }}</label>
                                        <div class="view_data">
                                            {{ getLocationname($msds->location_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.unit') }}</label>
                                        <div class="view_data">
                                            {{ getUnitname($msds->unit_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.department') }}</label>
                                        <div class="view_data">
                                            {{ getDepartment($msds->department_id) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getUsername(isset($msds->created_by) ? $msds->created_by : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displaydateformat(isset($msds->created_at) ? $msds->created_at : '') }}
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @foreach ($inspection_details as $msdsDetails)
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="card-header-inner">
                                            <h4 class="text-white">MSDS CheckList</h4>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('inspection.ser_no') }}</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->serial_number) ? $msdsDetails->serial_number : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('inspection.item_code') }}</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->item_code) ? $msdsDetails->item_code : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('inspection.name_of_chemical') }}</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->name_of_chemical) ? $msdsDetails->name_of_chemical : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('Storage Capacity') }}</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->storage_capacity) ? $msdsDetails->storage_capacity : '' }}
                                            </div>
                                        </div>
                                        {{-- <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">{{ __('NPFA Rating Type') }}</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->nfa_rating) ? getNFARating($msdsDetails->nfa_rating) : '' }}
                                            </div>
                                        </div> --}}
                                        @php
                                            $nfaRatings = json_decode($msdsDetails->nfa_rating, true);
                                        @endphp


                                        <div class="mb-3 col-md-4 form-input">
                                            <label
                                                class="form-label view_label">{{ __('inspection.msds_avl_sts') }}</label>
                                            <div class="view_data">
                                                @if ($msdsDetails->msds_availability_status == YES)
                                                    YES
                                                @elseif($msdsDetails->msds_availability_status == NO)
                                                    NO
                                                @endif
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Type of Chemical</label>
                                            <div class="view_data">
                                                {{ $msdsDetails->type_of_chemical == 1 ? 'Hazardous' : 'Non-Hazardous' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">File Upload</label>
                                            <div class="view_data">
                                                @if (!empty($msdsDetails->msds_file))
                                                    <a href="{{ asset($msdsDetails->msds_file->file_path) }}"
                                                        target="_blank" class="d-block mt-2">
                                                        <i class="fa-solid fa-eye text-danger"></i> View
                                                    </a>
                                                @else
                                                    <small class="text-muted">No file uploaded yet.</small>
                                                @endif

                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <label class="form-label view_label">Remark</label>
                                            <div class="view_data">
                                                {{ isset($msdsDetails->remark) ? $msdsDetails->remark : '' }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-4 form-input">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>NFPA Rating</th>
                                                        <th>Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($nfaRatings as $rating)
                                                        <tr>
                                                            <td>{{ getNFARating($rating['id']) }}</td>
                                                            <td>{{ $rating['value'] !== null && $rating['value'] !== '' ? $rating['value'] : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
