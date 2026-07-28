<?php

namespace Modules\Diet\Livewire\Admin\PrescribedDiets\Details;

use Livewire\Component;
use Modules\User\Entities\User;
use Modules\Core\Entities\Disease;
use Modules\Diet\Entities\DietRequest;
use Modules\Diet\Entities\DietRequestDetail;

class UserDetail extends Component
{
    public $dietReqestId;
    public ?array $userInfo;
    public User $assignedUser;
     public ?array $disease ;


    public function disease() {
        if(! empty($this->userInfo['DISEASES']))  {
            foreach ($this->userInfo['DISEASES'] as $key => $diseasseId) {
                $this->disease[] = Disease::where('id',$diseasseId)->first()->name ;
            }
        }
    }
    public function mount()
    {
        $dietRequest = (DietRequest::find($this->dietReqestId));
        $this->userInfo = $dietRequest->user_information;
        $this->assignedUser = $dietRequest->user;

        $this->disease();
        //  dd( $this->userInfo);
    }


    public function render()
    {
        return view('diet::livewire.admin.prescribed-diets.details.user-detail');
    }
}
