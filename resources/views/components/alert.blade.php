@if(session($type ?? 'success'))
<div class="alert alert-{{ $type ?? 'success' }} alert-dismissible fade show" role="alert">
    {{ session($type ?? 'success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
