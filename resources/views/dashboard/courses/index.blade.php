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

                    $actions = function($row) {
                        $summariesUrl = route('dashboard.courses.summaries.index', $row->id);
                        $summariesCount = $row->summaries()->count();
                        return '
                            <a href="' . $summariesUrl . '" class="btn btn-sm btn-info mx-1" title="الملخصات">
                                <i class="mdi mdi-file-document-outline"></i>
                                ' . ($summariesCount > 0 ? '<span class="badge badge-light">' . $summariesCount . '</span>' : '') . '
                            </a>
                            <a href="' . route('dashboard.courses.show', $row->id) . '" class="btn btn-sm btn-info mx-1">
                                <i class="mdi mdi-eye"></i>
                            </a>
                            <a href="' . route('dashboard.courses.edit', $row->id) . '" class="btn btn-sm btn-warning mx-1">
                                <i class="mdi mdi-pencil"></i>
                            </a>
                            <form action="' . route('dashboard.courses.destroy', $row->id) . '" method="POST" class="d-inline"
                                  onsubmit="return confirm(\'هل أنت متأكد من حذف هذا الكورس؟\');">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="submit" class="btn btn-sm btn-danger mx-1">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </form>
                        ';
                    };
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$courses"
                    :pagination="$courses"
                    :actions="$actions"
                    empty-message="لا توجد كورسات بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
