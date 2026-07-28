<?php

namespace Modules\Admin\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Admin\app\Models\WhatsappMessage;
use Illuminate\Support\Str;

class WhatsappMessages extends Component
{
    use WithPagination;

    public $search = '';        // فیلتر واقعی که روی کوئری می‌خوره
    public $searchInput = '';   // متن داخل اینپوت
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'page'   => ['except' => 1],
    ];

    public function mount()
    {
        // وقتی از url اومده، اینپوت هم sync بشه
        $this->searchInput = $this->search;
    }

    // وقتی دستی سرچ تایپ میشه و مکث می‌کنه، این اجرا میشه (به خاطر debounce در blade)
    public function updatedSearchInput($val)
    {
        $this->applySearch(false); // false یعنی بدون نیاز به Enter هم اعمال کن
    }

    // روی Enter یا دکمهٔ جستجو
    public function applySearch(bool $force = true)
    {
        $term = trim((string)$this->searchInput);

        // اگر طول کمتر از 2 کاراکتره و اجباری نیست، بی‌خیال (برای کاهش نویز)
        if (!$force && Str::length($term) > 0 && Str::length($term) < 2) {
            return;
        }

        $this->search = $term;
        $this->resetPage();
    }

    // پاک کردن سرچ
    public function clearSearch()
    {
        $this->search = '';
        $this->searchInput = '';
        $this->resetPage();
    }

    public function render()
    {
        $table   = (new WhatsappMessage)->getTable();
        $chatCol = 'chat_id';

        // اگر سرچ داریم، اول گفتگوهای match شده رو پیدا کن (distinct chat_id)
        $matchedChats = null;
        if (filled($this->search)) {
            $term = "%{$this->search}%";
            $matchedChats = WhatsappMessage::query()
                ->select($chatCol)
                ->whereNotNull($chatCol)
                ->where(function ($q) use ($term, $chatCol) {
                    $q->where($chatCol, 'like', $term)
                        ->orWhere('sender_id', 'like', $term)
                        ->orWhere('body', 'like', $term);
                })
                ->groupBy($chatCol);
        }

        // آخرین پیام هر گفتگو (فقط گفتگوهای match شده وقتی سرچ داریم)
        $latestIds = WhatsappMessage::query()
            ->selectRaw('MAX(id) as id')
            ->whereNotNull($chatCol)
            ->when($matchedChats, fn($q) => $q->whereIn($chatCol, $matchedChats))
            ->groupBy($chatCol);

        $rows = WhatsappMessage::query()
            ->whereIn('id', $latestIds)
            ->select($table.'.*')
            ->selectSub(function ($q) use ($table, $chatCol) {
                $q->from($table.' as wm2')
                    ->selectRaw('COUNT(1)')
                    ->whereColumn("wm2.$chatCol", "$table.$chatCol")
                    ->where('wm2.from_me', 0)
                    ->whereNull('wm2.read_at');
            }, 'unread_count')
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('admin::livewire.admin.whatsapp-messages', ['rows' => $rows]);
    }
}
