@extends('layouts.master')

@section('title', 'الأقسام')

@section('content')
<x-breadcrumb
    title="إدارة الأقسام"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الأقسام']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.categories.create') }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة قسم جديد
                        </x-btn>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.categories.index') }}" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="ابحث في الأقسام..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="parent_id"
                                        label="القسم الرئيسي"
                                        :value="request('parent_id')"
                                        :options="['' => '-- جميع الأقسام --', '0' => 'الأقسام الرئيسية فقط'] + $parentCategories->pluck('name', 'id')->toArray()"
                                        placeholder="بحث عن قسم رئيسي..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="status"
                                        label="الحالة"
                                        :value="request('status')"
                                        :options="['' => '-- الكل --', 'active' => 'نشط', 'inactive' => 'غير نشط']"
                                        :compact="true"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> تطبيق الفلاتر
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.categories.index') }}" variant="light" class="mx-1">
                                        <i class="mdi mdi-refresh"></i> إعادة تعيين
                                    </x-btn>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <x-alert type="success" />

                @php
                    $columns = [
                        [
                            'label' => '#',
                            'render' => function($row) {
                                return '<span class="badge badge-soft-primary font-size-12">#' . strtoupper(dechex($row->id)) . '</span>';
                            }
                        ],
                        [
                            'label' => 'الصورة',
                            'render' => function($row) {
                                $src = $row->image_url;
                                return $src ? '<img src="' . e($src) . '" alt="' . e($row->name) . '" width="50" height="50" class="rounded">' : '-';
                            }
                        ],
                        [
                            'field' => 'name',
                            'label' => 'الاسم',
                            'render' => function($row) {
                                return ($row->parent_id ? '<span class="mr-3">└─</span>' : '') . e($row->name);
                            }
                        ],
                        ['field' => 'slug', 'label' => 'Slug'],
                        [
                            'field' => 'parent.name',
                            'label' => 'القسم الرئيسي',
                            'render' => function($row) {
                                return $row->parent ? e($row->parent->name) : '-';
                            }
                        ],
                        ['field' => 'order', 'label' => 'الترتيب'],
                        [
                            'field' => 'status',
                            'label' => 'الحالة',
                            'render' => function($row) {
                                if ($row->status == 'active') {
                                    return '<span class="badge badge-success">نشط</span>';
                                } else {
                                    return '<span class="badge badge-danger">غير نشط</span>';
                                }
                            }
                        ]
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$categories"
                    :pagination="$categories"
                    route="dashboard.categories"
                    :actions="true"
                    empty-message="لا توجد أقسام بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
