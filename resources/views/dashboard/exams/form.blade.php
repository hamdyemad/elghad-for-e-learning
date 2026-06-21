@extends('layouts.master')

@section('title', $exam ? 'تعديل الاختبار' : 'إضافة اختبار جديد')

@section('content')
<x-breadcrumb
    :title="$exam ? 'تعديل الاختبار: ' . $exam->title : 'إضافة اختبار جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'الاختبارات', 'url' => route('dashboard.courses.exams.index', $course->id)],
        ['label' => $exam ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ $exam ? route('dashboard.courses.exams.update', [$course->id, $exam->id]) : route('dashboard.courses.exams.store', $course->id) }}"
                    method="POST"
                    id="examForm"
                >
                    @csrf
                    @if($exam)
                        @method('PUT')
                    @endif

                    <x-alert type="success" />
                    <x-alert type="error" />

                    <div class="alert alert-info">
                        <i class="mdi mdi-information-outline"></i>
                        الكورس: <strong>{{ $course->title }}</strong>
                    </div>

                    <x-form-input
                        name="title"
                        label="عنوان الاختبار"
                        :value="old('title', $exam->title ?? '')"
                        :required="true"
                        placeholder="أدخل عنوان الاختبار"
                    />

                    <div class="form-group mb-3">
                        <label for="description">وصف الاختبار</label>
                        <textarea name="description" id="description" class="form-control" rows="3"
                                  placeholder="أدخل وصف الاختبار (اختياري)">{{ old('description', $exam->description ?? '') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-form-input
                                name="duration_minutes"
                                label="مدة الاختبار (دقيقة)"
                                :value="old('duration_minutes', $exam->duration_minutes ?? '')"
                                placeholder="مثال: 30"
                                type="number"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-form-input
                                name="passing_score"
                                label="درجة النجاح (%)"
                                :value="old('passing_score', $exam->passing_score ?? 50)"
                                placeholder="50"
                                type="number"
                            />
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $exam->is_active ?? 1) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">نشط</label>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">الأسئلة</h5>

                    <div id="questions-container">
                        @if($exam && $exam->questions->count())
                            @foreach($exam->questions as $qIndex => $question)
                            <div class="question-block card mb-3" data-index="{{ $qIndex }}">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">سؤال {{ $qIndex + 1 }}</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-question">
                                        <i class="mdi mdi-delete"></i> حذف السؤال
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label>نص السؤال <span class="text-danger">*</span></label>
                                        <input type="text" name="questions[{{ $qIndex }}][question]" class="form-control"
                                               value="{{ old("questions.{$qIndex}.question", $question->question) }}" required>
                                    </div>
                                    <div class="row">
                                        @foreach($question->options as $oIndex => $option)
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input type="radio" name="correct_{{ $qIndex }}"
                                                               value="{{ $oIndex }}"
                                                               {{ $option->is_correct ? 'checked' : '' }}
                                                               required>
                                                    </div>
                                                </div>
                                                <input type="text" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][option_text]"
                                                       class="form-control" value="{{ old("questions.{$qIndex}.options.{$oIndex}.option_text", $option->option_text) }}" required>
                                                <input type="hidden" name="questions[{{ $qIndex }}][options][{{ $oIndex }}][is_correct]" value="0">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="question-block card mb-3" data-index="0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">سؤال 1</h6>
                                    <button type="button" class="btn btn-sm btn-danger remove-question">
                                        <i class="mdi mdi-delete"></i> حذف السؤال
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label>نص السؤال <span class="text-danger">*</span></label>
                                        <input type="text" name="questions[0][question]" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        @foreach(range(0, 3) as $oIndex)
                                        <div class="col-md-6 mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <input type="radio" name="correct_0"
                                                               value="{{ $oIndex }}"
                                                               {{ $oIndex === 0 ? 'checked' : '' }}
                                                               required>
                                                    </div>
                                                </div>
                                                <input type="text" name="questions[0][options][{{ $oIndex }}][option_text]"
                                                       class="form-control" required>
                                                <input type="hidden" name="questions[0][options][{{ $oIndex }}][is_correct]" value="0">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <button type="button" id="add-question" class="btn btn-outline-success mb-3">
                        <i class="mdi mdi-plus"></i> إضافة سؤال
                    </button>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ $exam ? 'حفظ التعديلات' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.courses.exams.index', $course->id) }}" class="btn btn-secondary">
                            <i class="mdi mdi-cancel"></i> إلغاء
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">معلومات</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="mdi mdi-information-outline text-info"></i>
                        كل اختبار يجب أن يحتوي على سؤال واحد على الأقل
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-numeric-4-box-multiple-outline text-warning"></i>
                        كل سؤال يجب أن يحتوي على 4 خيارات بالضبط
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-radiobox-marked text-success"></i>
                        حدد الإجابة الصحيحة بالنقر على الدائرة بجانب الخيار
                    </li>
                    <li>
                        <i class="mdi mdi-clock-outline text-primary"></i>
                        المدة ودرجة النجاح اختياريان
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let questionIndex = document.querySelectorAll('.question-block').length;

    document.getElementById('add-question').addEventListener('click', function() {
        const container = document.getElementById('questions-container');
        const html = `
            <div class="question-block card mb-3" data-index="${questionIndex}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">سؤال ${questionIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-danger remove-question">
                        <i class="mdi mdi-delete"></i> حذف السؤال
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>نص السؤال <span class="text-danger">*</span></label>
                        <input type="text" name="questions[${questionIndex}][question]" class="form-control" required>
                    </div>
                    <div class="row">
                        ${[0,1,2,3].map(oIndex => `
                            <div class="col-md-6 mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="radio" name="correct_${questionIndex}"
                                                   value="${oIndex}"
                                                   ${oIndex === 0 ? 'checked' : ''}
                                                   required>
                                        </div>
                                    </div>
                                    <input type="text" name="questions[${questionIndex}][options][${oIndex}][option_text]"
                                           class="form-control" required>
                                    <input type="hidden" name="questions[${questionIndex}][options][${oIndex}][is_correct]" value="0">
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        questionIndex++;
    });

    document.getElementById('questions-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-question')) {
            const block = e.target.closest('.question-block');
            if (document.querySelectorAll('.question-block').length > 1) {
                block.remove();
            } else {
                alert('يجب أن يحتوي الاختبار على سؤال واحد على الأقل');
            }
        }
    });

    document.getElementById('examForm').addEventListener('submit', function() {
        document.querySelectorAll('.question-block').forEach(function(block, qIndex) {
            const correctRadio = block.querySelector(`input[name="correct_${qIndex}"]:checked`);
            if (correctRadio) {
                const correctIndex = correctRadio.value;
                block.querySelectorAll(`input[type="hidden"][name*="is_correct"]`).forEach(function(input) {
                    input.value = '0';
                });
                const correctInput = block.querySelector(`input[name="questions[${qIndex}][options][${correctIndex}][is_correct]"]`);
                if (correctInput) correctInput.value = '1';
            }
        });
    });
});
</script>
@endpush
