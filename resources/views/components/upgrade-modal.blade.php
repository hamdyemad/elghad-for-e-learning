<!-- Upgrade to Instructor Confirmation Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-body py-5">
                <i class="mdi mdi-star-circle text-success" style="font-size: 80px;"></i>
                <h3 class="mt-4 text-dark font-weight-bold">{{ $title ?? 'ترقية الطالب إلى مدرب' }}</h3>
                <p class="text-muted mb-4 font-size-16">
                    {{ $message ?? 'هل أنت متأكد من رغبتك في ترقية هذا الطالب إلى مدرب؟ سيتم منحه صلاحيات المدرب.' }}
                </p>

                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-light btn-lg mx-2 px-4 border" data-dismiss="modal">
                        إلغاء
                    </button>
                    <form id="upgrade-modal-form" method="POST" style="display: inline-block;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg mx-2 px-4 shadow-sm">
                            <i class="mdi mdi-star-circle"></i> نعم، قم بالترقية
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
    $(document).on('click', '[data-toggle="modal"][data-target="#upgradeModal"]', function() {
        const actionUrl = $(this).data('action');
        const studentName = $(this).data('student-name');
        $('#upgrade-modal-form').attr('action', actionUrl);
        // Optionally customize modal text with student name
        // $('#upgradeModal .modal-body p').text('هل أنت متأكد من ترقية ' + studentName + ' إلى مدرب؟');
    });
</script>
@endpush
@endonce
