<?php

namespace Modules\Reminder\app\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Reminder\Enum\ReminderSendTypeEnum;
use Modules\Reminder\Enum\ReminderStatusEnum;
use Modules\Reminder\app\Models\ReminderQueue;
use Modules\Reminder\Enum\ReminderParametersEnum;
use Modules\Reminder\Enum\ReminderTypeEnum;
use Modules\User\app\Notifications\ReminderNotification;
use Modules\User\app\Notifications\SmsNotification;
use Modules\User\app\Notifications\UserMessageNotification;

class SendReminderscommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:sendreminders';

    /**
     * The console command description.
     */
    protected $description = 'send reminders.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ReminderQueue::where('send_at', '<', \now()->subHours(7))
            ->delete();
        $reminders =  ReminderQueue::where('send_at', '<', now())
            ->get();
        foreach ($reminders as $reminder) {
            if ($reminder->reminder->status == ReminderSendTypeEnum::SMS) {
                $template = $reminder->reminder->body;
                $params =  $reminder->detail['params'];
                $reminder->user->notify(new SmsNotification($template, $params));

                $reminder->delete();
            } elseif ($reminder->reminder->status == ReminderSendTypeEnum::NOTIFICATION){
                $params =  $reminder->detail['params'];
                $body = replaceParams( $reminder->reminder->body , $params);
                $reminder->user->notify(new UserMessageNotification(
                    title: "پیام مهمی از الان وقتشه داریم",
                    excerpt: $body,
                    message: $body,
                ));
                $reminder->delete();
            }
        }
        $this->info('SendRemindersCommand finished');
        Log::info('send reminders' . Carbon::now()->toDateTimeString());
    }


}
