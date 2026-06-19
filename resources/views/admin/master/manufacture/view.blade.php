@extends('admin.layouts.admin')
@section('title', 'Manufacture Show')
@section('pageurl', admin_url('master/manufacture/list'))

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
                                <x-button-back href="{{ admin_url('master/manufacture/list') }}"></x-button-back>
                            </div>
                            <div class="card-body ">

                                <div class="row">
                                    <div class="card-header-inner">
                                        <h4 class="text-white">{{ __('Manufacture Details') }}</h4>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Manufacture Id</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->manufacture_id ) ? $manufacture->manufacture_id  : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Manufacture Name</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->manufacturer_name) ? $manufacture->manufacturer_name : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">License Number</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->license_number) ? $manufacture->license_number : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Contact Person</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->contact_person) ? $manufacture->contact_person : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Mobile Number</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->mobile_no) ? $manufacture->mobile_no : '' }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Email</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->email) ? $manufacture->email : '' }}
                                        </div>
                                    </div>
                                    @if(!empty($manufacture->address))
                                         <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">Address</label>
                                        <div class="view_data">
                                            {{ isset($manufacture->address) ? $manufacture->address : '' }}
                                        </div>
                                    </div>
                                    @endif
                                   

                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_by') }}</label>
                                        <div class="view_data">
                                            {{ getusername($manufacture->created_by) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.created_date') }}</label>
                                        <div class="view_data">
                                            {{ displayDateformat($manufacture->created_at) }}
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-4 form-input">
                                        <label class="form-label view_label">{{ __('common.status') }}</label>
                                        <div class="view_data">
                                            @if ($manufacture->status == 1)
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
