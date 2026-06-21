@extends('layouts.master')

@section('title', $summary ? 'تعديل الملخص' : 'إضافة ملخص جديد')

@section('content')
<x-breadcrumb
    :title="$summary ? 'تعديل الملخص: ' . $summary->title : 'إضافة ملخص جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الملخصات', 'url' => route('dashboard.courses.summaries.index', $course->id)],
        ['label' => $summary ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ $summary ? route('dashboard.courses.summaries.update', [$course->id, $summary->id]) : route('dashboard.courses.summaries.store', $course->id) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if($summary)
                        @method('PUT')
                    @endif

                    <x-alert type="success" />
                    <x-alert type="error" />

                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        الكورس: <strong>{{ $course->title }}</strong>
                    </div>

                    <x-form-input
                        name="title"
                        label="عنوان الملخص"
                        :value="old('title', $summary->title ?? '')"
                        :required="true"
                        placeholder="أدخل عنوان الملخص"
                    />

                    <div class="form-group mb-4">
                        <label for="pdf">ملف PDF <span class="text-danger">*</span></label>
                        <input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf"
                               {{ $summary ? '' : 'required' }}>
                        @error('pdf') <span class="text-danger small">{{ $message }}</span> @enderror
                        @if($summary)
                            <small class="text-muted">اتركه فارغ إذا لم ترغب في التغيير</small>
                        @endif
                    </div>

                    @if($summary && $summary->pdf_url)
                    <div class="form-group mb-4">
                        <label>الملف الحالي:</label>
                        <div>
                            <a href="{{ $summary->pdf_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-file-pdf-box"></i> عرض PDF
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ $summary ? 'حفظ التعديلات' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.courses.summaries.index', $course->id) }}" class="btn btn-secondary">
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
                        الملفات المسموح بها: PDF فقط
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-file-document-outline text-warning"></i>
                        الحد الأقصى للحجم: 10MB
                    </li>
                    <li>
                        <i class="mdi mdi-lock-outline text-success"></i>
                        الملخصات متاحة للمشتركين فقط في الكورس
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
