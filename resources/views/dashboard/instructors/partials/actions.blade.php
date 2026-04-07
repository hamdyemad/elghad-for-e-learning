<div class="d-flex justify-content-center align-items-center">
    @php
        $showRoute = 'dashboard.instructors.show';
        $editRoute = 'dashboard.instructors.edit';
        $destroyRoute = 'dashboard.instructors.destroy';
    @endphp

    <!-- Wallet Button -->
    <x-btn href="{{ route('wallet.user.show', $row->id) }}" variant="primary" size="sm" class="mx-1" title="{{ __('auth.view_wallet') }}">
        <i class="mdi mdi-wallet"></i>
    </x-btn>

    @if(\Illuminate\Support\Facades\Route::has($showRoute))
        <x-btn href="{{ route($showRoute, $row->id) }}" variant="info" size="sm" class="mx-1">
            <i class="mdi mdi-eye"></i>
        </x-btn>
    @endif

    @if(\Illuminate\Support\Facades\Route::has($editRoute))
        <x-btn href="{{ route($editRoute, $row->id) }}" variant="warning" size="sm" class="mx-1">
            <i class="mdi mdi-pencil"></i>
        </x-btn>
    @endif

    @if(\Illuminate\Support\Facades\Route::has($destroyRoute))
        <x-btn type="button" variant="danger" size="sm" class="mx-1"
               data-toggle="modal" data-target="#deleteModal"
               data-action="{{ route($destroyRoute, $row->id) }}">
            <i class="mdi mdi-delete"></i>
        </x-btn>
    @endif
</div>
