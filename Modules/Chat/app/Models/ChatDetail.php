<?php

namespace Modules\Chat\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Database\factories\ChatDetailFactory;
use Modules\Chat\Enum\ChatDetailTypeEnum;
use Modules\User\Entities\User;

class ChatDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    protected $casts = [
        'type' => ChatDetailTypeEnum::class,
    ];
    protected static function newFactory(): ChatDetailFactory
    {
        //return ChatDetailFactory::new();
    }

    public function chat(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsQuestionAttribute(): bool
    {
        return $this->user_id === $this->chat?->user_id;
    }

    public function getIsAnswerAttribute(): bool
    {
        return $this->user_id !== $this->chat?->user_id;
    }

    public function getIsMessageAttribute(): bool
    {
        return $this->type === ChatDetailTypeEnum::MESSAGE;
    }

    public function getIsAttachAttribute(): bool
    {
        return $this->type === ChatDetailTypeEnum::ATTACH;
    }

}
