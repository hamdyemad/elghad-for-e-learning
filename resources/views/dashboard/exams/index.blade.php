@extends('layouts.master')

@section('title', 'اختبارات الكورس: ' . $course->title)

@section('content')
<x-breadcrumb
    title="اختبارات الكورس: {{ $course->title }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الاختبارات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.exams.create', $course->id) }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة اختبار جديد
                        </x-btn>
                    </div>
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.show', $course->id) }}" variant="secondary" block>
                            <i class="mdi mdi-arrow-right"></i> العودة للكورس
                        </x-btn>
                    </div>
                </div>

                <x-alert type="success" />

                @if($exams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>عدد الأسئلة</th>
                                <th>المدة (دقيقة)</th>
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
                                    <span class="badge badge-info">{{ $exam->questions->count() ?? $exam->questions_count ?? 0 }}</span>
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
                                    <a href="{{ route('dashboard.courses.exams.show', [$course->id, $exam->id]) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.courses.exams.edit', [$course->id, $exam->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dashboard.courses.exams.destroy', [$course->id, $exam->id]) }}" method="POST" class="d-inline"
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
                    <p class="text-muted mt-2">لا توجد اختبارات لهذا الكورس بعد</p>
                    <a href="{{ route('dashboard.courses.exams.create', $course->id) }}" class="btn btn-primary mt-2">
                        <i class="mdi mdi-plus"></i> إضافة أول اختبار
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
