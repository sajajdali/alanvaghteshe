<?php

namespace Modules\Diet\Livewire\Admin\Condition;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Enum\ConditionKeyEnum;

class ConditionCreateOrUpdate extends Component
{
    use AuthorizesRequests;

    public ?Condition $condition = null;

    public string $name;

    public array $form = [
        'applyTo' => [],
        'items' => [],
        'checked' => [],
        'conditionKey' => ConditionKeyEnum::NONE->value,
        'countItems' => 1,
    ];

    public bool $isEdited = false;

    protected array $rules = [
        'form.conditionKey' => 'required|not_in:'.ConditionKeyEnum::NONE->value,
        'form.applyTo' => 'required',
        'form.items' => 'required_unless:form.conditionKey,'.ConditionKeyEnum::DISEASE->value,
        'form.items.*' => 'required_unless:form.conditionKey,'.ConditionKeyEnum::DISEASE->value,
    ];

    protected array $messages = [
        'form.conditionKey.not_in' => 'حداقل باید یک ایتم را انتخاب کنید',
        'form.applyTo.required' => 'حداقل باید یکی از شرایط را انتخاب کنید',
        'form.countItems.required' => 'حداقل یک فیلد باید داشته باشد',
        'form.items' => 'باید ایتم وجود داشته باشد و متن هم داشته باشد',
        'form.items.required_without' => 'وارد کردن تمامی متن های ایتم ها ضروری است',
    ];

    public function addItems(): void
    {
        $this->form['countItems'] += 1;
        $this->form['checked'][count($this->form['items'])] = false;
    }

    public function deleteItem($itemSelected): void
    {
        $this->form['countItems'] -= 1;
        unset($this->form['items'][$itemSelected]);
        unset($this->form['checked'][$itemSelected]);
        $this->form['items'] = array_values($this->form['items']);
        $this->form['checked'] = array_values($this->form['checked']);
    }

    public function mount(): void
    {

        $condition = request()->route('condition');
        if ($condition instanceof Condition) {
            $this->authorize('update', $condition);
            $this->isEdited = true;
            $this->form['conditionKey'] = $condition->key->value;
            $this->form['applyTo'] = \Arr::flatten($condition->apply_to);
            $this->form['countItems'] = $condition->options['items']['keys'] ? count($condition->options['items']['keys']) : 1;
            $this->form['items'] = $condition->options['items']['values'];
            $this->form['checked'] = $condition->options['checked'];
        }
        //        dd($this->form);
    }

    public function updated($name, $value): void
    {
        if ($name == 'form.conditionKey') {
            if ($value == ConditionKeyEnum::DISEASE->value) {
                $this->form['countItems'] = 1;
                $this->form['items'] = [];
                $this->form['checked'] = [];
            }
        }
    }

    public function updateOrCreate()
    {
        $this->validate();


        $bodyRequest = [
            'key' => $this->form['conditionKey'],
            'options' => [
                'items' => [
                    'keys'  => array_keys($this->form['items']),
                    'values' => $this->form['items']
                ],
                'checked' => array_values($this->form['checked']),
            ],
            'apply_to' => $this->form['applyTo'],
        ];
        if ($this->isEdited) {
            $this->condition->update($bodyRequest);
            $message = ' شرایط با موفقیت ویرایش شد';
        } else {
            $this->condition = Condition::create($bodyRequest);
            $message = 'شرایط با موفقیت اضافه شد';
        }

        return redirect()->route('admin.condition.index')->with('success', $message);
    }

    public function render()
    {
        $title = (! is_null($this->condition) && $this->condition->exists) ? 'ویرایش شرایط' : 'ایجاد شرایط جدید';

        return view('diet::livewire.admin.condition.condition-create-or-update')->title($title);
    }
}
