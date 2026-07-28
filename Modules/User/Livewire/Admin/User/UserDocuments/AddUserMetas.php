<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\User\Entities\User;

class AddUserMetas extends Component
{
    public $user, $msgAddMeta;
    public array $user_disease = [];
    public ?Collection $srerver_disease;
    public $birthday,
        $tall,
        $weight,
        $target_weight,
        $body_fat;
    public
        $diet_plan = null,
        $target_plan = null,
        $daily_water_consumption = null,
        $body_physical_style = null,
        $type_daily_work = null,
        $food_restriction = null,
        $weaknesses_body = null,
        $habits = null,
        $wake_up = null,
        $how_much_experience_sports = null,
        $target_of_exercise = null,
        $activity_per_week = null,
        $how_many_days_week_exercise = null,
        $weight_change_per_week = null;

    public function saveUserMetas()
    {
        if ($this->birthday != null && $this->birthday !== false) {

            $birdayTime = Verta($this->birthday);
            $dateObject = [
                "day" => $birdayTime->day,
                "month" => $birdayTime->month,
                "year" => $birdayTime->year,
            ];
            $this->user->birthday = json_encode($dateObject);
        }
        if ($this->diet_plan != null) {
            $this->user->diet_plan = $this->diet_plan;
        }
        if ($this->target_plan != null) {
            $this->user->target_plan = $this->target_plan;
        }

        if ($this->tall != null) {
            $this->user->tall = $this->tall;
        }
        if ($this->weight != null) {
            $this->user->weight = $this->weight;
        }
        if ($this->target_weight != null) {
            $this->user->target_weight = $this->target_weight;
        }
        if ( $this->body_fat  != null) {
            $this->user->body_fat = $this->body_fat;
        }
        if ( $this->daily_water_consumption != null ) {
            $this->user->daily_water_consumption = $this->daily_water_consumption;
        }
        if ( $this->type_daily_work    != null ) {
            $this->user->type_daily_work = $this->type_daily_work;
        }
        if (isset($this->food_restriction)  && $this->food_restriction !== null) {
            $this->user->food_restriction = json_encode($this->food_restriction);
        }
        if (isset($this->weaknesses_body) && $this->weaknesses_body !== null) {
            $this->user->weaknesses_body = json_encode($this->weaknesses_body);
        }
        if (isset($this->habits) && $this->habits  !== null) {
            $this->user->habits = json_encode($this->habits);
        }
        if ( $this->wake_up !== null) {
            $this->user->wake_up = $this->wake_up;
        }
        if ( $this->how_much_experience_sports !== null) {
            $this->user->how_much_experience_sports = $this->how_much_experience_sports;
        }
        if ( $this->target_of_exercise !== null) {
            $this->user->target_of_exercise = $this->target_of_exercise;
        }
        if ( $this->activity_per_week !== null) {
            $this->user->activity_per_week = $this->activity_per_week;
        }
        if ( $this->how_many_days_week_exercise !== null ) {
            $this->user->how_many_days_week_exercise = $this->how_many_days_week_exercise;
        }
        if ( $this->body_physical_style !== null ) {
            $this->user->body_physical_style = $this->body_physical_style;
        }
        if ($this->weight_change_per_week != null) {
            $this->user->weight_change_per_week = $this->weight_change_per_week;
        }
        if (count($this->user_disease) > 0) {
            $filteredDiseases = array_filter($this->user_disease, function($value) {
                return !empty($value);
            });

            $this->user->diseases()->sync($filteredDiseases);
        } else {
            $this->user->diseases()->sync([]);
        }
        session()->flash('success', 'اطلاعات با موفقیت اضافه شد');
        return redirect()->route('admin.user.document', $this->user);
    }
    public function mount(User $user)
    {
        $this->user = $user;
        foreach ($this->user->diseases as $key => $disease) {
            $this->user_disease[$key] =  $disease->id;
        };
        $this->srerver_disease = Disease::all();
    }
    public function render()
    {
        return view('user::livewire.admin.user.user-documents.add-user-metas');
    }
}
