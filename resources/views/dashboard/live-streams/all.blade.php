@extends('layouts.master')

@section('title', 'البث المباشر')

@section('content')
<x-breadcrumb
    title="البث المباشر"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'البث المباشر']
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row mb-3">
                    <div class="col-md-3">
                        <select name="course_id" class="form-control" onchange="this.form.submit()">
                            <option value="">كل الكورسات</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="is_active" class="form-control" onchange="this.form.submit()">
                            <option value="">كل الحالات</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="بحث..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="mdi mdi-magnify"></i> بحث
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('dashboard.live-streams.all') }}" class="btn btn-secondary btn-block">
                            <i class="mdi mdi-refresh"></i>
                        </a>
                    </div>
                </form>

                <x-alert type="success" />

                @if($liveStreams->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العنوان</th>
                                <th>الكورس</th>
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
                                    <a href="{{ route('dashboard.courses.show', $stream->course_id) }}">
                                        {{ $stream->course->title }}
                                    </a>
                                </td>
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
                                    <a href="{{ route('dashboard.courses.live-streams.show', [$stream->course_id, $stream->id]) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.courses.live-streams.edit', [$stream->course_id, $stream->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('dashboard.courses.live-streams.destroy', [$stream->course_id, $stream->id]) }}" method="POST" class="d-inline"
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
                    <p class="text-muted mt-2">لا يوجد بث مباشر بعد</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
