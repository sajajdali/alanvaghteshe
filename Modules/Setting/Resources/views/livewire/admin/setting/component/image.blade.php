<div>
    <div class="form-group mb-4">
        <label for="image_{{ $meta->value }}">{{ $meta->getName() }}</label>
        <input
            id="image_{{ $meta->value }}"
            class="form-control"
            type="file"
            accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml"
            wire:model="image"
        >

        <small class="d-block mt-2 text-muted">فرمت‌های مجاز: PNG، JPG، WEBP و SVG — حداکثر ۲ مگابایت</small>
        <div class="mt-2 text-primary" wire:loading wire:target="image">در حال آپلود آیکن...</div>
        @error('image')
            <div class="mt-2 text-danger">{{ $message }}</div>
        @enderror

        @if($image)
            <div class="mt-3" style="height:80px;">
                <img
                    src="{{ $image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $image->temporaryUrl() : $image }}"
                    alt="{{ $meta->getName() }}"
                    style="height:100%;max-width:160px;object-fit:contain;"
                >
            </div>
        @endif
    </div>
</div>
