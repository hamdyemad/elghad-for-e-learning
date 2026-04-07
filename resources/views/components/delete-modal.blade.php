<!-- Delete Confirmation Modal -->
<div class="modal fade" id="{{ $modalId ?? 'deleteModal' }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-body py-5">
                <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 80px;"></i>
                <h3 class="mt-4 text-dark font-weight-bold">{{ $title ?? 'هل أنت متأكد؟' }}</h3>
                <p class="text-muted mb-4 font-size-16">{{ $message ?? 'لن تتمكن من التراجع عن حذف هذا العنصر!' }}</p>
                
                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-light btn-lg mx-2 px-4 border" data-dismiss="modal">
                        إلغاء
                    </button>
                    <form id="delete-modal-form" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-lg mx-2 px-4 shadow-sm">
                            نعم، قم بالحذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    $(document).on('click', '[data-toggle="modal"][data-target="#{{ $modalId ?? 'deleteModal' }}"]', function() {
        const actionUrl = $(this).data('action');
        $('#delete-modal-form').attr('action', actionUrl);
    });
</script>
@endpush
@endonce
