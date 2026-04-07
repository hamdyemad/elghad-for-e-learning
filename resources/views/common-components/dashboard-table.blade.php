<div class="col-xl-6">
    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="card-title mb-0">{{ $title }}</h4>
                <a href="{{ $viewAllRoute }}" class="btn btn-sm btn-light px-3" style="border-radius: 8px;">عرض الكل</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-centered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            @foreach($headers as $header)
                            <th class="border-0">{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        {{ $slot }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
