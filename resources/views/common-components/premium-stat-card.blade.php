<div class="col-md-6 col-xl-3">
    <div class="card mini-stat text-white" style="background: {{ $gradient }}; border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px {{ $shadowColor }};">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <p class="text-white-50 font-weight-bold mb-2 text-uppercase font-size-13">{{ $title }}</p>
                    <h2 class="text-white mb-0">{{ $value }}</h2>
                </div>
                <div class="mini-stat-icon" style="background: rgba(255, 255, 255, 0.2); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="mdi {{ $icon }} font-size-24"></i>
                </div>
            </div>
        </div>
    </div>
</div>
