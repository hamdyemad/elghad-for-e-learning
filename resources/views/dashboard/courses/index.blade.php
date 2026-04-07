@extends('layouts.master')

@section('title', 'الكورسات')

@section('content')
<x-breadcrumb
    title="إدارة الكورسات"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.create') }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة كورس جديد
                        </x-btn>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.courses.index') }}" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="ابحث في الكورسات..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="category_id"
                                        label="القسم"
                                        :value="request('category_id')"
                                        :options="['' => '-- جميع الأقسام --'] + $categories->pluck('name', 'id')->toArray()"
                                        placeholder="ابحث عن قسم..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="status"
                                        label="الحالة"
                                        :value="request('status')"
                                        :options="['' => '-- الكل --', 'draft' => 'مسودة', 'published' => 'منشور', 'public' => 'عام']"
                                        :compact="true"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> تطبيق الفلاتر
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.courses.index') }}" variant="light" class="mx-1">
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
                        ['field' => 'id', 'label' => '#'],
                        [
                            'label' => 'الصورة',
                            'render' => function($row) {
                                $default = "https://placehold.co/50x50/5b73e8/ffffff?text=" . urlencode(mb_substr($row->title, 0, 1));
                                $src = $row->thumbnail_url ?: $default;
                                return '<img src="' . e($src) . '" alt="' . e($row->title) . '" width="50" height="50" class="rounded shadow-sm" onerror="this.onerror=null;this.src=\'' . $default . '\';">';
                            }
                        ],
                        ['field' => 'title', 'label' => 'العنوان'],
                        ['field' => 'category.name', 'label' => 'القسم'],
                        ['field' => 'level', 'label' => 'المستوى'],
                        [
                            'field' => 'price',
                            'label' => 'السعر',
                            'render' => function($row) {
                                return $row->is_free ? 'مجاني' : e($row->price) . ' ج.م';
                            }
                        ],
                        [
                            'field' => 'status',
                            'label' => 'الحالة',
                            'render' => function($row) {
                                switch($row->status) {
                                    case 'published':
                                        return '<span class="badge badge-success">منشور</span>';
                                    case 'draft':
                                        return '<span class="badge badge-warning">مسودة</span>';
                                    case 'public':
                                        return '<span class="badge badge-primary">عام</span>';
                                    default:
                                        return '<span class="badge badge-secondary">' . e($row->status) . '</span>';
                                }
                            }
                        ]
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$courses"
                    :pagination="$courses"
                    route="dashboard.courses"
                    :actions="true"
                    empty-message="لا توجد كورسات بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
