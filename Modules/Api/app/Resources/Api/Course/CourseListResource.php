<?php

namespace Modules\Api\app\Resources\Api\Course;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Api\Enum\RouteEnum;
use Modules\Api\app\Resources\ButtonResource;
use Modules\Course\app\Models\Course;

class CourseListResource extends JsonResource
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
            'short_description' => $this->short_description,
            'thumbnail' => $this->thumbnail,
            'price' => $price,
            'discounted_price' => $discountedPrice,
            'final_price' => $finalPrice,
            'is_free' => $finalPrice === 0,
            'has_discount' => $hasDiscount,
            'discount_percentage' => $discountPercentage,
            'is_purchasable' => (bool) $this->is_purchasable,
            'duration_minutes' => (int) ($this->duration_minutes ?? 0),
            'access_days' => (int) ($this->access_days ?? 0),
            'capacity' => $this->capacity !== null ? (int) $this->capacity : null,
            'level' => [
                'id' => (int) $this->level,
                'title' => Course::levels()[$this->level] ?? 'نامشخص',
            ],
            'category' => $this->category ? [
                'id' => $this->category->id,
                'title' => $this->category->title,
                'image' => $this->category->image,
            ] : null,
            'counters' => [
                'sections' => (int) ($this->sections_count ?? 0),
                'faqs' => (int) ($this->faqs_count ?? 0),
                'comments' => (int) ($this->comments_count ?? 0),
                'students' => (int) ($this->active_purchases_count ?? 0),
            ],
            'badges' => [
                'is_latest' => (bool) $this->is_latest,
                'is_popular' => (bool) $this->is_popular,
                'is_purchased' => (bool) ($this->is_purchased ?? false),
            ],
            'button' => ButtonResource::make([
                'title' => 'مشاهده دوره',
                'subtitle' => $this->title,
                'route' => RouteEnum::COURSE_DETAIL,
                'data' => [
                    'course_id' => $this->id,
                    'slug' => $this->slug,
                ],
            ]),
        ];
    }
}
