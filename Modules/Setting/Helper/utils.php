<?php

if (! function_exists('setting')) {
    function setting(Modules\Setting\Enum\SettingKeyEnum $key)
    {
        return \Modules\Setting\Entities\Setting::v($key);
    }
}
