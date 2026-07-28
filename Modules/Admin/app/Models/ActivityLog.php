<?php

namespace Modules\Admin\app\Models;

use Modules\User\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\app\Enums\ActivityEventEnum;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Admin\Database\factories\ActivityLogFactory;

class ActivityLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = ['event' => ActivityEventEnum::class];


    public function action(): MorphTo
    {
        return $this->morphTo();
    }
    public function loggedBy() {
        return $this->belongsTo(User::class,'admin_id');
    }
    public function loggedFor() {
        return $this->belongsTo(User::class,'user_id');
    }
    protected static function log(
        ?model $model,
        ActivityEventEnum $event,
        int $admin_id,
        int $user_id,
        ?string $description = null
    ) {
       return Self::create([
            'action_type' => $model?->getMorphClass(),
            'action_id' => $model?->getKey(),
            'event' => $event,
            'admin_id' => $admin_id,
            'user_id' => $user_id,
            'description' => $description,
        ]);
    }
}
