@php
    // Normalize columns: string becomes array with field=string
    $normalizedColumns = array_map(function($col) {
        if (is_string($col)) {
            return ['field' => $col, 'label' => $col];
        }
        return $col;
    }, $columns);
@endphp

<div class="table-responsive mt-3">
    <table class="table table-bordered table-hover text-center">
        <thead>
            <tr>
                @foreach($normalizedColumns as $column)
                    @php
                        $width = $column['width'] ?? '';
                        $label = $column['label'] ?? ucfirst($column['field']);
                        $thClass = $column['thClass'] ?? '';
                    @endphp
                    <th width="{{ $width }}" class="text-center align-middle {{ $thClass }}">{{ $label }}</th>
                @endforeach
                @if($actions ?? false)
                    <th class="text-center align-middle">الإجراءات</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                @foreach($normalizedColumns as $column)
                    @php
                        $field = $column['field'] ?? null;
                        $class = $column['class'] ?? '';
                        $value = $field ? data_get($row, $field) : '';
                    @endphp

                    <td class="text-center align-middle {{ $class }}">
                        @isset($column['render'])
                            @if(is_callable($column['render']))
                                {!! call_user_func($column['render'], $row, $value) !!}
                            @elseif(is_string($column['render']) && view()->exists($column['render']))
                                @include($column['render'], ['row' => $row, 'value' => $value])
                            @else
                                {{ $value }}
                            @endif
                        @else
                            {{ $value }}
                        @endif
                    </td>
                @endforeach

                @if($actions ?? false)
                    <td class="text-center align-middle">
                        <div class="d-flex justify-content-center align-items-center">
                            @if($actions === true && $route)
                                @php
                                    $showRoute = $route . '.show';
                                    $editRoute = $route . '.edit';
                                    $destroyRoute = $route . '.destroy';
                                @endphp

                                @if(\Illuminate\Support\Facades\Route::has($showRoute))
                                    <x-btn href="{{ route($showRoute, $row->id) }}" variant="info" size="sm" class="mx-1">
                                        <i class="mdi mdi-eye"></i>
                                    </x-btn>
                                @endif

                                @if(\Illuminate\Support\Facades\Route::has($editRoute))
                                    <x-btn href="{{ route($editRoute, $row->id) }}" variant="warning" size="sm" class="mx-1">
                                        <i class="mdi mdi-pencil"></i>
                                    </x-btn>
                                @endif

                                @if(\Illuminate\Support\Facades\Route::has($destroyRoute))
                                    <x-btn type="button" variant="danger" size="sm" class="mx-1" 
                                           data-toggle="modal" data-target="#deleteModal" 
                                           data-action="{{ route($destroyRoute, $row->id) }}">
                                        <i class="mdi mdi-delete"></i>
                                    </x-btn>
                                @endif
                            @elseif(is_callable($actions) && $actions instanceof \Closure)
                                {!! call_user_func($actions, $row) !!}
                            @endif
                        </div>
                    </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($normalizedColumns) + (($actions ?? false) ? 1 : 0) }}" class="text-center">
                    {{ $emptyMessage ?? 'لا توجد بيانات' }}
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if(isset($pagination) && method_exists($pagination, 'links'))
    <div class="mt-4 d-flex justify-content-center">
        {!! $pagination->appends(request()->query())->links() !!}
    </div>
@endif

@once
    <x-delete-modal />
@endonce

