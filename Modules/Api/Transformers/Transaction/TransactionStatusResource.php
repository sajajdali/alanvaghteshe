<?php

namespace Modules\Api\Transformers\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Package\Entities\Package;
use Modules\Transaction\Enum\TransactionStatusEnum;

class TransactionStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'status' => [
                'title' => $this->status,
                'body'  => TransactionStatusEnum::tryFrom($this->status->value)->getName()
            ],
            'description' => '',

            'detail' => [
                'total_price' =>  [
                    'currency' => 'ریال',
                    'amount' => $this->cost,
                ],
                'coupon' => $this->coupon_id ? [
                    'amount' => ($this->coupon_id) ? number_format($this->coupon->value) . ( $this->coupon->is_percent ? ' درصد' : ' ریال') : null,
                    'code' => $this->coupon_id ? $this->coupon->code : null
                ] : null,
                'package_discount' => [
                    'currency' => 'ریال',
                    'amount' => Package::find($this->detail['package_id'])->getDiscountPackage(),
                ],
                'wallet' => [
                    'currency' => 'ریال',
                    'amount' => $this->detail['user_wallet'],
                ],
                'payable_price' => [
                    'currency' => 'ریال',
                    'amount' => $this->total_cost
                ]
            ],
            'payment_link' => $this->status == TransactionStatusEnum::SUCCESSFUL ? '' : str_replace("api", 'crm', route('payment', $this)),

        ];
    }
}
