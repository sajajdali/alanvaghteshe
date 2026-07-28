<div>
    <div class="example mb-5" wire:ignore>
        <div class="form-group" wire:ignore>
            <label for="text_{{ $meta->value }}">{{ $meta->getName() }}</label>
            <textarea id="editor_{{ $meta->value }}" class="content"
                      name="editor_{{ $meta->value }}">{{ $editorValue }}</textarea>
        </div>
    </div>
    <script>
        function prepare_editor_{{ $meta->value }}() {
            $(document).ready(function () {
                let editor = CKEDITOR.replace('editor_{{ $meta->value }}', {
                    language: 'fa',
                });
                editor.on('change', function (event) {
                @this.set('editorValue', event.editor.getData())
                    ;
                });
            });
        }

        let isEditorLoaded = false;
        document.addEventListener("DOMContentLoaded", function (event) {
            isEditorLoaded = true;
            prepare_editor_{{ $meta->value }}();
        });
        if (typeof $ == 'function') {
            prepare_editor_{{ $meta->value }}();
        }
    </script>
</div>
