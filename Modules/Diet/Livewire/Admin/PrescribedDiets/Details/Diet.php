<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Component;
use Modules\Diet\Entities\DietPlan;

class Diet extends Component
{
    public $dietRequest;
    public $changeDiet = false;
    public $successFully = null;
    public $form = [];


    protected function rules()
    {
        return [
            'form.selectedPlan' => 'required',
        ];
    }
    protected function validationAttributes()
    {
        return [
            'form.selectedPlan' => 'پلن انتخابی',
        ];
    }

    public function toggleChangeDiet()
    {
        $this->successFully = null;
        $this->changeDiet = !$this->changeDiet;
    }

    public function changePlan()
    {
        $this->validate();
        $this->dietRequest->diet_plan_id = $this->form['selectedPlan'];
        $this->dietRequest->save();
        $this->changeDiet = false;
        return $this->successFully = 'پلن با موفقیت تغییر کرد';
    }

    public function render()
    {
        $dietPlans = DietPlan::active()->specificPattern()->get();
        return view('diet::livewire.admin.prescribed-diets.details.diet' , compact('dietPlans'));
    }
}
