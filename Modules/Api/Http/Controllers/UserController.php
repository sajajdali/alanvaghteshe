<?php

namespace Modules\Api\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Api\Http\Requests\UserEditRequest;
use Modules\Api\Trait\ApiHandlerTrait;
use Modules\Api\Transformers\DiseaseResource;
use Modules\Api\Transformers\Notification\NotificationCollection;
use Modules\Api\Transformers\Package\PackageUserResource;
use Modules\Api\Transformers\Transaction\TransactionStatusResource;
use Modules\Api\Transformers\UserResource;
use Modules\Core\Entities\Disease;
use Modules\Transaction\Entities\Transaction;
use Modules\User\Entities\User;

class UserController extends Controller
{
    use ApiHandlerTrait;

    public function me(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->ok(
            data: [
                'user' => UserResource::make($request->user()),
                'user_package' => $request->user()->activePackage() != null ? PackageUserResource::make($request->user()->activePackage()) : null
            ]
        );
    }

    public function transaction($transaction_id)
    {
        $trans = Transaction::find($transaction_id);
        $user = request()->user();
        if ($trans == null || $trans->user->id <> $user->id){
            return $this->notFound();
        }
        return $this->ok(
            data: [
                'status' => true,
                'data' =>
                    TransactionStatusResource::make($trans)

            ]
        );
    }

    public function edit(UserEditRequest $request): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        if ($request->hasFile('avatar')) {
            //upload avatar with unique name UUID in avatar storage
            //make avatar directory in storage/app/public if not exist
            if (!Storage::disk('public')->exists('avatar')) {
                Storage::disk('public')->makeDirectory('avatar');
            }
            $user->avatar = url('storage/' . $request->file('avatar')->storeAs('avatar', \Str::UUID() . '.png',
                    'public'));
        }
        return $this->ok(
            data: [
                'user' => UserResource::make(User::find($user->id)),
            ]
        );
    }

    public function notification(Request $request): JsonResponse
    {
        //return list of notifications
        return $this->ok(
            data: new NotificationCollection($request->user()->notifications()->orderBy('created_at', 'desc')->paginate(10))
        );
    }

    public function notificationRead(Request $request): JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $request->notification_id)->first();
        if ($notification) {
            $notification->markAsRead();
            return $this->ok([
                'message' => 'success'
            ]);
        }
        return $this->notFound();
    }

    public function notificationReadAll(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return $this->ok([
            'message' => 'success'
        ]);
    }

    public function landing(Request $request)
    {
        $user = $request->user();
        $BMI = app('dietService')->computeCalorie($request->user());
        $data = [
            'title' => '  تخصیص برنامه',
            'header' => ' با برنامه به هدفت برس',
            'first_description' => [
                [
                    'text' => ' تو میتونی',
                    'color' => 'blue'
                ],
                [
                    'text' => '  با برنامه به ',
                    'color' => 'black'
                ],
                [
                    'text' => ' هدفی که داری ',
                    'color' => 'blue'
                ],
                [
                    'text' => '  برسی ',
                    'color' => 'black'
                ],
            ],
            'chart' => [
                [
                    'label' => 'شهریور ۱۴۰۳',
                    'value' => 120
                ],
                [
                    'label' => 'مهر ۱۴۰۳',
                    'value' => 116
                ],
                [
                    'label' => 'آبان ۱۴۰۳',
                    'value' => 110
                ],
                [
                    'label' => 'آذر ۱۴۰۳',
                    'value' => 95
                ],
            ],
            'after_chart' => ' با برنامه ای که برای شما در نظر گرفته ایم شما حتما به وزن ایده آل خود میرسید',
            'video' => 'https://sample-videos.com/video123/mp4/480/big_buck_bunny_480p_1mb.mp4',
            'after_video' => ' تمامی برنا های تجویز شده کاملا متناسب با شرایط شما میباشد',
            'bmi' => $BMI->BMI,
            'body_type' => $BMI->bodyType,
            'body_type_number' => $BMI->bodyTypeNumber,
            'weight_status' => $BMI->weightStatus,
            'bmi_message' => $this->bmiMessages($BMI->BMI),
            'diet_plan' => self::dietPlan($user, $BMI),
            'header_after_plan' => ' با برنامه به هدفت میرسی',
            'second_description' => [
                [
                    'text' => ' تو میتونی',
                    'color' => 'blue'
                ],
                [
                    'text' => '  با برنامه به ',
                    'color' => 'black'
                ],
                [
                    'text' => ' هدفی که داری ',
                    'color' => 'blue'
                ],
                [
                    'text' => '  برسی ',
                    'color' => 'black'
                ],
            ],
            'comments' => [
               [
                   'name' => ' رضا',
                   'comment' => ' خیلی برنامه شما عالی هست و ممنونم',
                   'avatar'    => 'https://mdbcdn.b-cdn.net/img/new/avatars/2.webp',
                   'star'    => 5
               ] ,
                [
                    'name' => ' احمد',
                    'comment' => ' خیلی برنامه شما عالی هست و ممنونم',
                    'avatar'    => 'https://mdbcdn.b-cdn.net/img/new/avatars/2.webp',
                    'star'    => 5
                ]
            ],
            'header_image' => ' تصویر یکی از مخاطبین',
            'before_after_image' => urlPublic('storage/before_and_after.png'),
            'description_of_the_end' => ' توضیحات انتهایی',



        ];
        return $this->ok($data);
    }

    public function bmiMessages($bmi): array
    {
        switch ($bmi) {
            case $bmi <= 18 :
                return [
                    'title' => ' کم وزنی',
                    'message'   => ' BMI شما کمتر از ۱۸ است، که ممکن است به عنوان یک نشانه‌ای از کمبود وزن تلقی شود. مهم است که با یک متخصص بهداشتی یا رژیم غذایی مشورت کنید تا بهترین راه حل‌ها را برای بهبود'
                ];
            case $bmi > 18 && $bmi < 25 :
                return [
                    'title' => ' وضعیت ایده آل',
                    'message'   => '  شما در محدوده وزن سالم هستید. برای حفظ این وضعیت، به یک رژیم غذایی سالم و فعالیت‌های ورزشی منظم ادامه دهید'
                ];
            case $bmi >= 25 && $bmi < 30 :
                return [
                    'title' => ' اضافه وزن',
                    'message'   => '  افزایش وزن شما به مرحله اضافه وزن رسیده است. برنامه‌ریزی برای افزایش فعالیت ورزشی و تنظیم رژیم غذایی خود را در نظر بگیرید'
                ];
            case $bmi >= 30 && $bmi < 35 :
                return [
                    'title' => ' چاقی درجه۱',
                    'message'   => '  شما در مرحله چاقی قرار دارید. مشاوره با متخصص تغذیه برای ایجاد برنامه‌ای مناسب برای کاهش وزن و بهبود وضعیت سلامتی خود مهم است'
                ];
            default :
                return [
                    'title' => ' چاقی درجه۲',
                    'message'   =>' شما در مرحله چاقی شدید یا بسیار چاق قرار دارید. توصیه می‌شود تحت نظر یک تیم بهداشتی متخصص قرار گیرید و گزینه‌های مختلف برای کاهش وزن را مطالعه کنید.'
                ];
        }
    }

    private function dietPlan(User $user, $bmi)
    {
        $tall = $user->tall;
        $after = round((($bmi->weightStatus->currentWeight / ($tall * $tall)) * 10000));

        if ($bmi->weightStatus->UnderweightOrOverweight == 1) {
            $days = $bmi->weightStatus->overWeight * 7;
        } else {
            $days = $bmi->weightStatus->underWeight * 7;
        }

        return [
            'bmi_now' => $bmi->BMI,
            'bmi_after' => $after,
            'days' => $days,
        ];
    }

    public function disease(Request $request): JsonResponse{
        $allDiseases = Disease::priority()->get();
        $userDiseases = $request->user()->diseases()->get();
        return $this->ok([
            'status' => true,
            'diseases' => DiseaseResource::collection($allDiseases),
            'user_diseases' => DiseaseResource::collection($userDiseases)
        ]);
    }
    public function storeDisease(Request $request): JsonResponse{
        $allDiseases = Disease::priority()->get();
        $newDiseases = $request->input('diseases');
        if ($newDiseases) {
            //new diseases should be in all diseases
            $newDiseases = array_intersect($newDiseases, $allDiseases->pluck('id')->toArray());
            $user = $request->user();
            $user->diseases()->sync($newDiseases);
        }
        return $this->ok([
            'status' => true,
            'diseases' => DiseaseResource::collection($allDiseases),
            'user_diseases' => DiseaseResource::collection($request->user()->diseases()->get())
        ]);
    }

}
