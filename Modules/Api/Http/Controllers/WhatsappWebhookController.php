<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Admin\app\Models\WhatsappMedia;
use Modules\Admin\app\Models\WhatsappMessage;

class WhatsappWebhookController extends Controller
{

    public function handle(Request $request)
    {
        // --- 1) ورودی را محکم دیکد کنیم (array یا string) ---
        $rawAll = $request->all();
        $rawPayload = $request->input('payload', null);
        $envelope = $this->decodeAny($rawPayload ?? $rawAll); // ممکن است خود «قاب» یا کل «پیام» باشد

        // --- 2) تشخیص لایه‌ها: envelope (متادیتا) / msg (پیام) / data (عمق) ---
        if (is_array($envelope) && isset($envelope['payload']) && is_array($envelope['payload']) && isset($envelope['payload']['from'])) {
            $msg   = $envelope['payload'];
            $data  = isset($msg['_data']) ? $this->decodeAny($msg['_data']) : [];
            $meta  = $envelope; // session/me/id اینجاست
        }
        elseif (is_array($envelope) && isset($envelope['from'])) {
            $msg   = $envelope;
            $data  = isset($msg['_data']) ? $this->decodeAny($msg['_data']) : [];
            $meta  = [
                'id'        => $envelope['id']        ?? ($rawAll['id']        ?? null),
                'session'   => $envelope['session']   ?? ($rawAll['session']   ?? 'default'),
                'me'        => $envelope['me']        ?? ($rawAll['me']        ?? []),
                'timestamp' => $envelope['timestamp'] ?? ($rawAll['timestamp'] ?? null),
            ];
        }
        elseif (is_array($envelope) && isset($envelope['payload']) && is_string($envelope['payload'])) {
            $msg  = $this->decodeAny($envelope['payload']);
            $data = isset($msg['_data']) ? $this->decodeAny($msg['_data']) : [];
            $meta = $envelope;
        }
        else {
            $maybe = $this->decodeAny($rawAll);
            $msg   = (is_array($maybe) && isset($maybe['payload']) && is_array($maybe['payload'])) ? $maybe['payload'] : (is_array($maybe) ? $maybe : []);
            $data  = isset($msg['_data']) ? $this->decodeAny($msg['_data']) : [];
            $meta  = is_array($maybe) ? $maybe : [];
        }

        // --- 3) فیلدها با fallback از چند لایه ---
        $session   = (string)($meta['session'] ?? 'default');
        $eventId   = $meta['id'] ?? null; // evt_...
        $fromMe    = (bool)($msg['fromMe'] ?? ($data['id']['fromMe'] ?? false));

        $from = $msg['from'] ?? ($data['from'] ?? null);
        $to   = $msg['to']   ?? ($data['to']   ?? null);
        if (!$from && !$fromMe) $from = $data['id']['remote'] ?? null;
        if (!$to &&  $fromMe)   $to   = ($meta['me']['id'] ?? null) ?: ($data['to'] ?? null);

        // بر اساس اسکیمای جدید
        $chatId   = (string)($fromMe ? ($to ?? '') : ($from ?? ''));
        $senderId = (string)($fromMe ? (($meta['me']['id'] ?? '') ?: ($from ?? '')) : ($from ?? ''));

        // کلید یکتا واقعی (اگر نبود، null؛ از fallback برای ذخیره استفاده نکن)
        $waKeyRaw = $data['id']['_serialized'] ?? ($msg['id'] ?? null);
        $waKey    = is_string($waKeyRaw) && trim($waKeyRaw) !== '' ? trim($waKeyRaw) : null;
        // (اختیاری) فقط برای لاگ/دیباگ می‌توانی fallbackKey بسازی، اما در DB استفاده نکن:
        // $fallbackKey = $this->buildFallbackKey($session, $from, $to, $data['id']['id'] ?? null, $eventId);

        // محتوا
        $hasMedia = (bool)($msg['hasMedia'] ?? false);
        $mime     = $msg['media']['mimetype'] ?? ($data['mimetype'] ?? null);
        $filename = $msg['media']['filename'] ?? ($data['filename'] ?? null);
        $body     = array_key_exists('body', $msg) ? $msg['body'] : ($data['caption'] ?? null);

        $type     = $this->detectType($hasMedia, $mime, $data['type'] ?? null, $body);

        // --- 4) ذخیره پیام ---
        if ($waKey) {
            // وقتی کلید واقعی داریم، از updateOrCreate استفاده می‌کنیم تا duplicate نشه
            $message = WhatsappMessage::updateOrCreate(
                ['wa_message_key' => $waKey],
                [
                    'user_id'      => null,
                    'session_name' => $session,
                    'chat_id'      => $chatId,
                    'sender_id'    => $senderId,
                    'from_me'      => $fromMe,

                    'type'         => $type,
                    'body'         => $body,
                    'read_at'      => null,

                    'has_media'    => $hasMedia,
                ]
            );
        } else {
            // وقتی کلید نداریم (سناریوی رایج برای «کاربر جدید»): همیشه رکورد جدید بساز
            $message = WhatsappMessage::create([
                'user_id'        => null,
                'session_name'   => $session,
                'chat_id'        => $chatId,
                'sender_id'      => $senderId,
                'from_me'        => $fromMe,

                'wa_message_key' => null, // کلید نداریم؛ unique را نقض نمی‌کند
                'type'           => $type,
                'body'           => $body,
                'read_at'        => null,

                'has_media'      => $hasMedia,
            ]);
        }

        // --- 5) رسانه (در صورت وجود) ---
        if ($hasMedia) {
            $this->handleMediaDownloadAndPersist($message->id, $msg, $data, $filename, $mime);
        }

        return response()->json(['status' => 'ok']);
    }
    /* —— کمک‌متد جدید برای دیکد امن —— */
    private function decodeAny($value): array
    {
        if (is_array($value)) return $value;

        if ($value instanceof \Illuminate\Http\Request) {
            $arr = $value->all();
            return is_array($arr) ? $arr : [];
        }

        if (is_string($value)) {
            $trim = trim($value);
            if ($trim === '') return [];
            $decoded = json_decode($trim, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            // اگر payload به‌صورت فرم‌داده آمده بود مثل: key1=...&key2=...
            parse_str($value, $parsed);
            return is_array($parsed) ? $parsed : [];
        }

        if (is_object($value)) {
            $arr = json_decode(json_encode($value), true);
            return is_array($arr) ? $arr : [];
        }

        return [];
    }
    private function buildFallbackKey(?string $session, ?string $from, ?string $to, ?string $shortId, ?string $eventId): string
    {
        // کلید یکتا در بدترین حالت، تا از duplicate جلوگیری شود
        return implode('|', array_filter([
            'sess:' . ($session ?? 'default'),
            'from:' . ($from ?? ''),
            'to:' . ($to ?? ''),
            'id:' . ($shortId ?? ''),
            'evt:' . ($eventId ?? ''),
        ]));
    }

    private function detectType(bool $hasMedia, ?string $mime, ?string $dataType, ?string $body): string
    {
        if ($hasMedia) {
            if ($mime && str_starts_with($mime, 'image/'))  return 'image';
            if ($mime && str_starts_with($mime, 'video/'))  return 'video';
            if ($mime && str_starts_with($mime, 'audio/'))  return 'audio';
            if ($dataType === 'document')                    return 'document';
            return 'unknown';
        }
        if (!empty($body)) return 'text';
        return in_array($dataType, ['sticker','image','video','audio','document','chat'], true)
            ? ($dataType === 'chat' ? 'text' : $dataType)
            : 'unknown';
    }


    private function handleMediaDownloadAndPersist(int $messageId, array $msg, array $data, ?string $filename, ?string $mime): int
    {
        // URL اصلی: media.url یا deprecatedMms3Url
        $url = Arr::get($msg, 'media.url') ?: Arr::get($data, 'deprecatedMms3Url');
        if (empty($url)) return 0;

        $token   = env('WHATSAPP_API_KEY');
        $timeout = (int) env('ZAPPO_TIMEOUT', 30);
        $disk    = env('FILES_DISK', 'public');

        // دانلود با هدر و توکن
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'X-Api-Key'     => $token,
            'Authorization' => 'Bearer ' . $token,
        ])->timeout($timeout)->get($url);

        if (!$response->successful()) {
            // logger()->warning('Media download failed', ['code' => $response->status(), 'url' => $url]);
            return 0;
        }

        $binary = $response->body();

        // اگر mime خالی بود از هدر پاسخ حدس بزن
        $mime = $mime ?: $response->header('Content-Type');

        // پسوند فایل را از filename یا mime یا URL حدس بزن
        $ext      = $this->guessExtension($filename, $mime, $url);
        $cleanExt = $ext ? ('.' . ltrim($ext, '.')) : '';

        // اگر filename خالی بود، یک نام امن و معنادار بساز
        $prefix = ($mime && str_starts_with($mime, 'image/')) ? 'image'
            : (($mime && str_starts_with($mime, 'video/')) ? 'video'
                : (($mime && str_starts_with($mime, 'audio/')) ? 'audio' : 'doc'));

        $baseName           = pathinfo((string) $filename, PATHINFO_FILENAME);
        $syntheticOriginal  = $prefix . '-' . now()->format('Ymd-His') . '-' . Str::lower(Str::random(8)) . $cleanExt;
        $originalForRecord  = $baseName ? ($baseName . $cleanExt) : $syntheticOriginal;

        // نام فایل ذخیره روی دیسک (اسلاگ شده)
        $finalName = $this->slugFilename($baseName ?: $prefix) . $cleanExt;

        // مسیر ذخیره‌سازی
        $path = $this->buildStoragePath($finalName, $mime);
        Storage::disk($disk)->put($path, $binary);

        // نوع رسانه برای رکورد
        $kind = $this->kindFromMimeOrType($mime, Arr::get($data, 'type'));

        // ساخت رکورد رسانه
        WhatsappMedia::create([
            'message_id'        => $messageId,
            'kind'              => $kind,
            'disk'              => $disk,
            'path'              => $path,
            'original_filename' => $originalForRecord,  // ← هرگز خالی نمی‌ماند
            'mime'              => $mime,
            'size'              => strlen($binary),
            'public_url'        => $this->publicUrlSafe($disk, $path),
            'width'             => null,
            'height'            => null,
            'duration_ms'       => null,
        ]);

        return 1;
    }
    private function guessExtension(?string $filename, ?string $mime, string $url): ?string
    {
        $ext = $filename ? pathinfo($filename, PATHINFO_EXTENSION) : null;
        if ($ext) return strtolower($ext);

        if ($mime) {
            $map = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/gif'  => 'gif',
                'video/mp4'  => 'mp4',
                'video/quicktime' => 'mov',
                'audio/mpeg' => 'mp3',
                'audio/ogg'  => 'ogg',
                'application/pdf' => 'pdf',
            ];
            if (isset($map[$mime])) return $map[$mime];
        }

        $path = parse_url($url, PHP_URL_PATH);
        $ext  = $path ? pathinfo($path, PATHINFO_EXTENSION) : null;
        return $ext ? strtolower($ext) : null;
    }

    private function buildStoragePath(string $finalName, ?string $mime): string
    {
        $date = now()->format('Y/m/d');
        $prefix = match (true) {
            $mime && str_starts_with($mime, 'image/') => 'images',
            $mime && str_starts_with($mime, 'video/') => 'videos',
            $mime && str_starts_with($mime, 'audio/') => 'audios',
            default => 'docs',
        };
        return "wa/{$prefix}/{$date}/" . Str::uuid() . '-' . $finalName;
        // مثال: wa/images/2025/10/11/uuid-screen-recording.mov
    }

    private function slugFilename(string $name): string
    {
        $name = Str::of($name)->replace(['/', '\\'], '-')->limit(60, '');
        return Str::slug($name, '-');
    }

    private function kindFromMimeOrType(?string $mime, ?string $dataType): string
    {
        if ($mime) {
            if (str_starts_with($mime, 'image/')) return 'image';
            if (str_starts_with($mime, 'video/')) return 'video';
            if (str_starts_with($mime, 'audio/')) return 'audio';
        }
        return in_array($dataType, ['image','video','audio','document','sticker'], true)
            ? $dataType
            : 'unknown';
    }

    private function publicUrlSafe(string $disk, string $path): ?string
    {
        try {
            return Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
