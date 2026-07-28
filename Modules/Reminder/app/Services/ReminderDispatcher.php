<?php

namespace Modules\Reminder\app\Services;

use Carbon\Carbon;
use Modules\Reminder\app\Models\Reminder;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\Reminder\Enum\ReminderParametersEnum;
use Modules\User\Entities\User;

class ReminderDispatcher
{
    /**
     * Dispatch reminders based on type and optional session number.
     */
    public static function dispatchForType(
        User $user,
        ReminderTypeEnum $type,
        ?int $sessionNumber = null,
        ?int $durationDays = null
    ): void {
        $reminders = Reminder::query()
            ->where('send_for', $type->value)
            ->when(!is_null($sessionNumber), function ($query) use ($sessionNumber) {
                $query->where(function ($q) use ($sessionNumber) {
                    $q->whereNull('session_number')
                        ->orWhere('session_number', $sessionNumber);
                });
            }, function ($query) {
                $query->whereNull('session_number');
            })
            ->where('active', 1)
            ->get();

        foreach ($reminders as $reminder) {
            $sendAt = now();

            if ($reminder->send_day < 0 && $durationDays !== null) {
                $sendAt = now()->addDays($durationDays)->addDays(abs($reminder->send_day));
            } else {
                $sendAt = now()->addDays((int) $reminder->send_day);
            }

            ReminderQueue::create([
                'user_id'        => $user->id,
                'reminder_id'    => $reminder->id,
                'type'           => $type,
                'session_number' => $sessionNumber,
                'send_at'        => $sendAt,
                'status'         => ReminderStatusEnum::PENDING,
                'detail'         => self::parseParameters($reminder->parameters , $user),
            ]);
        }
    }

    /**
     * Cancel all pending reminders of a specific type for a user.
     */
    public static function cancelByType(User $user, ReminderTypeEnum $type): void
    {
        ReminderQueue::where('user_id', $user->id)
            ->where('type', $type)
            ->where('status', ReminderStatusEnum::PENDING)
            ->delete();
    }

    /**
     * Parse parameters from the 'reminders' table into a detail array.
     */
    protected static function parseParameters(mixed $parameters, User $user): array
    {
        $result = ['params' => []];

        $raw = is_array($parameters) ? $parameters : json_decode($parameters ?? '[]', true);

        if (is_array($raw)) {
            foreach ($raw as $param) {
                $enum = \Modules\Reminder\Enum\ReminderParametersEnum::tryFrom($param);
                if (!$enum) {
                    continue;
                }

                switch ($enum) {
                    case ReminderParametersEnum::FIRST_NAME:
                        $value = $user->first_name;
                        break;
                    case ReminderParametersEnum::LAST_NAME:
                        $value = $user->last_name;
                        break;

                    case ReminderParametersEnum::FULL_NAME:
                        $value = $user->full_name;
                        break;

                    case ReminderParametersEnum::ID:
                        $value = $user->id;
                        break;

                    default:
                        $value = null;
                }

                if (!is_null($value)) {
                    $result['params'][] = $value;
                }
            }
        }

        return $result;
    }
}
