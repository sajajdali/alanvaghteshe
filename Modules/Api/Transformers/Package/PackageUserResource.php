<?php

namespace Modules\Api\Transformers\Package;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'package_name' => $this->package->name,
            'package_type' => $this->package->type,
            'start_at' => verta($this->start_at)->format("Y/m/d"),
            'end_at' => verta($this->end_at)->format("Y/m/d"),
            'type'  => $this->type->getName(),
            'package_length' => $this->package->days,
            'package_past_days' => $this->package->days - $this->remaining_days,
            'package_remaining_days' => $this->remaining_days,
            'type_id'  => $this->type->value,

        ];
    }
}
