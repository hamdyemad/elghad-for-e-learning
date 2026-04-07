@extends('layouts.master')

@section('title', isset($lesson) ? 'تعديل الدرس' : 'إضافة درس جديد')

@section('content')
<x-breadcrumb
    :title="isset($lesson) ? 'تعديل الدرس: ' . $lesson->title : 'إضافة درس جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدورات', 'url' => route('dashboard.courses.index')],
        ['label' => isset($lesson) ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($lesson) ? route('dashboard.lessons.update', $lesson->id) : route('dashboard.lessons.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if(isset($lesson))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="topic"
                                label="الموضوع"
                                :value="old('topic', isset($lesson) ? $lesson->topic : '')"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="title"
                                label="العنوان"
                                :value="old('title', isset($lesson) ? $lesson->title : '')"
                                :required="true"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-select
                                name="course_id"
                                label="الدورة"
                                :value="old('course_id', isset($lesson) ? $lesson->course_id : (request('course_id') ?? ''))"
                                :options="['' => '-- اختر الدورة --'] + \App\Models\Course::pluck('title', 'id')->toArray()"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-form-input
                                name="order"
                                label="الترتيب"
                                type="number"
                                :value="old('order', isset($lesson) ? $lesson->order : '')"
                                hint="سيتم ترتيب الدروس تلقائياً إذا تركتها فارغة"
                            />
                        </div>
                        <div class="col-md-3">
                            <x-form-input
                                name="duration"
                                label="المدة (بالدقائق)"
                                type="number"
                                :value="old('duration', isset($lesson) ? $lesson->duration : '')"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-select
                                name="outsource_type"
                                label="نوع الرابط الخارجي"
                                :value="old('outsource_type', isset($lesson) ? $lesson->outsource_type : '')"
                                :options="[
                                    '' => '-- اختر النوع --',
                                    'vimeo' => 'Vimeo',
                                    'firebase' => 'Firebase',
                                    'vdocipher' => 'VdoCipher',
                                    'youtube' => 'YouTube',
                                    'other' => 'أخرى'
                                ]"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="outsource_link"
                                label="الرابط الخارجي"
                                type="url"
                                :value="old('outsource_link', isset($lesson) ? $lesson->outsource_link : '')"
                                placeholder="https://..."
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            @php
                                $currentFile = null;
                                if(isset($lesson) && $lesson->file_pdf) {
                                    $currentFile = asset('storage/' . $lesson->file_pdf);
                                }
                            @endphp
                            <x-form-file
                                name="file_pdf"
                                label="ملف الفيديو/PDF"
                                :currentFile="$currentFile"
                                accept=".pdf,video/mp4,video/x-m4v,video/*"
                                placeholder="اسحب ملف الفيديو أو الـ PDF هنا أو اضغط للاختيار"
                            />
                            <small class="text-muted">
                                الصيغ المسموح بها: mp4, mkv, avi, mov, wmv, flv, webm, pdf - الحد الأقصى: 50 ميجابايت
                            </small>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_free" id="is_free" class="form-check-input"
                                       value="1" {{ old('is_free', isset($lesson) ? $lesson->is_free : false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_free">
                                    درس مجاني
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="mdi mdi-information"></i>
                        <strong>ملاحظة:</strong> يجب توفير إما <strong>الرابط الخارجي</strong> أو <strong>الملف المرفوع</strong> (أو كليهما).
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ isset($lesson) ? 'تحديث' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.lessons.index', ['course_id' => isset($lesson) ? $lesson->course_id : (request('course_id') ?? '')]) }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
