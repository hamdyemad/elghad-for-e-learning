@extends('layouts.master')

@section('title', 'بيانات الطالب')

@section('content')
<x-breadcrumb
    title="بيانات الطالب"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الطلاب', 'url' => route('dashboard.students.index')],
        ['label' => $student->name]
    ]"
/>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=150&background=random' }}"
                         alt="{{ $student->name }}"
                         class="rounded-circle mb-3"
                         width="150"
                         height="150">
                    <h4>{{ $student->name }}</h4>
                    <p class="text-muted">{{ $student->email }}</p>

                    @if($student->status == 'active')
                        <span class="badge badge-success">نشط</span>
                    @else
                        <span class="badge badge-danger">غير نشط</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">إجراءات سريعة</h5>
                <div class="d-grid gap-2">
                    <x-btn href="{{ route('dashboard.students.edit', $student->id) }}" variant="primary">
                        <i class="mdi mdi-pencil"></i> تعديل البيانات
                    </x-btn>
                    <x-btn href="{{ route('dashboard.students.index') }}" variant="secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </x-btn>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">معلومات الطالب</h5>

                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td width="30%"><strong>الاسم:</strong></td>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>البريد الإلكتروني:</strong></td>
                            <td>{{ $student->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>رقم الهاتف:</strong></td>
                            <td>{{ $student->phone ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>العنوان:</strong></td>
                            <td>{{ $student->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الميلاد:</strong></td>
                            <td>{{ $student->date_of_birth ? $student->date_of_birth->format('Y-m-d') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>الجنس:</strong></td>
                            <td>
                                @if($student->gender == 'male')
                                    ذكر
                                @elseif($student->gender == 'female')
                                    أنثى
                                @elseif($student->gender == 'other')
                                    أخرى
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>الحالة:</strong></td>
                            <td>
                                @if($student->status == 'active')
                                    <span class="badge badge-success">نشط</span>
                                @elseif($student->status == 'inactive')
                                    <span class="badge badge-danger">غير نشط</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>مُ verification:</strong></td>
                            <td>
                                @if($student->is_verified)
                                    <span class="badge badge-success">نعم</span>
                                @else
                                    <span class="badge badge-warning">لا</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ التسجيل:</strong></td>
                            <td>{{ $student->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>آخر تحديث:</strong></td>
                            <td>{{ $student->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
