<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;

class Text extends Component
{
    public mixed $textValue = null;

    public mixed $old_value = null;

    public SettingKeyEnum $meta;

    public function mount()
    {
        if ($this->old_value !== null) {
            $this->textValue = $this->old_value;
        }
    }

    public function updatedTextValue()
    {
        $this->dispatch('settingUpdateListener', settingKey: $this->meta->value, value: $this->textValue);
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.text');
    }
}
