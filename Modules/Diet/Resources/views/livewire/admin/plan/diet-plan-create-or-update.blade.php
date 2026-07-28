<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش پلن' : 'افزودن پلن جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">

                    <div class="card-header border-bottom">
                        <h3 class="card-title">
                            {{ $isEdited ? 'ویرایش پلن' : 'افزودن پلن جدید' }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <form wire:submit.prevent>

                            <div class="form-row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                    <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                        <div
                                            class="toggle toggle-lg toggle-danger my-1 @if(isset($form['general_pattern']) && $form['general_pattern']) on @else off @endif"
                                            id="general_pattern">
                                            <span></span>
                                        </div>
                                        <div class="ms-2">
                                            <p class="text-muted m-0">به عنوا پیش فرض وعده های نمایشی برای کاربران در نظر گرفته شود</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-3">
                                    <label for="name">نام پلن (الزامی)</label>
                                    <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                           id="name"
                                           wire:model="form.name" placeholder="نام پلن">
                                    @error('form.name')
                                    <div id="validationName"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                @unless($form['general_pattern'])
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-3">
                                    <label for="name">نوع پلن</label>
                                    <select wire:model.live="form.food_type" name="country"
                                            class="form-control form-select"
                                            id="default-dropdown"
                                            data-bs-placeholder="نوع را انتخاب کنید">
                                        {{--                                        <option value="{{\Modules\Diet\Enum\FoodTypeEnum::SIMPLE->value}}">غذای پایه--}}
                                        {{--                                            (ورزشکاران)--}}
                                        {{--                                        </option>--}}
                                        <option value="{{\Modules\Diet\Enum\FoodTypeEnum::COMBINED->value}}">ترکیبی
                                            (کالری محور)
                                        </option>
                                    </select>
                                </div>
                                @endunless
                            </div>
                            @unless($form['general_pattern'])
                                <div class="form-row">
                                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                        <label for="name">تعداد روز این پلن (الزامی)</label>
                                        <input type="number"
                                               class="form-control @error('form.dayCount') is-invalid @enderror"
                                               id="name"
                                               wire:model="form.dayCount" placeholder="تعداد روز">
                                        @error('form.dayCount')
                                        <div id="validationDayCount"
                                             class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-xl-6 col-12 col-lg-6 col-md-6 col-sm-12 mb-3">
                                        <label for="reduced_calories">چند کالری اضافه کم شود ؟</label>
                                        <input type="number"
                                               class="form-control @error('form.reducedCalories') is-invalid @enderror"
                                               id="calorie_end"
                                               wire:model="form.reducedCalories" placeholder="شروع از کالری">
                                        @error('form.calorieEnd')
                                        <div id="reduced_calories"
                                             class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-xl-6 col-12 col-lg-6 col-md-6 col-sm-12 mb-3">
                                        <label for="validationTextarea">توضیحات </label>
                                        <textarea wire:model='form.detail.description' class="form-control" id="validationTextarea"
                                                  placeholder="متن توضیحات اضافی"></textarea>
                                        @error('form.detail.description')
                                        <div id="reduced_calories"
                                             class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endunless
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                    <div
                                        class="toggle toggle-lg toggle-success my-1 @if(isset($form['status']) && $form['status']) on @else off @endif"
                                        id="status">
                                        <span></span>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted m-0">این پلن فعال باشد ؟</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                <div class="example">
                                    @error('form.status_meal')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-inner--text">{{ $message }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror
                                    @foreach($fetchData['meals'] as $meal)
                                        <div class="">
                                            <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                                <div
                                                    class="toggle toggle-lg meal_status toggle-info my-1 @if( isset($form['status_meal'][$meal->id]) && $form['status_meal'][$meal->id]) on @else off @endif"
                                                    id="meal_status_{{$meal}}" data-id="{{$meal->id}}">
                                                    <span></span>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted m-0">وعده {{$meal->name}} فعال باشد</p>
                                                </div>
                                            </div>
                                            @if( isset($form['status_meal'][$meal->id]) && $form['status_meal'][$meal->id])

{{--                                                @unless($form['general_pattern'])--}}
{{--                                                    <div class=" col-lg-12 mt-5">--}}
{{--                                                        <label class="ckbox" for="handwritten_{{$meal->id}}">--}}
{{--                                                            <input type="checkbox" name="form_handwrittenـ{{$meal->id}}"--}}
{{--                                                                   wire:model.live="form.handwritten.{{$meal->id}}.status"--}}
{{--                                                                   id="handwritten_{{$meal->id}}"><span>وارد کردن به صورت دست نویس</span></label>--}}
{{--                                                    </div>--}}
{{--                                                @endunless--}}

                                                @if(isset($form['handwritten'][$meal->id]['status']) && $form['handwritten'][$meal->id]['status'] === true)
                                                    <div class=" col-lg-12 mt-5 example">
                                                        <div class="row">
                                                            <input class="form-control mb-4"
                                                                   wire:model="form.handwritten.{{$meal->id}}.title"
                                                                   placeholder="عنوان فیلد در اپلیکیشن"
                                                                   type="text">
                                                        </div>
                                                        <div class="row">
                                                        <textarea class="form-control mb-4"
                                                                  placeholder="متنی که میخواهید نوشته شود را وارد کنید"
                                                                  wire:model="form.handwritten.{{$meal->id}}.body"
                                                                  rows="3"></textarea>
                                                        </div>

                                                    </div>
                                                @endif

                                                @unless($form['general_pattern'])
                                                    <div class=" col-lg-12 mt-5">
                                                        <label class="ckbox" for="is_athlete_meal_{{$meal->id}}">
                                                            <input type="checkbox" name="form_is_athlete_meal_{{$meal->id}}"
                                                                   wire:model.live="form.detail.is_athlete_meal.{{$meal->id}}"
                                                                   id="is_athlete_meal_{{$meal->id}}"><span>برای ورزشکاران باشد (نمایش داده نشود)</span></label>
                                                    </div>
                                                    <div class=" col-lg-12 mt-5">
                                                        <label class="ckbox" for="specialed_{{$meal->id}}">
                                                            <input type="checkbox" name="form_special_{{$meal->id}}"
                                                                   wire:model.live="form.special_meal.{{$meal->id}}"
                                                                   id="specialed_{{$meal->id}}"><span>به عنوان یک وعده ویژه در نظر گرفته شود</span></label>
                                                    </div>
                                                @endunless



                                                @if(isset($form['special_meal'][$meal->id]) && $form['special_meal'][$meal->id] === true)
                                                    <div class="form-group m-0 bg-gray-100 p-5 mt-2">
                                                        <p class="card-sub-title">لطفا در جدول زیر انتخاب کنید که کدام
                                                            غذا ها میتواند تجویز شود</p>
                                                        <div class="row ">
                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
                                                                @if($fetchData['specialFoods'][$meal->id]->count())
                                                                    <div class="table-responsive">
                                                                        <table
                                                                            class="table text-nowrap text-md-nowrap table-bordered">
                                                                            <tbody>
                                                                            @foreach($fetchData['specialFoods'][$meal->id] as $baseFood)
                                                                                <tr>
                                                                                    <td width="20"><input
                                                                                            type="checkbox"
                                                                                            name="form.special_basic_food.{{$meal->id}}.{{$baseFood->id}}"
                                                                                            wire:model="form.special_basic_food.{{$meal->id}}.{{$baseFood->id}}"></label>
                                                                                    </td>
                                                                                    <td>{{$baseFood->name}}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                @else
                                                                    <div
                                                                        class="alert alert-warning alert-dismissible fade show"
                                                                        role="alert">
                                                                        <span class="alert-inner--text">غذایی برای این وعده وارد نشده</span>

                                                                    </div>
                                                                @endif
                                                            </div>

                                                        </div>

                                                    </div>
                                                @endif
                                                    <div class="form-group m-0 bg-gray-100 p-5 mt-2">
                                                        <p class="card-sub-title">جزئیات واحد های غذا</p>
                                                        <div class="row ">
                                                            @if($form['food_type'] == \Modules\Diet\Enum\FoodTypeEnum::SIMPLE->value)
                                                                <div
                                                                    class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                                    <label for="fibe_{{$meal->id}}r">فیبر </label>
                                                                    <input type="text" dir="ltr"
                                                                           class="form-control @error('form.fiber.'.$meal->id) is-invalid @enderror"
                                                                           id="fiber_{{$meal->id}}"
                                                                           wire:model="form.fiber.{{$meal->id}}"
                                                                           placeholder="میزان فیبر">
                                                                    @error('form.fiber.'.$meal->id)
                                                                    <div id="validationName"
                                                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div
                                                                    class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                                    <label for="carb_{{$meal->id}}">کربو </label>
                                                                    <input type="text" dir="ltr"
                                                                           class="form-control @error('form.carb.'.$meal->id) is-invalid @enderror"
                                                                           id="carb_{{$meal->id}}"
                                                                           wire:model="form.carb.{{$meal->id}}"
                                                                           placeholder="میزان کربوهیدرات">
                                                                    @error('form.carb.'.$meal->id)
                                                                    <div id="validationName"
                                                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div
                                                                    class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                                    <label
                                                                        for="protein_{{$meal->id}}">پروتئین </label>
                                                                    <input type="text" dir="ltr"
                                                                           class="form-control @error('form.protein.'.$meal->id) is-invalid @enderror"
                                                                           id="protein_{{$meal->id}}"
                                                                           wire:model="form.protein.{{$meal->id}}"
                                                                           placeholder="میزان پروتئین">
                                                                    @error('form.protein.'.$meal->id)
                                                                    <div id="validationName"
                                                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div
                                                                    class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                                    <label for="fat_{{$meal->id}}">چربی </label>
                                                                    <input type="text" dir="ltr"
                                                                           class="form-control @error('form.fat.'.$meal->id) is-invalid @enderror"
                                                                           id="fat_{{$meal->id}}"
                                                                           wire:model="form.fat.{{$meal->id}}"
                                                                           placeholder="میزان چربی">
                                                                    @error('form.fat.'.$meal->id)
                                                                    <div id="validationName"
                                                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                            @else
                                                                <div
                                                                    class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
                                                                    @if($form['general_pattern'])
                                                                        <label for="fat">درصد پیشنهادی به کاربر </label>
                                                                    @else
                                                                        <label for="fat">درصد کالری تجویزی (جمع کالری ها
                                                                            بهتر است
                                                                            ۱۰۰
                                                                            درصد باشد) </label>
                                                                    @endif
                                                                    <input type="number" dir="ltr"
                                                                           class="form-control @error('form.calorie.'.$meal->id) is-invalid @enderror"
                                                                           wire:model="form.calorie.{{$meal->id}}"
                                                                           placeholder="میزان کالری به درصد ">
                                                                    @error('form.calorie.'.$meal->id)
                                                                    <div id="validationName"  class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                                                </div>
                                                            @endif

                                                        </div>
                                                    </div>


                                            @endif
                                        </div>
                                        @if(!$loop->last)
                                            <hr>
                                        @endif
                                    @endforeach

                                    @error('form.calorie')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-inner--text">{{ $message }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror
                                    @error('form.all_errors')
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <span class="alert-inner--text">{{ $message }}</span>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    @enderror
                                </div>
                            </div>

                            <div x-data="{ showFastDetails: @entangle('form.detail.fasting.status') }" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                <div class="example">

                                    <div class="form-row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                            <div class="main-toggle-group d-sm-flex align-items-center ms-0">
                                                <div
                                                    @click="showFastDetails = !showFastDetails"
                                                    :class="showFastDetails ? 'toggle toggle-lg toggle-alert my-1 on' : 'toggle toggle-lg toggle-alert my-1 off'"
                                                    id="show_fasting">
                                                    <span></span>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted m-0">این رژیم فستینگ است؟</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div x-show="showFastDetails" id="fasting_detail_content">
                                        <hr>

                                        <div class="form-row">
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                                <label for="fasting_name">ساعت شروع فستینگ چند باشد؟</label>
                                                <input type="number" min="1" max="24"
                                                       class="form-control @error('form.detail.fasting.start_at') is-invalid @enderror"
                                                       id="fasting_name"
                                                       wire:model="form.detail.fasting.start_at" placeholder="ساعت شروع">
                                                @error('form.detail.fasting.start_at')
                                                <div id="validationDayCount"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                                <label for="fasting_name">تو این رژیم باید چند ساعت فست باشد؟</label>
                                                <input type="number" min="1" max="24"
                                                       class="form-control @error('form.detail.fasting.fasting_hours') is-invalid @enderror"
                                                       id="fasting_name"
                                                       wire:model="form.detail.fasting.fasting_hours"  placeholder="فقط ساعت باشد و دقیقه  وارد نشود">
                                                @error('form.detail.fasting.fasting_hours')
                                                <div id="validationDayCount"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ showApplicationDetails: @entangle('form.detail.application.status') }" class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                <div class="example">

                                    <div class="form-row">
                                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 m-3 ms-0">
                                            <div class="main-toggle-group d-sm-flex align-items-center ms-0">
                                                <div
                                                    @click="showApplicationDetails = !showApplicationDetails"
                                                    :class="showApplicationDetails ? 'toggle toggle-lg toggle-warning my-1 on' : 'toggle toggle-lg toggle-warning my-1 off'"
                                                    id="show_in_application">
                                                    <span></span>
                                                </div>
                                                <div class="ms-2">
                                                    <p class="text-muted m-0">به عنوان الگو در اپلیکیشن نمایش داده شود؟</p>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div x-show="showApplicationDetails" id="application_detail_content">
                                        <hr>
                                        <div class="form-row ">
                                            <div class="col-9 mb-3">
                                                <label for="application_image">آیکون الگو در اپلیکیشن</label>
                                                <input type="text" class="form-control @error('form.detail.application.image') is-invalid @enderror"
                                                       id="application_image"
                                                       wire:model="form.detail.application.image" placeholder="آدرس فایل آیکون ">
                                                @error('application_image')
                                                <div id="validationapplication_image"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <button
                                                data-for="application_image"
                                                data-variable="application_image"
                                                class="btn btn-primary select_file col-3 mb-3 mt-5"
                                                data-bs-target="#file-selector-modal"
                                                data-bs-toggle="modal"
                                                type="button">
                                                انتخاب فایل
                                            </button>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                                <label for="application_name">نام الگو در اپلیکیشن</label>
                                                <input type="text"
                                                       class="form-control @error('form.detail.application.name') is-invalid @enderror"
                                                       id="application_name"
                                                       wire:model="form.detail.application.name" placeholder="نام نمایشی در اپلیکیشن">
                                                @error('form.detail.application.name')
                                                <div id="validationDayCount"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                                <label for="application_name">ترتیب نمایش</label>
                                                <input type="number"
                                                       class="form-control @error('form.detail.application.priority') is-invalid @enderror"
                                                       id="application_order"
                                                       wire:model="form.detail.application.priority" placeholder="ترتیب نمایش">
                                                @error('form.detail.application.priority')
                                                <div id="validationDayCount"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-12 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="validationTextarea">توضیحات در اپلیکیشن</label>
                                                <textarea wire:model='form.detail.application.description' class="form-control" id="validationTextarea"
                                                          placeholder="متن توضیحات نمایشی در اپلیکیشن"></textarea>
                                                @error('form.detail.application.description')
                                                <div id="reduced_calories"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{--  coditions--}}
        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">انتخاب شرایط خاص</h3>
                </div>
                <div class="card-body">
                    <div class="row">

                        @foreach($fetchData['conditions'] as $condition)

                            <div class="col-12 ">
                                <div class="">
                                    <div class="form-group">
                                        <label class="form-label">{{$condition->key->getName()}}</label>
                                        <div class="selectgroup selectgroup-pills">
                                            @if($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                                @foreach($fetchData['disease'] as $mealKey => $metaValue)
                                                    <label class="selectgroup-item">
                                                        <input type="checkbox" name="form.foodRestriction[]"
                                                               value="{{$mealKey}}"
                                                               class="selectgroup-input"
                                                               wire:model="form.conditions.{{$condition->id}}.{{$metaValue->id}}">
                                                        <span class="selectgroup-button">{{$metaValue->name}}</span>
                                                    </label>
                                                @endforeach
                                            @else
                                                @foreach($condition->options['items']['values'] as $optionKey => $optionValue)
                                                    <label class="selectgroup-item">
                                                        <input type="checkbox" name="form.foodRestriction[]"
                                                               value="{{$optionKey}}"
                                                               class="selectgroup-input"
                                                               wire:model="form.conditions.{{$condition->id}}.{{$optionKey}}">
                                                        <span class="selectgroup-button">{{$optionValue}}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @error('form.meals')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            @unless($loop->last)
                                <hr>
                            @endunless
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
        {{--  coditions--}}

        <div class="col-md-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa fa-frown-o me-2" aria-hidden="true"></i> لطفا اخطارهای بوجود امده را برطرف کنید

                        </div>
                    @endif
                    @if($fetchData['meals']->count() == 0 || !isset($form['status_meal']) || count($form['status_meal']) == 0)
                        <div class="alert alert-danger mb-0" role="alert">
                            <span class="alert-inner--icon me-2"><i class="fe fe-slash"></i></span>
                            <span class="alert-inner--text">شما هنوز هیچ وعده ای تعریف نکرده اید و ابتدا باید حداقل یک وعده تعریف کنید تا این قسمت فعال شود</span>
                        </div>
                    @else
                        <button type="submit" class="btn btn-primary"
                                wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش پلن
                            @else
                                ایجاد پلن
                            @endif
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <livewire:admin::file-manager-modal/>

</div>

@push('scripts')
    <script>

        Livewire.on('select_file', (param) => {
            @this.set('form.detail.application.image', param.url)
            ;
            //close modal
            $('#file-selector-modal').modal('hide');
        });
        $(document).ready(function () {

            $('#status').on('click', function () {
            @this.set('form.status', $('#status').hasClass('on'))
                ;
            });

            $('#show_fasting').on('click', function () {
                @this.set('form.detail.fasting.status', $('#show_fasting').hasClass('on'))
                ;
            });

            $('#show_in_application').on('click', function () {
                @this.set('form.detail.application.status', $('#show_in_application').hasClass('on'))
                ;
            });

            $('#general_pattern').on('click', function () {
            @this.set('form.general_pattern', $('#general_pattern').hasClass('on'))
                ;
            });

            $('.meal_status').on('click', function () {
                let mealId = ($(this).attr('data-id'));
                let thisStatus = $(this).hasClass('on')
            @this.dispatch('status-meal', {mealIdSelected: mealId, status: thisStatus})
                ;
            });


        });
    </script>
@endpush
