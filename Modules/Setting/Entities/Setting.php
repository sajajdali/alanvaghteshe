<?php

namespace Modules\Setting\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Setting\Enum\SettingKeyEnum;

/**
 * Modules\Setting\Entities\Setting
 *
 * @property int $id
 * @property SettingKeyEnum $setting_key
 * @property string|null $setting_value
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereSettingValue($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    public const CACHE_NAME = 'jesmino_setting_cache';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected $casts = [
        'setting_key' => SettingKeyEnum::class,
    ];

    public static function getSettingSections(): ?array
    {
        return config('setting.setting');
    }

    public static function setVal(int $key, $value): void
    {
        $settingMetaKey = SettingKeyEnum::tryFrom($key);
        if ($settingMetaKey !== null) {
            $insertValue = $value;
            self::updateOrCreate(
                ['setting_key' => $settingMetaKey],
                ['setting_value' => $insertValue]
            );
        }
    }

    public static function reBuild(): void
    {
        cache()->forget(self::CACHE_NAME);
        $cacheItem = [];
        foreach (self::all() as $setting) {
            if ($setting->setting_key->isSupportCache()) {
                $cacheItem[$setting->setting_key->value] = $setting->setting_value;
            }
        }
        cache()->put(self::CACHE_NAME, $cacheItem);
    }

    public static function getOriginalVal(int $key)
    {
        $settingMetaKey = SettingKeyEnum::tryFrom($key);
        if ($settingMetaKey !== null) {
            return Setting::whereSettingKey($settingMetaKey)->value('setting_value');
        }

        return null;
    }

    public static function v(SettingKeyEnum $keyEnum)
    {
        return self::getVal($keyEnum->value);
    }

    public static function getVal(int $key)
    {
        $settingMetaKey = SettingKeyEnum::tryFrom($key);
        $returnValue = null;
        if ($settingMetaKey !== null) {
            $returnValue = Setting::getOriginalVal($key);
        }

        return $returnValue;
    }
}
