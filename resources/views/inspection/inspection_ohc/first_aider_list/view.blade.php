@extends('admin.layouts.admin')
@section('title', 'First Aider List')
@section('pageurl', admin_url('ohc/first-aider/list'))

@push('style')
    <style>
        .view_label {
            display: block;

        }

        .image-wrapper {
            display: inline-block;
            margin: 5px;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
@endpush


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            {{-- <h4 class="text-black">{{ __('Company Show') }}</h4> --}}

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
                                    <x-button-back href="{{ admin_url('ohc/first-aider/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Daily Departmental First Aid Box</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Document Number</label>
                                        <div class="view_data">
                                            {{ isset($first_aider->doc_no) ? $first_aider->doc_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Issue Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($first_aider->issue_date) ? $first_aider->issue_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Review Date</label>
                                        <div class="view_data">
                                            {{ isset($first_aider->revision_date) ? $first_aider->revision_date : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Last Updated Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($first_aider->last_updated_date) ? $first_aider->last_updated_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Next Review  Date</label>
                                        <div class="view_data">
                                            {{ displayDateformat(isset($first_aider->next_review_date) ? $first_aider->next_review_date : '') }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($first_aider->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($first_aider->created_at) }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($first_aider->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <div class="col-md-12">
                                        <table class="table table-bordered ">

                                            <thead class="bg-secondary" style="color: #ffff">
                                                <tr>
                                                    <th>S.NO</th>
                                                    <th>Unit</th>
                                                    <th>Department</th>
                                                    <th>Employee Name</th>
                                                    <th>Designation</th>
                                                    <th>Mobile Number</th>
                                                   

                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($first_aider_details->isEmpty())
                                                    <tr>
                                                        <td colspan="4" class="text-center">No data is available</td>
                                                    </tr>
                                                @else
                                                    @foreach ($first_aider_details as $data)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ getUnitname($data->unit_id) }}</td>
                                                            <td>{{ getDepartment($data->department_id) }}</td>
                                                            <td>{{getEmployeename( $data->emp_id) }}</td>
                                                            <td>{{ ($data->designation_id) }}</td>
                                                            <td>{{ $data->mobile_no }}</td>

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
        </div>
        </form>
    </div>

@stop
