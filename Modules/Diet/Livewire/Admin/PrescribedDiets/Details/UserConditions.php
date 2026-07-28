<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Component;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\Condition;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Enum\ConditionApplyItemEnum;
use Modules\Diet\Enum\ConditionKeyEnum;
use Modules\Diet\Service\DietService;

class UserConditions extends Component
{
    public $dietReqestId;
    public $conditions;
    public ?DietRequest $dietRequest;
    public $successFully = null;
    public bool $changeConditions = false;
    public ?array $disease;
    public array $fetchData = [];

    public array $form = [];
    protected $rules = [
        'form.name' => 'required|string',
    ];
    protected $messages = [
        'form.name.required' => 'نام غذا را وارد کنید',
    ];

    public function editConditions()
    {
        $this->successFully = null;
        $this->changeConditions = !$this->changeConditions;
    }

    public function mount()
    {

        $this->dietRequest = (DietRequest::find($this->dietReqestId));
        $this->conditions = $this->dietRequest->detail;
        $userConditions = $this->conditions['condition'];

        $this->fetchData['disease'] = Disease::active()->get();

        $this->fetchData['conditions'] = Condition::whereJsonContains('apply_to', ConditionApplyItemEnum::FOOD->value)->get();
        foreach ($this->fetchData['conditions'] as $value) {

            // remove all disease in list checked
            if ($value->key == ConditionKeyEnum::DISEASE) {
                continue;
            }
            // remove all disease in list checked

            foreach ($value->options['checked'] as $k => $check) {
                if (isset($userConditions[$value->id])) {
                    $arrayCheck = is_array($userConditions[$value->id]) ? $userConditions[$value->id] : [];
                } else {
                    $arrayCheck = [];
                }

                $this->form['conditions'][$value->id][$value->options['items']['keys'][$k]] = in_array($k, $arrayCheck) == true ?? false;
            }
        }

        $this->disease = [];
        if (!empty($this->conditions['condition']) && array_key_exists(4, $this->conditions['condition'])) {
            foreach ($this->conditions['condition'][4] as $diseasseId) {
                $this->disease[] = Disease::where('id', $diseasseId)->first()->name;
            }
        }
    }

    public function saveConditions()
    {
        $detail = $this->dietRequest->detail;

        if (!isset($detail['oldUserCondition'])) {
            $detail['oldUserCondition'] = $detail['condition'] ?? [];
        }

        $newConditions = [];
        foreach ($this->form['conditions'] as $keyCondition => $valueCondition) {
            $filteredArray = array_filter($valueCondition, function ($value) {
                return $value === true;
            });

            $newConditions[$keyCondition] = array_keys($filteredArray);
        }

        $detail['condition'] = $newConditions;

        $this->dietRequest->detail = $detail;
        $this->dietRequest->save();

        $this->conditions = $this->dietRequest->detail;;
        $this->successFully = 'شرایط کاربر با موفقیت ویرایش شد';
        $this->changeConditions = false;

        $this->disease = [];
        if (!empty($this->conditions['condition']) && array_key_exists(4, $this->conditions['condition'])) {
            foreach ($this->conditions['condition'][4] as $diseasseId) {
                $this->disease[] = Disease::where('id', $diseasseId)->first()->name;
            }
        }
    }

    public function render()
    {
        return view('diet::livewire.admin.prescribed-diets.details.user-conditions');
    }
}
