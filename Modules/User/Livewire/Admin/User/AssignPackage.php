<?php

namespace Modules\User\Livewire\Admin\User;

use Carbon\Carbon;
use Livewire\Component;
use Modules\User\Entities\User;
use Modules\Package\Entities\Package;
use Modules\Package\Entities\PackageUser;
use Modules\Reminder\app\Models\Reminder;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Package\Enum\PackageUserTypeEnum;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\Reminder\Enum\ReminderParametersEnum;

class AssignPackage extends Component
{
    public User $user;
    public $userHasActivePackage, $selectedpackage;
    public $startDate_package, $EndDate_package;
    public function messages()
    {
        return [
            'selectedpackage.required' => 'لطفا یک بسته را انتخاب کنید',
        ];
    }

    public function assignpackage()
    {
        $this->validate([
            'selectedpackage' => 'required'
        ]);
        $startDate = Carbon::parse($this->startDate_package);
        $endDate   = Carbon::parse($this->EndDate_package);
        $package = Package::find($this->selectedpackage);
        $userPackages = $this->user->packages()->where('type', PackageUserTypeEnum::IN_USE)->get();
        if ($userPackages->isNotEmpty() && $userPackages->count() > 1) {
            foreach ($userPackages as $key => $userPaclage) {
                $userPaclage->update([
                    'type' => PackageUserTypeEnum::CANCEL,
                ]);
            }
        } elseif ($userPackages->isNotEmpty()) {
            $userPackages->first()->update([
                'type' => PackageUserTypeEnum::CANCEL,
            ]);
        }
        $userPackage =  PackageUser::create([
            'user_id' => $this->user->id,
            'package_id' => $package->id,
            'start_at'   => $startDate,
            'end_at'     => $endDate,
            'type'       => PackageUserTypeEnum::IN_USE,
        ]);

        // delete old reminders

        $this->user->dispatchPaymentReminders($package->days);

        session()->flash('success','بسته با موفقیت برای کاربر اضافه شد');
        return redirect()->route('admin.user.document',[$this->user]);
    }

    public function mount($user)
    {
        $this->user = $user;
        if (!empty($this->user->activePackage())) {
            $this->userHasActivePackage = $this->user->activePackage();
        }
    }
    public function render()
    {
        return view('user::livewire.admin.user.assign-package');
    }
}
