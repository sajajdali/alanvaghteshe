<?php

namespace Modules\Admin\Livewire\Admin\WhatApp;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\Admin\app\Models\WhatsappSession;
use Modules\Diet\Entities\FoodUnit;
use Modules\Diet\Livewire\Admin\Traits\ConditionTrait;

class WhatsAppSessionList extends Component
{
    use withPagination;

    #[Url]
    public $search = [];
    public $searchPanel = '';

    public array $statuses = [];
    public bool $statusesLoaded = false;

    public function loadStatuses(\App\Services\WhatsappService $wa): void
    {
        if ($this->statusesLoaded) return;

        $pageSessions = WhatsappSession::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->paginate(20);

        foreach ($pageSessions->items() as $sess) {
            try {
                $resp = $wa->getSession($sess->name);
                $this->statuses[$sess->id] = $resp['ok']
                    ? ($resp['sessStatus'] ?? '—')
                    : 'خطا';
            } catch (\Throwable $e) {
                $this->statuses[$sess->id] = 'خطا';
            }
        }

        $this->statusesLoaded = true;
    }

    public function startSearch()
    {
        $this->resetPage();
        $this->statuses = [];
        $this->statusesLoaded = false;
    }

    #[On('start')]
    public function start(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('update', $model);

        try {
            $resp = $wa->startSession($model->name);

            if ($resp['ok']) {
                // 201
                $statusText = data_get($resp, 'data.status', 'STARTED');
                $this->dispatch('swal', title: 'موفق', text: "سشن «{$model->name}» استارت شد. وضعیت: {$statusText}", icon: 'success');
                return;
            }

            if ($resp['already_started']) {
                // 422
                $msg = $resp['message'] ?: "سشن «{$model->name}» از قبل استارت شده است.";
                $this->dispatch('swal', title: 'توجه', text: $msg, icon: 'info');
                return;
            }

            // سایر کدها: خطای عمومی
            $this->dispatch('swal', title: 'خطا', text: 'شروع سشن ناموفق بود (کد: '.$resp['status'].')', icon: 'error');

        } catch (\Throwable $e) {
            \Log::error('WhatsApp session start error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد. دوباره تلاش کنید.', icon: 'error');
        }
    }

    #[On('logout')]
    public function logout(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('update', $model);

        try {
            $resp = $wa->logoutSession($model->name);

            if ($resp['ok']) {
                $sessStatus = $resp['sessStatus'] ?? 'N/A';
                $this->dispatch('swal',
                    title: 'موفق',
                    text: "سشن «{$model->name}» با موفقیت لاگ‌اوت شد. وضعیت: {$sessStatus}",
                    icon: 'success'
                );
                return;
            }

            if ($resp['already']) {
                $msg = $resp['message'] ?: "سشن «{$model->name}» از قبل لاگ‌اوت شده است.";
                $this->dispatch('swal', title: 'توجه', text: $msg, icon: 'info');
                return;
            }

            $this->dispatch('swal',
                title: 'خطا',
                text: 'لاگ‌اوت سشن ناموفق بود (کد: '.$resp['status'].')',
                icon: 'error'
            );

        } catch (\Throwable $e) {
            \Log::error('WhatsApp session logout error', ['e' => $e]);
            $this->dispatch('swal',
                title: 'خطا',
                text: 'ارتباط با سرویس برقرار نشد. دوباره تلاش کنید.',
                icon: 'error'
            );
        }
    }
    #[On('restart')]
    public function restart(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('update', $model);

        try {
            $resp = $wa->restartSession($model->name);

            if ($resp['ok']) {
                $sessStatus = $resp['sessStatus'] ?? 'STARTING';
                $this->dispatch('swal',
                    title: 'موفق',
                    text: "سشن «{$model->name}» ری‌استارت شد. وضعیت: {$sessStatus}",
                    icon: 'success'
                );
                return;
            }

            // اگر کد دیگری بود (مثلاً 422 یا 4xx/5xx)
            $msg = $resp['message'] ?: ('ری‌استارت ناموفق بود (کد: '.$resp['status'].')');
            $this->dispatch('swal', title: 'خطا', text: $msg, icon: 'error');

        } catch (\Throwable $e) {
            \Log::error('WhatsApp session restart error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد. دوباره تلاش کنید.', icon: 'error');
        }
    }

    public ?string $qrModalSrc = null;

    public ?array $profileData = null;

    #[On('showProfile')]
    public function showProfile(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('view', $model);

        try {
            $resp = $wa->getProfile($model->name);

            if ($resp['ok']) {
                $this->profileData = $resp['data'];
                $this->dispatch('show-profile-modal');
                return;
            }

            // اگر ۴۲۲ یا خطای دیگه
            $msg = $resp['message'] ?: 'وضعیت پروفایل قابل دریافت نیست. لطفاً بعداً تلاش کنید یا سشن را ری‌استارت کنید.';
            $this->dispatch('swal', title: 'خطا', text: $msg, icon: 'error');

        } catch (\Throwable $e) {
            \Log::error('WhatsApp get profile error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد.', icon: 'error');
        }
    }

    public ?string $screenshotModalSrc = null;

    #[On('screenshot')]
    public function screenshot(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('view', $model);

        try {
            $resp = $wa->getScreenshot($model->name);

            if ($resp['ok']) {
                $mime = $resp['content_type'] ?: 'image/jpeg';
                $this->screenshotModalSrc = 'data:'.$mime.';base64,'.base64_encode($resp['bytes']);
                $this->dispatch('show-screenshot-modal');
                return;
            }

            $this->dispatch('swal', title: 'خطا', text: 'اسکرین‌شات دریافت نشد (کد: '.$resp['status'].')', icon: 'error');
        } catch (\Throwable $e) {
            \Log::error('WhatsApp screenshot fetch error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد.', icon: 'error');
        }
    }

    // برای مودال ارسال پیام
    public ?int $sendSessionId = null;
    public string $sendChatId = '';
    public string $sendBody = '';
    public bool $sendAllowed = false;  // فقط وقتی پروفایل 200 (WORKING) باشد true می‌شود
    public ?string $sendBlockMsg = null;

    public function openSendModal(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('update', $model);

        // چکِ وضعیت با /{session}/profile → اگر 200 بود یعنی WORKING
        $prof = $wa->getProfile($model->name);

        $this->sendSessionId = $model->id;
        $this->sendChatId = '';
        $this->sendBody = '';

        if ($prof['ok']) {
            $this->sendAllowed = true;
            $this->sendBlockMsg = null;
        } else {
            $this->sendAllowed = false;
            $this->sendBlockMsg = 'دستگاه متصل نیست. ابتدا دستگاه را به وضعیت WORKING برسانید (اسکن QR/استارت).';
        }

        $this->dispatch('show-send-modal');
    }

    protected function normalizeMsisdn(string $input): array
    {
        // فقط رقم‌ها
        $digits = preg_replace('/\D+/', '', $input ?? '');

        // به 98... تبدیل کن
        if (Str::startsWith($digits, '0')) {
            $msisdn98 = '98'.ltrim($digits, '0'); // 09... -> 989...
        } elseif (Str::startsWith($digits, '98')) {
            $msisdn98 = $digits;                  // 98...
        } else {
            // اگر کاربر فقط 9xxxxxxxxx یا 912... داده بود، به 98 اضافه کن
            $msisdn98 = Str::startsWith($digits, '9') ? '98'.$digits : $digits;
        }

        // معادل محلی 0xxxxxxxxxx برای سرچ در کاربران
        $local = '0'.ltrim(substr($msisdn98, 2), '0'); // 98912... -> 0912...

        // chatId برای واتس‌اپ
        $chatId = $msisdn98.'@c.us';

        return [$msisdn98, $local, $chatId];
    }

    public function submitSend(\App\Services\WhatsappService $wa)
    {
        if (!$this->sendSessionId) return;

        $model = WhatsappSession::find($this->sendSessionId);
        if (!$model) return;

        $this->authorize('update', $model);

        if (!$this->sendAllowed) {
            $this->dispatch('swal', title: 'خطا', text: 'دستگاه متصل نیست. امکان ارسال پیام وجود ندارد.', icon: 'error');
            return;
        }

        $this->validate([
            'sendChatId' => 'required|string',
            'sendBody'   => 'required|string',
        ]);

        // 🔹 همینجا مقدار ورودی رو به فرمت درست تغییر می‌دیم
        [$msisdn98, $local, $chatId] = $this->normalizeMsisdn($this->sendChatId);
        $this->sendChatId = $msisdn98; // حالا مقدار داخل کامپوننت هم اصلاح میشه

        // کاربر مرتبط پیدا میشه
        $user = \Modules\User\Entities\User::query()
            ->where('mobile', $local)
            ->orWhere('mobile', $msisdn98)
            ->first();

        try {
            $resp = $wa->sendAndStoreText(
                session: $model->name,
                chatId: $chatId,   // همیشه در فرمت واتساپ
                text: $this->sendBody,
                user: $user
            );

            if ($resp['ok']) {
                $this->dispatch('messages-updated');
                $this->dispatch('swal', title: 'موفق', text: 'پیام با موفقیت ارسال شد.', icon: 'success');
                $this->sendChatId = '';
                $this->sendBody   = '';
            } else {
                $this->dispatch('swal', title: 'خطا', text: 'ارسال ناموفق بود (کد: '.$resp['status'].')', icon: 'error');
            }
        } catch (\Throwable $e) {
            \Log::error('WhatsApp send text error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد.', icon: 'error');
        }
    }
    #[On('scanQr')]
    public function scanQr(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('view', $model);

        try {
            $resp = $wa->getQrPng($model->name);

            if ($resp['ok']) {
                $this->qrModalSrc = 'data:'.$resp['content_type'].';base64,'.base64_encode($resp['bytes']);
                $this->dispatch('show-qr-modal'); // رویداد برای باز کردن مودال
                return;
            }

            $this->dispatch('swal', title: 'خطا', text: 'QR دریافت نشد (کد: '.$resp['status'].')', icon: 'error');
        } catch (\Throwable $e) {
            \Log::error('WhatsApp QR fetch error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد.', icon: 'error');
        }
    }

    #[On('showStatus')]
    public function showStatus(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('view', $model);

        try {
            $resp = $wa->getSession($model->name);

            if ($resp['ok']) {
                $status = $resp['sessStatus'] ?? 'N/A';
                // برای نمایش داخل جدول هم نگه می‌داریم:
                $this->statuses[$model->id] = $status;

                $this->dispatch('swal',
                    title: 'وضعیت سشن',
                    text: "نام: {$model->name}\nوضعیت: {$status}",
                    icon: 'info'
                );
                return;
            }

            // خطا
            $this->dispatch('swal',
                title: 'خطا',
                text: 'دریافت وضعیت ناموفق بود (کد: '.$resp['status'].')',
                icon: 'error'
            );

        } catch (\Throwable $e) {
            \Log::error('WhatsApp get session status error', ['e' => $e]);
            $this->dispatch('swal',
                title: 'خطا',
                text: 'ارتباط با سرویس برقرار نشد. دوباره تلاش کنید.',
                icon: 'error'
            );
        }
    }

    #[On('stop')]
    public function stop(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('update', $model);

        try {
            $resp = $wa->stopSession($model->name);

            if ($resp['ok']) {
                $this->dispatch('swal',
                    title: 'موفق',
                    text: "سشن «{$model->name}» با موفقیت متوقف شد.",
                    icon: 'success'
                );
                return;
            }

            if ($resp['already_stopped']) {
                $msg = $resp['message'] ?: "سشن «{$model->name}» از قبل متوقف شده است.";
                $this->dispatch('swal', title: 'توجه', text: $msg, icon: 'info');
                return;
            }

            $this->dispatch('swal',
                title: 'خطا',
                text: 'توقف سشن ناموفق بود (کد: '.$resp['status'].')',
                icon: 'error'
            );

        } catch (\Throwable $e) {
            \Log::error('WhatsApp session stop error', ['e' => $e]);
            $this->dispatch('swal',
                title: 'خطا',
                text: 'ارتباط با سرویس برقرار نشد. دوباره تلاش کنید.',
                icon: 'error'
            );
        }
    }

    #[On('delete')]
    public function delete(WhatsappSession $model, \App\Services\WhatsappService $wa)
    {
        $this->authorize('delete', $model);

        try {
            $resp = $wa->deleteSession($model->name);

            if (!$resp['ok']) {
                return redirect()->route('admin.whatsapp.index')
                    ->with('error', 'حذف در سرویس واتس‌اپ ناموفق بود (کد: '.$resp['status'].'). لطفاً مجدد تلاش کنید.');
            }

            $model->delete();

            return redirect()->route('admin.whatsapp.index')
                ->with('success', 'سشن با موفقیت حذف شد.');

        } catch (\Throwable $e) {
            \Log::error('WhatsApp session delete error', ['e' => $e]);
            return redirect()->route('admin.whatsapp.index')
                ->with('error', 'خطا در ارتباط با سرویس واتس‌اپ. لطفاً مجدد تلاش کنید.');
        }
    }


    public function render()
    {
        $WhatsappSessions = WhatsappSession::when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
            return $query->where('id', $this->search['id']);
        })
            ->when(isset($this->search['name']) && ! empty($this->search['name']), function ($query) {
                return $query->where('name', 'LIKE', "%{$this->search['name']}%");
            })
            ->paginate(20);
        return view('admin::livewire.admin.what-app.whats-app-list' , compact('WhatsappSessions'));
    }
}
