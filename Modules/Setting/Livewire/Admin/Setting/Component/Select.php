<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;

class Select extends Component
{
    public mixed $selectValue = null;

    public mixed $old_value;

    public SettingKeyEnum $meta;

    public function mount()
    {
        if ($this->old_value !== null) {
            $this->selectValue = $this->old_value;
        }
    }

    public function updatedSelectValue()
    {
        $this->dispatch('settingUpdateListener', settingKey:$this->meta->value, value:$this->selectValue);
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.select');
    }
}
