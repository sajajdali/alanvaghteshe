<?php

namespace Modules\Api\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Api\Enum\UserDeviceTypeEnum;
use Modules\User\Entities\User;

/**
 * Modules\Api\Entities\UserDevice
 *
 * @property int $id
 * @property int $user_id
 * @property int $access_token_id
 * @property UserDeviceTypeEnum $type
 * @property string|null $fcm_token
 * @property string|null $device_version
 * @property array|null $device_info
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, PersonalAccessToken> $accessTokens
 * @property-read int|null $access_tokens_count
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereAccessTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereDeviceInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereDeviceVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereFcmToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserDevice withoutTrashed()
 * @property string|null $appmetrica_profile_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserDevice whereAppmetricaProfileId($value)
 * @mixin \Eloquent
 */
class UserDevice extends Model
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'type' => UserDeviceTypeEnum::class,
        'device_info' => 'array',
    ];

    public function accessTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
