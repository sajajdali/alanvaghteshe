<?php

namespace Modules\Admin\Livewire\Component;

use Livewire\Component;

class MenuItem extends Component
{
    public array $item;

    public int $depth;

    public int $nextDepth;

    public array $aClassByDepth = [
        true => [
            0 => 'side-menu__item',
            1 => 'sub-side-menu__item',
            2 => 'sub-side-menu__item2',
        ],
        false => [
            0 => 'side-menu__item',
            1 => 'slide-item',
            2 => 'sub-slide-item',
            3 => 'sub-slide-item2',
        ],
    ];

    public array $aToggleByDepth = [
        0 => 'slide',
        1 => 'sub-slide',
        2 => 'sub-slide2',
    ];

    public array $ulClassByDepth = [
        0 => 'slide-menu',
        1 => 'sub-slide-menu',
        2 => 'sub-slide-menu2',
    ];

    public array $spanClassByDepth = [
        0 => 'side-menu__label',
        1 => 'sub-side-menu__label',
        2 => 'sub-side-menu__label2',
    ];

    public array $angleClassByDepth = [
        0 => 'angle',
        1 => 'sub-angle',
        2 => 'sub-angle2',
    ];

    public function mount()
    {
        $this->nextDepth = $this->depth + 1;
    }

    public function badgeCount($className): int
    {
        if (method_exists($className, 'badgeCount')) {
            return $className::badgeCount();
        }

        return 0;
    }

    public function render()
    {
        return view('admin::livewire.component.menu-item');
    }
}
