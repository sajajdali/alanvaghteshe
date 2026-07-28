<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Enum\FoodTypeEnum;

class PrescriptionDietDetail extends Component
{
    public $dietReqestId,$dietReqeust;
    //indicade that food_type is not simple and its combined ;
    public $foodTypeIsComined = false  ;

    public $isReplace = false ;
    public $form = [] ;
    public $parentDietdetail = null  ;
    public $successFully = null ;
    public bool $changeCalorie = false;
    public $color = [
        ''  ,
        '#f8fcff'
    ];
    public $colorIndex ;
    //chart numbers
    public $totallMeals , $totaldays ,$donedlMeals , $leftToConsume ,$consumedMealPresent ,$leftMealToConsume ;

    protected function rules()
    {
        return [
            'form.new_calorie' => 'required',
        ];
    }
    protected function validationAttributes()
    {
        return [
            'form.new_calorie' => 'کالری جدید',
        ];
    }


    #[Computed]
    public function details()
    {
        return DietRequestDetail::where('diet_request_id', $this->dietReqestId)->get()->groupBy('main_nutrition');

    }
    public function changeCalorieAction()
    {
        $this->validate();
        $this->dietReqeust->calories = (int) $this->form['new_calorie'];
        $this->dietReqeust->save();
        $this->changeCalorie = false;
        return $this->successFully = 'پلن با موفقیت تغییر کرد';
    }

    public function toggleChangeCalorie()
    {
        $this->successFully = null;
        $this->changeCalorie = !$this->changeCalorie;
    }


    #[Computed]
    public function complexFoodResult($detail , $showMaxAllow= false) :array
     {
        return app('dietService')->getComplexFood($detail , $showMaxAllow);
    }

    public function  caculateColorAndParent($detail) {
        $index = 0 ;
        $index = $detail->day_number;
        if ($index > count($this->color)) {
            while (true) {
                $index = $index - count($this->color);
                if ($index <= count($this->color)) {
                    break;
                }
            }
        }
        return  $index  ;
    }

    public function isReplaced($detail)
     {
        $this->isReplace = false;
        if ($detail->replaced_parent_id == !null) {

            $this->isReplace = true;
        }

        return $this->isReplace ;

    }

    public function parentDietdetail($detail_parent) {
      $parentd =  DietRequestDetail::find($detail_parent->replaced_parent_id);
      return $parentd ;
    }



    public function mount() {
        $this->dietReqeust = DietRequest::find($this->dietReqestId);
        $this->computeChart() ;
        $this->isThisCombineFood();
    }

    public function computeChart() {
        if($this->dietReqeust->dietPlan) {
            $this->totaldays = $this->dietReqeust->dietPlan->day_count ;
        }else{
            $this->totaldays = 0 ;
        }
        $this->totallMeals =  DietRequestDetail::where('diet_request_id', $this->dietReqestId)->get()->count();
        $this->donedlMeals =  DietRequestDetail::where('diet_request_id', $this->dietReqestId)->where('is_done',1)->get()->count();
        if($this->totallMeals && $this->donedlMeals) {
            $this->leftToConsume =  $this->totallMeals - $this->donedlMeals ;
            $this->consumedMealPresent = intval( ($this->totallMeals - $this->donedlMeals) / $this->totallMeals *100) ;
            $this->leftMealToConsume = intval(ceil( ($this->totallMeals - ($this->totallMeals - $this->donedlMeals)) / $this->totallMeals *100)) ;
        }
    }

    public function isThisCombineFood()
    {
        if($this->dietReqeust->dietPlan && $this->dietReqeust->dietPlan->food_type  == FoodTypeEnum::COMBINED) {
            $this->foodTypeIsComined = true ;
        }
    }

    public function render()
    {
        return view('diet::livewire.admin.prescribed-diets.details.prescription-diet-detail');
    }
}
