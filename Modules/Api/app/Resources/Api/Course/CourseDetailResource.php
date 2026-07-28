<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Transformers\FaqResource;
use Modules\Course\app\Models\Course;

class CourseDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        $price = (int) ($this->price ?? 0);
        $discountedPrice = $this->discounted_price !== null ? (int) $this->discounted_price : null;
        $finalPrice = (int) ($this->final_price ?? $this->price ?? 0);
        $hasDiscount = $discountedPrice !== null && $price > 0 && $discountedPrice < $price;
        $discountPercentage = $hasDiscount
            ? (int) floor((($price - $discountedPrice) / $price) * 100)
            : 0;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail,
            'is_purchased' => (bool) ($this->is_purchased ?? false),
            $this->mergeWhen((bool) ($this->can_mark_seen ?? false), [
                'can_mark_seen' => true,
            ]),
            'description' => $this->description,
            'short_description' => $this->short_description,
            'duration_minutes' => (int) ($this->duration_minutes ?? 0),
            'price' => $price,
            'discounted_price' => $discountedPrice,
            'final_price' => $finalPrice,
            'has_discount' => $hasDiscount,
            'discount_percentage' => $discountPercentage,
            'is_purchasable' => (bool) $this->is_purchasable,
            'level' => [
                'id' => (int) $this->level,
                'title' => Course::levels()[$this->level] ?? 'نامشخص',
            ],
            'category' => $this->category ? [
                'id' => $this->category->id,
                'title' => $this->category->title,
                'image' => $this->category->image,
            ] : null,
            'sections' => CourseSectionDetailResource::collection($this->whenLoaded('sections')),
            'faqs' => FaqResource::collection($this->whenLoaded('faqs')),
        ];
    }
}
