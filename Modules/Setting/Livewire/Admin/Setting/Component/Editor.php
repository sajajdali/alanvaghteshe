<?php

namespace Modules\Setting\Livewire\Admin\Setting\Component;

use Livewire\Component;
use Modules\Setting\Enum\SettingKeyEnum;

class Editor extends Component
{
    public mixed $editorValue = null;

    public mixed $old_value = null;

    public SettingKeyEnum $meta;

    public function mount()
    {
        if ($this->old_value !== null) {
            $this->editorValue = $this->old_value;
        }
    }

    public function updatedEditorValue()
    {
        $this->emit('settingUpdateListener', $this->meta->value, $this->editorValue);
    }

    public function render()
    {
        return view('setting::livewire.admin.setting.component.editor');
    }
}
