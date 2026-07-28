<?php

namespace Modules\Admin\Livewire\Component;

use Livewire\Component;

class Menu extends Component
{
    public $menuItems;

    public function mount()
    {
        $modules = \Module::allEnabled();
        foreach ($modules as $module) {
            $menu = config($module->getLowerName().'.menu') ?? [];
            if (is_array($menu) && count($menu) > 0) {
                $this->menuItems[] = $menu;
            }
        }
        usort($this->menuItems, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }

            return ($a['priority'] > $b['priority']) ? -1 : 1;
        });
    }

    public function render()
    {
        return view('admin::livewire.component.menu');
    }
}
