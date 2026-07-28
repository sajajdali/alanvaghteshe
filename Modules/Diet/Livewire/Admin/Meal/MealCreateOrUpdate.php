<?php

namespace Modules\Diet\Livewire\Admin\Meal;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Diet\Entities\Meal;

class MealCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?Meal $meal = null;
    public string $name;
    public string $iconUrl;
    public string $priority;
    public $isEdited = false;
    protected $rules = [
        'name' => 'required|string',
        'priority' => 'required|integer|min:1',
    ];
    protected $messages = [
        'name.required' => 'نام وعده را وارد کنید',
        'iconUrl.required' => 'وارد کردن ایکون اجباری است',
        'priority.integer' => 'مقدار الویت نمایش باید به صورت عددی باشد',
        'priority.required' => 'مقدار الویت نمایش باید وارد شود',
        'priority.min' => 'مقدار الویت نمایش حداقل باید ۱ باشد و کمتر نمیتواند باشد',
    ];

    public function mount()
    {
        $meal = request()->route('meal');
        if ($meal instanceof Meal){
            $this->authorize('update', $meal);
            $this->isEdited = true;
            $this->meal = $meal;
            $this->name = $this->meal->name ?? '';
            $this->iconUrl = $this->meal->icon_url ?? '';
            $this->priority = $this->meal->priority ?? 1;
        } else {
            $this->priority = Meal::maxOrder();
        }
    }

    public function updateOrCreate()
    {
            $this->validate();
        if ($this->isEdited) {
            $this->meal->update([
                'name' => $this->name,
                'icon_url' => isset($this->iconUrl) ? $this->iconUrl : '',
                'priority' => $this->priority,
            ]);
            $message = 'وعده با موفقیت ویرایش شد';
        } else {
            $this->meal = Meal::create([
                'name' => $this->name,
                'icon_url' => isset($this->iconUrl) ? $this->iconUrl : '',
                'priority' => $this->priority,
            ]);
            $message = 'وعده با موفقیت اضافه شد';
        }
        return redirect()->route('admin.meal.index')->with('success', $message);
    }


    public function render()
    {
        $title = (! is_null($this->meal) && $this->meal->exists) ? 'ویرایش وعده' : 'ایجاد وعده جدید';

        return view('diet::livewire.admin.meal.meal-create-or-update')->title($title);
    }
}
