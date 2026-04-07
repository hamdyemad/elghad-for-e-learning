@extends('layouts.master')

@section('title', $package->title)

@section('content')
<x-breadcrumb
    title="تفاصيل الباكج"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الباقات', 'url' => route('dashboard.packages.index')],
        ['label' => $package->title]
    ]"
/>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $package->title }}</h4>
                        <p class="text-muted">{{ $package->description }}</p>

                        <table class="table table-borderless">
                            <tr>
                                <th>القسم:</th>
                                <td>{{ $package->category ? $package->category->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>السعر:</th>
                                <td>{{ $package->price }} ر.س</td>
                            </tr>
                            <tr>
                                <th>الحالة:</th>
                                <td>
                                    @if($package->status == 'published')
                                        <span class="badge badge-success">منشور</span>
                                    @elseif($package->status == 'draft')
                                        <span class="badge badge-warning">مسودة</span>
                                    @else
                                        <span class="badge badge-info">{{ $package->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>الدورات المرتبطة:</th>
                                <td>
                                    @if($package->courses->count() > 0)
                                        <ul>
                                            @foreach($package->courses as $course)
                                                <li>
                                                    <a href="{{ route('dashboard.courses.show', $course->id) }}">
                                                        {{ $course->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-muted">لا توجد دورات مرتبطة</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        @if($package->image)
                            <img src="{{ asset('storage/' . $package->image) }}" alt="Package Image" class="img-fluid rounded">
                        @else
                            <div class="bg-light p-4 text-center rounded">
                                <i class="mdi mdi-image-off font-size-48 text-muted"></i>
                                <p class="text-muted mt-2">لا توجد صورة</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('dashboard.packages.edit', $package->id) }}" class="btn btn-warning">
                        <i class="mdi mdi-pencil"></i> تعديل
                    </a>
                    <a href="{{ route('dashboard.packages.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-right"></i> عودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
