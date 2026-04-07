@extends('layouts.master')

@section('title', $course->title)

@section('content')
<x-breadcrumb
    title="تفاصيل الدورة"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدورات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title]
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>{{ $course->title }}</h4>
                        <p class="text-muted">{{ $course->description }}</p>

                        <table class="table table-borderless">
                            <tr>
                                <th>القسم:</th>
                                <td>{{ $course->category ? $course->category->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>المحاضر:</th>
                                <td>{{ $course->instructor ? $course->instructor->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>السعر:</th>
                                <td>{{ $course->price }} ر.س</td>
                            </tr>
                            <tr>
                                <th>ربح المحاضر:</th>
                                <td>{{ $course->professor_profit }} ر.س</td>
                            </tr>
                            <tr>
                                <th>الحالة:</th>
                                <td>
                                    @if($course->status == 'published')
                                        <span class="badge badge-success">منشور</span>
                                    @elseif($course->status == 'draft')
                                        <span class="badge badge-warning">مسودة</span>
                                    @else
                                        <span class="badge badge-info">{{ $course->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>المستوى:</th>
                                <td>{{ $course->level ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>المدة:</th>
                                <td>{{ $course->duration ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Thumbnail" class="img-fluid rounded">
                        @else
                            <div class="bg-light p-4 text-center rounded">
                                <i class="mdi mdi-image-off font-size-48 text-muted"></i>
                                <p class="text-muted mt-2">لا توجد صورة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard.courses.edit', $course->id) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> تعديل
                    </a>
                    <a href="{{ route('dashboard.lessons.index', ['course_id' => $course->id]) }}" class="btn btn-info">
                        <i class="mdi mdi-book-open-page-variant"></i> عرض الدروس ({{ $course->lessons->count() }})
                    </a>
                    <a href="{{ route('dashboard.courses.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> عودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
