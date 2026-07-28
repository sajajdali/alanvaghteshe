<?php

namespace Modules\Chat\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Chat\Database\factories\ChatFactory;
use Modules\Chat\Enum\ChatStatusEnum;
use Modules\User\Entities\User;

class Chat extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */

    protected $guarded = ['id'];

    protected $casts = [
        'status' => ChatStatusEnum::class,
        'ban' => 'boolean',
    ];

    protected $with = ['user', 'chatDetails'];

    public static function getRoom($user_id) : Chat
    {
        $chat = Chat::where('user_id', $user_id)->first();
        if ($chat == null) {
            $chat = Chat::create([
                'user_id' => $user_id,
                'status' => ChatStatusEnum::JUST_CREATED,
                'new_message_by_user'=> 0,
                'new_message_by_support'=> 0,
            ]);
        }else{
            $chat->new_message_by_support = 0;
            $chat->save();
        }
        return $chat;
    }

    protected static function newFactory(): ChatFactory
    {
        //return ChatFactory::new();
    }

    public function chatDetails(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatDetail::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLatestMessageAgoAttribute(): ?string
    {
        return $this->chatDetails->last()?->created_at->format('Y-m-d H:i');
    }

    public function getLatestMessageExcerptAttribute(): ?string
    {
        //get excerpt of latest message
        return $this->chatDetails->last()?->content;
    }
}
