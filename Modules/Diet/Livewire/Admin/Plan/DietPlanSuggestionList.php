<?php

namespace Modules\Diet\Livewire\Admin\Plan;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietPlanSuggestion;
use Modules\User\Enum\UserMetaEnum;

#[Title('پیشنهاد رژیم')]
class DietPlanSuggestionList extends Component
{
    #[Url]
    public string $filterBudget = '';

    #[Url]
    public string $filterDietType = '';

    #[Url]
    public string $filterAthlete = '';

    public ?int $editingId = null;
    public string $foodBudget = '';
    public string $dietType = '';
    public string $weightLossMedication = '';
    public string $athleteOrNot = '';
    public string $sessionFrom = '';
    public string $sessionTo = '';
    public string $dietPlanId = '';

    public function save(): void
    {
        $validated = $this->validate([
            'foodBudget' => ['required', 'integer', Rule::in([0, 1, 2])],
            'dietType' => ['nullable', Rule::in(['', '0', '1', '2'])],
            'weightLossMedication' => ['nullable', Rule::in(['', '0', '1'])],
            'athleteOrNot' => ['nullable', Rule::in(['', '0', '1'])],
            'sessionFrom' => ['required', 'integer', 'min:1'],
            'sessionTo' => ['required', 'integer', 'gte:sessionFrom'],
            'dietPlanId' => ['required', 'integer', Rule::exists('diet_plans', 'id')->where('status', true)],
        ], [
            'sessionTo.gte' => 'جلسه پایانی نباید از جلسه شروع کمتر باشد.',
        ]);

        $overlaps = DietPlanSuggestion::query()
            ->where('food_budget', $validated['foodBudget'])
            ->where(function ($query) use ($validated) {
                $dietType = $validated['dietType'] ?? '';
                if ($dietType === '') {
                    $query->whereNull('diet_type');
                } else {
                    $query->where('diet_type', (int) $dietType);
                }
            })
            ->where(function ($query) use ($validated) {
                $medication = $validated['weightLossMedication'] ?? '';
                if ($medication === '') {
                    $query->whereNull('weight_loss_medication');
                } else {
                    $query->where('weight_loss_medication', (int) $medication);
                }
            })
            ->where(function ($query) use ($validated) {
                $athleteOrNot = $validated['athleteOrNot'] ?? '';
                if ($athleteOrNot === '') {
                    $query->whereNull('athlete_or_not');
                } else {
                    $query->where('athlete_or_not', (int) $athleteOrNot);
                }
            })
            ->when($this->editingId, fn ($query) => $query->whereKeyNot($this->editingId))
            ->where('session_from', '<=', $validated['sessionTo'])
            ->where('session_to', '>=', $validated['sessionFrom'])
            ->exists();

        if ($overlaps) {
            $this->addError('sessionFrom', 'این بازه جلسه برای بودجه، هدف، وضعیت دارو و ورزش انتخابی با یک قاعده دیگر هم‌پوشانی دارد.');
            return;
        }

        DietPlanSuggestion::updateOrCreate(['id' => $this->editingId], [
            'food_budget' => $validated['foodBudget'],
            'diet_type' => ($validated['dietType'] ?? '') === ''
                ? null
                : (int) $validated['dietType'],
            'weight_loss_medication' => ($validated['weightLossMedication'] ?? '') === ''
                ? null
                : (int) $validated['weightLossMedication'],
            'athlete_or_not' => ($validated['athleteOrNot'] ?? '') === ''
                ? null
                : (int) $validated['athleteOrNot'],
            'session_from' => $validated['sessionFrom'],
            'session_to' => $validated['sessionTo'],
            'diet_plan_id' => $validated['dietPlanId'],
        ]);

        session()->flash('success', 'قاعده پیشنهاد رژیم ذخیره شد.');
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $item = DietPlanSuggestion::findOrFail($id);
        $this->editingId = $item->id;
        $this->foodBudget = (string) $item->food_budget;
        $this->dietType = $item->diet_type === null ? '' : (string) $item->diet_type;
        $this->weightLossMedication = $item->weight_loss_medication === null
            ? ''
            : (string) $item->weight_loss_medication;
        $this->athleteOrNot = $item->athlete_or_not === null
            ? ''
            : (string) $item->athlete_or_not;
        $this->sessionFrom = (string) $item->session_from;
        $this->sessionTo = (string) $item->session_to;
        $this->dietPlanId = (string) $item->diet_plan_id;
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        DietPlanSuggestion::findOrFail($id)->delete();
        if ($this->editingId === $id) {
            $this->resetForm();
        }
        session()->flash('success', 'قاعده پیشنهاد رژیم حذف شد.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'foodBudget', 'dietType', 'weightLossMedication', 'athleteOrNot', 'sessionFrom', 'sessionTo', 'dietPlanId']);
        $this->resetValidation();
    }

    public function render()
    {
        $suggestions = DietPlanSuggestion::query()
            ->with('dietPlan')
            ->when($this->filterBudget !== '', fn ($query) => $query
                ->where('food_budget', (int) $this->filterBudget))
            ->when($this->filterDietType !== '', fn ($query) => $query
                ->where('diet_type', (int) $this->filterDietType))
            ->when($this->filterAthlete !== '', fn ($query) => $query
                ->where('athlete_or_not', (int) $this->filterAthlete))
            ->orderBy('food_budget')
            ->orderBy('diet_type')
            ->orderBy('athlete_or_not')
            ->orderBy('session_from')
            ->get();

        return view('diet::livewire.admin.plan.diet-plan-suggestion-list', [
            'suggestions' => $suggestions,
            'dietPlans' => DietPlan::active()->specificPattern()->orderBy('name')->get(),
            'budgetOptions' => UserMetaEnum::FOOD_BUDGET->getOptions(),
            'dietTypeOptions' => UserMetaEnum::DIET_TYPE->getOptions(),
            'medicationOptions' => UserMetaEnum::WEIGHT_LOSS_MEDICATION->getOptions(),
            'athleteOptions' => UserMetaEnum::ATHLETE_OR_NOT->getOptions(),
        ]);
    }
}
