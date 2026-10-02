<?php

namespace Modules\Diet\Entities;

use Illuminate\Database\Eloquent\Model;

class DietPlanSuggestion extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'food_budget' => 'integer',
        'weight_loss_medication' => 'integer',
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
        ?int $weightLossMedication = null
    ): ?DietPlan
    {
        return static::query()
            ->with('dietPlan')
            ->where('food_budget', $foodBudget)
            ->where('session_from', '<=', $sessionNumber)
            ->where('session_to', '>=', $sessionNumber)
            ->where(function ($query) use ($weightLossMedication) {
                $query->whereNull('weight_loss_medication');
                if ($weightLossMedication !== null) {
                    $query->orWhere('weight_loss_medication', $weightLossMedication);
                }
            })
            ->orderByRaw('weight_loss_medication IS NULL')
            ->first()?->dietPlan;
    }
}
