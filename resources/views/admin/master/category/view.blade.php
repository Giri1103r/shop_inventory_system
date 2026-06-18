@extends('admin.layouts.admin')
@section('title', 'Category Show')
@section('pageurl', admin_url('master/category/list'))

@push('style')
    <style>
        .card-header {
            position: relative;
        }

        .align-back-btc d-flex justify-content-end align-items-center {
            display: flex;
        }

        @media (max-width: 480px) {
            .align-back-btc d-flex justify-content-end align-items-center {
                width: 100%;
            }

            .align-back-btc d-flex justify-content-end align-items-center x-button-back,
            .align-back-btc d-flex justify-content-end align-items-center button {
                width: auto;
                max-width: 100%;
            }
        }
    </style>
@endpush
@section('content')
    <div class="clearfix"></div>
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


                            <div class="card-header d-flex justify-content-end align-items-center">
                                <x-button-back href="{{ admin_url('master/category/list') }}"></x-button-back>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('Category Details') }}</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.category_id') }}</label>
                                        <div class="view_data">
                                            {{ isset($category->category_id) ? $category->category_id : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.category_code') }}</label>
                                        <div class="view_data">
                                            {{ isset($category->category_code) ? $category->category_code : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.category_name') }}</label>
                                        <div class="view_data">
                                            {{ isset($category->category_name) ? $category->category_name : '' }}
                                        </div>
                                    </div>
                                    @if(!empty($category->description))
                                         <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.description') }}</label>
                                        <div class="view_data">
                                            {{ isset($category->description) ? $category->description : '' }}
                                        </div>
                                    </div>
                                    @endif
                                   

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($category->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($category->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($category->status == 1)
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
