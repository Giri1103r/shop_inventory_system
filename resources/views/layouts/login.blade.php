<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ url('public/assets/images/logo-dark.png') }}">


    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | KARAM</title>


    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ public_css('vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ public_css('style.css') }}">
    <link rel="stylesheet" href="{{ public_css('skin_color.css') }}">

    @stack('styless')

    @stack('style')


</head>
<body class="hold-transition theme-primary bg-img"
    style="background-image: url({{ public_image('common/neologin.png') }}); background-size:100% 100vh;">

    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">

            <div class="col-12">
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="bg-white rounded30 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <img style="width: 200px; padding-left: 23px; margin-bottom:5px;" src="{{asset('public/assets/images/common/logo/logo.jpg')}}" alt="">
                                <p class="mb-0">Sign in to continue to Indialand Shuttle </p>
                            </div>
                            <div class="p-40">
                                @yield('content')
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Vendor JS -->
    <script src="{{ public_js('vendors.min.js') }}"></script>
    <script src="{{ public_js('pages/chat-popup.js') }}"></script>
    <script src="{{ public_plugins('sweetalert/SweetAlertFull.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/jquery.validate.min.js') }}"></script>
    <script src="{{ public_plugins('jqueryvalidation/additional-methods.min.js') }}"></script>


    @stack('scripts')

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
