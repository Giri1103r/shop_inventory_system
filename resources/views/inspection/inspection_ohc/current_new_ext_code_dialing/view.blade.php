@extends('admin.layouts.admin')
@section('title', 'Current New Ext Code Dailing')
@section('pageurl', admin_url('ohc/current-new-ext-code-dialing/list'))

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
                                    <x-button-back
                                        href="{{ admin_url('ohc/current-new-ext-code-dialing/list') }}"></x-button-back>
                                </div>
                            </div>


                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">Code Dailing Deatils</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Unit Name</label>
                                        <div class="view_data">
                                            {{ isset($current_new_ext_code->unit_name) ? $current_new_ext_code->unit_name : '' }}
                                        </div>
                                    </div>

                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Department Name</label>
                                        <div class="view_data">
                                            {{ isset($current_new_ext_code->department_name) ? $current_new_ext_code->department_name : '' }}
                                        </div>
                                    </div>  

                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Employee Name</label>
                                        <div class="view_data">
                                            {{ isset($current_new_ext_code->emp_name) ? $current_new_ext_code->emp_name : '' }}
                                        </div>
                                    </div> 

                                    <div class="mb-3 col-md-4 form-input">
                                        <label
                                            class="form-label view_label">Dailing Number</label>
                                        <div class="view_data">
                                            {{ isset($current_new_ext_code->number) ? $current_new_ext_code->number : '' }}
                                        </div>
                                    </div> 

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($current_new_ext_code->created_by) }}
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($current_new_ext_code->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($current_new_ext_code->status == 1)
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
