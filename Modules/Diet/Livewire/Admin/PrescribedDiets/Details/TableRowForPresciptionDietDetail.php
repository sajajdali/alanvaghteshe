<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Modules\Diet\Entities\DietRequestDetail;

class TableRowForPresciptionDietDetail extends Component
{
    public $detail_parent ;

    #[Computed]
    public function complexFoodResult($detail_parent) {
        //collection of the basic food that create a complex food ;
        $basicFoodCollection = $detail_parent->foodClass->basicFoodPivot ;
        $returnValue = [];
        if($basicFoodCollection->count() > 0 ) {
            foreach ($basicFoodCollection as $key => $basicFood) {
                $returnValue[$key] = ($basicFood->pivot->quantity  * $detail_parent->number_of_unit) . ' ' . ($basicFood->foodUnit->name) . ' ' . $basicFood->name ;
            }
        }
        return $returnValue ;
    }

    public function parentDietdetail($detail) {
        $parentd =  DietRequestDetail::find($detail->replaced_parent_id);
        return $parentd ;
      }
    public function render()
    {
        return view('diet::livewire.admin.prescribed-diets.details.table-row-for-presciption-diet-detail');
    }
}
