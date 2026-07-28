<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\Diet\app\Jobs\CreateJsonDietJob;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Enum\DietRequestStatusEnum;
use Modules\Setting\Enum\SettingKeyEnum;
use Modules\User\app\Notifications\SmsNotification;
use Modules\User\Enum\UserMetaEnum;

class Index extends Component
{
    use AuthorizesRequests;

    public $searchPanel;

    #[Locked]
    public $dietReqestId;
    public $dietErrors;
    public $dietRequest ;

    //assign diet plan property
    public $dietPlans ;
    public string $selectedDietPlan = ''  ;
    protected $rules = [
        'selectedDietPlan' => 'required',
    ];
    protected $messages = [
        'selectedDietPlan.required' => 'لطفا یک پلن را برای کاربر انتخاب کنید',
    ];

    public function reNewRejim()
    {
        // $dietRequest = app('dietService')->makeRejim(DietRequest::find($this->dietReqestId));
    }

    public function  assignDietPlan() {
        $this->validate();
        $this->dietRequest->update([
            'diet_plan_id' => $this->selectedDietPlan
        ]);
        app('dietService')->insertUserDietPlan($this->dietRequest->user , DietPlan::find($this->selectedDietPlan) , $this->dietRequest);
        app('dietService')->makeRejim($this->dietRequest , DietRequest::ACTION_REQUEST_RE_GENERATE);
        session()->flash('success', 'پلن جدید انتخاب و رژیم برای کاربر تجویز شد');
        $this->redirect(route('admin.presCribed-diets.detail', $this->dietRequest));
    }

    public function sendSmsGenerateDiet()
    {
        if (isset($this->dietRequest->user) && isset($this->dietRequest->user->mobile) && $this->dietRequest->user->mobile != ''){
            $template = setting(SettingKeyEnum::SMS_AFTER_SEND_DIET);
            if (!$template ){
                session()->flash('error', 'قالب پیامک در بخش تنظیمات تعریف نشده است');
            } else {
                $sendParameter = [
                    $this->dietRequest->user->first_name,
                ];
                $this->dietRequest->user->notify(new SmsNotification($template, $sendParameter));
                session()->flash('success', 'پیامک با موفقیت برای کاربر ارسال شد');
            }
        } else {
            session()->flash('error', 'شماره موبایل کاربر تعریف نشده است');
        }
        $this->redirect(route('admin.presCribed-diets.detail', $this->dietRequest));


    }
    public function reGenerateAndDeleteOldData()
    {
        $this->dietRequest->dietRequestDetails()->delete();
        $makeDiet = app('dietService')->makeDietFromFoods($this->dietRequest);
        if (app('dietService')->countNullFoodId($makeDiet) == 0) {
            // remove alert when have free package
            $this->dietRequest->user->metas()->where('meta_key', UserMetaEnum::POPUP)->delete();
            $this->dietRequest->update([
                'active' => true,
                'status' => DietRequestStatusEnum::ACTIVE,
            ]);
            dispatch_sync(new CreateJsonDietJob( $this->dietRequest));

        } else {
            $this->dietRequest->update([
                'active' => false,
                'status' => DietRequestStatusEnum::REJECT_BY_SYSTEM_HAVE_ERROR,
            ]);

        }
        session()->flash('success', 'رژیم قبلی حذف و مجدد تجویز شد');
        $this->redirect(route('admin.presCribed-diets.detail', $this->dietRequest));
    }

    public function sendSms()
    {
        $template = setting(SettingKeyEnum::SMS_AFTER_SEND_DIET);
        if ($template){
            $sendParameter = [
                $this->dietRequest->id
            ];
            $this->dietRequest->user->notify(new SmsNotification($template, $sendParameter));
        } else {
            session()->flash('error', 'قالب پیامک تعریف نشده است . لطفا به بخش تنظیمات مراجعه کنید');
            return redirect()->back();
        }
        session()->flash('success', 'پیامک با موفقیت برای کاربر ارسال شد');
        return redirect()->back();
    }

    public function completeDiet()
    {
        app('dietService')->makeRejim($this->dietRequest , DietRequest::ACTION_REQUEST_UPDATE_LIST);
        session()->flash('success', 'رژیم باز نگری شد');
        $this->redirect(route('admin.presCribed-diets.detail', $this->dietRequest));
    }

    public function mount(DietRequest $diet_request)
    {
        $this->dietReqestId = $diet_request->id;
        $this->dietRequest = $diet_request ;
        if(array_key_exists('report_diet',$diet_request->detail)) {
            $this->dietErrors =  app('dietService')->resultFilterAndDisplayErrors($diet_request->detail['report_diet']);
        }else {
            $this->dietErrors = null ;
        }
        $this->dietPlans = DietPlan::where('status',1)->get();
        // $this->details = $diet_request->dietRequestDetails()->groupBy('diet_request_details.main_nutrition')->get() ;
        // $this->details = DietRequestDetail::where('diet_request_id', $diet_request->id)->get()->groupBy('main_nutrition');
    }
    public function render()
    {

        return view('diet::livewire.admin.prescribed-diets.details.index');
    }
}
