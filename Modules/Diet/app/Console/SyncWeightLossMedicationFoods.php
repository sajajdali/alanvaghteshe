<?php

namespace Modules\Diet\app\Console;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\Food;
use Modules\Diet\Enum\ConditionKeyEnum;

class SyncWeightLossMedicationFoods extends Command
{
    protected $signature = 'diet:sync-weight-loss-medication-foods {--dry-run : Only show how many foods need updating}';

    protected $description = 'Add the weight-loss medication diet pattern to foods marked as fat burning';

    public function handle(): int
    {
        $condition = Condition::query()
            ->where('key', ConditionKeyEnum::DIET_PATTERN->value)
            ->first();

        if (! $condition) {
            $this->error('The diet-pattern condition was not found.');

            return self::FAILURE;
        }

        $sourceKey = $this->findOptionKey($condition, 'چربی سوزی');
        $targetKey = $this->findOptionKey($condition, 'داروهای لاغری');

        if ($sourceKey === null || $targetKey === null) {
            $this->error('The fat-burning or weight-loss medication option was not found.');

            return self::FAILURE;
        }

        $query = $this->foodsNeedingUpdateQuery($condition->id, $sourceKey, $targetKey);
        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("{$count} food(s) need the weight-loss medication pattern.");

            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('All fat-burning foods already have the weight-loss medication pattern.');

            return self::SUCCESS;
        }

        $updated = $query->update([
            'options' => DB::raw("JSON_ARRAY_APPEND(options, '$', {$targetKey})"),
        ]);

        $this->info("Updated {$updated} food(s).");

        return self::SUCCESS;
    }

    private function findOptionKey(Condition $condition, string $label): ?int
    {
        $values = $condition->options['items']['values'] ?? [];
        $key = array_search($label, $values, true);

        return $key === false ? null : (int) $key;
    }

    private function foodsNeedingUpdateQuery(int $conditionId, int $sourceKey, int $targetKey): Builder
    {
        return DB::table('conditions_pivot')
            ->where('condition_id', $conditionId)
            ->where('conditionable_type', Food::class)
            ->whereJsonContains('options', $sourceKey)
            ->whereJsonDoesntContain('options', $targetKey);
    }
}
