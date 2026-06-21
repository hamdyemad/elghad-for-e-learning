@extends('layouts.master')

@section('title', $liveStream->title)

@section('content')
<x-breadcrumb
    :title="'تفاصيل البث: ' . $liveStream->title"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'البث المباشر', 'url' => route('dashboard.courses.live-streams.index', $course->id)],
        ['label' => $liveStream->title]
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h4>{{ $liveStream->title }}</h4>
                    </div>
                    <div class="col-md-4 text-left">
                        <span class="badge badge-soft-primary font-size-14 mb-2">#{{ strtoupper(dechex($liveStream->id)) }}</span>
                        @if($liveStream->is_active)
                            <span class="badge badge-success">مباشر</span>
                        @else
                            <span class="badge badge-secondary">متوقف</span>
                        @endif
                    </div>
                </div>

                <table class="table table-borderless">
                    <tr>
                        <th>الكورس:</th>
                        <td>{{ $course->title }}</td>
                    </tr>
                    <tr>
                        <th>رابط البث:</th>
                        <td>
                            <a href="{{ $liveStream->url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="mdi mdi-open-in-new"></i> فتح الرابط
                            </a>
                            <code class="ms-2">{{ $liveStream->url }}</code>
                        </td>
                    </tr>
                    <tr>
                        <th>الحالة:</th>
                        <td>
                            @if($liveStream->is_active)
                                <span class="badge badge-success">مباشر (نشط)</span>
                            @else
                                <span class="badge badge-secondary">متوقف</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>تاريخ الإنشاء:</th>
                        <td>{{ $liveStream->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>آخر تحديث:</th>
                        <td>{{ $liveStream->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>

                <div class="mt-4">
                    <a href="{{ route('dashboard.courses.live-streams.edit', [$course->id, $liveStream->id]) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> تعديل
                    </a>
                    <a href="{{ route('dashboard.courses.live-streams.index', $course->id) }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
