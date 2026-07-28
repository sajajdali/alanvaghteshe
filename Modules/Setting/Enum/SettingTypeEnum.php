<?php

namespace Modules\Setting\Enum;

enum SettingTypeEnum : string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case TEXTAREA = 'textarea';

    case EDITOR = 'editor';

    case RADIO = 'radio';

    case SELECT = 'select';

    public function component(int $key) : string {
        return match ($this){
            self::TEXT => 'setting::admin.setting.component.text',
            self::IMAGE => 'setting::admin.setting.component.image',
            self::TEXTAREA => 'setting::admin.setting.component.text-area',
            self::EDITOR => 'setting::admin.setting.component.editor',
            self::RADIO => 'setting::admin.setting.component.radio',
            self::SELECT => 'setting::admin.setting.component.select'
        };
    }
}
