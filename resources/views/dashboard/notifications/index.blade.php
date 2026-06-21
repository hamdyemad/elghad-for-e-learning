@extends('layouts.master')

@section('title', 'الإشعارات')

@section('content')
<x-breadcrumb
    title="إدارة الإشعارات"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الإعدادات', 'url' => route('dashboard.settings.edit')],
        ['label' => 'الإشعارات']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.notifications.create') }}" variant="primary" block>
                            <i class="mdi mdi-bell-plus"></i> إرسال إشعار جديد
                        </x-btn>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('dashboard.notifications.index') }}" id="filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <x-form-input
                                        name="search"
                                        label="بحث"
                                        :value="request('search')"
                                        placeholder="ابحث في الإشعارات..."
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-form-select
                                        name="recipient_type"
                                        label="نوع المستلم"
                                        :value="request('recipient_type')"
                                        :options="[
                                            '' => '-- الكل --',
                                            'all_students' => 'جميع الطلاب',
                                            'all_instructors' => 'جميع المحاضرين',
                                            'single_student' => 'طالب محدد',
                                            'single_instructor' => 'محاضر محدد'
                                        ]"
                                        :compact="true"
                                    />
                                </div>
                                <div class="col-md-4">
                                    <x-btn type="submit" variant="secondary" class="mx-1">
                                        <i class="mdi mdi-filter"></i> تطبيق الفلاتر
                                    </x-btn>
                                    <x-btn href="{{ route('dashboard.notifications.index') }}" variant="light" class="mx-1">
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
                        ['field' => 'title', 'label' => 'العنوان'],
                        [
                            'field' => 'body',
                            'label' => 'النص',
                            'render' => function($row) {
                                return '<span class="text-muted">' . e(Str::limit($row->body, 50)) . '</span>';
                            }
                        ],
                        [
                            'field' => 'recipient_type',
                            'label' => 'نوع المستلم',
                            'render' => function($row) {
                                $labels = [
                                    'all_students' => '<span class="badge badge-info">جميع الطلاب</span>',
                                    'all_instructors' => '<span class="badge badge-warning">جميع المحاضرين</span>',
                                    'single_student' => '<span class="badge badge-success">طالب</span>',
                                    'single_instructor' => '<span class="badge badge-primary">محاضر</span>',
                                ];
                                return $labels[$row->recipient_type] ?? $row->recipient_type;
                            }
                        ],
                        [
                            'field' => 'sender.name',
                            'label' => 'المرسل',
                            'render' => function($row) {
                                return $row->sender ? e($row->sender->name) : '-';
                            }
                        ],
                        [
                            'field' => 'is_read',
                            'label' => 'الحالة',
                            'render' => function($row) {
                                return $row->is_read
                                    ? '<span class="badge badge-success">مقروء</span>'
                                    : '<span class="badge badge-danger">غير مقروء</span>';
                            }
                        ],
                        [
                            'field' => 'sent_via_firebase',
                            'label' => 'Firebase',
                            'render' => function($row) {
                                return $row->sent_via_firebase
                                    ? '<span class="badge badge-success">مُرسل</span>'
                                    : '<span class="badge badge-secondary">لم يُرسل</span>';
                            }
                        ],
                        [
                            'field' => 'created_at',
                            'label' => 'التاريخ',
                            'render' => function($row) {
                                return $row->created_at->format('Y-m-d H:i');
                            }
                        ],
                    ];
                @endphp

                <x-data-table
                    :columns="$columns"
                    :rows="$notifications"
                    :pagination="$notifications"
                    route="dashboard.notifications"
                    :actions="true"
                    :show-action="true"
                    :delete-action="true"
                    empty-message="لا توجد إشعارات بعد"
                />
            </div>
        </div>
    </div>
</div>
@endsection
