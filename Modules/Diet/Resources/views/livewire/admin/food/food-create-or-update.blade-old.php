<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش غذا' : 'افزودن غذای جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')
    <form wire:submit.prevent>
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">
                            {{ $isEdited ? 'ویرایش غذا' : 'افزودن غذا جدید' }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                <label for="name">نام غذا</label>
                                <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                       id="name"
                                       wire:model="form.name" placeholder="نام">
                                @error('form.name')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="parent_id">نوع تقسیم بندی</label>
                                <select class="form-control @error('form.food_unit_id') is-invalid @enderror"
                                        data-placeholder="انتخاب نشده" id="food_unit_id">
                                    <option value="">انتخاب کنید</option>
                                    @foreach($fetchData['foodUnits'] as $foodUnit)
                                        <option value="{{ $foodUnit->id }}"
                                                @if($form['food_unit_id'] == $foodUnit->id) SELECTED @endif>{{ $foodUnit->name }}</option>
                                    @endforeach
                                </select>
                                @error('form.food_unit_id')
                                <div id="validationuserName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                <label for="calories">کالری </label>
                                <input type="text" dir="ltr"
                                       class="form-control  @error('form.calories') is-invalid @enderror"
                                       id="calories"
                                       wire:model="form.calories" placeholder="calories">
                                @error('form.calories')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 col-lg-12 my-1">
                                <div class="example">
                                    <div class="form-group m-0">
                                        <p class="card-sub-title">جزئیات واحد های غذا</p>
                                        <div class="row ">
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="fiber">فیبر </label>
                                                <input type="text" dir="ltr"
                                                       class="form-control  @error('form.fiber') is-invalid @enderror"
                                                       id="fiber"
                                                       wire:model="form.fiber" placeholder="میزان فیبر">
                                                @error('form.fiber')
                                                <div id="validationName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="carbohydrate">کربو </label>
                                                <input type="text" dir="ltr"
                                                       class="form-control  @error('form.carbohydrate') is-invalid @enderror"
                                                       id="carbohydrate"
                                                       wire:model="form.carbohydrate" placeholder="میزان کربوهیدرات">
                                                @error('form.carbohydrate')
                                                <div id="validationName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="protein">پروتئین </label>
                                                <input type="text" dir="ltr"
                                                       class="form-control  @error('form.protein') is-invalid @enderror"
                                                       id="protein"
                                                       wire:model="form.protein" placeholder="میزان پروتئین">
                                                @error('form.protein')
                                                <div id="validationName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="fat">چربی </label>
                                                <input type="text" dir="ltr"
                                                       class="form-control  @error('form.fat') is-invalid @enderror"
                                                       id="fat"
                                                       wire:model="form.fat" placeholder="میزان چربی">
                                                @error('form.fat')
                                                <div id="validationName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                <div>
                                                    <label for="fat">وعده اصلی این غذا : </label>
                                                    <select
                                                        class="form-control @error('form.main_nutrition') is-invalid @enderror"
                                                        data-placeholder="انتخاب نشده" id="main_nutrition">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach(\Modules\Diet\Enum\MainNutritionEnum::cases() as $targetEnum)
                                                            <option value="{{ $targetEnum->value }}"
                                                                    @if($form['main_nutrition']==$targetEnum->value) SELECTED @endif>{{ $targetEnum->getName() }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('form.main_nutrition')
                                                <div id="main_nutrition"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mt-3">
                                <div class="col-12 ">
                                    <div class="@error('form.meals') is-invalid @enderror">
                                        <div class="form-group">
                                            <label class="form-label"> وعده هایی که این غذا میتواند در آن تجویز
                                                شود</label>
                                            <div class="selectgroup selectgroup-pills">
                                                @foreach($fetchData['mealList'] as $mealKey => $meal)
                                                    <label class="selectgroup-item">
                                                        <input type="checkbox" name="form.meals[]" value="{{$meal->id}}"
                                                               @if(in_array($meal->id , $form['meals'])) checked
                                                               @endif class="selectgroup-input"
                                                               wire:model.live="form.meals">
                                                        <span class="selectgroup-button">{{$meal->name}}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @error('form.meals')
                                    <div id="validationName"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="recipe" class="form-label"> دستور پخت
                                </label>
                                <textarea class="form-control mb-4 @error('form.recipe') is-invalid @enderror"
                                          wire:model="form.recipe" id="recipe" placeholder="دستور پخت"
                                          rows="3"></textarea>
                                @error('form.recipe')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                    <div
                                        class="toggle toggle-lg toggle-success my-1 @if($form['typeFood']) on @else off @endif"
                                        id="type_food">
                                        <span></span>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted m-0">این یک غذای ترکیبی است ؟</p>
                                    </div>
                                </div>
                            </div>


                            @if($form['typeFood'])
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                    @if($form['main_nutrition'] == null)
                                        <div class="alert alert-warning alert-dismissible fade show p-0 mb-4"
                                             role="alert">
                                            <p class="py-3 px-5 mb-0 border-bottom border-bottom-warning-light">
                                                <span class="alert-inner--icon me-2"><i
                                                        class="fe fe-alert-triangle"></i></span>
                                                <strong>وعده اصلی انتخاب نشده است</strong>
                                            </p>
                                            <p class="py-3 px-5">این غذا میتواند با غذایی که به عنوان وعده اصلی انتخاب
                                                کرده اید جایگزین هایش انتخاب شود
                                                <br>
                                                برای فعال شدن این گزینه باید یک وعده اصلی انتخاب کنید تا لیست وعده ها
                                                برای شما بارگذاری شود</p>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                    aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                </div>
                            @else
                                @if(in_array($form['main_nutrition'] , [\Modules\Diet\Enum\MainNutritionEnum::carb->value  , \Modules\Diet\Enum\MainNutritionEnum::protein->value]  ))
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">

                                        <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                            <div class="card-body">
                                                <p class="text-muted">
                                                    شما باید اینجا تایین کنید که با چه غذاهایی که در دسته بندی
                                                    {{$form['main_nutrition'] == \Modules\Diet\Enum\MainNutritionEnum::carb->value ? \Modules\Diet\Enum\MainNutritionEnum::protein->getName() : \Modules\Diet\Enum\MainNutritionEnum::carb->getName()}}
                                                    هستند میتواند جایگزین شود
                                                </p>
                                                <div class="example">
                                                    <div class="row">
                                                        <div class="col-lg-6 col-sm-12">
                                                            <label class="rdiobox" for="rdio-secondary-unchecked">
                                                                <input
                                                                    wire:model.live="form.can_replaced_with_all_foods"
                                                                    value="{{\Modules\Diet\Enum\FoodDetailEnum::can_replace_all_foods->value}}"
                                                                    type="radio"
                                                                    class="radio-secondary"
                                                                    id="rdio-secondary-unchecked">
                                                                <span>بتواند از تمامی غذاهای اضافه شده ساده جایگزین کند</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-lg-6 col-sm-12">
                                                            <label class="rdiobox" for="rdio-secondary">
                                                                <input
                                                                    wire:model.live="form.can_replaced_with_all_foods"
                                                                    value="{{\Modules\Diet\Enum\FoodDetailEnum::can_not_replace_all_foods->value}}"
                                                                    type="radio"
                                                                    class="radio-secondary" id="rdio-secondary">
                                                                <span>فقط از غذاهایی که تعیین میکنیم بتواند جایگزین کند</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @if($form['can_replaced_with_all_foods'] == \Modules\Diet\Enum\FoodDetailEnum::can_not_replace_all_foods->value)

                                                        @if($relatedFoods && count($relatedFoods))
                                                            <hr>
                                                            <div class="col-12 mb-3"
                                                                 wire:key="{{$form['main_nutrition']}}_'relatedFoods'"
                                                                 wire:ignore>
                                                                <div class="alert alert-info" role="alert">
                                                                <span class="alert-inner--icon me-2"><i
                                                                        class="fe fe-bell"></i></span>
                                                                    <span
                                                                        class="alert-inner--text"><strong>توجه کنید!</strong>
                                                        شما میتوانید از لیست غذاهایی که به صورت ساده و ترکیبی نبود و از بین
                                                            {!! $form['main_nutrition'] == \Modules\Diet\Enum\MainNutritionEnum::carb->value ? \Modules\Diet\Enum\MainNutritionEnum::carb->getName()  : \Modules\Diet\Enum\MainNutritionEnum::protein->getName()!!}
                                                            میباشند در قسمت زیر انتخاب کنید تا کاربر بتواند جایگزینی خو درا انجام دهد
                                                        </span>
                                                                </div>
                                                                <label for="related_food">انتخاب غذاهایی که میتوانند
                                                                    جایگزین
                                                                    شوند</label>
                                                                <select multiple dir="rtl" class="form-control"
                                                                        data-placeholder="انتخاب لیست غذاهایی که میتوانند جایگزین شوند"
                                                                        id=related_food>
                                                                    @foreach($relatedFoods as $relatedFood)
                                                                        <option value="{{ $relatedFood->id }}"
                                                                                @if(in_array($relatedFood->id,$form['related_foods'],false)) SELECTED @endif>
                                                                            {{ $relatedFood->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                {!!  implode(" , " , $relatedFoods->pluck('name')->toArray()) !!}

                                                            </div>
                                                        @else
                                                            <div class="col-12 mb-3">
                                                                <div class="alert alert-danger" role="alert">
                                                                <span class="alert-inner--icon me-2"><i
                                                                        class="fe fe-bell"></i></span>
                                                                    <span
                                                                        class="alert-inner--text"><strong>اخطار !</strong>
                                                        هیچ غذایی که به صورت ساده باشد و مربوط به
                                                            {!! $form['main_nutrition'] == \Modules\Diet\Enum\MainNutritionEnum::carb->value ? \Modules\Diet\Enum\MainNutritionEnum::protein->getName()  : \Modules\Diet\Enum\MainNutritionEnum::carb->getName()!!}
                                                           برای جایگزین کردن یافت نشد
                                                        </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>

                                            </div>

                                        </div>

                                        <hr>
                                        <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                            <div class="card-body">
                                                <p class="text-muted">
                                                    همچنین باید انخاب کنید که غذای
                                                    {{$form['main_nutrition'] == \Modules\Diet\Enum\MainNutritionEnum::carb->value ? \Modules\Diet\Enum\MainNutritionEnum::protein->getName() : \Modules\Diet\Enum\MainNutritionEnum::carb->getName()}}
                                                    همراه این غذا کدام غذا باشد
                                                </p>
                                                @if(!$relatedFoodsForThisChoices->count())
                                                    <div class="col-12 mb-3">
                                                        <div class="alert alert-warning" role="alert">
                                                                <span class="alert-inner--icon me-2"><i
                                                                        class="fe fe-bell"></i></span>
                                                            <span
                                                                class="alert-inner--text"><strong>اخطار !</strong>
                                                                هیچ غذایی که تیک مکمل خورده باشد و در حالت ساده هم باشد برای این غذا یافت نشد
                                                        </span>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="form-group">
                                                        <label class="form-label" for="default-dropdown">انتخاب غذای
                                                            مکمل و همراه </label>
                                                        <select name="country" class="form-control form-select"
                                                                id="default-dropdown"
                                                                wire:model="form.relatedFoodsForThisChoice"
                                                                data-bs-placeholder="Select Country">
                                                            <option value="">انخاب کنید</option>
                                                            @foreach($relatedFoodsForThisChoices as $relatedFoodsForThisChoice)
                                                                <option
                                                                    label="{{$relatedFoodsForThisChoice->name}}"
                                                                    value="{{$relatedFoodsForThisChoice->id}}">{{$relatedFoodsForThisChoice->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>

                                @endif

                            @endif

                            @endif


                        </div>


                    </div>
                </div>
            </div>
        </div>
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
                    <button type="submit" class="btn btn-primary"
                            wire:loading.class="bg-gray btn-loading disabled"
                            wire:click="updateOrCreate">
                        @if($isEdited)
                            ویرایش وعده
                        @else
                            ایجاد وعده
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>


<!-- Row -->
<!-- Row Closed -->


</div>



@push('scripts')
    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function () {


            $('#food_unit_id').select2({
                dir: "rtl",
                placeholder: 'انتخاب نشده',
                allowClear: true,
                width: '100%',
            });
            $('#food_unit_id').on('change', function (e) {
                var data = $('#food_unit_id').select2("val");
            @this.set('form.food_unit_id', data)
                ;
            });

            $('#main_nutrition').select2({
                dir: "rtl",
                placeholder: 'انتخاب نشده',
                allowClear: true,
                width: '100%',
            });
            $('#main_nutrition').on('change', function (e) {

                var data = $('#main_nutrition').select2("val");
            @this.set('form.main_nutrition', data)
                ;
            });

            $('#type_food').on('click', function () {
            @this.set('form.typeFood', $('#type_food').hasClass('on'))
                ;
            });


            Livewire.on('updateUi', () => {

                setTimeout(function () {
                    $('#related_food').select2({
                        dir: "rtl",
                        allowClear: true,
                        multiple: true,
                        searchInputPlaceholder: 'جستجو',
                        search: true,
                        width: '100%',
                    });
                    $('#related_food').on('change', function (e) {
                        var data = $('#related_food').select2("val");
                    @this.set('form.related_foods', data)
                        ;
                    });
                }, 50);
            });
            $('#related_food').select2({
                dir: "rtl",
                allowClear: true,
                multiple: true,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });
            $('#related_food').on('change', function (e) {
                var data = $('#related_food').select2("val");
            @this.set('form.related_foods', data)
                ;
            });


        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
