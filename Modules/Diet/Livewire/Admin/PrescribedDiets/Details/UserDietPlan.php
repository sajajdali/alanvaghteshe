<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Component;
use Modules\Diet\Entities\DietPlan;
use Modules\Diet\Entities\DietRequest;

class UserDietPlan extends Component
{
    public $dietReqestId;
    public ?DietPlan $dietPlan;
    public DietRequest $dietRequest;



    public function mount()
    {
        $this->dietRequest = (DietRequest::find($this->dietReqestId));
        $this->dietPlan = $this->dietRequest->dietPlan ?? null;

    }


    public function render()
    {
        return view('diet::livewire.admin.prescribed-diets.details.user-diet-plan');
    }
}
