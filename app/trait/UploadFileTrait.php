<?php

namespace App\trait;

use Illuminate\Support\Facades\Storage;

trait UploadFileTrait
{
    public $photo;
    public $uploadedPhotoUrl;
    public $uploadedFileName;
    public $uploadedFileType;
    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'file',
        ]);
        $path = $this->photo->store('temp/'.$this->filePath, ['disk' => 'public']);
        $this->uploadedPhotoUrl = Storage::disk('public')->url($path);
        $this->uploadedFileName = $this->photo->getClientOriginalName();
        $this->uploadedFileType = $this->photo->getMimeType();

        $this->form['photo'] = $path;
    }

    public function deleteFile()
    {
        if ($this->uploadedPhotoUrl) {
            $relativePath = str_replace(Storage::disk('public')->url(''), '', $this->uploadedPhotoUrl);
            Storage::disk('public')->delete($relativePath);
        }

        $this->photo = null;
        $this->uploadedPhotoUrl = null;
        $this->uploadedFileName = null;
        $this->uploadedFileType = null;

    }
}
