<?php

namespace Modules\Admin\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Modules\User\Entities\User;
use Modules\Diet\Entities\DietRequest;
use Modules\Transaction\Entities\Transaction;

class Reports extends Component
{
    public array $showData = [];
    public bool $fromCache = false;
    public ?string $cachedAt = null;

    private const CACHE_KEY_DATA = 'reports:data';
    private const CACHE_KEY_META = 'reports:cached_at';

    public function mount(): void
    {
        $this->loadReports();
    }

    public function render()
    {
        return view('admin::livewire.reports');
    }

    /** دکمه: بازسازی از دیتابیس (بدون حذف کلیدها) */
    public function refreshReports(): void
    {
        $this->loadReports(force: true);
        $this->js('window.location.reload()');
        // در صورت نیاز برای بروزرسانی چارت‌ها در فرانت:
//         $this->dispatch('reports-updated', data: $this->showData);
    }

    /** دکمه: حذف کامل کش و سپس بازسازی */
    public function clearCache(): void
    {

        Cache::forget(self::CACHE_KEY_DATA);
        Cache::forget(self::CACHE_KEY_META);
        $this->loadReports(force: true);
        $this->js('window.location.reload()');

        // $this->dispatch('reports-updated', data: $this->showData);
    }

    /** گرفتن داده‌ها (از کش یا دیتابیس) */
    private function loadReports(bool $force = false): void
    {
        if (!$force && Cache::has(self::CACHE_KEY_DATA)) {
            $this->showData = Cache::get(self::CACHE_KEY_DATA);
            $this->fromCache = true;
            $this->cachedAt = Cache::get(self::CACHE_KEY_META);
            return;
        }

        // محاسبهٔ تازه
        $data = $this->computeReports();
        $expiresAt = now()->endOfDay();
        $nowStr = now()->toDateTimeString();

        Cache::put(self::CACHE_KEY_DATA, $data, $expiresAt);
        Cache::put(self::CACHE_KEY_META, $nowStr, $expiresAt);

        $this->showData = $data;
        $this->fromCache = false;       // همین لحظه تازه‌سازی شده
        $this->cachedAt = $nowStr;      // زمان ساخت کش جدید
    }

    /** محاسبهٔ گزارش‌ها (بدون وابستگی به کش) */
    private function computeReports(): array
    {
        $now         = now();
        $startToday  = $now->copy()->startOfDay();
        $startYday   = $now->copy()->subDay()->startOfDay();
        $endYday     = $now->copy()->subDay()->endOfDay();
        $startWeek   = verta()->startWeek()->toCarbon();   // شروع هفته جلالی
        $startMonth  = verta()->startMonth()->toCarbon();  // شروع ماه جلالی

        // Sales
        $baseT = Transaction::successful();
        $sale = [
            'total'     => (clone $baseT)->sum('total_cost'),
            'today'     => (clone $baseT)->whereBetween('created_at', [$startToday, $now])->sum('total_cost'),
            'yesterday' => (clone $baseT)->whereBetween('created_at', [$startYday, $endYday])->sum('total_cost'),
            'week'      => (clone $baseT)->where('created_at', '>=', $startWeek)->sum('total_cost'),
            'month'     => (clone $baseT)->where('created_at', '>=', $startMonth)->sum('total_cost'),
        ];

        // Users
        $usersQ = User::query();
        $user = [
            'last_three_months' => (clone $usersQ)->where('created_at', '>=', verta()->subMonths(3)->startMonth()->toCarbon())->count(),
            'last_month'        => (clone $usersQ)->where('created_at', '>=', $startMonth)->count(),
            'last_week'         => (clone $usersQ)->where('created_at', '>=', $startWeek)->count(),
            'yesterday'         => (clone $usersQ)->whereBetween('created_at', [$startYday, $endYday])->count(),
        ];

        // Diets
        $dietsQ = DietRequest::successfulDiet();
        $diets = [
            'last_three_months' => (clone $dietsQ)->where('start_date', '>=', verta()->subMonths(3)->startMonth()->toCarbon())->count(),
            'last_month'        => (clone $dietsQ)->where('start_date', '>=', $startMonth)->count(),
            'last_week'         => (clone $dietsQ)->where('start_date', '>=', $startWeek)->count(),
            'yesterday'         => (clone $dietsQ)->whereBetween('start_date', [$startYday, $endYday])->count(),
        ];

        return compact('sale', 'user', 'diets');
    }
}
