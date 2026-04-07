@extends('layouts.master')

@section('title', $instructor ? 'تعديل محاضر' : 'إضافة محاضر جديد')

@section('content')
<x-breadcrumb
    :title="$instructor ? 'تعديل محاضر' : 'إضافة محاضر جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'المحاضرين', 'url' => route('dashboard.instructors.index')],
        ['label' => $instructor ? 'تعديل' : 'إضافة جديد']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ $instructor ? route('dashboard.instructors.update', $instructor->id) : route('dashboard.instructors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($instructor)
                        @method('PUT')
                    @endif

                    <x-alert type="success" />

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="name"
                                label="اسم المحاضر"
                                :value="old('name', $instructor->name ?? '')"
                                :required="true"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-input
                                name="email"
                                type="email"
                                label="البريد الإلكتروني"
                                :value="old('email', $instructor->email ?? '')"
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
                                :required="!$instructor"
                                placeholder="{{ $instructor ? 'اتركه فارغًا إذا لم ترغب في التغيير' : '' }}"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-input
                                name="password_confirmation"
                                type="password"
                                label="تأكيد كلمة المرور"
                                :required="!$instructor"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="specialization"
                                label="التخصص"
                                :value="old('specialization', $instructor->specialization ?? '')"
                                placeholder="مثال: تطوير الويب، تصميم UI/UX..."
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-input
                                name="hourly_rate"
                                type="number"
                                step="0.01"
                                label="معدل الساعة (ج.م)"
                                :value="old('hourly_rate', $instructor->hourly_rate ?? '')"
                                placeholder="0.00"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="experience_years"
                                type="number"
                                label="سنوات الخبرة"
                                :value="old('experience_years', $instructor->experience_years ?? '')"
                                placeholder="0"
                            />
                        </div>

                        <div class="col-md-6">
                            <x-form-select
                                name="status"
                                label="الحالة"
                                :value="old('status', $instructor->status ?? 'active')"
                                :options="['active' => 'نشط', 'inactive' => 'غير نشط']"
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <x-form-textarea
                                name="bio"
                                label="نبذة عن المحاضر"
                                :value="old('bio', $instructor->bio ?? '')"
                                :rows="4"
                                placeholder="أهمية الخبرة والمهارات..."
                            />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <x-form-file
                                name="avatar"
                                label="الصورة الشخصية"
                                :currentFile="$instructor->avatar_url ?? null"
                                accept="image/*"
                            />
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <x-btn type="submit" variant="primary">
                            <i class="mdi mdi-content-save"></i> {{ $instructor ? 'تحديث البيانات' : 'إنشاء المحاضر' }}
                        </x-btn>
                        <x-btn href="{{ route('dashboard.instructors.index') }}" variant="secondary" class="mx-2">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </x-btn>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($instructor)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">معلومات المحاضر</h5>
                    <div class="text-center mb-3">
                        <img src="{{ $instructor->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($instructor->name) . '&size=100&background=random' }}"
                             alt="{{ $instructor->name }}"
                             class="rounded-circle mb-3"
                             width="100"
                             height="100">
                        <h6>{{ $instructor->name }}</h6>
                        <p class="text-muted">{{ $instructor->email }}</p>
                        @if($instructor->specialization)
                            <span class="badge badge-primary">{{ $instructor->specialization }}</span>
                        @endif
                    </div>
                    <hr>
                    <p><strong>تاريخ الانضمام:</strong> {{ $instructor->created_at->format('Y-m-d') }}</p>
                    <p><strong>آخر تحديث:</strong> {{ $instructor->updated_at->format('Y-m-d') }}</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title text-danger">حذف المحاضر</h5>
                    <p class="text-muted">لا يمكن التراجع عن هذا الإجراء.</p>
                    <form action="{{ route('dashboard.instructors.destroy', $instructor->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المحاضر؟');">
                        @csrf
                        @method('DELETE')
                        <x-btn type="submit" variant="danger" block>
                            <i class="mdi mdi-delete"></i> حذف المحاضر
                        </x-btn>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
