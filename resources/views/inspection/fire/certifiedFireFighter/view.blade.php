@extends('admin.layouts.admin')
@section('title', 'Certified Fire Fighter')
@section('pageurl', admin_url('fire/certified-fire-fighter/list'))


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
                                {{-- <h4 class="card-title">{{ __('master.company_add') }}</h4> --}}
                                <div class="align-back-btc">
                                    <x-button-back href="{{ admin_url('fire/certified-fire-fighter/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body">

                                <div class="basic-form">

                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">Certified Fire Fighter</h4>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Certified Fire Fighter No</label>
                                                <div class="view_data">
                                                    {{ isset($fireData->fire_no) ? $fireData->fire_no : '' }}
                                                </div>

                                            </div>
                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Doc. No</label>
                                                <div class="view_data">
                                                    {{ isset($staticDocno->doc_no) ? $staticDocno->doc_no : '' }}
                                                </div>
                                            </div>

                                            <div class="col-md-4 form-input">
                                                <label class="form-label">Issue Dt.</label>
                                                <div class="view_data">
                                                    {{ Displaydateformat($staticDocno->issue_date) }}
                                                </div>

                                            </div>

                                            <div class="col-md-4 form-input mt-2">
                                                <label class="form-label">Rev. & Dt.</label>
                                                <div class="view_data">
                                                    {{ isset($staticDocno->rev_dt) ? $staticDocno->rev_dt : '' }}
                                                </div>

                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                                <div class="view_data">
                                                    {{ getusername($fireData->created_by) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                                <div class="view_data">
                                                    {{ displayDateformat($fireData->created_at) }}
                                                </div>
                                            </div>
                                            <div class="mb-3 col-md-4 form-input">
                                                <label class="form-label view_label">{{ __('common.status') }}</label>
                                                <div class="view_data">
                                                    @if ($fireData->status == 1)
                                                        {{ __('common.active') }}
                                                    @else
                                                        {{ __('common.inactive') }}
                                                    @endif

                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="card-header-inner d-flex justify-content-between">
                                                <h4 class="text-white">Certified Fire Fighter Details</h4>
                                            </div>
                                        </div>

                                        <div id="lesson_learned_block">
                                            @foreach ($certifiedFireDataList as $certifiedFireData)
                                                <div class="row lesson_learned_row" style="margin-top: 20px;">

                                                    <div class="col-md-4 form-input">
                                                        <label class="form-label">SR NO</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->sr_no ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Name</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->emp_name ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input">
                                                        <label for="" class="form-label">Department</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->department_name ?? '-' }}
                                                        </div>
                                                    </div>


                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Emp Code</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->emp_code ?? '-' }}
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Contact Number</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->emp_phone ?? '-' }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 form-input mt-2">
                                                        <label class="form-label">Status</label>
                                                        <div class="view_data">
                                                            {{ $certifiedFireData->emp_status == 1 ? 'Active' : 'Not Active' }}

                                                        </div>
                                                    </div>


                                                </div>
                                                <hr>
                                            @endforeach
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
