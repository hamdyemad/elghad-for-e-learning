@extends('layouts.master')

@section('title', 'إرسال إشعار جديد')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px) !important;
        padding: 0.375rem 0.75rem !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    .select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem) !important;
    }
</style>
@endpush

@section('content')
<x-breadcrumb
    title="إرسال إشعار جديد"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الإعدادات', 'url' => route('dashboard.settings.edit')],
        ['label' => 'الإشعارات', 'url' => route('dashboard.notifications.index')],
        ['label' => 'إرسال إشعار']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('dashboard.notifications.store') }}" method="POST">
                    @csrf

                    <x-alert type="success" />
                    <x-alert type="error" />

                    <div class="form-group mb-4">
                        <label for="recipient_type">نوع المستلم <span class="text-danger">*</span></label>
                        <select name="recipient_type" id="recipient_type" class="form-control" required>
                            <option value="">-- اختر نوع المستلم --</option>
                            <option value="all_students" {{ old('recipient_type') == 'all_students' ? 'selected' : '' }}>جميع الطلاب</option>
                            <option value="all_instructors" {{ old('recipient_type') == 'all_instructors' ? 'selected' : '' }}>جميع المحاضرين</option>
                            <option value="single_student" {{ old('recipient_type') == 'single_student' ? 'selected' : '' }}>طالب محدد</option>
                            <option value="single_instructor" {{ old('recipient_type') == 'single_instructor' ? 'selected' : '' }}>محاضر محدد</option>
                        </select>
                        @error('recipient_type') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-4" id="recipient_id_group" style="display: none;">
                        <label for="recipient_id">المستخدم المستهدف <span class="text-danger">*</span></label>
                        <select name="recipient_id" id="recipient_id" class="form-control">
                            <option value="">-- اختر المستخدم --</option>
                        </select>
                        @error('recipient_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <x-form-input
                        name="title"
                        label="عنوان الإشعار"
                        :value="old('title')"
                        :required="true"
                        placeholder="أدخل عنوان الإشعار"
                    />

                    <x-form-textarea
                        name="body"
                        label="نص الإشعار"
                        :value="old('body')"
                        :required="true"
                        placeholder="أدخل نص الإشعار"
                    />

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-send"></i> إرسال الإشعار
                        </button>
                        <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-cancel"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">معلومات</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="mdi mdi-information-outline text-info"></i>
                        سيتم إرسال الإشعار لجميع المستخدمين المحددين
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-bell-ring-outline text-success"></i>
                        سيتم إرسال إشعار Firebase Push للمستخدمين الذين لديهم تطبيق
                    </li>
                    <li>
                        <i class="mdi mdi-account-check text-primary"></i>
                        سيتم حفظ الإشعار في سجل الإشعارات للمستخدم
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const students = @json($students);
    const instructors = @json($instructors);
    let recipientSelect2 = null;

    function initSelect2(users) {
        const $recipientId = $('#recipient_id');

        // Destroy existing Select2 if any
        if (recipientSelect2) {
            $recipientId.select2('destroy');
        }

        // Clear and populate options
        $recipientId.empty().append('<option value="">-- اختر المستخدم --</option>');
        users.forEach(user => {
            const option = new Option(user.name + ' (' + user.email + ')', user.id, false, false);
            $recipientId.append(option);
        });

        // Set old value if exists
        const oldVal = '{{ old("recipient_id") }}';
        if (oldVal) {
            $recipientId.val(oldVal);
        }

        // Initialize Select2
        recipientSelect2 = $recipientId.select2({
            theme: 'bootstrap4',
            dir: 'rtl',
            placeholder: 'ابحث عن مستخدم...',
            allowClear: true,
            language: {
                noResults: function() {
                    return 'لا توجد نتائج';
                },
                searching: function() {
                    return 'جاري البحث...';
                },
                inputTooShort: function() {
                    return 'يرجى إدخال حرف واحد على الأقل';
                }
            }
        });
    }

    document.getElementById('recipient_type').addEventListener('change', function() {
        const recipientIdGroup = document.getElementById('recipient_id_group');
        const type = this.value;

        if (type === 'single_student' || type === 'single_instructor') {
            recipientIdGroup.style.display = 'block';
            const users = type === 'single_student' ? students : instructors;
            initSelect2(users);
        } else {
            recipientIdGroup.style.display = 'none';
            if (recipientSelect2) {
                $('#recipient_id').select2('destroy');
                recipientSelect2 = null;
            }
            document.getElementById('recipient_id').innerHTML = '<option value="">-- اختر المستخدم --</option>';
        }
    });

    // Trigger on page load
    if (document.getElementById('recipient_type').value) {
        document.getElementById('recipient_type').dispatchEvent(new Event('change'));
    }
</script>
@endpush
@endsection
