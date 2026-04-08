<footer class="footer">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 col-sm-12 text-md-right text-center">
                © {{ date('Y', strtotime('-2 year')) }} - {{ date('Y') }}
                <strong>الغد</strong>
                <span class="d-none d-md-inline-block mx-2">|</span>
                <span class="d-none d-md-inline-block">Built with <i class="mdi mdi-heart text-danger mx-1"></i> by <a href="https://ibtikartech.netlify.app/" target="_blank" class="text-decoration-none">Ibtikar Tech</a></span>
            </div>
            <div class="col-md-6 col-sm-12 text-md-left text-center mt-2 mt-md-0">
                <span class="text-muted">{{ __('Version') }} 1.0.0</span>
            </div>
        </div>
    </div>
</footer>