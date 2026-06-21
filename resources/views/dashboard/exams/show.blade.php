@extends('layouts.master')

@section('title', $exam->title)

@section('content')
<x-breadcrumb
    :title="'تفاصيل الاختبار: ' . $exam->title"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الاختبارات', 'url' => route('dashboard.courses.exams.index', $course->id)],
        ['label' => $exam->title]
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $exam->title }}</h4>
                        @if($exam->description)
                            <p class="text-muted">{{ $exam->description }}</p>
                        @endif
                    </div>
                    <div class="col-md-4 text-left">
                        <span class="badge badge-soft-primary font-size-14 mb-2">#{{ strtoupper(dechex($exam->id)) }}</span>
                        @if($exam->is_active)
                            <span class="badge badge-success">نشط</span>
                        @else
                            <span class="badge badge-secondary">غير نشط</span>
                        @endif
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th>الكورس:</th>
                        <td>{{ $course->title }}</td>
                    </tr>
                    <tr>
                        <th>مدة الاختبار:</th>
                        <td>{{ $exam->duration_minutes ? $exam->duration_minutes . ' دقيقة' : 'غير محدد' }}</td>
                    </tr>
                    <tr>
                        <th>درجة النجاح:</th>
                        <td>{{ $exam->passing_score }}%</td>
                    </tr>
                    <tr>
                        <th>عدد الأسئلة:</th>
                        <td>{{ $exam->questions->count() }}</td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء:</th>
                        <td>{{ $exam->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>

                <hr>

                <h5 class="mb-3">الأسئلة والإجابات</h5>

                @foreach($exam->questions as $qIndex => $question)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">سؤال {{ $qIndex + 1 }}: {{ $question->question }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($question->options as $option)
                            <div class="col-md-6 mb-2">
                                <div class="p-2 rounded {{ $option->is_correct ? 'bg-success-light' : 'bg-light' }}">
                                    @if($option->is_correct)
                                        <i class="mdi mdi-check-circle text-success"></i>
                                    @else
                                        <i class="mdi mdi-circle-outline text-muted"></i>
                                    @endif
                                    {{ $option->option_text }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    <a href="{{ route('dashboard.courses.exams.edit', [$course->id, $exam->id]) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> تعديل
                    </a>
                    <a href="{{ route('dashboard.courses.exams.index', $course->id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
