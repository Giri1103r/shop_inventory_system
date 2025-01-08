<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | KARAM</title>

    <link rel="shortcut icon" href="{{ url('public/assets/images/logo-dark.png') }}">

    <link href="{{ url('public/assets/css/roboto-fontface.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('public/assets/css/karla.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ url('public/assets/css/app.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <link href="{{ url('public/assets/css/custom-style.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <link href="{{ public_plugins('datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('datatables.net-select-bs5/css/select.bootstrap5.min.css') }}"rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('admin-resources/rwd-table/rwd-table.min.css') }}" rel="stylesheet"
        type="text/css" />

    <link href="{{ public_plugins('spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('clockpicker/bootstrap-clockpicker.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ url('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />


    <link href="{{ public_plugins('select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('fontawesome/css/all.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ public_plugins('jasny-bootstrap/css/jasny-bootstrap.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ public_plugins('fullcalendar/main.min.css') }}" rel="stylesheet" type="text/css" />
    @stack('styless')
    <style>
        :root {
            --dt-header-background-color: #b8cde2 !important;
            --dt-header-text-color: #3375b4 !important;
        }

        body.dark-skin {
            --dt-header-background-color: #1a233a !important;
            --dt-header-text-color: #a5b2cb !important;
        }

        .select2-container--default .select2-selection--single {
            border: 1px solid #d9d9d9;
            border-radius: 5px;
            padding: 1px 5px;
        }

        .select2-container--default .select2-selection--single {
            height: 35px;
            border-color: #688cb4;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 7px;
            right: 5px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-top: 5px;
        }

        .sidebar-menu>li>a>i {
            width: 30px;
            line-height: 28px;
            font-size: 1.5714285714rem;
            display: inline-block;
            vertical-align: middle;
            color: #b5b5c3;
            text-align: center;
            border-radius: 5px;
            margin-right: 5px;
            background-color: transparent;
        }

        .treeview-menu>li>a>i {
            padding-right: 5px;
            padding-left: 5px;
        }

        .sidebar-menu li>a>span {
            top: 0px;
            position: relative;
        }

        .w-100 {
            width: 100% !important;
        }

        .theme-primary .dt-buttons .dt-button {
            background-color: #ffffff;
        }

        .theme-primary .dt-buttons .dt-button:hover {
            background-color: #cccccc;
            color: #000000;
        }

        hr {
            border-top: 1px solid #000;
        }

        .form-label {
            font-weight: 600;
        }

        .card-header-inner {
            background-color: #6c757d;
            color: #fff;
            padding-top: 15px;
            margin-bottom: 1rem;
        }

        .dataTable>thead>tr>th {
            color: var(--dt-header-text-color);
            font-size: 14px;
            background: var(--dt-header-background-color) !important;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td:first-child:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th:first-child:before {

            background-color: var(--dt-header-background-color);
        }

        .light-skin #icon-light {
            display: none;
        }

        .light-skin #icon-dark {
            display: block;
        }

        .dark-skin #icon-light {
            display: block;
            color: #ffffff;
        }

        .dark-skin #icon-dark {
            display: none;
        }

        label.require::after {
            content: '*';
            color: red;
        }


        /* Tooltip styling */
        .fc-event:hover .custom-tooltip {
            display: block;
        }



        .custom-tooltip {
            display: none;
            position: absolute;
            background-color: #333;
            color: white;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }
    </style>
    @stack('style')


</head>

<!-- body start -->

<body class="loading" data-layout-color="light" data-layout-mode="default" data-layout-size="fluid"
    data-topbar-color="light" data-leftbar-position="fixed" data-leftbar-color="dark" data-leftbar-size='default'
    data-sidebar-user='true'>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        @include('admin.partial.header')

        <!-- end Topbar -->

        <!-- ========== Left Sidebar Start ========== -->
        @include('admin.partial.left_menu')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                @yield('content')

                <!-- container-fluid -->

            </div> <!-- content -->

            <!-- Footer Start -->
            @include('admin.partial.footer')

            <!-- end Footer -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


    </div>
    <!-- END wrapper -->

    <!-- Right Sidebar -->
    @include('admin.partial.right_menu')

    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <script nonce="projectcab">
        var pageurl = '@yield('pageurl')';
    </script>

    <!-- Vendor -->
    <script src="{{ public_plugins('jquery/jquery.min.js') }}"></script>

    <script src="{{ url('public/assets/js/pages/responsive-table.init.js') }}"></script>
    <script src="{{ url('public/assets/js/pages/datatables.init.js') }}"></script>
    {{-- <script src="{{ url('public/assets/js/pages/form-pickers.init.js') }}"></script> --}}

    <script src="{{ public_plugins('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ public_plugins('moment/moment.js') }}"></script>
    <script src="{{ public_plugins('simplebar/simplebar.min.js') }}"></script>
    <script src="{{ public_plugins('node-waves/waves.min.js') }}"></script>
    <script src="{{ public_plugins('waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ public_plugins('jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ public_plugins('feather-icons/feather.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ public_plugins('datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-select/js/dataTables.select.min.js') }}"></script>
    <script src="{{ public_plugins('datatables.net-select-bs5/js/select.bootstrap5.min.js') }}"></script>
    <script src="{{ public_plugins('admin-resources/rwd-table/rwd-table.min.js') }}"></script>
    <script src="{{ public_plugins('pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ public_plugins('pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ public_plugins('bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ public_plugins('flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ public_plugins('spectrum-colorpicker2/spectrum.min.js') }}"></script>
    <script src="{{ public_plugins('clockpicker/bootstrap-clockpicker.min.js') }}"></script>
    <script src="{{ public_plugins('bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>



    <!-- knob plugin -->
    <script src="{{ public_plugins('jquery-knob/jquery.knob.min.js') }}"></script>

    <!--Morris Chart-->
    <script src="{{ public_plugins('morris.js06/morris.min.js') }}"></script>
    <script src="{{ public_plugins('raphael/raphael.min.js') }}"></script>
    <script src="{{ public_plugins('DataTables/datatables.min.js') }}"></script>


    <!-- App js-->
    <script src="{{ url('public/assets/js/app.min.js') }}"></script>

    <script src="{{ public_plugins('sweetalert/SweetAlertFull.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/jquery.validate.min.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/additional-methods.min.js') }}"></script>
    <script src="{{ url('public/assets/js/custom-validation.js') }}"></script>

    <script src="{{ public_plugins('select2/js/select2.full.min.js') }}"></script>
    <script src="{{ public_plugins('jasny-bootstrap/js/jasny-bootstrap.min.js') }}"></script>


    @stack('scripts')

    <script type="text/javascript" nonce="projectcab">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            statusCode: {
                419: function() {
                    window.location.href = '{{ url("") }}';
                }
            }
        });



        function datepickercall() {
            $(".datepicker").datepicker({
                'container': ".wrapper",
                'format': "dd-mm-yyyy",
                'autoclose': true,
                'orientation': 'bottom',
                'todayHighlight': true,
                'startDate': '16-07-2024',
            });

            $(".todaymaxdatepicker").datepicker({
                'container': ".wrapper",
                'format': "dd-mm-yyyy",
                'autoclose': true,
                'orientation': 'bottom',
                'todayHighlight': true,
                'endDate': '16-07-2024',
            });
        }

        function getEndDate(fromdate, addValue, type = {{ ADD_DATE }}, endDate = "") {

            var fromdate = moment(fromdate, 'DD-MM-YYYY').toDate();

            var currentDateTarget = new Date(fromdate);
            var futureDateTarget = new Date(fromdate);

            if (type === 1) {
                addValue = addValue - 1;
                futureDateTarget.setDate(currentDateTarget.getDate() + addValue);
            } else if (type === 2) {
                futureDateTarget.setMonth(currentDateTarget.getMonth() + addValue);
            } else if (type === 3) {
                futureDateTarget.setFullYear(currentDateTarget.getFullYear() + addValue);
            } else {
                return null;
            }

            if (endDate != '') {
                var endDate = moment(endDate, 'DD-MM-YYYY').toDate();
                if (endDate && futureDateTarget > new Date(endDate)) {

                    futureDateTarget = new Date(endDate);
                }
            }

            var day = futureDateTarget.getDate();
            var month = futureDateTarget.getMonth() + 1;
            var year = futureDateTarget.getFullYear();

            if (day < 10) {
                day = "0" + day;
            }

            if (month < 10) {
                month = "0" + month;
            }

            var formattedDate = day + "-" + month + "-" + year;

            return formattedDate;
        }

        function timepickercall() {

            $(".clockpicker").clockpicker({
                twelvehour: true,
                placement: 'bottom',
                autoclose: true,
                donetext: 'Done',
                'default': 'now'
            });
        }

        // function datetimepickercall() {

        //     $(".datetimepicker").datetimepicker({
        //         format: 'dd-mm-yyyy hh:ii',
        //         autoclose: true,
        //         todayHighlight: true,
        //         minuteStep: 5,
        //     });
        // }

        function datetimepickercall() {
            $(".datetimepicker").datetimepicker({
                format: 'DD-MM-YYYY HH:mm', // Correct date format (DD-MM-YYYY) and time format (HH:mm)
                autoclose: true, // Automatically closes after date selection
                todayHighlight: true, // Highlights today’s date
                minuteStep: 5, // Sets minute interval to 5 minutes
            });
        }

        function callsingleselect() {
            $('.single-select').select2({
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
        }

        function callmultipleselect() {
            $('.multiple-select').select2({
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
            });
        }

        function callpopupsingleselect() {
            $('.single-select-popup').select2({
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
                dropdownParent: $('#popupwindowmodal'),
            });
        }

        function callpopupmultipleselect() {
            $('.multiple-select-popup').select2({
                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                placeholder: $(this).data('placeholder'),
                allowClear: Boolean($(this).data('allow-clear')),
                dropdownParent: $('#popupwindowmodal'),
            });
        }

        function calltooltip() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }

        $(document).ready(function() {

            var toastMixin = Swal.mixin({
                toast: true,
                icon: 'success',
                title: 'General Title',
                animation: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if ($message = Session::get('success'))
                toastMixin.fire({
                    icon: 'success',
                    animation: true,
                    title: '{{ $message }}',
                    showCloseButton: true,
                });
            @endif

            @if ($message = Session::get('error'))
                toastMixin.fire({
                    icon: 'error',
                    animation: true,
                    title: '{{ $message }}',
                    showCloseButton: true,
                });
            @endif

            /** Tooltips **/
            $(function() {
                $('[data-toggle="tooltip"]').tooltip()
            })

            callsingleselect();
            callmultipleselect();
            calltooltip();

        });

        $(document).on('click', '.popupwindow', function(e) {
            e.preventDefault();
            $('#popupwindowmodal').modal('show').find('.modal-content').load($(this).attr('href'));
        });

        /*
         * Menu Active dynamically
         */

        $(function() {
            baseurl = "@yield('pageurl')";
            $('.sidebar-menu a').each(function() {
                var $this = $(this);
                if ($this.attr('href') === baseurl) {
                    $this.parent().addClass('active');
                    $this.closest('.treeview').addClass('active menu-open');
                }
            });
        });

        /*
         * Theme mode change
         */

        var theme = '{{ $themetype }}';

        $(document).ready(function() {
            $('#themeToggle').click(function() {
                $('body').toggleClass('light-skin dark-skin');
            });
        });

        $(document).on('click', '.themechange', function() {

            if (theme == 'light-skin') {
                theme = 'dark-skin';
            } else {
                theme = 'light-skin';
            }

            $.ajax({
                url: "{{ admin_url('theme/change') }}",
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    theme: theme
                },

                success: function(data) {

                }
            });
        });
    </script>

    @stack('script')

</body>

</html>
