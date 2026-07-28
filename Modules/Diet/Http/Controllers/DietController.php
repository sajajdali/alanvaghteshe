<?php

namespace Modules\Diet\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Modules\Diet\Entities\BasicFood;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Entities\Meal;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Diet\Service\DietService;
use Modules\User\Entities\User;

class DietController extends Controller
{

    public function sample(){

        //
        $user = User::first();

        $basicFoodConsumption = $user->foodConsumptions()->create([
            'consumable_id' => BasicFood::first()->id,
            'consumable_type' => BasicFood::class,
            'meal_id' => Meal::first()->id,
            'food_unit_id' => FoodUnit::first()->id,
            'quantity' => 2.5,
        ]);
        $basicFoods = BasicFood::whereHas('units')->get();
        dd($basicFoods->count());
        foreach ($basicFoods as $basicFood){
//            dd($basicFood->conditions->where('id' , 5)->pivot);
            $basicFood->conditions()->detach(5);
            $basicFood->conditions()->attach(5 , ['options' => json_encode([1,2]) ]);
        }
        dd($basicFoods);
    }

    public function testDiet()
    {
        $resultMakeDiet = app('dietService')->makeDietFromFoods(DietRequest::latest()->first());
        dd($resultMakeDiet);
        $resultMakeDiet = app('dietService')->makeDietFromBasicFoods(DietRequest::latest()->first(), DietRequest::ACTION_REQUEST_RE_GENERATE);
        dd($resultMakeDiet);

    }
    public function test()
    {


        $makeDiet = DietRequest::latest()->first();
        dd(app(DietService::class)->handleConditions($makeDiet->detail['condition']));
        $makeDiet = app('dietService')->makeDietFromFoods($makeDiet);
        dd($makeDiet);
        $status = null;
        $user = User::find(76);
//        app('dietService')->computeCalorie($user);
//        $conditions = app('dietService')->getUserConditions($user);
//        unset($conditions[5]);
//        $dietPlans = app('dietService')->getDietPlans($conditions);

        $dietPlanModel = DietPlan::find(2);
        app('dietService')->generateDiet($user ,$dietPlanModel);
        return 'done';
//        $dietPlan = app('dietService')->insertUserDietPlan($user , $dietPlanModel );
//        if ($dietPlan['status']){
//            $makeDiet = app('dietService')->makeDietFromFoods($dietPlan['dietRequestModel']);
//        }
//        $requestDiet = $dietPlan['dietRequestModel'];
//        if (app('dietService')->countNullFoodId($makeDiet) == 0){
//            $requestDiet->update([
//                'active' => true,
//                'status' => DietRequestStatusEnum::ACTIVE,
//                'start_date' => Carbon::now()->addDay()->toDateTimeString(),
//                'end_date' => Carbon::now()->addDays($dietPlanModel->$dietPlanModel)->toDateTimeString(),
//            ]);
//        } else{
//            $requestDiet->update([
//                'active' => false,
//                'status' => DietRequestStatusEnum::REJECT_BY_SYSTEM_HAVE_ERROR,
//            ]);
//        }
        dd($makeDiet);
        $dietPlan = [
            'status' => true,
            'dietRequestModel' => DietRequest::find(11),
        ];
        if ($dietPlan['status']){
            DietRequestDetail::truncate();
            $status = app('dietService')->makeRejim($dietPlan['dietRequestModel'] , DietRequest::ACTION_REQUEST_INSERT);
        }
        return null;
//        $calorie = app('dietService')->computeCalorie($user);

    }

    public function changeList()
    {
        $dietRequestDetail  = DietRequestDetail::find(1);
        $replaceFood        = app('dietService')->replaceMealListFoods($dietRequestDetail);
        $replaced           = app('dietService')->replaceFood($dietRequestDetail, $replaceFood->first());
         dd($replaced);
    }


}
