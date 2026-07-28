<?php

namespace Modules\Diet\Livewire\Admin\FoodUnit;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Diet\Entities\FoodUnit;

class FoodUnitCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?FoodUnit $foodUnit = null;
    public string $name;


    protected $rules = [
        'name' => 'required|string',
    ];

    protected $messages = [
        'name.required' => 'نام نوع وعده را وارد کنید',
    ];

    public $isEdited = false;


    public function mount()
    {
        $foodUnit = request()->route('food_unit');
        if ($foodUnit instanceof FoodUnit){
            $this->authorize('update', $foodUnit);
            $this->isEdited = true;
            $this->foodUnit = $foodUnit;
            $this->name = $this->foodUnit->name ?? '';
        }
    }

    public function updateOrCreate()
    {
        $this->validate();
        if ($this->isEdited) {
            $this->foodUnit->update([
                'name' => $this->name,
            ]);
            $message = 'وعده با موفقیت ویرایش شد';
        } else {
            $this->foodUnit = FoodUnit::create([
                'name' => $this->name,
            ]);
            $message = 'الگو با موفقیت اضافه شد';
        }
        return redirect()->route('admin.food_unit.index')->with('success', $message);
    }

    public function render()
    {
        $title = (! is_null($this->foodUnit) && $this->foodUnit->exists) ? 'ویرایش وعده' : 'ایجاد وعده جدید';

        return view('diet::livewire.admin.food-unit.food-unit-create-or-update')->title($title);

    }
}
