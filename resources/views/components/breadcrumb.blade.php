<div class="row">
    <div class="col-12">
        <div class="page-title-box d-flex align-items-center justify-content-between">
            <h4 class="mb-0">{{ $title }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    @foreach($items as $item)
                        @php
                            $label = $item['label'];
                            if (strtolower($label) == 'dashboard') {
                                $label = 'لوحة التحكم';
                            }
                        @endphp
                        @if($loop->last)
                            <li class="breadcrumb-item active">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ $item['url'] }}">{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</div>
