@php
    $rich = $rich ?? false;
    $height = $height ?? '300px';
    $dir = $dir ?? 'rtl';
    $placeholder = $placeholder ?? '';
    $id = $id ?? $name;
@endphp

<div class="form-group mb-4">
    <label for="{{ $id }}" class="mb-2">{{ $label }}</label>

    @if($rich)
        <div 
            id="{{ $id }}-editor" 
            class="quill-editor" 
            style="height: {{ $height }}; direction: {{ $dir }};"
        ></div>
        <textarea 
            name="{{ $name }}" 
            id="{{ $id }}" 
            class="d-none"
        >{{ old($name, $value ?? '') }}</textarea>
    @else
        <textarea 
            name="{{ $name }}" 
            id="{{ $id }}" 
            class="form-control @error($name) is-invalid @enderror" 
            rows="{{ $rows ?? 4 }}"
            placeholder="{{ $placeholder }}"
        >{{ old($name, $value ?? '') }}</textarea>
    @endif

    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

@if($rich)
    @once
    @push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        .quill-editor {
            border: 1px solid #ced4da;
            border-radius: 0 0 0.25rem 0.25rem;
            background: #fff;
        }
        .ql-toolbar.ql-snow {
            border: 1px solid #ced4da;
            border-radius: 0.25rem 0.25rem 0 0;
            background: #f8f9fa;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    @endpush
    @endonce

    @push('scripts')
    <script>
    $(document).ready(function() {
        var editorId = '#{{ $id }}-editor';
        var textareaId = '#{{ $id }}';
        
        if ($(editorId).length) {
            var quill = new Quill(editorId, {
                theme: 'snow',
                placeholder: '{{ $placeholder }}',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'direction': 'rtl' }],
                        [{ 'align': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'blockquote'],
                        ['clean']
                    ]
                }
            });

            // Set initial content
            var initialContent = $(textareaId).val();
            if (initialContent) {
                quill.root.innerHTML = initialContent;
            }

            // Sync on form submit
            var form = $(textareaId).closest('form');
            form.on('submit', function() {
                var content = quill.root.innerHTML;
                if (quill.getText().trim().length === 0) {
                    content = '';
                }
                $(textareaId).val(content);
            });
        }
    });
    </script>
    @endpush
@endif
