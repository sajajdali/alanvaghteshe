<?php

namespace Modules\Diet\app\Console;

use DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ChangeFoodRestriction extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diet:changeFoodRestriction';

    /**
     * The console command description.
     */
    protected $description = 'change food Restriction.';


    public function handle()
    {
        $this->info("start");
        $countUpdate = 0;
        DB::table('conditions_pivot')
            ->where('condition_id', 3)
            ->orderBy('conditionable_id') // استفاده از کلید مرتب‌سازی
            ->chunkById(1000, function ($conditionPivots) use (&$countUpdate) {
                foreach ($conditionPivots as $conditionPivot) {
                    $countUpdate++;
                    $newValue = $this->getInverseOptions($conditionPivot->options);

                    DB::table('conditions_pivot')->where([
                        'conditionable_type' => $conditionPivot->conditionable_type,
                        'conditionable_id' => $conditionPivot->conditionable_id,
                        'condition_id' => $conditionPivot->condition_id
                    ])->update(['options' => $newValue]);
                }
            }, 'conditionable_id'); // دسته‌بندی بر اساس شرط

        $this->info("done count : {$countUpdate}");
    }

    private function getInverseOptions(string $options): string
    {
        $optionsArray = json_decode($options, true);
        $fullRange = range(0, 6);

        if (empty($optionsArray)) {
            return json_encode($fullRange);
        }

        $inverseOptions = array_values(array_diff($fullRange, $optionsArray));

        return empty($inverseOptions) ? json_encode([]) : json_encode($inverseOptions);
    }

}
