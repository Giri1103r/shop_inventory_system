<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | KARAM</title>

    <link rel="shortcut icon" href="{{ url('public/assets/images/logo-dark.png') }}">
    <link rel="manifest" href="{{ url('manifest') }}">

    <!-- App css -->
    <link href="{{ url('public/assets/css/roboto-fontface.css') }}" rel="stylesheet" type="text/css"  />
    <link href="{{ url('public/assets/css/karla.css') }}" rel="stylesheet" type="text/css"  />
    <link href="{{ url('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <!-- icons -->
    <link href="{{ url('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    @stack('styless')

    @stack('style')

</head>

<body class="loading authentication-bg authentication-bg-pattern">

    <div class="account-pages my-3">
        <div class="container">

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-4">
                    <div class="text-center">
                        <a href="{{ url('/') }}">
                            <img src="{{ url('public/assets/images/logo-dark.png') }}" alt="logo" class="mx-auto">
                        </a>
                        <p class="text-muted mt-2 mb-4"></p>
                    </div>

                    @yield('content')

                </div>
            </div>
        </div>
    </div>


    <!-- Vendor -->
    <script src="{{ url('public/assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/node-waves/waves.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/waypoints/lib/jquery.waypoints.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/jquery.counterup/jquery.counterup.min.js') }}"></script>
    <script src="{{ url('public/assets/plugins/feather-icons/feather.min.js') }}"></script>

    <!-- App js -->

    <script src="{{ url('public/assets/js/app.min.js') }}"></script>

    <script src="{{ public_plugins('sweetalert/SweetAlertFull.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/jquery.validate.min.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/additional-methods.min.js') }}"></script>

    @stack('scripts')
    <script>
        // console.log(123);
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('public/sw.js')
                .then(reg => console.log('Service Worker registered', reg))
                .catch(err => console.error('Service Worker failed', err));
        }
    </script>

    <script type="text/javascript" nonce="projectcab">
        var toastMixin = Swal.mixin({
            toast: true,
            showCloseButton: true,
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
                title: '{{ $message }}'
            });
        @endif

        @if ($message = Session::get('error'))
            toastMixin.fire({
                icon: 'error',
                animation: true,
                title: '{{ $message }}',
            });
        @endif
    </script>

    @stack('script')

</body>

</html>
