@extends('layouts.master')

@section('title', 'الاختبارات')

@section('content')
<x-breadcrumb
    title="الاختبارات"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الاختبارات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row mb-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small">الكورس</label>
                        <select name="course_id" id="course_id_filter" class="form-control" style="width:100%">
                            <option value="">كل الكورسات</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small">بحث</label>
                        <input type="text" name="search" class="form-control" placeholder="بحث عن اختبار..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="mdi mdi-magnify"></i> بحث
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('dashboard.exams.all') }}" class="btn btn-secondary btn-block">
                            <i class="mdi mdi-refresh"></i> إعادة تعيين
                        </a>
                    </div>
                </form>

                <x-alert type="success" />

                @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الكورس</th>
                                <th>عدد الأسئلة</th>
                                <th>المدة</th>
                                <th>درجة النجاح</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($exams as $exam)
                            <tr>
                                <td>
                                    <span class="badge badge-soft-primary font-size-12">#{{ strtoupper(dechex($exam->id)) }}</span>
                                </td>
                                <td>{{ $exam->title }}</td>
                                <td>
                                    <a href="{{ route('dashboard.courses.show', $exam->course_id) }}">
                                        {{ $exam->course->title }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $exam->questions_count ?? 0 }}</span>
                                </td>
                                <td>{{ $exam->duration_minutes ?? 'غير محدد' }}</td>
                                <td>{{ $exam->passing_score }}%</td>
                                <td>
                                    @if($exam->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.courses.exams.show', [$exam->course_id, $exam->id]) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.courses.exams.edit', [$exam->course_id, $exam->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dashboard.courses.exams.destroy', [$exam->course_id, $exam->id]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الاختبار؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($exams->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $exams->withQueryString()->links() }}
                </div>
                @endif
                @else
                <div class="text-center p-4">
                    <i class="mdi mdi-clipboard-text-outline text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">لا توجد اختبارات بعد</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container { width: 100% !important; }
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da; border-radius: 0.25rem;
    height: calc(1.5em + 0.75rem + 2px); display: flex; align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal; padding: 0 0.75rem; text-align: right; direction: rtl;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%; position: absolute; left: 5px; right: auto;
    display: flex; align-items: center;
}
.select2-dropdown { border: 1px solid #ced4da; border-radius: 0.25rem; z-index: 1060; }
.select2-results__option { text-align: right; direction: rtl; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#course_id_filter').select2({
        dir: 'rtl',
        placeholder: 'كل الكورسات',
        allowClear: true
    }).on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
@endpush
@endsection
