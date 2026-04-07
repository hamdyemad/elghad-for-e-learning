@php
    $isCompact = $compact ?? false;
    $groupClasses = ['form-group'];
    if ($isCompact) {
        $groupClasses[] = 'mb-md-0';
    }
@endphp

<div class="{{ implode(' ', $groupClasses) }}">
    <label for="{{ $name }}" @if($isCompact) class="small" @endif>
        {{ $label }}
        @if($required ?? false)
            <span class="text-danger">*</span>
        @endif
    </label>

    <input
        type="{{ $type ?? 'text' }}"
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value ?? '') }}"
        placeholder="{{ $placeholder ?? '' }}"
        {{ ($required ?? false) ? 'required' : '' }}
    >

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if($hint ?? false)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>
