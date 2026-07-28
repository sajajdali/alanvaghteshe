<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Livewire\Component;
use Modules\User\Entities\User;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Api\Http\Controllers\PaymentController;

class UserExercises extends Component
{
    public User $user;
    public Collection $userExercise;

    public function removeExercise(ExercisePlanRequest $exercisePlanRequest)
    {
        if ($exercisePlanRequest->user == $this->user) {
            $exercisePlanRequest->delete();
            session()->flash('success', 'ورزش کاربر با موفقیت حذف شد');
            return redirect()->route('admin.user.document', $this->user);
        } else {
            session()->flash('error', 'دسترسی غیر مجاز - ارور ۵۵۶۶۷۷۸۸ - با پشتیبانی تماس بگیرید');
            return redirect()->route('admin.user.document', $this->user);
        }
    }
    public function mount($user)
    {
        $this->user = $user;
        $this->userExercise = $this->user->exercisePlanRequests()->orderByDesc('id')->get();
    }

    public function assignExercise()
    {


        $paymentController = new PaymentController();
        $userExercise = $paymentController->assginExersicePlan($this->user);
        if ($userExercise) {
            session()->flash('success', 'ورزش با موفقیت برای کاربر اختصاص داده شد');
            return redirect()->route('admin.user.document', $this->user);
        }
        if (!$userExercise) {
            session()->flash('error', 'هیچ برنامه ای با توجه به دیتای کاربر یافت نشد');
            return redirect()->route('admin.user.document', $this->user);
        }
    }

    public function render()
    {
        return view('user::livewire.admin.user.user-documents.user-exercises');
    }
}
