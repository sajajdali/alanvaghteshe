<?php

namespace Modules\Api\app\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestDietCompexFoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request) :array
    {

        return [
            'type' => $this->main_nutrition->value,
            'id' => $this->id,
            'title' => $this->combinedFoodResult($this),
            'carb' => round($this->carb),
            'protein' => round($this->protein),
            'fat' => round($this->fat),
            'fiber' => round($this->fiber),
            'calorie' => round($this->calories),
        ];
    }

    public function combinedFoodResult($detail): string
    {
        //collection of the basic food that create a complex food ;
        $basicFoodCollection = $detail->foodClass->basicFoodPivot;
        $returnValue = '';
        $return_arr = [];
        if ($basicFoodCollection->count() > 0) {
            foreach ($basicFoodCollection as $key => $basicFood) {
                $return_arr[] = ($basicFood->pivot->quantity  * $detail->number_of_unit) . ' ' . ( $basicFood->units()->where('is_primary', '1')->first()?->name ?? $basicFood->units()->first()->name) . ' ' . $basicFood->name  ;
            }
            // if ($key < $basicFoodCollection->count() - 1) {
            //     $returnValue .= ' + ';
            // }
        }
        return implode(' + ' , $return_arr);
    }
}
