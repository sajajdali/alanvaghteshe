<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\User;

/**
 * App\UserSession
 *
 * @property int $id
 * @property int $chat_id
 * @property string $state
 * @property mixed|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSession whereUpdatedAt($value)
 * @property-read User|null $user
 * @property int|null $user_id
 * @property int $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserSession whereUserId($value)
 * @mixin \Eloquent
 */
class UserSession extends Model
{
    protected $casts = [
        'data' => 'array',
    ];
    protected $table = 'user_sessions';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
