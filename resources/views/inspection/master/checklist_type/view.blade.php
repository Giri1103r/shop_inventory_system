@extends('admin.layouts.admin')
@section('title', 'Checklist Type')
@section('pageurl', admin_url('inspection/master/checklist-type/list'))

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
                                <div class="align-back-btc d-flex justify-content-end align-items-center">
                                    <x-button-back
                                        href="{{ admin_url('inspection/master/checklist-type/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Checklist Type</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('inspection.checklist_type_id') }}</label>
                                        <div class="view_data">
                                            {{ isset($checklist_type->category_id) ? $checklist_type->category_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">{{ __('inspection.checklist_type_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($checklist_type->category_name) ? $checklist_type->category_name : '' }}
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
                                    @if ($checklist_images)
                                        <div class="mb-3 col-md-4 form-input custom-image-container">
                                            <label class="form-label view_label">{{ __('inspection.image') }}</label>
                                            <div class="image-wrapper">
                                                <img src="{{ admin_url($checklist_images->file_path) }}"
                                                    alt="Checklist Type" class="img-fluid custom-image"
                                                    style=" height:80px; width:130px" />
                                            </div>
                                        @else
                                        <div class="mb-3 col-md-4 form-input custom-image-container">
                                            <label class="form-label view_label">{{ __('inspection.image') }}</label>
                                            <div class="image-wrapper">
                                                <div class="view_data">No Image Uploaded</div>
                                            </div>
                                    @endif
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
    </div>

@stop
