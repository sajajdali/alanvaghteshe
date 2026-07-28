<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Livewire\Component;
use Modules\Package\Entities\PackageUser;
use Modules\User\Entities\User;
use Livewire\WithPagination;

class UserPackages extends Component
{
    use WithPagination;

    public User $user;
    public $startDate, $endDate, $selectedPackageId;

    public function mount($user)
    {
        $this->user = $user;
    }

    // متد برای باز کردن modal و بارگذاری تاریخ‌های فعلی
    public function updatePackage($packageId)
    {
        $this->selectedPackageId = $packageId;
        $package = PackageUser::find($packageId);

        // بارگذاری تاریخ‌های فعلی
        $this->startDate = $package->start_at;
        $this->endDate = $package->end_at;

        // نمایش modal از طریق رویداد جاوا اسکریپت
        $this->dispatch('showModal');
    }

    // متد برای ذخیره تغییرات تاریخ
    public function saveDates()
    {
        $package = PackageUser::find($this->selectedPackageId);

        // بروزرسانی تاریخ‌ها
        $package->start_at = $this->startDate;
        $package->end_at = $this->endDate;
        $package->save();

        session()->flash('success', 'تاریخ‌ها با موفقیت بروزرسانی شدند.');

        // بستن modal از طریق رویداد جاوا اسکریپت
        $this->dispatch('hideModal');
    }

    public function render()
    {
        $packageUsers = $this->user->packages()->orderByDesc('id')->get();

        return view('user::livewire.admin.user.user-documents.user-packages', compact('packageUsers'));
    }
}
