<?php

namespace Modules\Setting\Interface;

use Modules\Setting\Enum\SettingTypeEnum;

interface SettingTypeInterface
{
    public function getType() : SettingTypeEnum;
}
