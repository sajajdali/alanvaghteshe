<?php

namespace Modules\Setting\Interface;

interface ModelHasSettingOptionInterface
{
    public static function getArrayForSetting(mixed $type = null) : array;
}
