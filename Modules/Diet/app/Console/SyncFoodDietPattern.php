<?php

namespace Modules\Diet\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\Food;
use Modules\Diet\Enum\ConditionKeyEnum;

class SyncFoodDietPattern extends Command
{
    protected $signature = 'diet:sync-food-pattern
                            {source : Source diet-pattern name}
                            {target : Target diet-pattern name}
                            {--dry-run : Only show how many foods need updating}';

    protected $description = 'Add a target diet pattern to every food having the source diet pattern';

    public function handle(): int
    {
        $condition = Condition::query()
            ->where('key', ConditionKeyEnum::DIET_PATTERN->value)
            ->first();

        if (! $condition) {
            $this->error('The diet-pattern condition was not found.');

            return self::FAILURE;
        }

        $values = $condition->options['items']['values'] ?? [];
        $sourceKey = array_search($this->argument('source'), $values, true);
        $targetKey = array_search($this->argument('target'), $values, true);

        if ($sourceKey === false || $targetKey === false) {
            $this->error('The source or target diet pattern was not found.');

            return self::FAILURE;
        }

        $query = DB::table('conditions_pivot')
            ->where('condition_id', $condition->id)
            ->where('conditionable_type', Food::class)
            ->whereJsonContains('options', (int) $sourceKey)
            ->whereJsonDoesntContain('options', (int) $targetKey);

        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("{$count} food(s) need the target diet pattern.");

            return self::SUCCESS;
        }

        if ($count === 0) {
            $this->info('All matching foods already have the target diet pattern.');

            return self::SUCCESS;
        }

        $updated = $query->update([
            'options' => DB::raw("JSON_ARRAY_APPEND(options, '$', ".(int) $targetKey.')'),
        ]);

        $this->info("Updated {$updated} food(s).");

        return self::SUCCESS;
    }
}
