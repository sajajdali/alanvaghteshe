<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\User\Entities\User;

class WhatsappService
{
    protected PendingRequest $http;

    public function __construct()
    {
        $this->http = Http::baseUrl(config('whatsapp.base_url'))
            ->timeout(config('whatsapp.timeout'))
            ->withHeaders([
                'accept'     => 'application/json',
                'X-Api-Key'  => config('whatsapp.api_key'),
            ]);
    }

    public function logoutSession(string $name): array
    {
        $resp = $this->http->post("/sessions/{$name}/logout", []);

        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status'   => $status,
            'ok'       => $status === 201,            // موفق
            'already'  => $status === 422,            // قبلاً logout
            'data'     => $json,
            'message'  => $json['message'] ?? null,
            'sessName' => $json['name'] ?? $name,
            'sessStatus' => $json['status'] ?? null,
        ];
    }

    public function getQrPng(string $name): array
    {
        // GET /{name}/auth/qr?format=image  با Accept: image/png
        $resp = $this->http
            ->withHeaders(['accept' => 'image/png'])
            ->get("/{$name}/auth/qr", ['format' => 'image']);

        $status = $resp->status();
        $ctype  = $resp->header('content-type');

        return [
            'status'       => $status,
            'ok'           => $status === 200 && is_string($ctype) && str_starts_with(strtolower($ctype), 'image/'),
            'content_type' => $ctype,
            'bytes'        => $resp->body(), // raw PNG
        ];
    }


    public function getScreenshot(string $name): array
    {
        $resp = $this->http
            ->withHeaders(['accept' => 'image/jpeg'])
            ->get("/screenshot", ['session' => $name]);

        $status = $resp->status();
        $ctype  = (string) $resp->header('content-type');

        return [
            'status'       => $status,
            'ok'           => $status === 200 && str_starts_with(strtolower($ctype), 'image/'),
            'content_type' => $ctype,
            'bytes'        => $resp->body(),
        ];
    }


    public function sendAndStoreText(
        string $session,
        string $chatId,
        string $text,
        ?User $user = null,
        ?string $replyTo = null,
        bool $linkPreview = true,
        bool $linkPreviewHighQuality = false
    ): array {
        $resp = $this->sendText($session, $chatId, $text, $replyTo, $linkPreview, $linkPreviewHighQuality);

        if ($resp['ok']) {
            $data  = $resp['data'] ?? [];

            // تلاش برای خروجی‌های مفید از پاسخ سرویس
            $rawType = (string) (data_get($data, 'type', 'text'));
            $type    = strtolower($rawType) === 'chat' ? 'text' : strtolower($rawType);

            // اگر سرویس آیدی پیام واتس‌اپ را برگرداند، ذخیره می‌کنیم تا Duplicate نشود
            $waKey = (string) (data_get($data, '_data.id._serialized') ?? data_get($data, 'id') ?? '');

            // فرستنده واقعی: چون پیام از طرف ماست، اگر آیدی «me» در پاسخ نبود، خالی می‌گذاریم (NULL نیست، رشته‌ی خالی است)
            $senderId = (string) (
                data_get($data, 'from') ??
                data_get($data, 'author') ??
                data_get($data, 'sender') ??
                ''
            );

            // ساخت رکورد مطابق اسکیمای جدید
            WhatsappMessage::create([
                'user_id'       => $user?->id,
                'session_name'  => $session,
                'chat_id'       => $chatId,    // گیرنده (طرف مقابل)
                'sender_id'     => $senderId,  // فرستنده (ما). اگر خالی ماند مشکلی نیست چون NOT NULL نیست
                'from_me'       => true,       // پیام ارسالی از سمت ما
                'wa_message_key'=> $waKey !== '' ? $waKey : null,

                'type'          => in_array($type, ['text','image','video','audio','document','sticker','unknown'], true)
                    ? $type : 'text',
                'body'          => $text,
                'read_at'       => null,       // پیام خودمان معمولاً read_at نمی‌خورد
                'has_media'     => false,      // این متد مخصوص متن است
            ]);
        }

        return $resp;
    }

    public function sendText(string $session, string $chatId, string $text, ?string $replyTo = null, bool $linkPreview = true, bool $linkPreviewHighQuality = false): array
    {
        $payload = [
            'chatId'                 => $chatId,
            'reply_to'               => $replyTo,
            'text'                   => $text,
            'linkPreview'            => $linkPreview,
            'linkPreviewHighQuality' => $linkPreviewHighQuality,
            'session'                => $session,
        ];

        $resp = $this->http->post('/sendText', $payload);

        return [
            'status' => $resp->status(),
            'ok'     => $resp->status() === 201,
            'data'   => $resp->json() ?? [],
            'error'  => $resp->body(),
        ];
    }

    public function getProfile(string $name): array
    {
        $resp = $this->http->get("/{$name}/profile");

        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status'  => $status,
            'ok'      => $status === 200,
            'data'    => $json,
            'message' => $json['error'] ?? null,
        ];
    }
    public function getSession(string $name): array
    {
        $resp = $this->http->get("/sessions/{$name}");
        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status'      => $status,               // 200 روی موفق
            'ok'          => $resp->successful(),
            'data'        => $json,
            'sessName'    => $json['name']   ?? $name,
            'sessStatus'  => $json['status'] ?? null, // مثل: SCAN_QR_CODE / STARTED / STOPPED / STARTING ...
            'engine'      => $json['engine']['engine'] ?? null, // WEBJS
            'message'     => $json['message'] ?? null,
        ];
    }
    public function restartSession(string $name): array
    {
        // POST /sessions/{name}/restart  (بدون بادی)
        $resp = $this->http->post("/sessions/{$name}/restart", []);

        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status'      => $status,
            'ok'          => $status === 201,          // موفق
            'data'        => $json,
            'message'     => $json['message'] ?? null,
            'sessName'    => $json['name'] ?? $name,
            'sessStatus'  => $json['status'] ?? null,  // مثل STARTING
        ];
    }
    public function stopSession(string $name): array
    {
        // POST /sessions/{name}/stop
        $resp = $this->http->post("/sessions/{$name}/stop", []);

        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status' => $status,
            'ok'     => $status === 201,          // موفق
            'already_stopped' => $status === 422, // اگر سرویس برگردونه که از قبل stop بوده
            'data'   => $json,
            'error'  => $resp->body(),
            'message'=> $json['message'] ?? null,
        ];
    }
    public function startSession(string $name): array
    {
        $resp = $this->http->post("/sessions/{$name}/start", []);

        $status = $resp->status();
        $json   = $resp->json() ?? [];

        return [
            'status' => $status,
            'ok'     => $status === 201,
            'already_started' => $status === 422,
            'data'   => $json,
            'error'  => $resp->body(),
            'message'=> $json['message'] ?? null, // برای 422: "Session 'default' is already started."
        ];
    }
    public function deleteSession(string $name): array
    {
        $resp = $this->http->delete("/sessions/{$name}");

        return [
            'status' => $resp->status(),
            'ok'     => in_array($resp->status(), [200, 202, 204], true),
            'data'   => $resp->json() ?? [],
            'error'  => $resp->body(),
        ];
    }
    public function createSession(string $name, ?string $webhookUrl = null, bool $start = true): array
    {
        $payload = [
            'name'   => $name,
            'start'  => $start,
            'config' => [
                'webhooks' => $webhookUrl ? [[
                    'url'    => $webhookUrl,
                    'events' => ['message', 'session.status'],
                ]] : null,
            ],
        ];

        // توجه: اینجا throw() نمی‌کنیم تا بتونیم status رو خودمون چک کنیم
        $resp = $this->http->post('/sessions', $payload);

        return [
            'status' => $resp->status(),
            'ok'     => $resp->successful(),   // true برای 2xx
            'data'   => $resp->json() ?? [],
            'error'  => $resp->body(),
        ];
    }

    /** لیست همهٔ سشن‌ها */
    public function sessions(): array
    {
        return $this->call('GET', '/sessions');
    }

    /** اطلاعات یک سشن خاص */
    public function session(string $name = null): array
    {
        $name = $name ?: config('whatsapp.default_session');
        return $this->call('GET', "/sessions/{$name}");
    }


    /** اگر API مسیر خاص حذف/فوروارد/تایپینگ داشته باشد اینجا اضافه کن */
    public function markTyping(string $chatId, bool $on = true, ?string $session = null): array
    {
        $payload = [
            'chatId'  => $chatId,
            'typing'  => $on,
            'session' => $session ?: config('whatsapp.default_session'),
        ];
        // اگر سرویس شما مسیر دیگری دارد، این را تغییر دهید
        return $this->call('POST', '/typing', $payload);
    }

    /* ----------------- Helpers ----------------- */

    protected function endpointByMime(string $mime): string
    {
        $mime = strtolower($mime);
        if (str_starts_with($mime, 'image/')) return '/sendImage';
        if (str_starts_with($mime, 'video/')) return '/sendVideo';
        if (str_starts_with($mime, 'audio/')) return '/sendAudio';
        return '/sendFile';
    }

    /**
     * فراخوان عمومی با ریتری و مدیریت خطا
     * @throws RequestException
     */
    protected function call(string $method, string $url, array $payload = []): array
    {
        $times = (int) data_get(config('whatsapp.retry'), 'times', 3);
        $sleep = (int) data_get(config('whatsapp.retry'), 'sleep', 200);

        return retry($times, function () use ($method, $url, $payload) {
            $resp = $method === 'GET'
                ? $this->http->get($url, $payload)
                : $this->http->post($url, $payload);

            // اگر خطای 4xx/5xx بود throw کن تا retry کار کند
            $resp->throw();

            return $resp->json() ?? [];
        }, $sleep);
    }
}
