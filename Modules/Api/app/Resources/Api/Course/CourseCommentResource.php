<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseCommentResource extends JsonResource
{
    public function toArray($request): array
    {
        $authorName = null;

        if ($this->user) {
            $authorName = filled(trim((string) ($this->user->full_name ?? '')))
                ? $this->user->full_name
                : ($this->user->mobile ?? null);
        }

        return [
            'id' => $this->id,
            'author_name' => $authorName ?: ($this->author_name ?? 'کاربر ناشناس'),
            'body' => $this->body,
            'admin_reply' => $this->admin_reply,
            'created_at' => optional($this->created_at)->toDateTimeString(),
        ];
    }
}
