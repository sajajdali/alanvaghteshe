<?php

namespace Modules\Recipe\app\Console;

use Illuminate\Console\Command;
use Modules\Diet\Livewire\Admin\Recipe\RecipeUpdateOrCreate;
use Modules\Recipe\app\Models\Recipe;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FixRecipeData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fix:recipe-data';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';

    public function handle()
    {
        $recipes = Recipe::all();
        $this->info("editing {$recipes->count()} ");

        foreach ($recipes as $recipe) {
            $this->info("🔄 start {$recipe->id}");

            // ایجاد یک نمونه از کامپوننت Livewire
            $livewireComponent = new RecipeUpdateOrCreate();
            $livewireComponent->recipe = $recipe;

            // پر کردن فرم با داده‌های موجود
            $livewireComponent->fillTheForm();

            // اجرای مجدد محاسبات
            $livewireComponent->calculateData();

            dd($livewireComponent);

            
            // ذخیره تغییرات در دیتابیس
            $recipe->update([
                'calorie' => $livewireComponent->form['calorie'],
                'weight' => $livewireComponent->form['weight'],
            ]);

            $this->info("✅ دستور '{$recipe->name}' اصلاح شد.");
        }

        $this->info("🎉 تمام دستورات پخت با موفقیت اصلاح شدند!");
        return Command::SUCCESS;
    }
}
