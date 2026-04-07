@extends('layouts.master')

@section('title', 'المحاضرين')

@section('content')
<x-breadcrumb
    title="إدارة المحاضرين"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'المحاضرين']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.instructors.create') }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة محاضر جديد
                        </x-btn>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row mb-3">
                    <!-- Stats -->
                    <div class="col-md-4">
                        <div class="card mini-stat bg-primary text-white shadow-sm border-0" style="background: linear-gradient(to right, #5b73e8, #4458d1);">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="float-left mini-stat-img">
                                        <i class="mdi mdi-tie font-size-24 text-white-50"></i>
                                    </div>
                                    <h5 class="font-size-16 text-uppercase mt-0 text-white-50">إجمالي المحاضرين</h5>
                                    <h4 class="font-weight-medium font-size-24">{{ $totalInstructors ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stat bg-success text-white shadow-sm border-0" style="background: linear-gradient(to right, #34c38f, #2aa175);">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="float-left mini-stat-img">
                                        <i class="mdi mdi-certificate font-size-24 text-white-50"></i>
                                    </div>
                                    <h5 class="font-size-16 text-uppercase mt-0 text-white-50">المحاضرين النشطين</h5>
                                    <h4 class="font-weight-medium font-size-24">{{ $activeInstructors ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stat bg-danger text-white shadow-sm border-0" style="background: linear-gradient(to right, #f46a6a, #d9534f);">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="float-left mini-stat-img">
                                        <i class="mdi mdi-account-cancel font-size-24 text-white-50"></i>
                                    </div>
                                    <h5 class="font-size-16 text-uppercase mt-0 text-white-50">المحاضرين غير النشطين</h5>
                                    <h4 class="font-weight-medium font-size-24">{{ $inactiveInstructors ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.instructors.index') }}" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="ابحث عن محاضر بالاسم أو البريد الإلكتروني..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="status"
                                        label="الحالة"
                                        :value="request('status')"
                                        :options="['' => '-- الكل --', 'active' => 'نشط', 'inactive' => 'غير نشط']"
                                        placeholder="اختر الحالة..."
                                        :compact="true"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> تطبيق الفلاتر
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.instructors.index') }}" variant="light" class="mx-1">
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
                                $src = $row->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($row->name) . '&size=50&background=random';
                                return '<img src="' . e($src) . '" alt="' . e($row->name) . '" width="50" height="50" class="rounded-circle">';
                            }
                        ],
                        ['field' => 'name', 'label' => 'الاسم'],
                        ['field' => 'email', 'label' => 'البريد الإلكتروني'],
                        [
                            'field' => 'balance',
                            'label' => __('auth.balance'),
                            'render' => function($row) {
                                return format_currency($row->balance ?? 0);
                            }
                        ],
                        [
                            'field' => 'specialization',
                            'label' => 'التخصص',
                            'render' => function($row) {
                                return $row->specialization ?? '-';
                            }
                        ],
                        [
                            'field' => 'hourly_rate',
                            'label' => 'معدل الساعة',
                            'render' => function($row) {
                                return $row->hourly_rate ? number_format($row->hourly_rate, 2) . ' ج.م' : '-';
                            }
                        ],
                        [
                            'field' => 'is_verified',
                            'label' => 'تفعيل البريد',
                            'render' => function($row) {
                                return $row->is_verified
                                    ? '<span class="badge badge-success">مفعل</span>'
                                    : '<span class="badge badge-warning">غير مفعل</span>';
                            }
                        ],
                        [
                            'field' => 'status',
                            'label' => 'الحالة',
                            'render' => function($row) {
                                if (isset($row->status) && $row->status == 'active') {
                                    return '<span class="badge badge-success">نشط</span>';
                                } elseif (isset($row->status) && $row->status == 'inactive') {
                                    return '<span class="badge badge-danger">غير نشط</span>';
                                }
                                return '<span class="badge badge-secondary">-</span>';
                            }
                        ],
                        [
                            'field' => 'created_at',
                            'label' => 'تاريخ الإضافة',
                            'render' => function($row) {
                                return $row->created_at ? $row->created_at->format('Y-m-d') : '-';
                            }
                        ]
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$instructors"
                    :pagination="$instructors"
                    route="dashboard.instructors"
                    :actions="fn($row) => view('dashboard.instructors.partials.actions', compact('row'))"
                    empty-message="لا يوجد محاضرين بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
