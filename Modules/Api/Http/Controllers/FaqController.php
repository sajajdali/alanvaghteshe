<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\FaqResource;
use Modules\Core\Entities\Faq;

class FaqController extends Controller
{
    use ApiHandlerTrait;

    public function index(): \Illuminate\Http\JsonResponse
    {
        $faqs['faqs'] = FaqResource::collection(Faq::priority()->get());
        return $this->ok(data: $faqs);
    }
}
