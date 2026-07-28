<?php
namespace Modules\Setting\Livewire\Admin\Setting;

use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[title('تنظیمات سایت')]
class Setting extends Component
{
    #[Url]
    public string $section = 'exercise';

    public ?array $options = null;

    public ?array $menuSections = null;

    public array $settingValues;

    #[On('settingUpdateListener')]
    public function settingUpdateListener(int $settingKey, mixed $value): void
    {
        $this->settingValues[$settingKey] = $value;
    }

    public function changeMenu(?string $menuId): void
    {
        if ($this->section === $menuId && $this->options !== null) {
            return;
        }
        $setting = \Modules\Setting\Entities\Setting::getSettingSections();
        $this->settingValues = [];
        $this->section = $menuId;
        foreach ($setting as $key => $menu) {
            if ($key === $this->section) {
                $this->options = $menu['settings'];
                foreach ($menu['settings'] as $tmpSet) {
                    $this->settingValues[$tmpSet->value] = \Modules\Setting\Entities\Setting::getOriginalVal($tmpSet->value);
                }
            }
        }
    }

    public function storeSetting()
    {
        foreach ($this->settingValues as $key => $value) {
            \Modules\Setting\Entities\Setting::setVal((int) $key, $value);
        }
        \Modules\Setting\Entities\Setting::reBuild();
        $this->dispatch('success-saving', message:'تنظیمات با موفقیت ذخیره شدند');
    }

    public function mount()
    {
        $this->changeMenu($this->section);
        $setting = \Modules\Setting\Entities\Setting::getSettingSections();
        $this->menuSections = [];
        foreach ($setting as $key => $menu) {
            $this->menuSections[] = [
                'id' => $key,
                'title' => $menu['title'],
                'icon' => $menu['icon'],
            ];
        }
    }

    public function render()
    {
        $setting = \Modules\Setting\Entities\Setting::getSettingSections();
        foreach ($setting as $key => $menu) {
            if ($key === $this->section) {
                $this->options = $menu['settings'];
            }
        }
        return view('setting::livewire.admin.setting.setting');
    }
}
