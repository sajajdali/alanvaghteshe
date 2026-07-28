<?php
// app/Actions/Whatsapp/SendUserWhatsappMessage.php
namespace App\Actions;

use App\Services\PhoneNormalizer;
use App\Services\WhatsappService;
use Modules\User\Entities\User;
use Illuminate\Support\Facades\Log;

class SendUserWhatsappMessage
{
    public function __construct(
        private WhatsappService $wa
    ) {}

    /**
     * تلاش برای ارسال پیام و ذخیره در DB (همان متد سرویس شما: sendAndStoreText)
     * @return array{ok:bool,status?:int,message?:string}
     */
    public function __invoke(User $user, string $text): array
    {
        if (blank($user->mobile)) {
            return ['ok' => false, 'message' => 'شماره واتساپ کاربر ثبت نشده است.'];
        }

        // چک سشن (می‌تونی این را بیرون کشیده و cache کنی)
        $session = config('whatsapp.default_session', 'default');
        $sess = $this->wa->getSession($session);
        if (($sess['ok'] ?? false) !== true) {
            return ['ok' => false, 'message' => 'هیچ دستگاهی تعریف نشده است.'];
        }
        $prof = $this->wa->getProfile($session);
        if (($prof['ok'] ?? false) !== true) {
            return ['ok' => false, 'message' => 'دستگاه شما لاگین نیست.'];
        }

        [, , $chatId] = PhoneNormalizer::toIran98($user->mobile);

        try {
            $resp = $this->wa->sendAndStoreText(
                session: $session,
                chatId:  $chatId,
                text:    $text,
                user:    $user
            );

            return $resp['ok']
                ? ['ok' => true]
                : ['ok' => false, 'status' => $resp['status'] ?? 500, 'message' => 'ارسال ناموفق بود.'];

        } catch (\Throwable $e) {
            Log::error('Whatsapp send failed', ['user_id' => $user->id, 'e' => $e]);
            return ['ok' => false, 'message' => 'ارتباط با سرویس برقرار نشد.'];
        }
    }
}
