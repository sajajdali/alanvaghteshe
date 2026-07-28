<?php

namespace Modules\Api\app\Resources\diet_request_detail;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Diet\Entities\Food;

class RequestDietDetailResource extends JsonResource
{
    private $additionalData = [];
    private $foodName;

    public function __construct($resource, $foodName = null , $additionalData = [])
    {
        parent::__construct($resource);
        $this->foodName = $foodName;
        if ($additionalData) {
            $this->additionalData = $additionalData;
        }
    }

    public function withAdditionalData(array $data)
    {
        $this->additionalData = $data;
        return $this;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {

        $quantity = $this->additionalData['quantity'] ?? 1;

        if($this->foodable_type == Food::class){
            $foodNames = is_array($this->foodName) ? $this->foodName : [];

            $meal =  [
                'b_id'      =>  $this->pivot,
                'id'      =>  $this->id,
                'title' => count($foodNames) ? implode(" + ", $foodNames) : '',
            ];
            $quantityPerUnit = 100;
        } else {
            $unit = $this->units()->where('is_primary', '1')->first() ?? $this->units()->first();
            $quantityPerUnit = $unit->pivot->quantity_per_unit;
            $unitName = $this->numberToFraction($quantity) . ' ' . $unit->name;

            $meal =  [
                'id'      =>  $this->id,
                'title' =>  $unitName . ' ' . $this->name ,
            ];
        }


        // calculate per each unit gram
        $thisCalorie        = clcPerUnit($this->food_fact['CALORIE'] , $quantityPerUnit);
        $thisCarbohydrate   = clcPerUnit($this->food_fact['CARBOHYDRATE'] , $quantityPerUnit);
        $thisProtein        = clcPerUnit($this->food_fact['PROTEIN'] , $quantityPerUnit);
        $thisFat            = clcPerUnit($this->food_fact['FAT'] , $quantityPerUnit);
        $thisFiber          = clcPerUnit($this->food_fact['FIBER'] , $quantityPerUnit);

        $detailMeal = [
            'carb'    =>  $this->food_fact['CARBOHYDRATE'] > 0  ? round(($thisCarbohydrate * $quantity) , 2) : null,
            'protein' =>  $this->food_fact['PROTEIN'] > 0  ? round(($thisProtein * $quantity), 2) : null,
            'fat'    =>  $this->food_fact['FAT'] > 0  ? round(($thisFat * $quantity), 2) : null,
            'fiber'  =>  $this->food_fact['FIBER'] > 0  ? round(($thisFiber * $quantity), 2) : null,
            'calorie'  => $this->food_fact['CALORIE'] > 0  ? round(( $thisCalorie * $quantity), 2) : null,
        ];
        $formattedData = array_map(function ($value) {
            return number_format($value, 2, '.', '');
        }, $detailMeal);
        return array_merge($meal, $formattedData);

    }

    private function numberToFraction($number) {
        // ابتدا قسمت صحیح را جدا می‌کنیم
        $whole = floor($number);
        // سپس قسمت کسری را محاسبه می‌کنیم
        $fraction = $number - $whole;

        // تبدیل قسمت کسری به متن
        if ($fraction == 0.25) {
            $fractionText = 'یک چهارم';
        } elseif ($fraction == 0.5) {
            $fractionText = 'نیم';
        } elseif ($fraction == 0.75) {
            $fractionText = 'سه چهارم';
        } else {
            $fractionText = '';
        }

        if ($fraction == 0) {
            return $whole;
        }

        // اگر فقط قسمت کسری باشد
        if ($whole == 0) {
            return $fractionText;
        }

        // ترکیب قسمت صحیح و کسری
        return $whole . ' و ' . $fractionText;
    }

    private function unitDisplayGram()
    {
        if ($this->foodClass->foodUnit?->display_gram){
            return ' '. $this->foodClass->quantity_per_unit . ' گرم ';
        }
        return '';
    }

    private function convertToString($data): string
    {
        if (is_array($data) ){
            return implode(" + ", $data);
        }
        return $data;
    }
}
