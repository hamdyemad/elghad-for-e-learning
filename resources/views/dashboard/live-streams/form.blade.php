@extends('layouts.master')

@section('title', $liveStream ? 'تعديل البث المباشر' : 'إضافة بث مباشر جديد')

@section('content')
<x-breadcrumb
    :title="$liveStream ? 'تعديل البث: ' . $liveStream->title : 'إضافة بث مباشر جديد'"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'البث المباشر', 'url' => route('dashboard.courses.live-streams.index', $course->id)],
        ['label' => $liveStream ? 'تعديل' : 'إضافة']
    ]"
/>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form
                    action="{{ $liveStream ? route('dashboard.courses.live-streams.update', [$course->id, $liveStream->id]) : route('dashboard.courses.live-streams.store', $course->id) }}"
                    method="POST"
                >
                    @csrf
                    @if($liveStream)
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
                        label="عنوان البث"
                        :value="old('title', $liveStream->title ?? '')"
                        :required="true"
                        placeholder="مثال: محاضرة مباشرة - الفصل الأول"
                    />

                    <x-form-input
                        name="url"
                        label="رابط البث"
                        :value="old('url', $liveStream->url ?? '')"
                        :required="true"
                        placeholder="https://youtube.com/watch?v=... أو https://facebook.com/..."
                        type="url"
                    />

                    <div class="form-group mb-3">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $liveStream->is_active ?? 0) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">مباشر (نشط)</label>
                        </div>
                        <small class="text-muted">عند التفعيل، سيتم إرسال إشعار تلقائي لطلاب الكورس المشتركين</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> {{ $liveStream ? 'حفظ التعديلات' : 'حفظ' }}
                        </button>
                        <a href="{{ route('dashboard.courses.live-streams.index', $course->id) }}" class="btn btn-secondary">
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
                        <i class="mdi mdi-youtube text-danger"></i>
                        يدعم روابط YouTube
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-facebook text-primary"></i>
                        يدعم روابط Facebook Live
                    </li>
                    <li class="mb-2">
                        <i class="mdi mdi-broadcast text-success"></i>
                        تفعيل البث يرسل إشعاراً تلقائياً للطلاب
                    </li>
                    <li>
                        <i class="mdi mdi-link text-info"></i>
                        يمكن إضافة عدة روابط بث لكل كورس
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
