<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Setting\Enum\SettingKeyEnum;
use Illuminate\Support\Str;

class Image extends Component
{
    use WithFileUploads;

    public mixed $image = null;

    public mixed $old_value;

    public SettingKeyEnum $meta;


    public function mount()
    {
        if ($this->old_value !== null) {
            $this->image = $this->old_value;
        }
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => ['required', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ], [
            'image.mimes' => 'فرمت آیکن باید PNG، JPG، WEBP یا SVG باشد.',
            'image.max' => 'حجم آیکن نباید بیشتر از ۲ مگابایت باشد.',
        ]);

        if (! $this->image instanceof TemporaryUploadedFile) {
            return;
        }

        $path = $this->image->storePubliclyAs(
            'settings/icons',
            Str::uuid().'.'.$this->image->getClientOriginalExtension(),
            'public'
        );

        $this->image = '/storage/'.$path;
        $this->dispatch(
            'settingUpdateListener',
            settingKey: $this->meta->value,
            value: $this->image
        );
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.image');
    }
}
