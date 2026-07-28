<?php

namespace Modules\Setting\Interface;

use Modules\Setting\Enum\SettingTypeEnum;

interface SettingRenderAbleInterface
{
    public function render() : string;
}
