@extends('layouts.master')

@section('title', isset($course) ? 'تعديل الدورة' : 'إضافة دورة جديدة')

@section('content')
<x-breadcrumb
    :title="isset($course) ? 'تعديل الدورة: ' . $course->title : 'إضافة دورة جديدة'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدورات', 'url' => route('dashboard.courses.index')],
        ['label' => isset($course) ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($course) ? route('dashboard.courses.update', $course->id) : route('dashboard.courses.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if(isset($course))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="title"
                                label="عنوان الدورة"
                                :value="old('title', isset($course) ? $course->title : '')"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-select
                                name="category_id"
                                label="القسم"
                                :value="old('category_id', isset($course) ? $course->category_id : '')"
                                :options="['' => '-- اختر القسم --'] + $categories->pluck('name', 'id')->toArray()"
                                :required="true"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-select
                                name="instructor_id"
                                label="المحاضر"
                                :value="old('instructor_id', isset($course) ? $course->instructor_id : '')"
                                :options="['' => '-- اختر المحاضر --'] + $instructors->pluck('name', 'id')->toArray()"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="price"
                                label="السعر"
                                type="number"
                                step="0.01"
                                :value="old('price', isset($course) ? $course->price : '')"
                                :required="false"
                                min="0"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-input
                                name="professor_profit"
                                label="ربح المحاضر"
                                type="number"
                                step="0.01"
                                :value="old('professor_profit', isset($course) ? $course->professor_profit : '')"
                                min="0"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-select
                                name="status"
                                label="الحالة"
                                :value="old('status', isset($course) ? $course->status : 'draft')"
                                :options="[
                                    'draft' => 'مسودة',
                                    'published' => 'منشور',
                                    'public' => 'عام'
                                ]"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-input
                                name="level"
                                label="المستوى"
                                :value="old('level', isset($course) ? $course->level : '')"
                                maxlength="100"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="duration"
                                label="المدة"
                                :value="old('duration', isset($course) ? $course->duration : '')"
                                maxlength="100"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-form-textarea
                                name="description"
                                label="الوصف"
                                :value="old('description', isset($course) ? $course->description : '')"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-form-file
                                name="thumbnail"
                                label="الصورة المصغرة"
                                :currentFile="isset($course) && $course->thumbnail ? asset('storage/' . $course->thumbnail) : null"
                            />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ isset($course) ? 'تحديث' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.courses.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
