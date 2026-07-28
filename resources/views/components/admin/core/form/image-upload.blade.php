@props([
    'label' => 'Upload File',
    'uploadedPhotoUrl' => null,
    'uploadedFileName' => null,
    'uploadedFileType' => null,
    'deleteAction' => null,
    'model' => '',
    'id' => 'file',
])

@push('styles')
<style>
    .upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        border-radius: 8px;
        z-index: 10;
        transition: opacity 0.3s ease-in-out;
    }

    .upload-container {
        position: relative;
        display: inline-block;
        width: 120px;
        height: 120px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .img-thumbnail {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .spinner-border {
        width: 1.5rem;
        height: 1.5rem;
    }
</style>
@endpush
<label class="form-label fw-bold" for="{{ $id }}">{{ $label }}</label>
<div class="d-flex align-items-center gap-3">

    @if ($uploadedPhotoUrl)
        <div class="upload-container shadow-sm">
            @if (str_contains($uploadedFileType, 'image'))
                <img src="{{ $uploadedPhotoUrl }}" alt="Uploaded Image" class="img-thumbnail">
            @else
                <div class="d-flex flex-column align-items-center justify-content-center bg-light" style="height: 100%;">
                    <i class="fa {{ getFileIconClass($uploadedFileType) }} fa-2x text-primary"></i>
                    <p class="small text-truncate mt-1">{{ $uploadedFileName }}</p>
                </div>
            @endif

            <!-- Overlay هنگام آپلود یا حذف -->
            <div wire:loading wire:target="{{ $model }}, {{ $deleteAction }}" class="upload-overlay">
                <div class="spinner-border text-light" role="status"></div>
            </div>
        </div>

        <!-- دکمه حذف -->
        <button type="button"
                wire:click="{{ $deleteAction }}"
                class="btn btn-danger btn-sm d-flex align-items-center gap-1 shadow-sm">
            <i class="fa fa-trash"></i>
            <span wire:loading.remove wire:target="{{ $deleteAction }}">حذف</span>
        </button>
    @endif
</div>

<!-- ورودی آپلود فایل -->
<div class="input-group mt-3 position-relative">
    <input wire:model="{{ $model }}" type="file" class="form-control" id="{{ $id }}"
           wire:loading.attr="disabled" wire:target="{{ $model }}">
</div>

<!-- نمایش خطا -->
@error($model)
<div class="text-danger mt-2 fw-bold">{{ $message }}</div>
@enderror
