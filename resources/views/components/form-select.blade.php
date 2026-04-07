@php
    $isMultiple = $multiple ?? false;
    $isCompact = $compact ?? false;
    $groupClasses = ['form-group'];
    if ($isCompact) {
        $groupClasses[] = 'mb-md-0';
    }

    $currentValue = old($name, $value ?? ($isMultiple ? [] : ''));
    if ($isMultiple && !is_array($currentValue)) {
        $currentValue = [$currentValue];
    }
    
    // Unique ID for each instance to avoid conflicts
    $id = $id ?? $name . '_' . rand(1000, 9999);
@endphp

<div class="{{ implode(' ', $groupClasses) }}">
    <label for="{{ $id }}" @if($isCompact) class="small" @endif>
        {{ $label }}
        @if($required ?? false)
            <span class="text-danger">*</span>
        @endif
    </label>

    <select
        class="form-control select2-control @error($name) is-invalid @enderror"
        id="{{ $id }}"
        name="{{ $isMultiple ? $name . '[]' : $name }}"
        @if($isMultiple) multiple @endif
        style="width: 100%; opacity: 0;"
    >
        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}"
                {{
                    ($isMultiple ? in_array($optionValue, $currentValue) : $currentValue == $optionValue)
                    ? 'selected' : ''
                }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@once
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container {
    width: 100% !important;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    height: calc(1.5em + 0.75rem + 2px);
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: normal;
    padding: 0 0.75rem;
    text-align: right;
    direction: rtl;
    width: 100%;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 100%;
    position: absolute;
    left: 5px;
    right: auto;
    display: flex;
    align-items: center;
}
.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    z-index: 1060;
}
.select2-results__option {
    text-align: right;
    direction: rtl;
}
/* Hide original select */
select.select2-control {
    opacity: 0 !important;
    position: absolute;
    z-index: -1;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush
@endonce

@push('scripts')
<script>
$(document).ready(function() {
    $('#{{ $id }}').select2({
        dir: 'rtl',
        placeholder: '{{ $placeholder ?? "اختر..." }}',
        allowClear: true
    });
});
</script>
@endpush
