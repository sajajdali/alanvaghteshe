<div>
    @include('admin::layouts.components.alert')
    <!-- Row -->
    <div class="row mt-4" id="user-profile">
        <div class="col-lg-12">
            <div class="card">
                <div class="wideget-user-tab">
                    <div class="tab-menu-heading">
                        <div class="tabs-menu1" wire:ignore>
                            <ul class="nav">
                                <li>
                                    <a href="#userInformation"
                                        class="@if (!session()->has('success') &&  !$form['activityLog']) active show @endif"
                                        data-bs-toggle="tab">اطلاعات
                                        کاربری</a>
                                </li>
                                <li>
                                    <a href="#editProfile" data-bs-toggle="tab">ویرایش اطلاعات </a>
                                </li>
                                <li>
                                    <a href="#addMetas" class=" @if (session()->has('success')) active show @endif"
                                        data-bs-toggle="tab">افزودن مشخصات </a>
                                </li>
                                <li>
                                    <a href="#supportCall" class="@if (isset($form['activityLog']) && $form['calling']) active show @endif"
                                        data-bs-toggle="tab">
                                        تماس های پشتیبانی
                                    </a>
                                </li>
                                <li>
                                    <a href="#userWhatsapp" class="@if (isset($form['user_whatsapp']) && $form['user_whatsapp']) active show @endif"
                                       data-bs-toggle="tab">
                                        پیغام واتس اپ
                                    </a>
                                </li>
                                <li>
                                    <a href="#foodConsumption" class="@if (isset($form['user_whatsapp']) && $form['user_whatsapp']) active show @endif"
                                       data-bs-toggle="tab">
                                        غذاهای خورده شده
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content">
                <div wire:ignore.self class="tab-pane @if (!session()->has('success') &&  !$form['activityLog']) active show @endif"
                    id="userInformation">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="table-responsive p-5">
                                <div class="card-title d-flex justify-content-between">
                                    <span>
                                        اطلاعات کاربری
                                    </span>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table border text-nowrap text-md-nowrap table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><strong>جنسیت:</strong>
                                                        {{ $user->gender ?? '--' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>هدف کلی:</strong>
                                                        {{ $user->metaOptionsNames('diet_plan_meta') }}
                                                        @if ($user->diet_plan_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("diet_plan_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("diet_plan_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("diet_plan_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("diet_plan_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->diet_plan_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong>وزن:</strong>
                                                        {{ $user->weightMeta?->last()->meta_value ?? '--' }}
                                                        @if ($user->weightMeta?->count() > 1)
                                                            <button type="button" wire:click='luchModal("weightMeta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading wire:target='luchModal("weightMeta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("weightMeta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("weightMeta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->weightMeta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>

                                                    <td><strong>برنامه هدف:</strong>
                                                        {{ $user->target_plan_meta?->last()?->meta_key->getOptionName($user->target_plan_meta?->last()->meta_value) ?? '--' }}
                                                        @if ($user->weightMeta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("target_plan_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("target_plan_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("target_plan_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("target_plan_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->target_plan_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>مصرف روزانه ی آب:</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->daily_water_consumption_meta?->last()?->meta_key->getOptionName($user->daily_water_consumption_meta->last()->meta_value) ?? '--' }}
                                                        @if ($user->daily_water_consumption_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("daily_water_consumption_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("daily_water_consumption_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("daily_water_consumption_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("daily_water_consumption_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->daily_water_consumption_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>نقاط ضعف بدن:</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->metaOptionsNames('weaknesses_body_meta') }}
                                                        @if ($user->weaknesses_body_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("weaknesses_body_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("weaknesses_body_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("weaknesses_body_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("weaknesses_body_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->weaknesses_body_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>چند وقته ورزش میکنی:</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->metaOptionsNames('how_much_experience_sports_meta') }}
                                                        @if (isset($weaknesses_body) && $user->how_much_experience_sports_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("how_much_experience_sports_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("how_much_experience_sports_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("how_much_experience_sports_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("how_much_experience_sports_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->how_much_experience_sports_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>میزان فعالیت:</strong>
                                                        {{ $user->metaOptionsNames('activity_per_week_meta') }}
                                                        @if ($user->activity_per_week_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("activity_per_week_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("activity_per_week_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("activity_per_week_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("activity_per_week_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->activity_per_week_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>چند روز در هفته ورزش میکنی :</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->metaOptionsNames('how_many_days_week_exercise_meta') }}
                                                        @if ($user->how_many_days_week_exercise_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("how_many_days_week_exercise_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("how_many_days_week_exercise_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("how_many_days_week_exercise_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("how_many_days_week_exercise_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->how_many_days_week_exercise_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table border text-nowrap text-md-nowrap table-striped">
                                            <tbody>
                                                <tr>
                                                    <td><strong>سن:</strong>
                                                        {{ $user->age() ?? '--' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>قد:</strong>
                                                        {{ $user->tallMeta->last()?->meta_value ?? '--' }}
                                                        @if ($user->tallMeta?->count() > 1)
                                                            <button type="button" wire:click='luchModal("tallMeta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading wire:target='luchModal("tallMeta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("tallMeta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("tallMeta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->tallMeta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>میزان کاهش وزن در هفته:</strong>
                                                        {{
                                                            match((int) $user->weight_change_per_week) {
                                                                0 => 'نیم کیلو در هفته',
                                                                1 => '۱ کیلو در هفته',
                                                                2 => '۱.۵ کیلو در هفته',
                                                                default => '--'
                                                            }
                                                        }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>وزن هدف:</strong>
                                                        {{ $user->targetWeightMeta->last()?->meta_value ?? '--' }}

                                                        @if ($user->targetWeightMeta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("targetWeightMeta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("targetWeightMeta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("targetWeightMeta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("targetWeightMeta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->targetWeightMeta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>چربی بدنی:</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->bodyFatMeta->last()?->meta_value ?? '--' }}
                                                        @if ($user->bodyFatMeta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("bodyFatMeta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("bodyFatMeta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("bodyFatMeta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("bodyFatMeta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->bodyFatMeta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>استایل بدنی :</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->metaOptionsNames('body_physical_style_meta') }}
                                                        @if ($user->body_physical_style_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("body_physical_style_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("body_physical_style_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("body_physical_style_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("body_physical_style_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->body_physical_style_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>محدودیت غذایی :</strong>
                                                        {{ $user->metaOptionsNames('food_restriction_meta') }}
                                                        @if ($user->food_restriction_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("food_restriction_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("food_restriction_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("food_restriction_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("food_restriction_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->food_restriction_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>هدف از ورزش کردن :</strong>
                                                        <span class="badge bg-warning">برای ورزش</span>
                                                        {{ $user->metaOptionsNames('target_of_exercise_meta') }}
                                                        @if ($user->target_of_exercise_meta?->count() > 1)
                                                            <button type="button"
                                                                wire:click='luchModal("target_fof_exercise_meta")'
                                                                class="btn  btn-outline-info ms-3 ">
                                                                <div wire:loading
                                                                    wire:target='luchModal("target_of_exercise_meta")'
                                                                    class="spinner-border spinner-border-sm text-info"
                                                                    role="status">
                                                                </div>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("target_of_exercise_meta")'>سابقه</span>
                                                                <span wire:loading.remove
                                                                    wire:target='luchModal("target_of_exercise_meta")'
                                                                    class="badge rounded-pill bg-info">{{ $user->target_of_exercise_meta?->count() - 1 }}</span>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>


                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card custom-card">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table border text-nowrap text-md-nowrap table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>بیماری های کاربر</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>
                                                                        @if ($user->diseases->isNotEmpty())
                                                                            @foreach ($user->diseases as $disease)
                                                                                {{ $disease->name }} @if (!$loop->last)
                                                                                    ,
                                                                                @endif
                                                                            @endforeach
                                                                        @else
                                                                            ---
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top"></div>
                            <div class="row row-sm">
                                <div class="col-lg-12">
                                    <div class="card custom-card">
                                        <div class="card-header border-bottom">
                                            <h3 class="card-title">وضعیت بدنی کاربر</h3>
                                        </div>
                                        <div class="card-body">
                                            @if ($userBmi)
                                                @if ($userBmi->status === false)
                                                    <div class="alert alert-danger" role="alert">
                                                        {{ $userBmi->message }}
                                                    </div>
                                                @else
                                                    <div class="table-responsive">
                                                        <table
                                                            class="table text-nowrap  text-md-nowrap table-bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <td>bmi</td>
                                                                    <td>{{ $userBmi->BMI }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>وضعیت بدنی</td>
                                                                    <td>{{ $userBmi->bodyType }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>وضعیت وزنی</td>
                                                                    <td>

                                                                        @if ($userBmi->weightStatus->UnderweightOrOverweight == 1)
                                                                            اضافه وزن
                                                                        @else
                                                                            کمبود وزن
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>میزان
                                                                        @if ($userBmi->weightStatus->UnderweightOrOverweight == 1)
                                                                            اضافه وزن
                                                                        @else
                                                                            کمبود وزن
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if ($userBmi->weightStatus->UnderweightOrOverweight == 2)
                                                                            {{ $userBmi->weightStatus->underWeight }}
                                                                        @else
                                                                            {{ $userBmi->weightStatus->overWeight }}
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>وزن منتاسب</td>
                                                                    <td>{{ $userBmi->weightStatus->currentWeight }}
                                                                    </td>
                                                                </tr>
                                                                {{--                                                            <tr> --}}
                                                                {{--                                                                <td>وضعیت</td> --}}
                                                                {{--                                                                <td>{{ $userBmi->bodyFatStatus }}</td> --}}
                                                                {{--                                                            </tr> --}}
                                                                <tr>
                                                                    <td>پروتئین مورد نیاز</td>
                                                                    <td>{{ $userBmi->unitsNeeded->protein }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>کربو مورد نیاز</td>
                                                                    <td>{{ $userBmi->unitsNeeded->carb }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>چربی مورد نیاز</td>
                                                                    <td>{{ $userBmi->unitsNeeded->fat }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>فیبر مورد نیاز</td>
                                                                    <td>{{ $userBmi->unitsNeeded->fiber }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>کالری مورد نیاز روزانه</td>
                                                                    <td>{{ $userBmi->base_calorie }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>کالری محاسبه شده</td>
                                                                    <td>{{ $userBmi->calorie }}</td>
                                                                </tr>
                                                                @if ($userBmi->body_physical_style !== '')
                                                                    <tr>
                                                                        <td>نوع استخوان بندی</td>
                                                                        <td>{{ Modules\User\Enum\UserMetaEnum::tryFrom('14')->getOptionName($userBmi->body_physical_style) }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            @else
                                                <p class="text-muted">برای محاسبه روی کلید زیر بزنید</p>
                                            @endif
                                            <button wire:click="calculateBmi" wire:loading.class.remove="btn-success"
                                                class="btn btn-success ">
                                                <span wire:loading wire:target="calculateBmi"
                                                    class="spinner-grow spinner-grow-sm" role="status"
                                                    aria-hidden="true"></span>
                                                محاسبه BMI
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">

                                <div class="card-body">
                                    <div class="card custom-card">
                                        <div class="card-header border-bottom">
                                            <h3 class="card-title">وضعیت درآمد</h3>
                                        </div>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table text-nowrap text-md-nowrap table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th>میزان درآمد</th>
                                                    <th>{{ $referralStatics['total_benefit'] }} ریال</th>
                                                </tr>
                                                <tr>
                                                    <td>تعداد افراد معرفی شده</td>
                                                    <th><a href="{{route('admin.user.referral.index' , ['inviteFriend' => $user])}}"> {{ $referralStatics['number_invited_friends'] }} نفر</a></th>
                                                </tr>
                                                <tr>
                                                    <td>تعداد خرید های انجام شده</td>
                                                    <th><a href="{{route('admin.user.referral.index' , ['inviteFriend' => $user , 'payment' => 1])}}"> {{ $referralStatics['successful_referrals'] }} نفر</a> </th>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-sm">
                                <div class="col-lg-12">
                                    <div class="card custom-card">
                                        <div class="card-header border-bottom">
                                            <h3 class="card-title">ارسال پیغام برای کاربر</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="col-lg-12 col-md-12">
                                                @if (session()->has('success'))
                                                    <div class="alert alert-success alert-dismissible fade show"
                                                        role="alert">
                                                        {{ session('success') }}
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="alert" aria-label="Close"></button>
                                                    </div>
                                                @endif
                                                <p class="text-muted">این پیغام فقط در اپلیکیشن برای کاربر ارسال میشود
                                                </p>
                                                <div class="row row-xs form-group-wrapper">

                                                    <div class="col-md-6 mb-3">
                                                        <div class="main-form-group mb-2">
                                                            <input
                                                                class="form-control border-0 @error('title') is-invalid @enderror"
                                                                id="title" placeholder="عنوان پیام"
                                                                wire:model="title" type="text">
                                                            <label for="title" class="form-label mb-1">عنوان
                                                                پیام</label>
                                                        </div>
                                                        @error('title')
                                                            <span
                                                                class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="main-form-group mb-2">
                                                            <input
                                                                class="form-control border-0 @error('message') is-invalid @enderror"
                                                                id="message" placeholder="متن پیام"
                                                                wire:model="message" type="text">
                                                            <label for="message" class="form-label mb-1">متن
                                                                پیام</label>
                                                        </div>
                                                        @error('message')
                                                            <span
                                                                class="invalid-feedback d-block">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-12 mb-3">
                                                        <button wire:click="sendMessage"
                                                            wire:loading.class.remove="btn-success"
                                                            class="btn btn-success ">
                                                            <span wire:loading wire:target="sendMessage"
                                                                class="spinner-grow spinner-grow-sm" role="status"
                                                                aria-hidden="true"></span>
                                                            ارسال پیغام
                                                        </button>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12">
                                                        <div class="card">
                                                            <div class="card-header border-bottom">
                                                                <h3 class="card-title">پیغام‌های ارسالی</h3>
                                                            </div>
                                                            <div class="card-body">
                                                                @if ($messages && $messages->isNotEmpty())
                                                                    <ul class="task-list">
                                                                        @foreach ($messages as $message)
                                                                            <li>
                                                                                <i
                                                                                    class="task-icon {{ $message->is_read ? 'bg-success' : 'bg-danger' }}"></i>
                                                                                <h6
                                                                                    class="{{ $message->is_read ? 'text-success' : 'text-danger' }}">
                                                                                    {{ $message->title }}
                                                                                </h6>
                                                                                <p>{{ $message->body }}</p>
                                                                                <p class="text-muted fs-12">
                                                                                    {{ verta($message->created_at)->format('Y/m/d ساعت H:i') }}
                                                                                </p>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                @else
                                                                    <p class="text-muted">هیچ پیغامی برای نمایش وجود
                                                                        ندارد.</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top"></div>
                            <div class="p-5">
                                <h3 class="card-title">اطلاعات تماس</h3>
                                <div class="d-sm-flex">
                                    <div>
                                        <div class="main-profile-contact-list">
                                            <div class="media mx-2">
                                                <div class="media-icon bg-primary-transparent text-primary"><i
                                                        class="fe fe-phone fs-21"></i></div>
                                                <div class="media-body ms-1">
                                                    <span class="text-muted">موبایل</span>
                                                    <p class="mb-0"> {{ $user->mobile }} </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="main-profile-contact-list">
                                            <div class="media mx-2">
                                                <div class="media-icon bg-success-transparent text-success"><i
                                                        class="fe fe-slack fs-21"></i></div>
                                                <div class="media-body ms-2">
                                                    <span class="text-muted">email</span>
                                                    <p class="mb-0">{{ $user->email }} </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- diet and package section  --}}
                            <div class="border-top" style="opacity: 0.5"></div>
                            @if ($user->getApplicationSms())
                                <div class="p-5">
                                    @include('user::components.app-link-sms-log')
                                </div>
                            @endif
                            <div class="p-5">
                                <livewire:user::admin.user.user-documents.prescribed-diet :user="$user" />
                            </div>
                            <div class="border-top" style="opacity: 0.5"></div>
                            <div class="p-5">
                                <livewire:user::admin.user.user-documents.user-packages :user="$user" />
                            </div>
                            <div class="p-5">
                                <livewire:user::admin.user.user-documents.user-courses :user="$user" />
                            </div>
                            <div class="p-5">
                                <livewire:user::admin.user.user-transactions :user="$user" />
                            </div>
                            <div class="p-5">
                                <livewire:user::admin.user.user-documents.user-exercises :user="$user" />
                            </div>
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane" id="editProfile">
                    <div class="card">
                        @if ($mmsg)
                            <div class="alert alert-primary alert-dismissible fade show" role="alert"> <span
                                    class="alert-inner--text">{{ $mmsg }}</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span></button>
                            </div>
                        @endif
                        <div class="card-body border-0">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="username" class="form-label">نام </label>
                                        <input wire:model='first_name' type="text" class="form-control"
                                            id="username">
                                    </div>
                                    @error('first_name')
                                        <span class="text-info">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="firstname" class="form-label">نام خانوادگی</label>
                                        <input wire:model='last_name' type="text" class="form-control"
                                            id="firstname" placeholder="نام خانوادگی">
                                    </div>
                                    @error('last_name')
                                        <span class="text-info">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="validationCustom04" class="form-label">جنسیت</label>
                                    <select wire:model='gender' class="form-select" id="validationCustom04">
                                        <option value="">انتخاب کنید...
                                        </option>
                                        <option value="0">مرد
                                        </option>
                                        <option value="1">زن
                                        </option>
                                    </select>
                                    @error('gender')
                                        <span class="text-info">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mobile" class="form-label">شماره تماس</label>
                                        <input wire:model='mobile' type="text" class="form-control"
                                            id="mobile" placeholder="شماره تماس" value="Gilbert">
                                    </div>
                                    @error('mobile')
                                        <span class="text-info">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nickname" class="form-label">کلمه عبور</label>
                                        <input wire:model='password' type="text" class="form-control mb-1"
                                            id="nickname" placeholder="کلمه ی عبور" value="Noa">
                                        <span class="text-muted fs-6 px-1">*فقط در صورتی که مایل هستید کلمه عبور کاربر
                                            را تغییر دهید این فیلد را تکمیل نمایید</span>
                                    </div>
                                    @error('password')
                                        <span class="text-info">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-12 d-flex justify-content-end mt-4">
                                    <button wire:loading.attr="disabled" type="button" wire:click='editbasicInfo'
                                        class="btn btn-success">
                                        <span>ذخیره ی تنظیمات</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane @if (session()->has('success')) active @endif"
                    id="addMetas">
                    <div class="card">
                        <div class="card-body border-0">
                            <livewire:user::admin.user.user-documents.add-user-metas :user="$user" />
                        </div>
                    </div>
                </div>

                <div wire:ignore.self class="tab-pane @if (isset($form['whatsappUser']) && $form['whatsappUser']) active @endif"
                     id="userWhatsapp">
                    <div class="card">
                        <div class="card-body border-0">
                            <livewire:user::admin.user.user-documents.user-whatsapp :user="$user" />
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="tab-pane @if (isset($form['whatsappUser']) && $form['whatsappUser']) active @endif"
                     id="foodConsumption">
                    <div class="card">
                        <div class="card-body border-0">
                            <livewire:user::admin.user.user-documents.user-food-consumption :user="$user" />
                        </div>
                    </div>
                </div>

                <div wire:ignore.self class="tab-pane @if (isset($form['activityLog']) && $form['calling']) active @endif"
                    id="supportCall">
                    <div class="card">
                        <div class="card-body border-0">
                            @if (isset($form['calling']) && $form['calling'])
                                <div class="row px-5 mt-3 mb-5">
                                    <div class=" col-12 d-flex justify-content-between">
                                        <h4 class="my-3">
                                            خلاصه تماس
                                        </h4>
                                        <span class="badge rounded-pill bg-light-gradient my-1 text-white">
                                            <span>
                                                <span class="blink-soft"> در حال ثبت تماس.... </span>
                                                <i class="fa fa-phone ring" aria-hidden="true"></i>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="col-12 mt-3">
                                        <textarea rows="8" wire:model='form.log.description' class="form-control" id="validationTextarea"
                                            placeholder="خلاصه تماس گرفته شده با کاربر"></textarea>
                                        @error('form.log.description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                    </div>
                                    <div class="col-12 text-end mt-2">
                                        <button type="button" class="btn btn-success my-1 text-white"
                                                wire:click="storeDescriptionForCall"
                                                wire:loading.attr="disabled"
                                                wire:target="storeDescriptionForCall">
        <span wire:loading wire:target="storeDescriptionForCall"
              class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                            ذخیره توضیحات و اتمام مکالمه
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="row mt-4">
                                <div class="table-responsive mb-3">
                                    <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                                        <thead>
                                        <tr>
                                            <th>زمان</th>
                                            <th>بابت</th>
                                            <th>توسط</th>
                                            <th>توضیحات</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if ($user->logFor->isNotEmpty())
                                            @foreach ($user->logFor as $log)
                                                <tr>
                                                    <td>{{ verta($log->created_at)->format('Y/m/d ساعت H:i') }}</td>
                                                    <td>{{ $log->event->getName() }}</td>
                                                    <td>{{ $log->loggedBy->fullName }}</td>
                                                    <td class="break-anywhere">{!! nl2br($log->description)!!}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="8">
                                                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                        <span class="alert-inner--text">سابقه تماسی یافت نشد!!</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- COL-END -->
    </div>

    {{-- modal --}}
    <!-- Modal -->
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModaltitle">سوابق ثبت شده
                        <strong>{{ $modaldata?->name }}</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>تاریخ ایجاد</th>
                                    <th>مقدار</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($modaldata)

                                    @foreach ($modaldata as $item)
                                        <tr>
                                            <td>{{ verta($item->created_at)->format('Y/m/d ساعت H:i') }}</td>
                                            @if ($meta_is_json_array)
                                                @php
                                                    $meta_array = json_decode($item->meta_value, true);
                                                @endphp
                                                <td>
                                                    @if (count($meta_array) > 1)
                                                        @foreach ($meta_array as $key => $value)
                                                            {{ $item->meta_key?->getOptionName($value) ?? '--' }}
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        {{ $item->meta_key?->getOptionName($meta_array[0]) ?? '--' }}
                                                    @endif
                                                </td>
                                            @else
                                                <td>{{ $item->meta_key->getOptionName($item->meta_value) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">حله</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            var myModal = new bootstrap.Modal(document.getElementById('historyModal'), {
                keyboard: false
            })
            Livewire.on('lunchHistoryModal', function() {
                myModal.toggle();
            })
        });
    </script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                swal({
                    title: data.type === 'success' ? 'موفق' : 'خطا',
                    text: data.message ?? '',
                    type: data.type ?? 'info',
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        });
    </script>
@endpush
@push('styles')
    <style>
        /* Softer fade blink (recommended) */
        .blink-soft {
            display: inline-block;
            animation: blinkSoft 2s linear infinite;
            will-change: opacity;
        }

        @keyframes blinkSoft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        /* Optional: harder on/off blink
                .blink-hard { animation: blinkHard 0.8s steps(1, end) infinite; }
                @keyframes blinkHard { 50% { opacity: 0; } }
                */

        /* Make the phone icon “ring” a bit */
        .ring {
            display: inline-block;
            transform-origin: 50% 55%;
            animation: phoneWiggle 0.7s ease-in-out infinite;
        }

        @keyframes phoneWiggle {

            0%,
            50%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(15deg);
            }

            75% {
                transform: rotate(-15deg);
            }
        }

        /* If you previously added a reduced-motion rule, remove it or comment it out.
                @media (prefers-reduced-motion: reduce) { .blink-soft, .ring { animation: none; } }
                */
    </style>
@endpush
