@extends('layouts.master')

@section('title') لوحة التحكم @endsection

@section('content')
<!-- start page title -->
<div class="row">
    @component('common-components.breadcrumb')
        @slot('title') لوحة التحكم @endslot
        @slot('li1') منصة الغد @endslot
        @slot('li2') لوحة التحكم @endslot
        @slot('li3') الرئيسية @endslot
    @endcomponent
</div>
<!-- end page title -->

<div class="row">
    <!-- Student Stat -->
    @component('common-components.premium-stat-card')
        @slot('title') إجمالي الطلاب @endslot
        @slot('value') {{ $studentsCount }} @endslot
        @slot('icon') mdi-account-group @endslot
        @slot('gradient') linear-gradient(135deg, #667eea 0%, #764ba2 100%) @endslot
        @slot('shadowColor') rgba(102, 126, 234, 0.3) @endslot
    @endcomponent

    <!-- Instructor Stat -->
    @component('common-components.premium-stat-card')
        @slot('title') المحاضرين @endslot
        @slot('value') {{ $instructorsCount }} @endslot
        @slot('icon') mdi-account-tie @endslot
        @slot('gradient') linear-gradient(135deg, #00c6fb 0%, #005bea 100%) @endslot
        @slot('shadowColor') rgba(0, 198, 251, 0.3) @endslot
    @endcomponent

    <!-- Course Stat -->
    @component('common-components.premium-stat-card')
        @slot('title') الكورسات @endslot
        @slot('value') {{ $coursesCount }} @endslot
        @slot('icon') mdi-book-open-page-variant @endslot
        @slot('gradient') linear-gradient(135deg, #f093fb 0%, #f5576c 100%) @endslot
        @slot('shadowColor') rgba(240, 147, 251, 0.3) @endslot
    @endcomponent

    <!-- Lesson Stat -->
    @component('common-components.premium-stat-card')
        @slot('title') الدروس @endslot
        @slot('value') {{ $lessonsCount }} @endslot
        @slot('icon') mdi-play-circle-outline @endslot
        @slot('gradient') linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) @endslot
        @slot('shadowColor') rgba(79, 172, 254, 0.3) @endslot
    @endcomponent
</div>
<!-- end row -->

<div class="row mt-4">
    <!-- Latest Students Table -->
    @component('common-components.dashboard-table')
        @slot('title') آخر الطلاب المسجلين @endslot
        @slot('viewAllRoute') {{ route('dashboard.students.index') }} @endslot
        @slot('headers', ['الطالب', 'البريد الإلكتروني', 'تاريخ التسجيل', 'الإجراء'])
        
        @forelse($recentStudents as $student)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <img src="{{ $student->avatar_url }}" alt="" class="avatar-xs rounded-circle ml-2" />
                    <span class="font-weight-medium">{{ $student->name }}</span>
                </div>
            </td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->created_at->format('Y/m/d') }}</td>
            <td class="text-center">
                <a href="{{ route('dashboard.students.edit', $student->id) }}" class="btn btn-outline-primary btn-sm waves-effect" style="border-radius: 6px;">تعديل</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center py-4 text-muted">لا يوجد طلاب جدد حالياً</td></tr>
        @endforelse
    @endcomponent

    <!-- Recent Courses Table -->
    @component('common-components.dashboard-table')
        @slot('title') آخر الكورسات المحدثة @endslot
        @slot('viewAllRoute') {{ route('dashboard.courses.index') }} @endslot
        @slot('headers', ['الكورس', 'المحاضر', 'السعر', 'الإجراء'])
        
        @forelse($recentCourses as $course)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <img src="{{ $course->thumbnail_url }}" alt="" class="avatar-xs rounded ml-2" style="object-fit: cover;" />
                    <span class="font-weight-medium">{{ $course->title }}</span>
                </div>
            </td>
            <td>{{ $course->instructor->name ?? '---' }}</td>
            <td>
                <span class="font-weight-bold text-success">{{ $course->price }} ج.م</span>
            </td>
            <td class="text-center">
                <a href="{{ route('dashboard.courses.edit', $course->id) }}" class="btn btn-outline-primary btn-sm waves-effect" style="border-radius: 6px;">تعديل</a>
            </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center py-4 text-muted">لا توجد كورسات حالياً</td></tr>
        @endforelse
    @endcomponent
</div>
<!-- end row -->
@endsection

@section('footerScript')
<!-- Keep scripts -->
@endsection