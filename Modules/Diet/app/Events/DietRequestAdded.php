<?php

namespace Modules\Diet\app\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Diet\Entities\DietRequest;
use Modules\User\Entities\User;

class DietRequestAdded
{
    use Dispatchable,SerializesModels;

    public DietRequest $dietRequest;

    /**
     * @param DietRequest $dietRequest
     */
    public function __construct(DietRequest $dietRequest)
    {
        $this->dietRequest = $dietRequest;
    }


}
