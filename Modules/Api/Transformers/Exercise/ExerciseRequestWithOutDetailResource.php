<?php

namespace Modules\Api\Transformers\Exercise;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;
use Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum;

class ExerciseRequestWithOutDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? 0,
            'plan_name' => $this->exercisePlanStrategy->name ?? "",
            'session_count' => $this->session_count ?? 0,
            'level' => $this->level->is(ExercisePlanStrategyLevelEnum::BEGINNER) ? 'Beginner' : 'Advanced',
            'status' => $this->status->is(ExercisePlanRequestEnum::COMPLETED),
            'start_weight' => "90",//todo: get user start weight
            'target_weight' => "90",//todo: get user target weight
            "start_at" => jdate('Y/m/d', $this->start_at->timestamp) ?? "---/--/--",
            "end_at" => jdate('Y/m/d', $this->end_at->timestamp) ?? "---/--/--",
            "exercises" => null
        ];
    }
}
