@extends('admin.layouts.admin')
@section('title', 'Location Type Add')
@section('pageurl', admin_url('location_type/list'))


@section('content')
    <div class="clearfix"></div>
    <div class="page-titles">
        <div class="d-flex align-items-center">
            <h2 style="color:#0D173F">{{ __('administration.role') }}</h2>

        </div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active ms-auto">
                <a class="d-flex align-self-center" href="{{ admin_url('dashboard') }}">
                    <svg class="me-2 svg-main-icon" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24"
                        version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"></rect>
                            <path
                                d="M3.95709826,8.41510662 L11.47855,3.81866389 C11.7986624,3.62303967 12.2013376,3.62303967 12.52145,3.81866389 L20.0429,8.41510557 C20.6374094,8.77841684 21,9.42493654 21,10.1216692 L21,19.0000642 C21,20.1046337 20.1045695,21.0000642 19,21.0000642 L4.99998155,21.0000673 C3.89541205,21.0000673 2.99998155,20.1046368 2.99998155,19.0000673 L2.99999828,10.1216672 C2.99999935,9.42493561 3.36258984,8.77841732 3.95709826,8.41510662 Z M10,13 C9.44771525,13 9,13.4477153 9,14 L9,17 C9,17.5522847 9.44771525,18 10,18 L14,18 C14.5522847,18 15,17.5522847 15,17 L15,14 C15,13.4477153 14.5522847,13 14,13 L10,13 Z"
                                fill="#aaa9ff"></path>
                        </g>
                    </svg>
                    {{ __('common.dashboard') }}
                </a>
            </li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_2') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('leftmenu.menu_4') }}</a></li>
        </ol>
    </div>

    <div class="content-body  default-height">
        <div class="container-fluid main-content">
            <!-- row -->
            <div class="row">

                <div class="col-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">{{ __('administration.role_view') }}</h4>
                                <div>
                                    <x-button-back href="{{ admin_url('administration/role/list') }}"></x-button-back>
                                </div>
                            </div>

                            <div class="card-body ">

                                <div class="basic-form">

                                    <div class="row">
                                        <div class="mb-3 col-md-6 form-input">
                                            <label
                                                class="form-label ">{{ __('administration.role_id') }}</label>
                                            <div>
                                                {{ $role->role_id }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6 form-input">
                                            <label
                                                class="form-label ">{{ __('administration.role_name') }}</label>
                                            <div>
                                                {{ $role->role_name }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6 form-input">
                                            <label
                                                class="form-label ">{{ __('common.created_by') }}</label>
                                            <div>
                                                {{ getusername($role->created_by) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6 form-input">
                                            <label
                                                class="form-label ">{{ __('common.created_date') }}</label>
                                            <div>
                                                {{ displayDateformat($role->created_at ) }}
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6 form-input">
                                            <label
                                                class="form-label ">{{ __('common.status') }}</label>
                                            <div>
                                                @if ($role->status == 1)
                                                    {{ __('common.active') }}
                                                @else
                                                {{ __('common.inactive') }}
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('script')
    <script type="text/javascript"></script>
@endpush
