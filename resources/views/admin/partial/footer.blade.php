<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> &copy; {{ env('APP_NAME') }}
            </div>
            <div class="col-md-6">
                <div class="text-md-end footer-links d-none d-sm-block">
                   Powered By <img src="{{ url('public/assets/images/neoehs.svg') }}" height="25" alt="NeoEHS">
                </div>
            </div>
        </div>
    </div>
</footer>
