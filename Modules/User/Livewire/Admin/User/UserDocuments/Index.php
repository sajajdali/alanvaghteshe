<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Dompdf\Dompdf;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Admin\app\Enums\ActivityEventEnum;
use Modules\Admin\app\Enums\UserConditionFiltersEnum;
use Modules\Admin\app\Models\ActivityLog;
use Modules\User\Entities\User;

#[Title("پرونده کاربر")]
// #[Layout('admin::layouts.app')]
class Index extends Component
{
    public $user;
    public $first_name, $last_name, $mobile, $password, $gender;
    public $modaldata;
    public $userBmi;
    public $title = null;
    public $message = null;
    public $messages = null;
    public $referralStatics = [];
    public $mmsg = false;
    public $meta_is_json_array = false;
    public array $form = [];
    public function editbasicInfo()
    {
        $this->validate([
            'first_name' => 'required|max:225|string',
            'last_name'  => 'required|max:225|string',
            'mobile'     => 'required|max:225|string',
            'gender'     => 'required|max:225',
            'password'   => 'nullable|max:225|string',
        ]);
        $this->user->first_name = $this->first_name;
        $this->user->last_name  = $this->last_name;
        $this->user->mobile     = $this->mobile;
        $this->user->gender     = $this->gender;
        if ($this->password) {
            $this->user->update(['password' =>  Hash::make($this->password)]);
        }
        $this->mmsg = 'اطلااعات کاربر با موفقیت ویرایش شد';
    }

    public function sendMessage()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ], [
            'title.required' => 'عنوان پیام الزامی است.',
            'title.max' => 'عنوان پیام نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد.',
            'message.required' => 'متن پیام الزامی است.',
        ]);

        $this->user->messages()->create([
            'title' => $this->title,
            'body' => $this->message,
        ]);

        session()->flash('success', 'پیغام با موفقیت ارسال شد.');

        // بازنشانی ورودی‌ها
        $this->reset(['title', 'message']);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->messages = $this->user->messages()->latest()->get();
    }
    public function luchModal($metaName)
    {
        $this->meta_is_json_array = false;
        $this->modaldata = $this->user->$metaName;
        $this->modaldata->name = $this->user->$metaName?->last()->meta_key->getName();
        if ($metaName == 'habits_meta' || $metaName ==  'food_restriction_meta' || $metaName ==  'weaknesses_body_meta') {
            $this->meta_is_json_array = true;
        }
        $this->dispatch('lunchHistoryModal');
    }

    public function calculateBmi()
    {
        $this->modaldata = null;
        $this->userBmi = app('dietService')->computeCalorie($this->user);
    }

    #[Url(as: 'filter', except: null)]
    public ?int $filter = null;
    public function storeDescriptionForCall()
    {
        // ✅ اگر خالی بود، پیام خطا بده
        $this->validate([
            'form.log.description' => 'required|string|min:2',
        ], [
            'form.log.description.required' => 'وارد کردن متن پیغام ضروری هست.',
            'form.log.description.min' => 'متن پیغام خیلی کوتاه است.',
        ]);

        $filter = $this->filter;
        $activityLogId = data_get($this->form, 'activityLog');

        if (blank($activityLogId)) {
            ActivityLog::create([
                'event'       => ActivityEventEnum::ADD_PROFILE_NOTE,
                'user_id'     => $this->user->id,
                'admin_id'    => auth()->id(),
                'description' => data_get($this->form, 'log.description'),
            ]);

            // ✅ پیام موفقیت (بدون redirect)
            $this->dispatch('notify', type: 'success', message: 'ثبت پیغام به صورتی دستی با موفقیت انجام شد.');
            return;
        }

        $logModel = ActivityLog::find($activityLogId);
        if ($logModel) {
            $logModel->update([
                'description' => data_get($this->form, 'log.description'),
            ]);
        }

        $msg = 'مکالمه با ' . $this->user->fullName . ' با موفقیت ذخیره شد';

        // اگر می‌خوای همینجا هم بدون redirect پیام بده:
        // $this->dispatch('notify', type: 'success', message: $msg);
        // return;

        return redirect()
            ->route('admin.user.reports', ['filter' => $filter])
            ->with('success', $msg);
    }
    public function mount()
    {

        $user =  User::find(request()->route('user'))->load('logFor', 'messages');
        $this->messages   = $user->messages()->latest()->get();
        $this->user       = $user;
        $this->first_name =  $this->user->first_name;
        $this->last_name  =  $this->user->last_name;
        $this->mobile     =  $this->user->mobile;
        $this->gender     =  $this->user->gender;

        // referral
        $inviteFriends = $user->inviteFriends;
        $this->referralStatics = [
            'number_invited_friends' => $inviteFriends->count(),
            'total_benefit' => number_format($inviteFriends->sum('benefit')),
            'successful_referrals' => number_format($inviteFriends->where('benefit', '>', '0')->count()),
        ];
        $this->form['calling'] = true;
        if ($this->form['calling'] ) {
            $this->form['activityLog'] = request()->get('log');
        }
    }
    public function render()
    {
        return view('user::livewire.admin.user.user-documents.index');
    }
}
