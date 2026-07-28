<?php

namespace Modules\Api\app\Resources;


use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class RequestDietResourse extends JsonResource
{


    /**
     * Transform the resource into an array.
     */


    private function calculatePercentage($total, $part) {
        if ($total == 0) {
            return 0; // جلوگیری از تقسیم بر صفر
        }
        return ($part / $total) * 100;
    }
    public function toArray($request): array
    {
        if ($this->dietPlan) {
            $diet_plan =  new DietPlanResource($this->dietPlan);
        } else {
            $diet_plan = null;
        }
        // diff days
        $startDate = Carbon::parse($this->start_date);
        $diffInDays = $startDate->diffInDays(Carbon::now());

        $total = abs(Carbon::parse($this->end_date)->diffInDays($this->start_date)) ?? 0;
        $currentDay = Carbon::parse($this->end_date)->isFuture() ? round($diffInDays + 1) : 0;


        return [
            'id'                 =>   $this->id,
            'top_chart'          =>  [
                'total_day' =>  abs(Carbon::parse($this->end_date)->diffInDays($this->start_date) )?? null,
                'current_day' =>  $currentDay,
                'percent' => round($this->calculatePercentage($total , $currentDay))
            ],
            'diet_plan_name'     =>  $diet_plan?->name,
            'start_weight'       =>  $this->user_information['WEIGHT'] ?? '',
            'status'             => Carbon::parse($this->end_date)->isFuture() == true,
            'start_date'         =>  $this->start_date ==! null ?  verta($this->start_date)->format('Y/m/d') : '',
            'end_date'           =>  $this->end_date ==! null ?  verta($this->end_date)->format('Y/m/d') : '' ,
            'calories'           =>  $this->calories  ,
        ];
    }
}
