<?php

namespace Modules\Admin\Livewire\Admin\WhatApp;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Admin\app\Models\WhatsappSession;
use App\Services\WhatsappService; // اضافه شد
use Illuminate\Support\Facades\Log;

class WhatsAppSessionCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?WhatsappSession $whatsappSession = null;

    public string $name = '';
    public string $webhook_url = ''; // فیلد وبهوک

    protected $rules = [
        'name' => 'required|string',
        'webhook_url' => 'nullable|url', // اختیاری ولی اگر وجود داشت باید URL معتبر باشد
    ];

    protected $messages = [
        'name.required' => 'نام دستگاه را وارد کنید',
        'webhook_url.url' => 'آدرس وبهوک معتبر نیست',
    ];

    public bool $isEdited = false;

    public function mount()
    {
        $whatsappSession = request()->route('whatsappSession');
        if ($this->whatsappSession = $whatsappSession instanceof WhatsappSession ? $whatsappSession : null) {
            $this->authorize('update', $this->whatsappSession);
            $this->isEdited = true;
            $this->name = $this->whatsappSession->name ?? '';
            $this->webhook_url = $this->whatsappSession->webhook_url ?? '';
        }
    }

    public function updateOrCreate(WhatsappService $wa)
    {
        $this->validate();

        if ($this->isEdited) {
            // ویرایش فقط در DB محلی (اگر می‌خواهی سمت API هم rename کنی، متد جدا بساز)
            $this->whatsappSession->update([
                'name' => $this->name,
                'webhook_url' => $this->webhook_url,
            ]);
            $message = 'دستگاه با موفقیت ویرایش شد';
            return redirect()->route('admin.whatsapp.index')->with('success', $message);
        }

        // ایجاد: اول API را صدا بزن
        try {
            $resp = $wa->createSession(
                name: $this->name,
                webhookUrl: $this->webhook_url ?: null,
                start: true
            );

            if ((int) $resp['status'] !== 201) {
                // ساخت محلی انجام نشود
                Log::warning('WhatsApp session create failed', ['status' => $resp['status'], 'resp' => $resp]);
                $this->addError('name', 'ارتباط برقرار نشد. لطفاً مجدد تلاش کنید.');
                session()->flash('error', 'ساخت سشن در سرویس خارجی ناموفق بود (کد: '.$resp['status'].').');
                return null;
            }

            // فقط در صورت موفقیت 201، رکورد محلی بساز
            $this->whatsappSession = WhatsappSession::create([
                'name' => $this->name,
                'webhook_url' => $this->webhook_url, // ← تایپو اصلاح شد
            ]);

            $message = 'دستگاه با موفقیت اضافه شد';
            return redirect()->route('admin.whatsapp.index')->with('success', $message);

        } catch (\Throwable $e) {
            Log::error('WhatsApp session create exception', ['e' => $e]);
            $this->addError('name', 'ارتباط برقرار نشد. لطفاً مجدد تلاش کنید.');
            session()->flash('error', 'خطا در برقراری ارتباط با سرویس واتس‌اپ.');
            return null;
        }
    }

    public function render()
    {
        $title = (!is_null($this->whatsappSession) && $this->whatsappSession->exists) ? 'ویرایش دستگاه' : 'ایجاد دستگاه جدید';
        return view('admin::livewire.admin.what-app.whats-app-create-or-update')->title($title);
    }
}
