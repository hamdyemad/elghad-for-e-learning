@extends('layouts.master')

@section('title', 'الدروس')

@section('content')
<x-breadcrumb
    title="إدارة الدروس"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدروس']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        @if($course)
                            <x-btn href="{{ route('dashboard.lessons.create', ['course_id' => $course->id]) }}" variant="primary" block>
                                <i class="mdi mdi-plus"></i> إضافة درس جديد
                            </x-btn>
                        @else
                            <x-btn type="button" variant="primary" block disabled title="اختر كورس أولاً لتمكين إضافة الدروس">
                                <i class="mdi mdi-plus"></i> إضافة درس جديد
                            </x-btn>
                        @endif
                    </div>
                    @if($course)
                        <div class="col-md-3">
                            <x-btn href="{{ route('dashboard.lessons.reorder.form', ['course_id' => $course->id]) }}" variant="info" block>
                                <i class="mdi mdi-sort-variant"></i> ترتيب الدروس
                            </x-btn>
                        </div>
                    @endif
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.lessons.index') }}" id="filter-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="بحث في الدروس..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <x-form-select
                                        name="course_id"
                                        label="تصفية حسب الكورس"
                                        :value="request('course_id')"
                                        :options="['' => '-- جميع الكورسات --'] + $courses->pluck('title', 'id')->toArray()"
                                        placeholder="اختر كورس أو ابحث..."
                                        :compact="true"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-end">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> عرض الدروس
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.lessons.index') }}" variant="light" class="mx-1">
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
                        ['field' => 'id', 'label' => '#', 'width' => '60'],
                        [
                            'field' => 'order',
                            'label' => 'الترتيب',
                            'width' => '100',
                            'class' => 'text-center',
                            'render' => function($row) {
                                return '<span class="badge badge-soft-primary px-3">' . e($row->order) . '</span>';
                            }
                        ],
                        ['field' => 'title', 'label' => 'العنوان', 'class' => 'text-right'],
                        ['field' => 'course.title', 'label' => 'الكورس'],
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$lessons"
                    :pagination="$lessons"
                    route="dashboard.lessons"
                    :actions="true"
                    empty-message="لا توجد دروس بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
