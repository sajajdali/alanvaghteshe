<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Carbon\Carbon;
use Livewire\Component;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseUser;
use Modules\Transaction\Entities\Transaction;
use Modules\Transaction\Enum\TransactionPaidEnum;
use Modules\Transaction\Enum\TransactionPaymentForEnum;
use Modules\Transaction\Enum\TransactionStatusEnum;
use Modules\User\Entities\User;

class UserCourses extends Component
{
    public User $user;
    public ?int $selectedCourseId = null;

    protected function rules(): array
    {
        return [
            'selectedCourseId' => 'required|exists:courses,id',
        ];
    }

    protected $messages = [
        'selectedCourseId.required' => 'لطفا یک دوره را انتخاب کنید.',
        'selectedCourseId.exists' => 'دوره انتخاب‌شده معتبر نیست.',
    ];

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function openAssignCourseModal(): void
    {
        $this->selectedCourseId = null;
        $this->resetErrorBag();
        $this->dispatch('show-user-course-modal');
    }

    public function assignCourse(): void
    {
        $this->validate();

        $course = Course::query()->findOrFail($this->selectedCourseId);

        $existingActiveCourse = $this->user->courses()
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhereDate('end_at', '>=', now()->toDateString());
            })
            ->exists();

        if ($existingActiveCourse) {
            $this->addError('selectedCourseId', 'این دوره در حال حاضر برای کاربر فعال است.');
            return;
        }

        $discount = max(0, (int) $course->price - (int) $course->final_price);

        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'transaction_code' => Transaction::generateTransactionCode(),
            'payment_for' => TransactionPaymentForEnum::COURSE,
            'status' => TransactionStatusEnum::SUCCESSFUL,
            'paid_by' => TransactionPaidEnum::BY_ADMIN,
            'cost' => (int) $course->price,
            'total_cost' => (int) $course->final_price,
            'discount' => $discount,
            'detail' => [
                'course_id' => $course->id,
                'course_title' => $course->title,
                'source' => 'admin_assign',
                'assigned_by_admin_id' => auth()->id(),
            ],
        ]);

        $startAt = Carbon::now()->startOfDay();
        $endAt = (int) $course->access_days > 0
            ? $startAt->copy()->addDays((int) $course->access_days)
            : null;

        CourseUser::create([
            'user_id' => $this->user->id,
            'course_id' => $course->id,
            'transaction_id' => $transaction->id,
            'assigned_by_admin_id' => auth()->id(),
            'paid_by' => TransactionPaidEnum::BY_ADMIN,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'is_active' => true,
            'detail' => [
                'source' => 'admin_assign',
                'assigned_by_admin_id' => auth()->id(),
            ],
        ]);

        session()->flash('success', 'دوره با موفقیت برای کاربر ثبت شد.');
        $this->dispatch('hide-user-course-modal');
    }

    public function render()
    {
        $courseUsers = $this->user->courses()
            ->with(['course', 'transaction', 'progress', 'assignedByAdmin'])
            ->withCount('progress')
            ->get()
            ->load(['course' => fn ($query) => $query->withCount('lessons')]);

        $courses = Course::query()
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'final_price', 'access_days']);

        return view('user::livewire.admin.user.user-documents.user-courses', compact('courseUsers', 'courses'));
    }
}
