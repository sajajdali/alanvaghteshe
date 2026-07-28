<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\DiseaseResource;
use Modules\Core\Entities\Disease;

class DiseaseController extends Controller
{
    use ApiHandlerTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $diseases['diseases'] = DiseaseResource::collection(Disease::priority()->get());
        return $this->ok($diseases);
    }
}
