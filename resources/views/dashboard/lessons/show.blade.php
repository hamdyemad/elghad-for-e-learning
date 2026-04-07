@extends('layouts.master')

@section('title', $lesson->title)

@section('content')
<x-breadcrumb
    title="تفاصيل الدرس: {{ $lesson->title }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدورات', 'url' => route('dashboard.courses.index')],
        ['label' => $lesson->course->title ?? 'دورة', 'url' => route('dashboard.lessons.index', ['course_id' => $lesson->course_id])],
        ['label' => $lesson->title]
    ]"
/>

<div class="row">
    <div class="col-lg-8 col-md-12">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="header-title mb-0 text-primary">المعلومات الأساسية</h5>
                    <span class="badge badge-soft-info font-size-13 px-3">الترتيب: {{ $lesson->order }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h3 class="font-weight-bold text-dark">{{ $lesson->title }}</h3>
                    <p class="text-muted font-size-15">{{ $lesson->topic }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted mb-1">اسم الدورة</label>
                        <p class="font-weight-bold font-size-16">
                            <i class="mdi mdi-book-open-outline text-primary mr-1"></i>
                            {{ $lesson->course->title ?? 'غير متوفر' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted mb-1">المدة</label>
                        <p class="font-weight-bold font-size-16">
                            <i class="mdi mdi-clock-outline text-primary mr-1"></i>
                            {{ $lesson->duration ?? '0' }} دقيقة
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted mb-1">حالة الدرس</label>
                        <div class="mt-1">
                            @if($lesson->is_free)
                                <span class="badge badge-success px-3 py-1">مجاني للجميع</span>
                            @else
                                <span class="badge badge-danger px-3 py-1">مدفوع للمشتركين</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light border-top">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dashboard.lessons.edit', $lesson->id) }}" class="btn btn-warning waves-effect waves-light mx-1">
                        <i class="mdi mdi-pencil-outline"></i> تعديل البيانات
                    </a>
                    <a href="{{ route('dashboard.lessons.reorder.form', ['course_id' => $lesson->course_id]) }}" class="btn btn-primary waves-effect waves-light mx-1">
                        <i class="mdi mdi-sort-variant"></i> إعادة ترتيب
                    </a>
                    <button type="button" class="btn btn-danger waves-effect waves-light mx-1" 
                            data-toggle="modal" data-target="#deleteModal" 
                            data-action="{{ route('dashboard.lessons.destroy', $lesson->id) }}">
                        <i class="mdi mdi-delete-outline"></i> حذف الدرس
                    </button>
                    <a href="{{ route('dashboard.lessons.index', ['course_id' => $lesson->course_id]) }}" class="btn btn-secondary waves-effect waves-light mx-1">
                        <i class="mdi mdi-arrow-right"></i> عودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-12">
        <!-- Resource: Video -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="header-title mb-0"><i class="mdi mdi-video-outline mr-1"></i> محتوى الفيديو</h5>
            </div>
            <div class="card-body text-center py-4">
                @if($lesson->outsource_link)
                    <div class="display-4 text-danger mb-3">
                        <i class="mdi mdi-play-circle-outline"></i>
                    </div>
                    <h6 class="mb-3 font-weight-bold">يتوفر فيديو من نوع: {{ strtoupper($lesson->outsource_type) }}</h6>
                    <a href="{{ $lesson->outsource_link }}" target="_blank" class="btn btn-outline-danger btn-block btn-lg shadow-sm">
                        <i class="mdi mdi-open-in-new"></i> مشاهدة الفيديو الآن
                    </a>
                @else
                    <div class="text-muted text-center py-3">
                        <i class="mdi mdi-video-off-outline font-size-24 d-block mb-2"></i>
                        لا يوجد رابط فيديو لهذا الدرس
                    </div>
                @endif
            </div>
        </div>

        <!-- Resource: PDF -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="header-title mb-0"><i class="mdi mdi-file-pdf-outline mr-1"></i> الملف المرفق</h5>
            </div>
            <div class="card-body text-center py-4">
                @if($lesson->file_pdf)
                    <div class="display-4 text-success mb-3">
                        <i class="mdi mdi-file-document-outline"></i>
                    </div>
                    <h6 class="mb-3 font-weight-bold text-dark">يتوفر ملف PDF لهذا الدرس</h6>
                    <a href="{{ asset('storage/' . $lesson->file_pdf) }}" target="_blank" class="btn btn-outline-success btn-block btn-lg shadow-sm">
                        <i class="mdi mdi-download"></i> تحميل/عرض الملف
                    </a>
                @else
                    <div class="text-muted text-center py-3">
                        <i class="mdi mdi-file-remove-outline font-size-24 d-block mb-2"></i>
                        لا يوجد ملف مرفق لهذا الدرس
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<x-delete-modal />
@endsection
