@extends('layouts.master')

@section('title', 'إعادة ترتيب الدروس')

@section('content')
<x-breadcrumb
    title="إعادة ترتيب دروس: {{ $course->title }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الدورات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.lessons.index', ['course_id' => $course->id])],
        ['label' => 'إعادة ترتيب']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information"></i>
                    <strong>تعليمات:</strong> اسحب الدروس وأفلتها لترتيبها بالشكل المطلوب. الترتيب يبدأ من 0.
                </div>

                <form id="reorder-form" method="POST" action="{{ route('dashboard.lessons.reorder') }}">
                    @csrf

                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div id="lessons-list" class="sortable-list">
                        @forelse($lessons as $lesson)
                            <div class="lesson-item card mb-2" data-lesson-id="{{ $lesson->id }}">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-1">
                                            <div class="move-handle text-center">
                                                <i class="mdi mdi-drag-vertical font-size-18"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h5 class="mb-1 font-size-15 text-dark font-weight-bold">{{ $lesson->title }}</h5>
                                            <small class="text-muted d-block">{{ $lesson->topic }}</small>
                                        </div>
                                        <div class="col-md-3 text-right">
                                            <span class="badge badge-soft-info badge-order px-3 py-1 font-size-12">
                                                الترتيب: {{ $lesson->order }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-warning">
                                لا توجد دروس لهذه الدورة.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4 border-top pt-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light px-4">
                            <i class="mdi mdi-content-save-outline"></i> حفظ الترتيب الجديد
                        </button>
                        <a href="{{ route('dashboard.lessons.index', ['course_id' => $course->id]) }}" class="btn btn-secondary">
                            <i class="mdi mdi-arrow-right"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        const el = document.getElementById('lessons-list');
        const sortable = Sortable.create(el, {
            animation: 300,
            handle: '.move-handle',
            ghostClass: 'sortable-ghost',
            onEnd: function() {
                // Update badges visually
                updateOrderNumbers();
            }
        });

        function updateOrderNumbers() {
            $('.lesson-item').each(function(index) {
                $(this).find('.badge-order').text('الترتيب: ' + (index + 1));
            });
        }

        $('#reorder-form').on('submit', function(e) {
            // Remove old hidden inputs
            $('.order-input').remove();
            
            // Add new hidden inputs for each lesson
            $('.lesson-item').each(function(index) {
                const lessonId = $(this).data('lesson-id');
                const order = index + 1; // 1-based order
                $(this).append(`<input type="hidden" name="lesson_orders[${lessonId}]" value="${order}" class="order-input">`);
            });
        });
    });
</script>
<style>
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f3f3f3;
        border: 2px dashed #727cf5;
    }
    .lesson-item {
        transition: all 0.2s;
        border: 1px solid #eef2f7;
    }
    .lesson-item:hover {
        border-color: #727cf5;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .move-handle {
        cursor: grab;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 4px;
        color: #6c757d;
    }
    .move-handle:active {
        cursor: grabbing;
    }
</style>
@endpush

@endsection
