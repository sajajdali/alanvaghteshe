<?php

namespace Modules\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        if (auth()->user()) {
            $session = auth()->user()?->currentAccessToken()?->name ?? '';
        } else {
            //get user last session
            $session = $this->tokens()->latest()->first()?->name ?? '';
        }

        return [
            'id' => (int) $this->id ?? 0,
            'first_name' => $this->first_name ?? '',
            'last_name' => $this->last_name ?? '',
            'avatar' => $this->avatar ?? '',
            'session' => $session
        ];
    }
}
