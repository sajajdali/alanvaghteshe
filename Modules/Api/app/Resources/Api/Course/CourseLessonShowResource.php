<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseLessonShowResource extends JsonResource
{
    public function toArray($request): array
    {
        $progress = $this->progress_for_user ?? null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content_type' => $this->content_type,
            'video_url' => $this->video_url,
            'document_url' => $this->document_url,
            'content_url' => $this->content_type === 'document' ? $this->document_url : $this->video_url,
            'duration_seconds' => (int) ($this->duration_seconds ?? 0),
            'duration_minutes' => (int) ceil(((int) ($this->duration_seconds ?? 0)) / 60),
            'is_preview' => (bool) $this->is_preview,
            'is_free' => (bool) $this->is_preview,
            'is_seen' => $progress !== null,
            'can_mark_seen' => (bool) ($this->can_mark_seen ?? false),
            'seen_at' => optional($progress?->completed_at)->toDateTimeString(),
            'course' => $this->section?->course ? [
                'id' => $this->section->course->id,
                'title' => $this->section->course->title,
                'slug' => $this->section->course->slug,
            ] : null,
            'section' => $this->section ? [
                'id' => $this->section->id,
                'title' => $this->section->title,
                'sort_order' => (int) $this->section->sort_order,
            ] : null,
        ];
    }
}
