@extends('layouts.master')

@section('title', $category ? 'تعديل القسم' : 'إضافة قسم جديد')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@endpush

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb 
    :title="$category ? 'تعديل القسم: ' . $category->name : 'إضافة قسم جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الأقسام', 'url' => route('dashboard.categories.index')],
        ['label' => $category ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form 
                    action="{{ $category ? route('dashboard.categories.update', $category->id) : route('dashboard.categories.store') }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if($category)
                        @method('PUT')
                    @endif

                    <x-form-input 
                        name="name" 
                        label="اسم القسم" 
                        :value="$category->name ?? ''"
                        :required="true"
                    />

                    <x-form-select 
                        name="parent_id" 
                        label="القسم الرئيسي (اختياري)"
                        :value="$category->parent_id ?? ''"
                        :options="collect(['' => '-- قسم رئيسي --'])->merge($parentCategories->filter(function($parent) use ($category) {
                            return !$category || $parent->id != $category->id;
                        })->pluck('name', 'id'))"
                    />

                    <x-form-textarea 
                        name="description" 
                        label="الوصف"
                        :value="$category->description ?? ''"
                    />

                    <x-form-file 
                        name="image" 
                        label="الصورة (اختياري)"
                        :currentFile="$category->image_url ?? null"
                    />

                    <x-form-input 
                        name="order" 
                        type="number"
                        label="الترتيب" 
                        :value="$category->order ?? 0"
                    />

                    <x-form-switch 
                        name="status" 
                        label="الحالة"
                        :value="$category->status ?? 'active'"
                        onLabel="نشط"
                        offLabel="غير نشط"
                    />

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ $category ? 'حفظ التعديلات' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-cancel"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
