@extends('admin.layouts.admin')
@section('title', 'OHC Dashboard')
@section('header', 'OHC Dashboard')

@section('pageurl', admin_url('ohc/dashboard'))


@push('style')
    <style>
        .green-box {
            background-color: green;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .yellow-box {
            background-color: yellow;
            color: black;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .red-box {
            background-color: red;
            color: white;
            padding: 10px;
            border-radius: 5px;
            display: inline-block;
        }

        .chart-container {
            width: 100px;
            height: 80px;
            margin-left: 16%;
        }
    </style>
@endpush

@section('content')

    <div class="content-body default-height ">
        <div class="container-fluid" style="padding-top: 30px !important;padding-bottom: 20px !important;">
            <div class="row">
                <div class="col-xl-12">
                    <div class="coin-warpper d-flex align-items-center justify-content-between flex-wrap">
                        <div class="d-flex align-items-center dz-head-title">
                            <h4 class="m-0 " style="padding-left: 10px;">Welcome Back {{ Auth::user()->name }}!
                            </h4>
                        </div>

                    </div>
                </div>
            </div>
            @if (checkUserRole(ROLE_SUPERADMIN) || checkUserRole(ROLE_EHS_HEAD))
                <div class="d-flex justify-content-end p-2 me-2">
                    <x-button-filter dataId="" class="search me-2" href=""></x-button-filter>

                </div>



                <div id="search" class="collapse">
                    <form action="{{ admin_url('ohc/dashboard') }}" method="GET" id="formsearch">
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="row">


                                    <div class="col-md-3 mb-2">
                                        <div class="form-group form-input">
                                            <label class="form-label ">Unit</label>
                                            <select name="unit_id" id="unit_id" class="form-control single-select"
                                                style="width: 100%">
                                                <option value="">Select the unit</option>
                                                @foreach ($unit as $list)
                                                    <option value="{{ encryptId($list->id) }}">
                                                        {{ $list->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-3">
                                        <x-button-search></x-button-search>
                                        <x-button-reset></x-button-reset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                </div>
            @endif
            <div class="card view_card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="redirectopermanage('{{ isset($masterLink[0]['link']) ? $masterLink[0]['link'] : '' }}')"
                                style="cursor: pointer;background-image: linear-gradient(to right, #ffecd2 0%, #fcb69f 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#FFAB2D" />
                                                <path
                                                    d="M19.2734 19.2734L16.8828 16.8828C15.8211 15.8211 14 16.5734 14 18.0711V25.8125C14 26.7477 14.7523 27.5 15.6875 27.5H23.4289C24.9336 27.5 25.6859 25.6789 24.6242 24.6172L22.4586 22.4516C24.8984 20.0117 28.2734 18.5 32 18.5C39.4531 18.5 45.5 24.5469 45.5 32C45.5 39.4531 39.4531 45.5 32 45.5C29.1313 45.5 26.4734 44.607 24.2867 43.0813C23.2672 42.3711 21.868 42.6172 21.1508 43.6367C20.4336 44.6562 20.6867 46.0555 21.7062 46.7727C24.6312 48.8047 28.182 50 32 50C41.9422 50 50 41.9422 50 32C50 22.0578 41.9422 14 32 14C27.0289 14 22.5289 16.018 19.2734 19.2734ZM32 23C31.0648 23 30.3125 23.7523 30.3125 24.6875V32C30.3125 32.45 30.4883 32.8789 30.8047 33.1953L35.8672 38.2578C36.5281 38.9187 37.5969 38.9187 38.2508 38.2578C38.9047 37.5969 38.9117 36.5281 38.2508 35.8742L33.6805 31.3039V24.6875C33.6805 23.7523 32.9281 23 31.993 23H32Z"
                                                    stroke="white" stroke-width="2" />
                                            </svg>
                                        </span>
                                        <div class="media-body" style="display: block;">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[0]['name']) ? $masterLink[0]['name'] : '' }}
                                            </p>
                                            <h4 class="mb-0 medicine-requisition">
                                                {{ isset($masterLink[0]['count']) ? $masterLink[0]['count'] : 0 }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-warning text-warning">
                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#4CAF50" />
                                                <!-- Green background -->
                                                <path d="M16 24L32 14L48 24V40L32 50L16 40V24Z" stroke="white"
                                                    stroke-width="2" />
                                                <path d="M32 50V32" stroke="white" stroke-width="2" />
                                                <path d="M16 24L32 34L48 24" stroke="white" stroke-width="2" />
                                            </svg>

                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[1]['name']) ? $masterLink[1]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[1]['count']) ? $masterLink[1]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-3  col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to left, #cd9cf2 0%, #f6f3ff 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-danger text-danger">

                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#FFAB2D" />
                                                <path d="M20 20H48L44 36H24L20 20Z" stroke="white" stroke-width="2"
                                                    fill="none" />
                                                <circle cx="26" cy="44" r="2" fill="white" />
                                                <circle cx="42" cy="44" r="2" fill="white" />
                                            </svg>


                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1"style="color:black;">
                                                {{ isset($masterLink[2]['name']) ? $masterLink[2]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[2]['count']) ? $masterLink[2]['count'] : 0 }}</h4>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to right, #FFC796 0%, #FF6B95 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-success text-success">
                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#4CAF50" />
                                                <!-- Green background -->
                                                <path d="M22 32H42M32 22V42M24 16L16 24M40 16L48 24M24 48L16 40M40 48L48 40"
                                                    stroke="white" stroke-width="2" />
                                            </svg>

                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[3]['name']) ? $masterLink[3]['name'] : '' }}</p>
                                            <h4 class="mb-0 medicine-issuance">
                                                {{ isset($masterLink[3]['count']) ? $masterLink[3]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card view_card">

                <div class="card-body">
                    <div class="row">
                        <h4>Unit Wise OPD Details</h4>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to right, #a3f0dc 0%, #83aeee 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">

                                        <div class="media-body" style="display: block;">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[4]['name']) ? $masterLink[4]['name'] : '' }}
                                            </p>
                                            <h4 class="mb-0 medicine-issuance">
                                                {{ isset($masterLink[4]['count']) ? $masterLink[4]['count'] : 0 }}
                                            </h4>
                                        </div>

                                        <div id="chart1" class="chart-container"></div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(135deg, #d2f8bc 0%, #c3cfe2 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">

                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[5]['name']) ? $masterLink[5]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[5]['count']) ? $masterLink[5]['count'] : 0 }}</h4>
                                        </div>

                                        <div id="chart2" class="chart-container"></div>

                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-xl-3  col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to left, #d9ecee 0%, #7fdbf1 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">

                                        <div class="media-body">
                                            <p class="mb-1"style="color:black;">
                                                {{ isset($masterLink[6]['name']) ? $masterLink[6]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[6]['count']) ? $masterLink[6]['count'] : 0 }}</h4>
                                        </div>

                                        <div id="chart3" class="chart-container"></div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(to right, #e2757e 0%, #f1b1c4 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">

                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[7]['name']) ? $masterLink[7]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[7]['count']) ? $masterLink[7]['count'] : 0 }}</h4>
                                        </div>

                                        <div id="chart4" class="chart-container"></div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="card view_card">

                <div class="card-body">
                    <div class="row">
                        <h4>OHC Details</h4>
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(135deg, #007BFF, #cfeeee);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-primary text-primary" style="flex-shrink: 0;">
                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#cfeeee" />
                                                <path d="M20 22H44V46H20V22Z" stroke="black" stroke-width="2" />
                                                <path d="M28 46V36H36V46" stroke="black" stroke-width="2" />
                                                <path d="M24 26H30" stroke="black" stroke-width="2" />
                                                <path d="M34 26H40" stroke="black" stroke-width="2" />
                                                <path d="M32 16V22" stroke="black" stroke-width="2" />
                                                <path d="M28 19H36" stroke="black" stroke-width="2" />
                                            </svg>

                                        </span>
                                        <div class="media-body" style="display: block;">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[8]['name']) ? $masterLink[8]['name'] : '' }}
                                            </p>
                                            <h4 class="mb-0 today-opd">
                                                {{ isset($masterLink[8]['count']) ? $masterLink[8]['count'] : 0 }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(135deg, #ec94cf, #c2daf3);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-warning text-warning">
                                            <svg width="50" height="50" viewBox="0 0 64 64" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="32" cy="32" r="32" fill="#cfeeee" />
                                                <path d="M22 16H42V48H22V16Z" stroke="black" stroke-width="2" />
                                                <path d="M28 20H36" stroke="black" stroke-width="2" />
                                                <path d="M24 26H40" stroke="black" stroke-width="2" />
                                                <path d="M24 32H40" stroke="black" stroke-width="2" />
                                                <path d="M24 38H32" stroke="black" stroke-width="2" />
                                                <circle cx="40" cy="44" r="6" stroke="black"
                                                    stroke-width="2" />
                                                <line x1="37" y1="41" x2="43" y2="47"
                                                    stroke="black" stroke-width="2" />
                                            </svg>

                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[9]['name']) ? $masterLink[9]['name'] : '' }}</p>
                                            <h4 class="mb-0 today-issue">
                                                {{ isset($masterLink[9]['count']) ? $masterLink[9]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"

                                style="cursor: pointer;background-image: linear-gradient(135deg, #74a4ec 0%, #c3cfe2 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.014), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">

                                            <path d="M9 2h6l3 3v14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"
                                                stroke="black" fill="#f8f9fa" />
                                            <path d="M14 2v4h4" stroke="black" />


                                            <circle cx="10" cy="14" r="3" stroke="black" fill="none" />
                                            <line x1="13" y1="17" x2="16" y2="20"
                                                stroke="black" />
                                        </svg>

                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[10]['name']) ? $masterLink[10]['name'] : '' }}</p>
                                            <h4 class="mb-0">
                                                {{ isset($masterLink[10]['count']) ? $masterLink[10]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                style="cursor: pointer;background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                                <div class="card-body p-4"
                                    style = "box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-warning text-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" fill="green"
                                                    stroke="green" />


                                                <path d="M9 12h6M12 9v6" stroke="white" stroke-width="3" />
                                            </svg>


                                        </span>
                                        <div class="media-body">
                                            <p class="mb-1" style="color:black;">
                                                {{ isset($masterLink[10]['name']) ? $masterLink[10]['name'] : '' }}</p>
                                            <h4 class="mb-0 certified-first-aider">
                                                {{ isset($masterLink[10]['count']) ? $masterLink[10]['count'] : 0 }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="card shadow-lg border-0">
                <div class="card-body">
                    <h4 class="card-title text-dark mb-3">Medicine Status</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="medicine-table">


                            <thead class="bg-secondary" style="color: #ffff">
                                <tr>
                                    <th>Medicine</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicines as $medicine)
                                    <tr>
                                        <td class="">{{ $medicine->medicine }}</td>
                                        <td>
                                            <span
                                                class="badge
                                                @if ($medicine->balance > 50) bg-success
                                                @elseif ($medicine->balance > 10) bg-warning text-dark
                                                @else bg-danger @endif
                                                px-3 py-2">
                                                {{ $medicine->balance }}
                                            </span>
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

@endsection


@push('scripts')
    <script>
        function redirectopermanage(link) {
            var url = "{{ admin_url('') }}" + link;
            window.location.href = url;
        }
        $('#resetform').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#searchform').click(function() {
                let unitId = $('#unit_id').val();

                $.ajax({
                    url: "{{ admin_url('ohc/dashboard/medicine-requisition') }}",
                    method: "GET",
                    data: {
                        unit_id: unitId
                    },
                    beforeSend: function() {
                        $('#searchform').prop('disabled', true).text('Searching...');
                    },
                    success: function(response) {

                        if (response.masterLink && response.masterLink.length > 0) {
                            $('.medicine-requisition').text(response.masterLink[0].count);
                            $('.medicine-issuance').text(response.masterLink[3].count);
                            $('.today-opd').text(response.masterLink[8].count);
                            $('.today-issue').text(response.masterLink[9].count);
                            $('.certified-first-aider').text(response.masterLink[10].count);
                        }

                        let medicines = response.medicines;
                        let tbodyHtml = '';

                        if (medicines && medicines.length > 0) {
                            medicines.forEach(medicine => {
                                let badgeClass = 'bg-danger';

                                if (medicine.balance > 50) {
                                    badgeClass = 'bg-success';
                                } else if (medicine.balance > 10) {
                                    badgeClass = 'bg-warning text-dark';
                                }

                                tbodyHtml += `
                                        <tr>
                                            <td>${medicine.medicine}</td>
                                            <td>
                                                <span class="badge ${badgeClass} px-3 py-2">
                                                    ${medicine.balance}
                                                </span>
                                            </td>
                                        </tr>
                                    `;
                            });
                        } else {
                            tbodyHtml = `<tr><td colspan="2">No data found</td></tr>`;
                        }


                        $('#medicine-table tbody').html(tbodyHtml);
                        console.log(response);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        alert('An error occurred while fetching data.');
                    },
                    complete: function() {
                        $('#searchform').prop('disabled', false).text('Search');
                    }
                });
            });
        });






        $('#resetform').on('click', function(e) {
            e.preventDefault();
            $('#unit_id').val('');


        });

        document.addEventListener("DOMContentLoaded", function() {
            var opdCounts = [
                {{ isset($masterLink[4]['count']) ? $masterLink[4]['count'] : 0 }},
                {{ isset($masterLink[5]['count']) ? $masterLink[5]['count'] : 0 }},
                {{ isset($masterLink[6]['count']) ? $masterLink[6]['count'] : 0 }},
                {{ isset($masterLink[7]['count']) ? $masterLink[7]['count'] : 0 }}
            ];

            var colors = ["#FF5733", "#1E90FF", "#28A745", "#FFC107"];

            opdCounts.forEach(function(count, index) {
                var options = {
                    chart: {
                        type: 'radialBar',
                        height: 80,
                        width: 80,
                        sparkline: {
                            enabled: true
                        }
                    },
                    series: [count],
                    colors: [colors[index % colors.length]],
                    plotOptions: {
                        radialBar: {
                            hollow: {
                                size: '40%'
                            },
                            track: {
                                background: "#EAEAEA"
                            },
                            dataLabels: {
                                name: {
                                    show: false
                                },
                                value: {
                                    fontSize: '17px',
                                    color: "#333"
                                }
                            }
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#chart" + (index + 1)), options);
                chart.render();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.35.3"></script>
@endpush
