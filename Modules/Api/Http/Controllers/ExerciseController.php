<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\Exercise\ExerciseCollection;
use Modules\Api\Transformers\Exercise\ExerciseResource;
use Modules\Exercise\Entities\ExercisePlanRequest;
use Modules\Exercise\Enum\ExercisePlanRequestEnum;

class ExerciseController extends Controller
{
    use ApiHandlerTrait;

    public function plan(Request $request): JsonResponse
    {
        $user = $request->user();
        $exercises = $user->exercisePlanRequests()->active()->orderBy('id', 'desc')->paginate();

        return $this->ok(new ExerciseCollection($exercises));
    }

    public function planDetails(Request $request, ExercisePlanRequest $exercisePlanRequest): JsonResponse
    {
        $user = $request->user();
        //check if user is owner of this plan
        if ($user->id !== $exercisePlanRequest->user_id || $exercisePlanRequest->status->isNotOf([
                ExercisePlanRequestEnum::ENDED,
                ExercisePlanRequestEnum::COMPLETED
            ])) {
            return $this->notFound();
        }

        return $this->ok([
            'plan_with_detail' => ExerciseResource::make($exercisePlanRequest)
        ]);
    }
}
