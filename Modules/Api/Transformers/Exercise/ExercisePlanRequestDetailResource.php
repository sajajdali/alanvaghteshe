<?php

namespace Modules\Api\Transformers\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;

class ExercisePlanRequestDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->exercise_name ?? "",
            'reps' => $this->reps ?? [],
            'video' => $this->exercise?->video,
            'for_day'   => $this->day,
            'has_super_set' => $this->has_super_set ?? false,
            'super_set' => $this->has_super_set ? ExercisePlanRequestDetailResource::make($this->superSet) : null,
        ];
    }
}
