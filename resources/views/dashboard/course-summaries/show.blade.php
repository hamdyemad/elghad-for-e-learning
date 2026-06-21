@extends('layouts.master')

@section('title', 'تفاصيل الملخص')

@section('content')
<x-breadcrumb
    title="تفاصيل الملخص"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الملخصات', 'url' => route('dashboard.courses.summaries.index', $course->id)],
        ['label' => 'التفاصيل']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">{{ $summary->title }}</h4>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>الكورس:</strong>
                        <span class="text-muted">{{ $course->title }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>تاريخ الإنشاء:</strong>
                        <span class="text-muted">{{ $summary->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <strong>الملف:</strong>
                    <div class="mt-2">
                        <a href="{{ $summary->pdf_url }}" target="_blank" class="btn btn-primary">
                            <i class="mdi mdi-file-pdf-box"></i> عرض PDF
                        </a>
                        <a href="{{ $summary->pdf_url }}" download class="btn btn-outline-primary">
                            <i class="mdi mdi-download"></i> تحميل
                        </a>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard.courses.summaries.edit', [$course->id, $summary->id]) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> تعديل
                    </a>
                    <form action="{{ route('dashboard.courses.summaries.destroy', [$course->id, $summary->id]) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الملخص؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-delete"></i> حذف
                        </button>
                    </form>
                    <a href="{{ route('dashboard.courses.summaries.index', $course->id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">معلومات الملخص</h5>
                <table class="table table-sm mb-0">
                    <tr>
                        <td><strong>المعرف:</strong></td>
                        <td>#{{ strtoupper(dechex($summary->id)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>الكورس:</strong></td>
                        <td>{{ $course->title }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
