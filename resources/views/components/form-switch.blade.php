@props([
    'name',
    'label',
    'value' => 'active',
    'onLabel' => 'نشط',
    'offLabel' => 'غير نشط'
])

@php
    $isChecked = old($name, $value) === 'active';
@endphp

<div class="form-group">
    <label class="d-block">{{ $label }}</label>
    <div class="custom-control custom-switch custom-switch-lg">
        <input type="checkbox" 
               class="custom-control-input" 
               id="{{ $name }}_switch" 
               {{ $isChecked ? 'checked' : '' }}
               onchange="document.getElementById('{{ $name }}_hidden').value = this.checked ? 'active' : 'inactive'; updateSwitchLabel('{{ $name }}', this.checked);">
        <label class="custom-control-label" for="{{ $name }}_switch">
            <span id="{{ $name }}_label">{{ $isChecked ? $onLabel : $offLabel }}</span>
        </label>
        
        <input type="hidden" 
               name="{{ $name }}" 
               id="{{ $name }}_hidden" 
               value="{{ $isChecked ? 'active' : 'inactive' }}">
    </div>
    @error($name)
        <span class="text-danger small d-block mt-1">{{ $message }}</span>
    @enderror
</div>

@push('scripts')
<script>
function updateSwitchLabel(name, isChecked) {
    const label = document.getElementById(name + '_label');
    if (label) {
        label.textContent = isChecked ? '{{ $onLabel }}' : '{{ $offLabel }}';
    }
}
</script>
@endpush

@push('styles')
<style>
.custom-switch-lg .custom-control-label {
    padding-right: 3rem;
    padding-top: 0.25rem;
}

.custom-switch-lg .custom-control-label::before {
    height: 1.75rem;
    width: 3.5rem;
    border-radius: 2rem;
}

.custom-switch-lg .custom-control-label::after {
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
}

.custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
    transform: translateX(-1.75rem);
}
</style>
@endpush
