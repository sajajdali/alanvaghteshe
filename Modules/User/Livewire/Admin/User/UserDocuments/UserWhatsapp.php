<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use App\Actions\SendUserWhatsappMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\User\Entities\User;

class UserWhatsapp extends Component
{
    use WithPagination;

    public bool $hasSession = true;
    public bool $canSend    = false;
    public ?string $sessionStatus = null;
    public string $sessionName;

    protected string $messagesPageName = 'wm_page';
    protected $paginationTheme = 'bootstrap';

    public User $user;

    /** متن پیام واتساپ که کاربر وارد می‌کند */
    public string $whatsappMessage = '';

    public function initSessionGate(\App\Services\WhatsappService $wa): void
    {
        $this->sessionName = config('whatsapp.default_session', 'default');

        // 1) آیا سشن از سرویس قابل دریافت است؟
        $sess = $wa->getSession($this->sessionName);
        $this->hasSession = $sess['ok'] === true;

        if (! $this->hasSession) {
            $this->canSend = false;
            $this->sessionStatus = null;
            return;
        }

        // 2) وضعیت WORKING را از طریق پروفایل چک می‌کنیم (200 => WORKING)
        $prof = $wa->getProfile($this->sessionName);
        $this->canSend = $prof['ok'] === true;
        $this->sessionStatus = $this->canSend ? 'WORKING' : ($sess['sessStatus'] ?? 'UNKNOWN');
    }
    protected function normalizeMsisdn(string $input): array
    {
        $digits = preg_replace('/\D+/', '', $input ?? '');
        if (str_starts_with($digits, '0'))      $msisdn98 = '98'.ltrim($digits, '0');
        elseif (str_starts_with($digits, '98')) $msisdn98 = $digits;
        elseif (str_starts_with($digits, '9'))  $msisdn98 = '98'.$digits;
        else                                    $msisdn98 = $digits;

        $local  = '0'.ltrim(substr($msisdn98, 2), '0');
        $chatId = $msisdn98.'@c.us';
        return [$msisdn98, $local, $chatId];
    }


    // Modules/User/Livewire/Admin/User/UserDocuments/UserWhatsapp.php


    public function sendWhatsappMessage(SendUserWhatsappMessage $sendUserWhatsappMessage): void
    {
        $this->validate([
            'whatsappMessage' => 'required|string|min:1',
        ], ['whatsappMessage.required' => 'متن پیغام را وارد کنید.']);

        $resp = $sendUserWhatsappMessage($this->user, $this->whatsappMessage);

        if ($resp['ok'] ?? false) {
            $this->whatsappMessage = '';
            $this->dispatch('messages-updated');
            $this->dispatch('swal', title: 'موفق', text: 'پیغام با موفقیت ارسال شد.', icon: 'success');
        } else {
            $msg = $resp['message'] ?? ('ارسال ناموفق بود'.(isset($resp['status']) ? " (کد: {$resp['status']})" : ''));
            $this->dispatch('swal', title: 'خطا', text: $msg, icon: 'error');
        }
    }
    public function render()
    {
        $messages = $this->user->whatsappMessages()
            ->latest()
            ->paginate(60, ['*'], $this->messagesPageName);

        return view('user::livewire.admin.user.user-documents.user-whatsapp', compact('messages'));
    }
}
