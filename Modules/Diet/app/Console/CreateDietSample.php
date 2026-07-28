<?php

namespace Modules\Diet\app\Console;

use Illuminate\Console\Command;
use Modules\Diet\Entities\Food;
use Modules\Diet\Livewire\Admin\Food\FoodCreateOrUpdate;

class CreateDietSample extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diet:dietSample {--chunk=50}';

    /**
     * The console command description.
     */
    protected $description = 'Create child variants for all foods in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chunkSize = (int)$this->option('chunk');

        $this->info('Starting to process food variants...');

        Food::whereNull('parent_id')->chunk($chunkSize, function ($foods) {

            foreach ($foods as $food) {
                $this->processFood($food);
            }
        });

        $this->info('All food variants have been processed successfully!');
    }

    private function processFood(Food $food)
    {
        $this->info("Processing food:  (ID: {$food->id})");

        // حذف فرزندان قدیمی
        $food->children()->delete();

        // ایجاد یک نمونه از FoodCreateOrUpdate
        $foodManager = new FoodCreateOrUpdate();
        $foodManager->food = $food;

        // مقداردهی اولیه به form
        $foodManager->form = [
            'name' => $food->name,
            'calories' => $food->calories,
            'carb' => $food->carb,
            'fat' => $food->fat,
            'protein' => $food->protein,
            'fiber' => $food->fiber,
            'recipe' => $food->recipe,
            'interval_step' => $food->detail['interval_step'] ?? null,
            'meals' => $food->meals()->pluck('id')->toArray(),
            'conditions' => [],
            'foods' => [],
        ];

        // همگام‌سازی شرایط (conditions)
        foreach ($food->conditions as $condition) {
            $foodManager->form['conditions'][$condition->id] = $foodManager->convertToAssociativeArray(json_decode($condition->pivot->options, true));
        }

        // تنظیم basic foods
        $basicFoodList = $food->basicFoodPivot()->orderBy('basic_food_pivot.id')->get();
        foreach ($basicFoodList as $index => $basicFood) {
            $foodManager->form['foods'][$index] = [
                'basic_food_id' => $basicFood->id,
                'unit_name' => $basicFood->units()->where('is_primary', '1')->first()?->name ?? $basicFood->units()->first()->name,
                'unit' => $basicFood->pivot->quantity,
                'max' => $basicFood->pivot->maximum,
                'extend' => $basicFood->pivot->extend == 1,
            ];
        }

        // محاسبه داده‌های غذایی
        $foodManager->calculateData();

        // تولید ترکیبات جدید
        $intervalStep = $food->detail['interval_step'] ?? null;
        $allSampleDiets = $this->generateCombinationsWithStepLimit($food, null);
        $this->info("Total combinations for  (ID: {$food->id}): " . count($allSampleDiets));

//        $allSampleDiets = app('dietService')->generateFoodCombinations($food, $intervalStep);


        $foodManager->saveFoodVariants($allSampleDiets, $food->id);

        $this->info("Food: (ID: {$food->id}) processed successfully!");
    }

    private function generateCombinationsWithStepLimit(Food $food, $intervalStep)
    {
        // تولید ترکیبات
        $allSampleDiets = app('dietService')->generateFoodCombinations($food, $intervalStep);

        // بررسی تعداد ترکیبات
        if (count($allSampleDiets) > 1000) {
            if ($intervalStep === null) {
                $this->info("Too many combinations with intervalStep = null for {$food->id} is : " . count($allSampleDiets));
                $intervalStep = 5; // شروع مقدار اولیه برای کاهش
            } elseif ($intervalStep > 1) {
                $this->info("Reducing intervalStep for (ID: {$food->id}). Current step: {$intervalStep}");
                $intervalStep--;
            } else {
                $this->warn("Interval step is at minimum (1) but combinations still exceed 1000 for (ID: {$food->id}) and step : {$intervalStep} is : " . count($allSampleDiets) );
                return $allSampleDiets;
            }

            return $this->generateCombinationsWithStepLimit($food, $intervalStep);
        }
        $this->saveFinalIntervalStep($food, $intervalStep);

        return $allSampleDiets;
    }
    private function saveFinalIntervalStep(Food $food, $intervalStep)
    {
        $detail = $food->detail;
        $detail['interval_step'] = $intervalStep;

        $food->update([
            'detail' => $detail, // به‌روزرسانی ستون detail
        ]);

        $this->info("Saved final intervalStep = {$intervalStep} for food ID: {$food->id}");
    }
}
