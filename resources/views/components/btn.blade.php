@php
    // Determine if this is a link or button
    $isLink = $href ?? false;
    $isButton = $type ?? 'button'; // button, submit, reset
    $classes = [
        'btn',
        'btn-' . ($variant ?? 'primary'),
        isset($size) && $size === 'sm' ? 'btn-sm' : '',
        isset($size) && $size === 'lg' ? 'btn-lg' : '',
        $block ?? false ? 'btn-block' : '',
        $class ?? ''
    ];
    $classes = array_filter($classes, fn($c) => $c !== '');
    $classString = implode(' ', $classes);
@endphp

@if($isLink)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classString]) }}>
        {!! $slot !!}
    </a>
@else
    <button type="{{ $isButton }}" {{ $attributes->merge(['class' => $classString]) }}>
        {!! $slot !!}
    </button>
@endif
