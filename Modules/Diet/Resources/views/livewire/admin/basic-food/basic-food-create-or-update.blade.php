<div>

    <div class="page-header">
        <div>
            <h1 class="page-title"> {{ $isEdited ? 'ویرایش غذای پایه' : 'افزودن غذای پایه جدید' }}</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @if ($nextItem)
                <a href="{{ $nextItem }}" class="btn btn-radius btn-light"> <i class="fa  fa-angle-left"></i> بعدی</a>
            @endif
            @if ($prevItem)
                <a href="{{ $prevItem }}" class="btn btn-radius btn-light">قبلی <i class="fa  fa-angle-right"></i></a>
            @endif

        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <form wire:submit.prevent>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom d-flex justify-content-between">
                        <h3 class="card-title">
                            {{ $isEdited ? 'ویرایش غذای پایه' : 'افزودن غذای پایه جدید' }}
                        </h3>
                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                            wire:click="updateOrCreate">
                            @if ($isEdited)
                                ویرایش غذای پایه
                            @else
                                ایجاد غذای پایه
                            @endif
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 mb-3">
                                <label for="name">نام غذای پایه (الزامی)</label>
                                <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                    id="name" wire:model="form.name" placeholder="نام">
                                @error('form.name')
                                    <div id="validationName" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div class="example mb-4">
                            <div class="form-row">
                                @for ($i = 0; $i < count($form['food_categories']); $i++)
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">

                                        <label for="parent_id">گروه بندی مورد نظر </label>
                                        <div class="input-group">
                                            <select wire:model.live="form.food_categories.{{ $i }}"
                                                class="form-select @error('form.food_categories.' . $i) is-invalid @enderror"
                                                data-placeholder="انتخاب نشده" id="food_category">
                                                <option value="">انتخاب کنید</option>
                                                @foreach ($fetchData['foodCategories'] as $category)
                                                    <option value="{{ $category->id }}"
                                                        @if ($form['food_categories'] == $category->id) SELECTED @endif>
                                                        {{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                            @if ($i == 0)
                                                <button type="button" class="input-group-text btn btn-outline-success"
                                                    wire:click="addCategory">
                                                    <i class="fa fa-plus"></i></button>
                                            @else
                                                <button type="button" class="input-group-text btn btn-outline-danger "
                                                    wire:click="removeFoodCategory({{ $i }})">
                                                    <i class="fa fa-minus"></i></button>
                                            @endif
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        @foreach ($form['foodUnits'] as $numberListKey => $formUnitList)
                            <div class="example mb-4">
                                <div class="form-row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">

                                        <label for="parent_id">نوع تقسیم بندی

                                        </label>
                                        <div class="input-group">
                                            <select wire:model.live="form.food_unit.{{ $numberListKey }}"
                                                class="form-select @error('form.food_unit.' . $numberListKey) is-invalid @enderror"
                                                data-placeholder="انتخاب نشده" id="food_unit">
                                                <option value="">انتخاب کنید</option>
                                                @foreach ($formUnitList as $foodUnit)
                                                    <option value="{{ $foodUnit->id }}"
                                                        @if ($form['food_unit'] == $foodUnit->id) SELECTED @endif>
                                                        {{ $foodUnit->name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($numberListKey == 1)
                                                <button type="button" class="input-group-text btn btn-outline-success"
                                                    wire:click="addFoodUnit">
                                                    <i class="fa fa-plus"></i></button>
                                            @else
                                                <button type="button" class="input-group-text btn btn-outline-danger "
                                                    wire:click="removeFoodUnit({{ $numberListKey }})">
                                                    <i class="fa fa-minus"></i></button>
                                            @endif

                                        </div>
                                        @error('form.food_unit')
                                            <div id="validationuserName" class="invalid-feedback d-block">
                                                {{ $message }}</div>
                                        @enderror
                                        @error('form.isPrimary')
                                            <div id="validationuserName" class="invalid-feedback d-block">
                                                هیچ کدام به عنوان پیشفرض انتخاب نشده است!</div>
                                        @enderror
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                        <label for="parent_id">پیش فرض در نظر گرفته شود؟

                                        </label>
                                        <label class="rdiobox"><input wire:model="form.isPrimary" name="primary"
                                                value="{{ $numberListKey }}" type="radio"> <span>انخاب شده
                                                باشد</span></label>
                                    </div>
                                </div>
                                @if (isset($form['food_unit'][$numberListKey]) && $form['food_unit'][$numberListKey] != '')
                                    @php
                                        $foodUnitName = $formUnitList
                                            ->where('id', $form['food_unit'][$numberListKey])
                                            ?->first()->name;
                                    @endphp
                                    <div class="form-row">
                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                            <label for="name">مقدار در هر
                                                <span class="tag tag-light">{{ $foodUnitName }}</span>
                                                چند گرم است</label>
                                            <input type="number"
                                                class="form-control @error('form.quantity_per_unit.' . $numberListKey) is-invalid @enderror"
                                                id="name" wire:model="form.quantity_per_unit.{{ $numberListKey }}"
                                                placeholder="در واحد گرم وارد شود">

                                            @error('form.quantity_per_unit' . $numberListKey)
                                                <div id="validationName" class="invalid-feedback d-block">
                                                    {{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                            <label for="name">حداکثر تجویز</label>

                                            <select wire:model.live="form.max_allowed.{{ $numberListKey }}"
                                                dir="rtl" class="form-control">
                                                <option value="">نامحدود</option>
                                                @for ($b = 01; $b < 100; $b++)
                                                    <option value="{{ $b }}"> {!! $b !!}
                                                        {!! $foodUnitName !!}</option>
                                                @endfor
                                            </select>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <div
                            class="bg-light col-md-12 col-lg-12 my-1 p-0 mb-4 @error('form.foodFact') border border-danger @enderror">
                            <div class="example">
                                <div class="form-group m-0">
                                    <p class="card-sub-title">جزئیات واحد های غذا <span class="tag tag-light">بر پایه
                                            100 گرم</span></p>
                                    <div class="row ">
                                        @foreach (\Modules\Diet\Enum\FoodFactEnum::cases() as $foodFactEnum)
                                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-3">
                                                <label for="fiber">{{ $foodFactEnum->getName() }} </label>
                                                <input type="text" dir="ltr"
                                                    class="form-control   @error('form.foodFact.' . $foodFactEnum->name) is-invalid @enderror"
                                                    id="fiber"
                                                    wire:model="form.foodFact.{{ $foodFactEnum->name }}"
                                                    placeholder=" {{ camelCaseToSpace($foodFactEnum->value) }}">
                                                @error('form.foodFact.' . $foodFactEnum->name)
                                                    <div id="validationName" class="invalid-feedback d-block">
                                                        {{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="form-row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                <div class="main-toggle-group d-sm-flex align-items-center ms-0">

                                    <div class="toggle toggle-lg toggle-success my-1 @if (isset($form['typeFood']) && $form['typeFood'] == \Modules\Diet\Enum\BasicFoodTypeEnum::COMBINED->value) on @else off @endif"
                                        id="type_food">
                                        <span></span>
                                    </div>
                                    <div class="ms-2">
                                        <p class="text-muted m-0">این غذا فقط به عنوان ترکیبی استفاده شود</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <label for="recipe" class="form-label"> دستور پخت
                                </label>
                                <textarea class="form-control mb-4 @error('form.recipe') is-invalid @enderror" wire:model="form.recipe"
                                    id="recipe" placeholder="دستور پخت" rows="3"></textarea>
                                @error('form.recipe')
                                    <div id="validationName" class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
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

                            @foreach ($fetchData['conditions'] as $condition)
                                <div class="col-12 ">
                                    <div class="">
                                        <div class="form-group">
                                            <label class="form-label">{{ $condition->key->getName() }}</label>
                                            <div class="selectgroup selectgroup-pills">
                                                @if ($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                                    @foreach ($fetchData['disease'] as $mealKey => $metaValue)
                                                        <label class="selectgroup-item">
                                                            <input type="checkbox" name="form.foodRestriction[]"
                                                                value="{{ $metaValue->id }}"
                                                                class="selectgroup-input"
                                                                wire:model="form.conditions.{{ $condition->id }}.{{ $metaValue->id }}">
                                                            <span
                                                                class="selectgroup-button">{{ $metaValue->name }}</span>
                                                        </label>
                                                    @endforeach
                                                @else
                                                    @foreach ($condition->options['items']['values'] as $optionKey => $optionValue)
                                                        <label class="selectgroup-item">
                                                            <input type="checkbox" name="form.foodRestriction[]"
                                                                value="{{ $optionKey }}" class="selectgroup-input"
                                                                wire:model="form.conditions.{{ $condition->id }}.{{ $optionKey }}">
                                                            <span
                                                                class="selectgroup-button">{{ $optionValue }}</span>
                                                        </label>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                @unless ($loop->last)
                                    <hr>
                                @endunless
                            @endforeach
                            @error('*')
                                <div class="col-md-12 alert alert-danger fade  mb-2 show" role="alert">
                                    لطفا خطا های موجود را برطرف نمایید
                                </div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"
                                wire:loading.class="bg-gray btn-loading disabled" wire:click="updateOrCreate">
                                @if ($isEdited)
                                    ویرایش غذای پایه
                                @else
                                    ایجاد غذای پایه
                                @endif
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

@push('scripts')
    <!-- SELECT2 JS -->
    <script>
        $(document).ready(function() {
            $('#type_food').on('click', function() {
                @this.set('form.typeFood', $('#type_food').hasClass('on'));
            });
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
