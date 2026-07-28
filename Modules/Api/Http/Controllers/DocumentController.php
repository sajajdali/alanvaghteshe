<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Api\Http\Requests\DietRequest;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\Package\PackageResource;
use Modules\Api\Transformers\UserResource;
use Modules\User\Entities\UserMeta;
use Modules\User\Enum\UserActionEnum;
use Modules\User\Enum\UserMetaEnum;

class DocumentController extends Controller
{
    use ApiHandlerTrait;

    public function sendRequest(DietRequest $request): \Illuminate\Http\JsonResponse
    {

        $user = $request->user();
//        $user->route = 'payment2';
        $listMetas = [];
        foreach (UserMetaEnum::keys() as $key){
            $fieldKey = strtolower($key->name);
            if ($request->has($fieldKey)){
                $value = $request->get($fieldKey);

                //for set route address
                if ($key == UserMetaEnum::ROUTE){
                    userRoute($user, $value);
                    continue;
                }
                //for set route address

                if (is_array($value)){
                    $value = json_encode($value , JSON_UNESCAPED_UNICODE);
                }
                $listMetas[] = new UserMeta([
                    'meta_key' =>  UserMetaEnum::tryFrom($key->value),
                    'meta_value'    => $value
                ]);
            }

            // insert user diseases
            if ($request->has('diseases')){
                $userDiseases = $request->get('diseases');
                if (count($userDiseases) && is_array($userDiseases)){
                    $user->diseases()->sync($request->get('diseases'));
                }
            }
            // insert user diseases
        }
        if (count($listMetas)){
            $user->metas()->saveMany($listMetas);
        }

        if ($request->get('action') == UserActionEnum::REGISTER->value){
            return $this->ok([
                'user' => UserResource::make($user),
                'route' => userRoute($user),
                'packages' => PackageResource::collection(getPackages($request->get('target_weight_plan')))
            ]);
        }
        return $this->ok([
            'user' => $user ,
            'route' => userRoute($user)
        ]);
    }
}
