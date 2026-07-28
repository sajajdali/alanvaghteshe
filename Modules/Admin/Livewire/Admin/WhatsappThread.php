<?php

namespace Modules\Admin\Livewire\Admin;

use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Admin\app\Models\WhatsappMessage;
use Modules\Admin\app\Models\WhatsappSession;

class WhatsappThread extends Component
{
    use WithPagination;

    public $chat;       // همان chat_id در Route
    public $perPage = 20;
    public $messageText = '';



    protected $paginationTheme = 'bootstrap';

    protected string $chatCol = 'chat_id';
    protected string $tsCol   = 'created_at';


    public function mount(string $chat)
    {
        $this->chat = $chat;

        // این‌ها دیگر لازم نیست، چون مقدار پیش‌فرض داریم
        // $this->chatCol = 'chat_id';
        // $this->tsCol   = 'created_at';

        WhatsappMessage::where($this->chatCol, $this->chat)
            ->where('from_me', false)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function updatingPage() { /* nothing */ }

    public function send(\App\Services\WhatsappService $wa)
    {
        $this->validate([
            'messageText' => ['required', 'string', 'max:5000'],
        ]);

        // 1) session_name را تشخیص بده
        $sessionName = WhatsappMessage::where($this->chatCol, $this->chat)->value('session_name');
        if (!$sessionName) {
            $sessionName = WhatsappSession::query()->value('name') ?? 'default';
        }

        try {
            // 2) ارسال واقعی + ذخیره شدن با اسکیمای جدید
            $resp = $wa->sendAndStoreText(
                session: $sessionName,
                chatId: $this->chat,       // خود chat_id
                text: $this->messageText,
                user: auth()->user()       // برای ثبت user_id
            );

            if (!empty($resp['ok'])) {
                $this->reset('messageText');
                $this->resetPage(); // به آخرین پیام‌ها
                $this->dispatch('swal', title: 'موفق', text: 'پیام ارسال شد.', icon: 'success');
            } else {
                $code = $resp['status'] ?? 'unknown';
                $this->dispatch('swal', title: 'خطا', text: "ارسال ناموفق بود (کد: {$code})", icon: 'error');
            }
        } catch (\Throwable $e) {
            \Log::error('WhatsApp thread send error', ['e' => $e]);
            $this->dispatch('swal', title: 'خطا', text: 'ارتباط با سرویس برقرار نشد.', icon: 'error');
        }
    }

    public function render()
    {
        $query = WhatsappMessage::with(['media'])
            ->where($this->chatCol, $this->chat)
            ->orderByDesc($this->tsCol)
            ->orderByDesc('id');

        $messages = $query->paginate($this->perPage);

        return view('admin::livewire.admin.whatsapp-thread', [
            'messages' => $messages,
            'chatKey'  => $this->chat,
        ]);
    }
}
