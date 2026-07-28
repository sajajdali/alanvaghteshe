<?php

namespace Modules\Setting\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Setting\Entities\Setting;

class SettingCacheClearedListener
{
    public function handle($event): void
    {
        Setting::reBuild();
    }
}
