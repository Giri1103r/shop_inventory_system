@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>
        body {
            background-color: #f4f6f9;
        }

        .dashboard-header {
            background-color: #ffffff;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }

        .dashboard-header h4 {
            font-size: 24px;
            color: #333;
            margin: 0;
        }

    </style>
@endpush

@section('content')

    <div class="content-body default-height">
        <div class="container-fluid" style="padding: 30px 20px;">
            <!-- Welcome Header -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="dashboard-header d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center dz-head-title">
                            <h4 class="m-0">Welcome {{ Auth::user()->name }}!</h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function redirectopermanage(link) {
            var url = "{{ admin_url('') }}" + link;
            window.location.href = url;
        }
    </script>
@endpush

