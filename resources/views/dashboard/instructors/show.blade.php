@extends('layouts.master')

@section('title', 'بيانات المحاضر')

@section('content')
<x-breadcrumb
    title="بيانات المحاضر"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'المحاضرين', 'url' => route('dashboard.instructors.index')],
        ['label' => $instructor->name]
    ]"
/>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center">
                    <img src="{{ $instructor->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($instructor->name) . '&size=150&background=random' }}"
                         alt="{{ $instructor->name }}"
                         class="rounded-circle mb-3"
                         width="150"
                         height="150">
                    <h4>{{ $instructor->name }}</h4>
                    <p class="text-muted">{{ $instructor->email }}</p>

                    @if($instructor->specialization)
                        <span class="badge badge-primary mb-2 d-inline-block">{{ $instructor->specialization }}</span>
                    @endif

                    <br>

                    @if($instructor->status == 'active')
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
                    <x-btn href="{{ route('dashboard.instructors.edit', $instructor->id) }}" variant="primary">
                        <i class="mdi mdi-pencil"></i> تعديل البيانات
                    </x-btn>
                    <x-btn href="{{ route('dashboard.instructors.index') }}" variant="secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </x-btn>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">معلومات المحاضر</h5>

                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <td width="30%"><strong>الاسم:</strong></td>
                            <td>{{ $instructor->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>البريد الإلكتروني:</strong></td>
                            <td>{{ $instructor->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>التخصص:</strong></td>
                            <td>{{ $instructor->specialization ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>معدل الساعة:</strong></td>
                            <td>{{ $instructor->hourly_rate ? number_format($instructor->hourly_rate, 2) . ' ج.م' : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>سنوات الخبرة:</strong></td>
                            <td>{{ $instructor->experience_years ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>الحالة:</strong></td>
                            <td>
                                @if($instructor->status == 'active')
                                    <span class="badge badge-success">نشط</span>
                                @elseif($instructor->status == 'inactive')
                                    <span class="badge badge-danger">غير نشط</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>مُ verification:</strong></td>
                            <td>
                                @if($instructor->is_verified)
                                    <span class="badge badge-success">نعم</span>
                                @else
                                    <span class="badge badge-warning">لا</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>نبذة عن المحاضر:</strong></td>
                            <td>{!! nl2br(e($instructor->bio)) ?: '-' !!}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الانضمام:</strong></td>
                            <td>{{ $instructor->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>آخر تحديث:</strong></td>
                            <td>{{ $instructor->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $courses = $instructor->courses()->with('category')->get();
        @endphp
        @if($courses->count() > 0)
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title mb-3">الكورسات التي يقدمها</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>عنوان الكورس</th>
                                    <th>القسم</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $index => $course)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $course->title }}</td>
                                        <td>{{ $course->category->name ?? '-' }}</td>
                                        <td>{{ $course->is_free ? 'مجاني' : number_format($course->price, 2) . ' ج.م' }}</td>
                                        <td>
                                            @if($course->status == 'published')
                                                <span class="badge badge-success">منشور</span>
                                            @elseif($course->status == 'draft')
                                                <span class="badge badge-warning">مسودة</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $course->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
