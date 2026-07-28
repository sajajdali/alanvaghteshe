<?php
namespace App\Helpers;
use Illuminate\Support\Str;
use Modules\User\Entities\UserMeta;
use Modules\User\Enum\UserMetaEnum;

class InvitationCodeGenerator
{
    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::lower(Str::random(1)) . rand(10000, 99999);
        } while (UserMeta::where('meta_key', UserMetaEnum::INVITATION_CODE)->where('meta_value', $code)->exists());

        return $code;
    }
}
