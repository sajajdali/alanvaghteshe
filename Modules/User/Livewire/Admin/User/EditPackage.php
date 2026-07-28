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
use Modules\User\Livewire\Admin\User\UserDocuments\UserPackages;

class EditPackage extends Component
{
    public ?User $user = null;
    public packageUser $packageUser;
    public $userHasActivePackage, $selectedpackage;
    public $start_at, $end_at;

    public function messages()
    {
        return [
            'selectedpackage.required' => 'لطفا یک بسته را انتخاب کنید',
        ];
    }

    public function updatePackage()
    {
        $startDate = verta()->parse($this->start_at)->toCarbon();
        $endDate = verta()->parse($this->end_at)->toCarbon();

        $this->packageUser->update([
            'start_at' => $startDate,
            'end_at' => $endDate,
        ]);


        session()->flash('success', 'بسته با موفقیت برای کاربر ویرایش شد');
        return redirect()->route('admin.user.document', [$this->user]);
    }

    public function mount($user, $packageUser)
    {
        $this->user = $user;
        $this->packageUser = $packageUser;

        $this->start_at = verta($packageUser->start_at)->format("Y/m/d");
        $this->end_at = verta($packageUser->end_at)->format("Y/m/d");

    }

    public function render()
    {
        return view('user::livewire.admin.user.edit-package');
    }
}
