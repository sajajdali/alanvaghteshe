<?php

namespace Modules\User\Livewire\Admin\User;

use Livewire\Component;
use Modules\User\Entities\User;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\User\app\Notifications\SmsNotification;

class AssignDiet extends Component
{

    public User $user;
    public $suggestedDiet;
    public $selectedDiet;
    public $sendSmsAfterSendDiet = false;

    public function messages()
    {
        return [
            'selectedDiet.required' => 'لطفا رژیم مورد نظر را انتخاب کنید',
        ];
    }

    public function assignDiet()
    {

        $this->validate([
            'selectedDiet' => 'required'
        ]);
        $diet_plan =  DietPlan::find($this->selectedDiet);
        //create new function for assigning selective diet
//        $dietPlan = app('dietService')->insertUserDietPlan($this->user, $diet_plan);

//        app('dietService')->makeRejim($dietPlan['dietRequestModel'], DietRequest::ACTION_REQUEST_INSERT);
        app('dietService')->generateDiet($this->user ,$diet_plan , $this->user->dietPlan);


        // send sms after send diet
        if ($this->sendSmsAfterSendDiet) {
            $template = setting(SettingKeyEnum::SMS_AFTER_SEND_DIET);
            if ($template) {
                $sendParameter = [
                    $this->selectedDiet,
                ];
                $this->user->notify(new SmsNotification($template, $sendParameter));
            }
        }

        session()->flash('success', 'رژریم با موفقیت برای کابر تجویز شد');
        return redirect()->route('admin.user.document', [$this->user]);
    }
    public function mount($user)
    {
        $this->user = $user;
        $this->findSuggestedDiet();
    }

    public function findSuggestedDiet()
    {
        $conditions = app('dietService')->getUserConditions($this->user, true);
        $dietPlans  = app('dietService')->getDietPlans($conditions);
        $this->suggestedDiet = $dietPlans->first()?->id;
        $this->selectedDiet = $this->suggestedDiet;
    }

    public function render()
    {
        return view('user::livewire.admin.user.assign-diet');
    }
}
