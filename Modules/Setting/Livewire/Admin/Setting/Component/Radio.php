<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;

class Radio extends Component
{
    public mixed $radioValue = null;

    public mixed $old_value;

    public SettingKeyEnum $meta;

    public function mount()
    {
        if ($this->old_value !== null) {
            $this->radioValue = $this->old_value;
        }
    }

    public function updatedRadioValue()
    {
        $this->emit('settingUpdateListener', $this->meta->value, $this->radioValue);
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.radio');
    }
}
