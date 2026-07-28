<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Modules\Diet\Entities\DietRequest;
use Modules\User\Entities\User;

class PrescribedDiet extends Component
{
    public User $user;
    public ?Collection $userDiets ;

    public function compelete()
    {
        session()->flash('error','لطفا ابتدا اطلاعات کاربر را تکمیل کنید!!!!
        <br />
        وارد کردن جنسیت ، قد ،‌وزن ، هدف کلی ، سن برای دریافت و تجویز رژیم هر کاربر اجباری است .
        <br>
        قبل از هر چیز چک کنید که این مشخصات برای کاربر وارد شده باشد
        ');
        return redirect()->route('admin.user.document',[$this->user]);
    }
    public function removeDiet(DietRequest $id)
    {
        $id->delete();
        session()->flash('success','رژیم مورد نظر با موفقیت حذف شد');
        return redirect()->route('admin.user.document',[$this->user]);
    }
    public function mount($user)
    {
        $this->user = $user ;
        $this->userDiets=$this->user->dietRequests()->orderByDesc('id')->get() ;
    }

    public function render()
    {
        return view('user::livewire.admin.user.user-documents.prescribed-diet');
    }
}
