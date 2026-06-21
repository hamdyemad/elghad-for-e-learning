@extends('layouts.master')

@section('title', 'ملخصات الكورس: ' . $course->title)

@section('content')
<x-breadcrumb
    title="ملخصات الكورس: {{ $course->title }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الملخصات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.summaries.create', $course->id) }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة ملخص جديد
                        </x-btn>
                    </div>
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.show', $course->id) }}" variant="secondary" block>
                            <i class="mdi mdi-arrow-right"></i> العودة للكورس
                        </x-btn>
                    </div>
                </div>

                <x-alert type="success" />

                @if($summaries->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الملف</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summaries as $summary)
                            <tr>
                                <td>
                                    <span class="badge badge-soft-primary font-size-12">#{{ strtoupper(dechex($summary->id)) }}</span>
                                </td>
                                <td>{{ $summary->title }}</td>
                                <td>
                                    <a href="{{ $summary->pdf_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-file-pdf-box"></i> PDF
                                    </a>
                                </td>
                                <td>{{ $summary->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.courses.summaries.edit', [$course->id, $summary->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dashboard.courses.summaries.destroy', [$course->id, $summary->id]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الملخص؟');">
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
                @if($summaries->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $summaries->withQueryString()->links() }}
                </div>
                @endif
                @else
                <div class="text-center p-4">
                    <i class="mdi mdi-file-document-outline text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">لا توجد ملخصات لهذا الكورس بعد</p>
                    <a href="{{ route('dashboard.courses.summaries.create', $course->id) }}" class="btn btn-primary mt-2">
                        <i class="mdi mdi-plus"></i> إضافة أول ملخص
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
