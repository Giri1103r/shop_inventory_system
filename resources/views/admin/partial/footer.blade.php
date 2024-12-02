<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <script>
                    document.write(new Date().getFullYear())
                </script> &copy; {{ config('app.name') }}
            </div>
            <div class="col-md-6" style = 'display:none;'>
                <div class="text-md-end footer-links d-none d-sm-block">
                   Powered By <img src="{{ url('public/assets/images/neoehs.svg') }}" height="25" alt="KARAM">
                </div>
            </div>
        </div>
    </div>
</footer>
