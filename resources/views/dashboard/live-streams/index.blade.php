@extends('layouts.master')

@section('title', 'البث المباشر - ' . $course->title)

@section('content')
<x-breadcrumb
    title="البث المباشر: {{ $course->title }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الكورسات', 'url' => route('dashboard.courses.index')],
        ['label' => $course->title, 'url' => route('dashboard.courses.show', $course->id)],
        ['label' => 'البث المباشر']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.live-streams.create', $course->id) }}" variant="primary" block>
                            <i class="mdi mdi-plus"></i> إضافة بث جديد
                        </x-btn>
                    </div>
                    <div class="col-md-3">
                        <x-btn href="{{ route('dashboard.courses.show', $course->id) }}" variant="secondary" block>
                            <i class="mdi mdi-arrow-right"></i> العودة للكورس
                        </x-btn>
                    </div>
                </div>

                <x-alert type="success" />

                @if($liveStreams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الرابط</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($liveStreams as $stream)
                            <tr>
                                <td>
                                    <span class="badge badge-soft-primary font-size-12">#{{ strtoupper(dechex($stream->id)) }}</span>
                                </td>
                                <td>{{ $stream->title }}</td>
                                <td>
                                    <a href="{{ $stream->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="mdi mdi-open-in-new"></i> فتح
                                    </a>
                                </td>
                                <td>
                                    @if($stream->is_active)
                                        <span class="badge badge-success">مباشر</span>
                                    @else
                                        <span class="badge badge-secondary">متوقف</span>
                                    @endif
                                </td>
                                <td>{{ $stream->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('dashboard.courses.live-streams.show', [$course->id, $stream->id]) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.courses.live-streams.edit', [$course->id, $stream->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dashboard.courses.live-streams.destroy', [$course->id, $stream->id]) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا البث؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($liveStreams->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $liveStreams->withQueryString()->links() }}
                </div>
                @endif
                @else
                <div class="text-center p-4">
                    <i class="mdi mdi-broadcast text-muted" style="font-size: 48px;"></i>
                    <p class="text-muted mt-2">لا يوجد بث مباشر لهذا الكورس بعد</p>
                    <a href="{{ route('dashboard.courses.live-streams.create', $course->id) }}" class="btn btn-primary mt-2">
                        <i class="mdi mdi-plus"></i> إضافة أول بث
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
