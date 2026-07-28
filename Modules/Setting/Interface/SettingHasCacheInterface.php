<?php

namespace Modules\Setting\Interface;

use Modules\Setting\Enum\SettingTypeEnum;

interface SettingHasCacheInterface
{
    public function isSupportCache() : bool;
}
