@extends('layouts.master')

@section('title', 'الباقات')

@section('content')
<x-breadcrumb
    title="إدارة الباقات"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الباقات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.packages.create') }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة باقة جديدة
                        </x-btn>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.packages.index') }}" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="ابحث في الباقات..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form-select
                                        name="status"
                                        label="الحالة"
                                        :value="request('status')"
                                        :options="collect(['' => '-- الكل --'])->merge(['draft' => 'مسودة', 'published' => 'منشور', 'public' => 'عام'])"
                                        :compact="true"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> تطبيق الفلاتر
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.packages.index') }}" variant="light" class="mx-1">
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
                        ['field' => 'title', 'label' => 'العنوان'],
                        [
                            'field' => 'price',
                            'label' => 'السعر',
                            'render' => function($row) {
                                return e($row->price) . ' ج.م';
                            }
                        ],
                        ['field' => 'category.name', 'label' => 'القسم'],
                        ['field' => 'courses_count', 'label' => 'عدد الكورسات'],
                        [
                            'field' => 'status',
                            'label' => 'الحالة',
                            'render' => function($row) {
                                switch($row->status) {
                                    case 'published':
                                        return '<span class="badge badge-success">منشور</span>';
                                    case 'public':
                                        return '<span class="badge badge-info">عام</span>';
                                    case 'draft':
                                    default:
                                        return '<span class="badge badge-warning">مسودة</span>';
                                }
                            }
                        ]
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$packages"
                    :pagination="$packages"
                    route="dashboard.packages"
                    :actions="true"
                    empty-message="لا توجد باقات بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
