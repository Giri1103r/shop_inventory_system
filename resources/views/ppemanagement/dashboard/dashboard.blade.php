@extends('admin.layouts.admin')
@section('title', ' PPE Management Dashboard')
@section('header', 'Dashboard')

@section('pageurl', admin_url('dashboard'))

@push('style')
    <style>


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

            <div class="col-xl-12 col-xxl-12 mt-2">
                <div class="ppeExemption" style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @foreach ($exemptionUnits as $unit)
                        <div class="col-xl-3 col-lg-6 col-sm-6">
                            <div class="widget-stat card card-dashbaord"
                                onclick="window.location.href='{{ admin_url('ppe_exemption/list') }}';"
                                style="cursor: pointer; background-image: linear-gradient(135deg, {{ $unit->color ?? '#f5f7fa' }} 0%, {{ $unit->color ?? '#c3cfe2' }} 100%);">
                                <div class="card-body p-4"
                                    style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);">
                                    <div class="media ai-icon" style="display: flex; align-items: center;">
                                        <span class="me-3 bgl-warning text-warning">
                                            <svg width="50" height="35" viewBox="0 0 50 35" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                                                <!-- SVG content here -->
                                            </svg>
                                        </span>
                                        <div>
                                            <!-- Display unit name in bold -->
                                            <div style="font-weight: bold; font-size: 16px;">{{ $unit->unit_name }}</div>
                                            <!-- Display total count centered -->
                                            <div style="text-align: center; font-size: 18px; font-weight: bold;">
                                                {{ $unit->total }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>





            <div class="col-xl-12 col-xxl-12 mt-2">
                <div class="card view_card">
                    <div class="card-header border-0 pb-1 bg-danger d-flex justify-content-between align-items-center">
                        <h4 class="card-title" style="color: white;">PPE EXEMPTION</h4>
                        <a class="fas fa-arrow-alt-circle-down chartdownload" id="ppeExemption" style="color: white;"></a>
                    </div>
                    <div class="card-body px-0 pt-0 dlab-scroll height450" id="PpeExemptionData">
                        <!-- This div will display the chart -->
                        <div id="Ppe_Exemption_Data"></div>
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

        function PpeChartExemptiondata() {
        var url = "{{ admin_url('ppe-dashboard/ppeExemption') }}";
        $('#PpeExemptionData').html(''); // Clear the chart container

        $.ajax({
            type: 'get',
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function (dataAjx) {

                $('#PpeExemptionData').html(dataAjx); // Load the chart
            },

            error: function (xhr) {
                console.error('Failed to fetch data:', xhr.responseText);
            }
        });
    }

    // Call the function to load the chart initially
    PpeChartExemptiondata();
    </script>
@endpush
