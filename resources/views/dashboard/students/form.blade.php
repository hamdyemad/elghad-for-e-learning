@extends('layouts.master')

@section('title', $student ? 'تعديل طالب' : 'إضافة طالب جديد')

@section('content')
<x-breadcrumb
    :title="$student ? 'تعديل طالب' : 'إضافة طالب جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الطلاب', 'url' => route('dashboard.students.index')],
        ['label' => $student ? 'تعديل' : 'إضافة جديد']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ $student ? route('dashboard.students.update', $student->id) : route('dashboard.students.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($student)
                        @method('PUT')
                    @endif

                    <x-alert type="success" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="name"
                                label="اسم الطالب"
                                :value="old('name', $student->name ?? '')"
                                :required="true"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-input
                                name="email"
                                type="email"
                                label="البريد الإلكتروني"
                                :value="old('email', $student->email ?? '')"
                                :required="true"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="password"
                                type="password"
                                label="كلمة المرور"
                                :required="!$student"
                                placeholder="{{ $student ? 'اتركه فارغًا إذا لم ترغب في التغيير' : '' }}"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-input
                                name="password_confirmation"
                                type="password"
                                label="تأكيد كلمة المرور"
                                :required="!$student"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="phone"
                                type="tel"
                                label="رقم الهاتف"
                                :value="old('phone', $student->phone ?? '')"
                                placeholder="+966500000000"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-select
                                name="status"
                                label="الحالة"
                                :value="old('status', $student->status ?? 'active')"
                                :options="['active' => 'نشط', 'inactive' => 'غير نشط']"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="date_of_birth"
                                type="date"
                                label="تاريخ الميلاد"
                                :value="old('date_of_birth', $student->date_of_birth ?? '')"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-select
                                name="gender"
                                label="الجنس"
                                :value="old('gender', $student->gender ?? '')"
                                :options="['' => '-- اختر الجنس --', 'male' => 'ذكر', 'female' => 'أنثى', 'other' => 'أخرى']"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <x-form-textarea
                                name="address"
                                label="العنوان"
                                :value="old('address', $student->address ?? '')"
                                :rows="3"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <x-form-file
                                name="avatar"
                                label="الصورة الشخصية"
                                :currentFile="$student->avatar_url ?? null"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <x-btn type="submit" variant="primary">
                            <i class="mdi mdi-content-save"></i> {{ $student ? 'تحديث البيانات' : 'إنشاء الحساب' }}
                        </x-btn>
                        <x-btn href="{{ route('dashboard.students.index') }}" variant="secondary" class="mx-2">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </x-btn>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($student)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">معلومات إضافية</h5>
                    <div class="text-center mb-3">
                        <img src="{{ $student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&size=100&background=random' }}"
                             alt="{{ $student->name }}"
                             class="rounded-circle mb-3"
                             width="100"
                             height="100">
                        <h6>{{ $student->name }}</h6>
                        <p class="text-muted">{{ $student->email }}</p>
                    </div>
                    <hr>
                    <p><strong>تاريخ التسجيل:</strong> {{ $student->created_at->format('Y-m-d') }}</p>
                    <p><strong>آخر تحديث:</strong> {{ $student->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title text-danger">حذف الطالب</h5>
                    <p class="text-muted">لا يمكن التراجع عن هذا الإجراء.</p>
                    <form action="{{ route('dashboard.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">
                        @csrf
                        @method('DELETE')
                        <x-btn type="submit" variant="danger" block>
                            <i class="mdi mdi-delete"></i> حذف الطالب
                        </x-btn>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
