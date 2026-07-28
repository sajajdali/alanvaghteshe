<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Database\factories\UserMetaFactory;
use Modules\User\Enum\UserMetaEnum;

/**
 * Modules\User\Entities\UserMeta
 *
 * @property int $id
 * @property int $user_id
 * @property UserMetaEnum $meta_key
 * @property string $meta_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\User\Entities\User $user
 * @method static \Modules\User\Database\factories\UserMetaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereMetaKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereMetaValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserMeta whereUserId($value)
 * @mixin \Eloquent
 */
class UserMeta extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'meta_key' => UserMetaEnum::class,
    ];

    protected static function newFactory(): UserMetaFactory
    {
        return UserMetaFactory::new();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    

}
