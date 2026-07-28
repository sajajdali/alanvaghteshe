<?php

namespace Modules\Diet\app\Events;

use Illuminate\Queue\SerializesModels;

class UserFoodConsumptionUpdated
{
    use SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
