<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;

class Image extends Component
{
    public mixed $image = null;

    public mixed $old_value;

    public SettingKeyEnum $meta;


    #[On('imageChange')]
    public function imageChange($newValue)
    {
        $this->image = $newValue;
        $this->updatedImage();
    }

    public function mount()
    {
        if ($this->old_value !== null) {
            $this->image = $this->old_value;
        }
    }

    public function updatedImage()
    {
        $this->dispatch('settingUpdateListener', settingKey:$this->meta->value, value:$this->image);
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.image');
    }
}
