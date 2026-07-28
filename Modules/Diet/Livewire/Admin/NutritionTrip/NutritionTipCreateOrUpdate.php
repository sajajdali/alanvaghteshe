<?php

namespace Modules\Diet\Livewire\Admin\NutritionTrip;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Diet\app\Models\NutritionTip;
use Modules\Diet\Entities\Meal;

class NutritionTipCreateOrUpdate extends Component
{
    use AuthorizesRequests;
    public ?NutritionTip $nutritionTip;
    public string $name;
    public string $description;
    public $isEdited = false;

    protected $rules = [
        'name' => 'required',
        'description' => 'required',
    ];
    protected $messages = [
        'name.required' => 'نام  را وارد کنید',
        'description.required' => 'نکته تغذیه  را وارد کنید',
    ];

    public function mount()
    {
        $nutritionTip = request()->route('nutritionTip');
        if ($nutritionTip instanceof NutritionTip){
            $this->authorize('update', $nutritionTip);
            $this->isEdited = true;
            $this->nutritionTip = $nutritionTip;
            $this->name = $this->nutritionTip->name ?? '';
            $this->description = $this->nutritionTip->description ?? '';
        }
    }

    public function updateOrCreate()
    {
        $this->validate();
        if ($this->isEdited) {
            $this->nutritionTip->update([
                'name' => $this->name,
                'description' =>  $this->description,
            ]);
            $message = 'نکته تغذیه با موفقیت ویرایش شد';
        } else {
            $this->nutritionTip = NutritionTip::create([
                'name' => $this->name,
                'description' =>  $this->description,
            ]);
            $message = 'نکته تغذیه با موفقیت اضافه شد';
        }
        return redirect()->route('admin.nutrition_tip.index')->with('success', $message);
    }
    public function render()
    {
        return view('diet::livewire.admin.nutrition-trip.nutrition-tip-create-or-update');
    }
}
