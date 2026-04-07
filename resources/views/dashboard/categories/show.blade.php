@extends('layouts.master')

@section('title', 'عرض القسم')

@section('content')
<!-- Breadcrumb -->
<x-breadcrumb 
    title="عرض القسم: {{ $category->name }}"
    :items="[
        ['label' => 'Dashboard', 'url' => '/dashboard/index'],
        ['label' => 'الأقسام', 'url' => route('dashboard.categories.index')],
        ['label' => 'عرض']
    ]"
/>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="img-fluid rounded shadow-sm mb-3" style="max-width: 100%;">
                <h4 class="mb-1">{{ $category->name }}</h4>
                <p class="text-muted mb-3">
                    <code>{{ $category->slug }}</code>
                </p>
                <span class="badge badge-soft-primary font-size-14 mb-2">#{{ strtoupper(dechex($category->id)) }}</span>
                <br>
                @if($category->status == 'active')
                    <span class="badge badge-success font-size-14">نشط</span>
                @else
                    <span class="badge badge-danger font-size-14">غير نشط</span>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="mdi mdi-cog text-primary"></i>
                    إجراءات
                </h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('dashboard.categories.edit', $category->id) }}" class="btn btn-warning btn-block mb-2">
                        <i class="mdi mdi-pencil"></i> تعديل القسم
                    </a>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#deleteModal">
                        <i class="mdi mdi-delete"></i> حذف القسم
                    </button>
                    <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary btn-block">
                        <i class="mdi mdi-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-information text-primary"></i>
                    معلومات القسم
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <th width="30%" class="text-muted">المعرف (ID):</th>
                                <td>
                                    <span class="badge badge-soft-primary">#{{ $category->id }}</span>
                                    <span class="badge badge-soft-info">#{{ strtoupper(dechex($category->id)) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">اسم القسم:</th>
                                <td><strong>{{ $category->name }}</strong></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Slug:</th>
                                <td><code class="text-primary">{{ $category->slug }}</code></td>
                            </tr>
                            <tr>
                                <th class="text-muted">الوصف:</th>
                                <td>{{ $category->description ?? 'لا يوجد وصف' }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">القسم الرئيسي:</th>
                                <td>
                                    @if($category->parent)
                                        <a href="{{ route('dashboard.categories.show', $category->parent->id) }}" class="badge badge-info">
                                            <i class="mdi mdi-folder"></i> {{ $category->parent->name }}
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="mdi mdi-folder-star"></i> قسم رئيسي
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">الترتيب:</th>
                                <td><span class="badge badge-soft-dark">{{ $category->order }}</span></td>
                            </tr>
                            <tr>
                                <th class="text-muted">الحالة:</th>
                                <td>
                                    @if($category->status == 'active')
                                        <span class="badge badge-success">
                                            <i class="mdi mdi-check-circle"></i> نشط
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="mdi mdi-close-circle"></i> غير نشط
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">تاريخ الإنشاء:</th>
                                <td>
                                    <i class="mdi mdi-calendar text-muted"></i>
                                    {{ $category->created_at->format('Y-m-d') }}
                                    <span class="text-muted">|</span>
                                    <i class="mdi mdi-clock text-muted"></i>
                                    {{ $category->created_at->format('H:i:s') }}
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">آخر تحديث:</th>
                                <td>
                                    <i class="mdi mdi-calendar text-muted"></i>
                                    {{ $category->updated_at->format('Y-m-d') }}
                                    <span class="text-muted">|</span>
                                    <i class="mdi mdi-clock text-muted"></i>
                                    {{ $category->updated_at->format('H:i:s') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($category->children->count() > 0)
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">
                    <i class="mdi mdi-folder-multiple text-primary"></i>
                    الأقسام الفرعية ({{ $category->children->count() }})
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الصورة</th>
                                <th>الاسم</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->children as $child)
                            <tr>
                                <td><span class="badge badge-soft-primary">#{{ strtoupper(dechex($child->id)) }}</span></td>
                                <td>
                                    <img src="{{ $child->image_url }}" alt="{{ $child->name }}" width="40" height="40" class="rounded">
                                </td>
                                <td><strong>{{ $child->name }}</strong></td>
                                <td>
                                    @if($child->status == 'active')
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('dashboard.categories.show', $child->id) }}" class="btn btn-sm btn-info">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    <a href="{{ route('dashboard.categories.edit', $child->id) }}" class="btn btn-sm btn-warning">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
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

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 72px;"></i>
                <h4 class="mt-3">هل أنت متأكد من حذف هذا القسم؟</h4>
                <p class="text-muted mb-2"><strong>{{ $category->name }}</strong></p>
                @if($category->has_children)
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert"></i>
                        تحذير: سيتم حذف جميع الأقسام الفرعية ({{ $category->children->count() }})
                    </div>
                @endif
                <p class="text-muted">لن تتمكن من التراجع عن هذا الإجراء</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="mdi mdi-close"></i> إلغاء
                </button>
                <form action="{{ route('dashboard.categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="mdi mdi-delete"></i> حذف نهائياً
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
