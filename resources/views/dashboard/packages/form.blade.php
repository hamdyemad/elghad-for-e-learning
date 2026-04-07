@extends('layouts.master')

@section('title', isset($package) ? 'تعديل الباكج' : 'إضافة باكج جديد')

@section('content')
<x-breadcrumb
    :title="isset($package) ? 'تعديل الباكج: ' . $package->title : 'إضافة باكج جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الباقات', 'url' => route('dashboard.packages.index')],
        ['label' => isset($package) ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ isset($package) ? route('dashboard.packages.update', $package->id) : route('dashboard.packages.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if(isset($package))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="title"
                                label="عنوان الباكج"
                                :value="old('title', isset($package) ? $package->title : '')"
                                :required="true"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-select
                                name="category_id"
                                label="القسم"
                                :value="old('category_id', isset($package) ? $package->category_id : '')"
                                :options="collect(['' => '-- اختر القسم --'])->merge($categories->pluck('name', 'id'))"
                                :required="true"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <x-form-input
                                name="price"
                                label="السعر"
                                type="number"
                                step="0.01"
                                :value="old('price', isset($package) ? $package->price : '')"
                                min="0"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-select
                                name="status"
                                label="الحالة"
                                :value="old('status', isset($package) ? $package->status : 'draft')"
                                :options="[
                                    'draft' => 'مسودة',
                                    'published' => 'منشور',
                                    'public' => 'عام'
                                ]"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-form-textarea
                                name="description"
                                label="الوصف"
                                :value="old('description', isset($package) ? $package->description : '')"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-form-file
                                name="image"
                                label="الصورة"
                                :currentFile="isset($package) && $package->image ? asset('storage/' . $package->image) : null"
                            />
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <x-form-select
                                name="course_ids"
                                label="الدورات"
                                :value="old('course_ids', isset($package) ? $package->courses->pluck('id')->toArray() : [])"
                                :options="\App\Models\Course::pluck('title', 'id')"
                                :multiple="true"
                                :placeholder="'اختر الدورات...'"
                            />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ isset($package) ? 'تحديث' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.packages.index') }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#course_ids').select2({
            dir: 'rtl',
            placeholder: 'اختر الدورات...',
            allowClear: true,
            language: {
                noResults: function() { return "لا توجد نتائج"; },
                searching: function() { return "جاري البحث..."; }
            }
        }).css('opacity', '1');
    });
</script>
@endpush

@endsection
