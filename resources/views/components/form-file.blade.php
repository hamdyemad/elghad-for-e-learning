<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    
    @if($currentFile ?? false)
    <div class="mb-3" id="current-file-container-{{ $name }}">
        <div class="d-flex align-items-center">
            <img src="{{ $currentFile }}" alt="Current" class="rounded shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
            <div class="mr-3">
                <p class="mb-0 font-weight-bold">{{ $currentFileLabel ?? 'الصورة الحالية' }}</p>
                <small class="text-muted">اختر صورة جديدة للتغيير</small>
            </div>
        </div>
    </div>
    @endif
    
    <div class="file-upload-wrapper" data-text="{{ $placeholder ?? 'اسحب الملف هنا أو اضغط للاختيار' }}" id="wrapper-{{ $name }}">
        <input 
            type="file" 
            class="file-upload-input @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            accept="{{ $accept ?? '*' }}"
        >
    </div>
    
    @error($name)
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
    
    <div id="{{ $name }}-preview" class="mt-3" style="display: none;">
        <p class="mb-2 font-weight-bold">معاينة الصورة الجديدة:</p>
        <img src="" alt="Preview" class="rounded shadow-sm" style="max-width: 300px; max-height: 300px;">
    </div>
</div>

@once
@push('styles')
<style>
.file-upload-wrapper {
    position: relative;
    width: 100%;
    height: 150px;
    border: 2px dashed #ced4da;
    border-radius: 8px;
    background: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.file-upload-wrapper:hover {
    border-color: #5b73e8;
    background: #f0f3ff;
}

.file-upload-wrapper.dragover {
    border-color: #5b73e8;
    background: #e8ecff;
    transform: scale(1.02);
}

.file-upload-wrapper::before {
    content: attr(data-text);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 16px;
    color: #6c757d;
    pointer-events: none;
    text-align: center;
    width: 80%;
}

.file-upload-wrapper::after {
    content: '\F0552';
    font-family: 'Material Design Icons';
    position: absolute;
    top: 35%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 32px;
    color: #5b73e8;
    pointer-events: none;
}

.file-upload-input {
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-upload-wrapper.has-file {
    border-color: #28a745;
    background: #f0fff4;
}

.file-upload-wrapper.has-file::before {
    color: #28a745;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const input = document.getElementById('{{ $name }}');
    const wrapper = document.getElementById('wrapper-{{ $name }}');
    const preview = document.getElementById('{{ $name }}-preview');
    
    // File selection
    input.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            wrapper.setAttribute('data-text', file.name);
            wrapper.classList.add('has-file');
            
            // Hide current file container if it exists
            const currentContainer = document.getElementById('current-file-container-{{ $name }}');
            if (currentContainer) currentContainer.style.display = 'none';
            
            // Preview only if it's an image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.querySelector('img').src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }
    });
    
    // Drag and drop
    wrapper.addEventListener('dragover', function(e) {
        e.preventDefault();
        wrapper.classList.add('dragover');
    });
    
    wrapper.addEventListener('dragleave', function(e) {
        e.preventDefault();
        wrapper.classList.remove('dragover');
    });
    
    wrapper.addEventListener('drop', function(e) {
        e.preventDefault();
        wrapper.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            input.files = e.dataTransfer.files;
            input.dispatchEvent(new Event('change'));
        }
    });
})();
</script>
@endpush
@endonce
