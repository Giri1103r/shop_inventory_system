@extends('admin.layouts.admin')
@section('title', 'Checklist Sub Type Data')
@section('pageurl', admin_url('inspection/master/checklist-sub-type-data/list'))


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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-sub-type-data/list') }}"></x-button-back>

                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Checklist Sub Type Data Details</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Checklist Type Name</label>
                                        <div class="view_data">
                                            {{ isset($checklist_type->category_name) ? $checklist_type->category_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Checklist Sub Type Name</label>
                                        <div class="view_data">
                                            {{ isset($checklist_type->subcategory_name) ? $checklist_type->subcategory_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($checklist_type->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($checklist_type->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($checklist_type->status == 1)
                                                {{ __('common.active') }}
                                            @else
                                                {{ __('common.inactive') }}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="card p-3">

                                        <!-- Table -->
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>Checklist Sub-Type Data Name</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="checklistBody">
                                                    @foreach ($checklistSubTypeDataNameList as $dataNameList)
                                                        <tr id="RowchecklistView0">
                                                            <td style="width: 40%;">
                                                                <div class="view_data">
                                                                    {{ isset($dataNameList->name) ? $dataNameList->name : '' }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="view_data">
                                                                    {{ isset($dataNameList->description) ? $dataNameList->description : '' }}
                                                                </div>
                                                            </td>

                                                        </tr>
                                                    @endforeach
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
        </div>
    </div>

@stop
