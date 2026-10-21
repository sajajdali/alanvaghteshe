<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Model;

class DietPlanSuggestion extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'food_budget' => 'integer',
        'diet_type' => 'integer',
        'weight_loss_medication' => 'integer',
        'athlete_or_not' => 'integer',
        'session_from' => 'integer',
        'session_to' => 'integer',
    ];

    public function dietPlan(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DietPlan::class);
    }

    public static function suggestedPlanFor(
        int $foodBudget,
        int $sessionNumber,
        ?int $weightLossMedication = null,
        ?int $dietType = null,
        ?int $athleteOrNot = null
    ): ?DietPlan
    {
        return static::query()
            ->with('dietPlan')
            ->where('food_budget', $foodBudget)
            ->where('session_from', '<=', $sessionNumber)
            ->where('session_to', '>=', $sessionNumber)
            ->where(function ($query) use ($dietType) {
                $query->whereNull('diet_type');
                if ($dietType !== null) {
                    $query->orWhere('diet_type', $dietType);
                }
            })
            ->where(function ($query) use ($weightLossMedication) {
                $query->whereNull('weight_loss_medication');
                if ($weightLossMedication !== null) {
                    $query->orWhere('weight_loss_medication', $weightLossMedication);
                }
            })
            ->where(function ($query) use ($athleteOrNot) {
                $query->whereNull('athlete_or_not');
                if ($athleteOrNot !== null) {
                    $query->orWhere('athlete_or_not', $athleteOrNot);
                }
            })
            ->orderByRaw('(diet_type IS NOT NULL) + (weight_loss_medication IS NOT NULL) + (athlete_or_not IS NOT NULL) DESC')
            ->orderByRaw('diet_type IS NULL')
            ->orderByRaw('weight_loss_medication IS NULL')
            ->orderByRaw('athlete_or_not IS NULL')
            ->orderBy('id')
            ->first()?->dietPlan;
    }
}
