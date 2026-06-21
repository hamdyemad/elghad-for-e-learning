@extends('layouts.master')

@section('title', 'تفاصيل الإشعار')

@section('content')
<x-breadcrumb
    title="تفاصيل الإشعار"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الإعدادات', 'url' => route('dashboard.settings.edit')],
        ['label' => 'الإشعارات', 'url' => route('dashboard.notifications.index')],
        ['label' => 'التفاصيل']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">{{ $notification->title }}</h4>
                    <div>
                        @if($notification->is_read)
                            <span class="badge badge-success">مقروء</span>
                        @else
                            <span class="badge badge-danger">غير مقروء</span>
                        @endif
                        @if($notification->sent_via_firebase)
                            <span class="badge badge-info">Firebase</span>
                        @endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>المرسل:</strong>
                        <span class="text-muted">{{ $notification->sender->name ?? 'غير محدد' }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>نوع المستلم:</strong>
                        @php
                            $typeLabels = [
                                'all_students' => 'جميع الطلاب',
                                'all_instructors' => 'جميع المحاضرين',
                                'single_student' => 'طالب محدد',
                                'single_instructor' => 'محاضر محدد',
                            ];
                        @endphp
                        <span class="badge badge-info">{{ $typeLabels[$notification->recipient_type] ?? $notification->recipient_type }}</span>
                    </div>
                </div>

                @if($notification->recipient)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>المستخدم المستهدف:</strong>
                        <span class="text-muted">{{ $notification->recipient->name }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>البريد الإلكتروني:</strong>
                        <span class="text-muted">{{ $notification->recipient->email }}</span>
                    </div>
                </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>تاريخ الإرسال:</strong>
                        <span class="text-muted">{{ $notification->created_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    @if($notification->read_at)
                    <div class="col-md-6">
                        <strong>تاريخ القراءة:</strong>
                        <span class="text-muted">{{ $notification->read_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                    @endif
                </div>

                <hr>

                <div class="mb-3">
                    <strong>نص الإشعار:</strong>
                    <div class="card bg-light mt-2">
                        <div class="card-body">
                            {!! nl2br(e($notification->body)) !!}
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <form action="{{ route('dashboard.notifications.destroy', $notification->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="mdi mdi-delete"></i> حذف الإشعار
                        </button>
                    </form>
                    <a href="{{ route('dashboard.notifications.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">معلومات الإشعار</h5>
                <table class="table table-sm mb-0">
                    <tr>
                        <td><strong>المعرف:</strong></td>
                        <td>#{{ strtoupper(dechex($notification->id)) }}</td>
                    </tr>
                    <tr>
                        <td><strong>النوع:</strong></td>
                        <td>{{ $typeLabels[$notification->recipient_type] ?? $notification->recipient_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>الحالة:</strong></td>
                        <td>{{ $notification->is_read ? 'مقروء' : 'غير مقروء' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Firebase:</strong></td>
                        <td>{{ $notification->sent_via_firebase ? 'مُرسل' : 'لم يُرسل' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
