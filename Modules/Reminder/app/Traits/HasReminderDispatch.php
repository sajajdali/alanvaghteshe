<?php

namespace Modules\Reminder\app\Traits;

use Modules\Reminder\app\Services\ReminderDispatcher;
use Modules\Reminder\Enum\ReminderTypeEnum;

trait HasReminderDispatch
{
    // Registration
    public function dispatchRegistrationReminders(): void
    {
        ReminderDispatcher::dispatchForType($this, ReminderTypeEnum::REGISTRATION);
    }

    public function cancelRegistrationReminders(): void
    {
        ReminderDispatcher::cancelByType($this, ReminderTypeEnum::REGISTRATION);
    }

    // Payment
    public function dispatchPaymentReminders(?int $durationDays = null): void
    {
        // Cancel existing registration and payment reminders before dispatching new ones
        $this->cancelRegistrationReminders();
        $this->cancelPaymentReminders();

        ReminderDispatcher::dispatchForType($this, ReminderTypeEnum::PAYMENT, null, $durationDays);
    }


    public function cancelPaymentReminders(): void
    {
        ReminderDispatcher::cancelByType($this, ReminderTypeEnum::PAYMENT);
    }

    // Diet
    public function dispatchDietReminders(?int $sessionNumber, ?int $durationDays = null): void
    {
        $this->cancelRegistrationReminders();
        $this->cancelDietReminders();

        ReminderDispatcher::dispatchForType($this, ReminderTypeEnum::DIET, $sessionNumber, $durationDays);
    }

    public function cancelDietReminders(): void
    {
        ReminderDispatcher::cancelByType($this, ReminderTypeEnum::DIET);
    }

    // Exercise
    public function dispatchExerciseReminders(int $sessionNumber, ?int $durationDays = null): void
    {
        ReminderDispatcher::dispatchForType($this, ReminderTypeEnum::EXERCISE, $sessionNumber, $durationDays);
    }

    public function cancelExerciseReminders(): void
    {
        ReminderDispatcher::cancelByType($this, ReminderTypeEnum::EXERCISE);
    }
}
