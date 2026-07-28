<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DietPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id ,
            'name'       => $this->name ,
            'status'     => $this->status ,
            'day_count'  => $this->day_count ,
            'created_at' =>$this->created_at ,
        ];
    }
}
