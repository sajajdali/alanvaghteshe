<?php

namespace Modules\Api\Transformers\Package;

use AllowDynamicProperties;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Package\Entities\Package;

#[AllowDynamicProperties] class PackageResource extends JsonResource
{

    public function __construct($resource, $additionalData = [])
    {
        parent::__construct($resource);
        $this->additionalData = $additionalData;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'days'         => $this->days,
            'suggested'      => isset($this->detail[Package::JSON_DETAIL_SUGGESTED]) && $this->detail[Package::JSON_DETAIL_SUGGESTED] === true,
            'type'        => $this->type->getName(),
            'price'        => $this->price,
            'user_wallet' => auth()->user()->wallet,
            'support_price' => $this->support_price ?? 0,
//            'price_per_day'  => self::pricePaeDay( $this->additionalData['payablePrice'] ?? $this->getPrice()),
            'special_price'=> $this->special_price,
            'priority'     => $this->priority,
            'payable_price' => $this->additionalData['payablePrice'] ?? $this->getPrice() - auth()->user()->wallet,
        ];
    }

    private function pricePaeDay($price) : int
    {
        $amount = (int) round($price / $this->days);
        $amount = max($amount, 0);
        return round($amount, -3) / 1000;
    }
}
