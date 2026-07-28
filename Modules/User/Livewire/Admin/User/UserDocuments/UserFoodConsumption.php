<?php

namespace Modules\User\Livewire\Admin\User\UserDocuments;

use Carbon\Carbon;
use Hekmatinasser\Verta\Facades\Verta;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Diet\app\Models\FoodConsumption;
use Modules\User\Entities\User;

class UserFoodConsumption extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public User $user;

    // اینجا دیگه شمسی نگه می‌داریم، مثل 1403/09/01
    public string $selectedDate;

    public array $dailyTotals = [
        'calories' => 0,
        'protein'  => 0,
        'carb'     => 0,
        'fat'      => 0,
        'fiber'    => 0,
    ];

    public bool $isToday = true;

    public function mount(User $user): void
    {
        $this->user = $user;

        // تاریخ امروز به شمسی
        $this->selectedDate = Verta::now()->format('Y/m/d');

        $this->refreshData();
    }

    public function updatedSelectedDate($value): void
    {
        if (empty($value)) {
            $this->selectedDate = Verta::now()->format('Y/m/d');
        }

        $this->resetPage();
        $this->refreshData();
    }

    public function goToPrevDay(): void
    {
        $v = Verta::parse($this->selectedDate)->subDay();
        $this->selectedDate = $v->format('Y/m/d');

        $this->resetPage();
        $this->refreshData();
    }

    public function goToNextDay(): void
    {
        $todayCarbon    = now()->startOfDay();
        $selectedCarbon = $this->selectedDateToCarbon()->startOfDay();
        $nextCarbon     = $selectedCarbon->copy()->addDay();

        // اگر از امروز جلوتر شد، جلو نرو
        if ($nextCarbon->greaterThan($todayCarbon)) {
            return;
        }

        // تبدیل روز بعد به شمسی و ست‌کردن
        $this->selectedDate = Verta::instance($nextCarbon)->format('Y/m/d');

        $this->resetPage();
        $this->refreshData();
    }

    /**
     * تبدیل selectedDate (شمسی) به Carbon میلادی
     */
    protected function selectedDateToCarbon(): Carbon
    {
        // selectedDate مثل 1403/09/01
        $verta = Verta::parse($this->selectedDate);

        $gregorian = $verta->formatGregorian('Y-m-d'); // 2025-11-19
        return Carbon::parse($gregorian);
    }

    protected function refreshData(): void
    {
        $todayCarbon    = now()->startOfDay();
        $selectedCarbon = $this->selectedDateToCarbon()->startOfDay();

        // اگر تاریخ انتخابی جلوتر از امروز بود، برگردان به امروز
        if ($selectedCarbon->greaterThan($todayCarbon)) {
            $selectedCarbon   = $todayCarbon;
            $this->selectedDate = Verta::instance($todayCarbon)->format('Y/m/d');
        }

        $this->isToday = $selectedCarbon->equalTo($todayCarbon);

        $startOfDay = $selectedCarbon->copy()->startOfDay();
        $endOfDay   = $selectedCarbon->copy()->endOfDay();

        // برای جمع روزانه
        $allConsumptions = FoodConsumption::where('user_id', $this->user->id)
            ->whereBetween('consumed_at', [$startOfDay, $endOfDay])
            ->get();

        $this->dailyTotals = [
            'calories' => round($allConsumptions->sum('calories'), 2),
            'protein'  => round($allConsumptions->sum('protein'), 2),
            'carb'     => round($allConsumptions->sum('carb'), 2),
            'fat'      => round($allConsumptions->sum('fat'), 2),
            'fiber'    => round($allConsumptions->sum('fiber'), 2),
        ];
    }

    public function render()
    {
        $selectedCarbon = $this->selectedDateToCarbon()->startOfDay();
        $startOfDay     = $selectedCarbon->copy()->startOfDay();
        $endOfDay       = $selectedCarbon->copy()->endOfDay();

        // گرفتن مصرف‌ها با ترتیب priority وعده (کدی که قبلاً داشتی)
        $consumptions = FoodConsumption::with(['consumable', 'unit', 'meal'])
            ->where('user_id', $this->user->id)
            ->whereBetween('consumed_at', [$startOfDay, $endOfDay])
            ->leftJoin('meals', 'food_consumptions.meal_id', '=', 'meals.id')
            ->orderBy('meals.priority')
            ->orderBy('consumed_at')
            ->select('food_consumptions.*')
            ->get();

        $groupedByMeal = $consumptions->groupBy(function ($item) {
            return optional($item->meal)->id ?? 'no_meal';
        });

        return view('user::livewire.admin.user.user-documents.user-food-consumption', [
            'groupedByMeal' => $groupedByMeal,
        ]);
    }
}
