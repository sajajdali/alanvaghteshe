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
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            {{ $isEdited ? 'ویرایش غذا' : 'افزودن غذا جدید' }}
                        </h3>

                        <button type="button"
                                class="btn btn-sm btn-outline-danger"
                                wire:click="clearFoodCaches"
                                wire:loading.attr="disabled">
                            🗑 پاک کردن کش‌ها
                        </button>
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
                            @unless($fetchData['parent_id'])
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <label for="name">گام تجویز بین غذاها </label>
                                    <select wire:model='form.interval_step'
                                                class="form-control @error('form.interval_step') is-invalid @enderror "
                                            name="interval_step" id="interval_step">
                                        <option value="">در نظر گرفته نشود...</option>
                                        @for ($i = 0; $i <= 20; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            @endunless
                        </div>
                        <div class="form-row">
                            <div class="table-responsive">
                                @if($fetchData['parent_id'])
                                    <div class="alert alert-warning">
                                        شما در حال ویرایش یکی از زیر دستورها هستید. در نظر داشته باشید در صورتی که
                                        دستور اصلی ویرایش یا حذف شود ، تمامی زیر دستور ها تغییر میکند
                                    </div>
                                @endif
                                <div class="alert alert-info">
                                    در صورتی که برای هرچ کدام از ایتم ها حداکثر مقدار تجویز را مشخص نکنید ، سیتم به صورت
                                    پیش فرض برای کمتری مقدار که
                                    در لیست موجود است ، حداکثر ۲۰ برابر را انتخاب میکند.
                                    <br>
                                    بهتر است شما حداقل برای یکی از ایتم ها حداکثر تعریف کنید
                                </div>
                                @error('form.main')
                                <div id="validationName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <table class="table  border text-nowrap text-md-nowrap">

                                    <tbody>
                                    @for($i = 0 ; $i < $form['countBasicFoods'] ; $i++ )
                                        <tr wire:key="food-row-{{ $form['foods'][$i]['row_key'] ?? $i }}">
                                            <td>{{$i+1}}</td>
                                            <td wire:ignore>
                                                <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                    <label for="name">نام غذا</label>

                                                    <select dir="rtl"
                                                            class="form-control select2-show-search form-select basic_foods"
                                                            data-row-key="{{ $form['foods'][$i]['row_key'] ?? '' }}"
                                                            data-placeholder="انتخاب کنید">
                                                        <option label="انتخاب کنید"></option>
                                                        @foreach($fetchData['BasicFoods'] as $basicFood)
                                                            <option  value="{{ $basicFood->id }}"

                                                                    @if(isset($form['foods'][$i]['basic_food_id']) && $basicFood->id == $form['foods'][$i]['basic_food_id'] ) SELECTED @endif>
                                                                {{ $basicFood->name }}
                                                                [{{$basicFood->food_fact['CALORIE']}} کالری]
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </td>
{{--                                            @unless($fetchData['parent_id'])--}}
{{--                                                <td>--}}
{{--                                                    <div--}}
{{--                                                        class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3 @error('form.main') is-invalid @enderror">--}}

{{--                                                        <label for="name">امکان گسترش</label>--}}
{{--                                                        <br>--}}
{{--                                                        @isset($form['foods'][$i]['unit_name'])--}}
{{--                                                            <input type="checkbox" wire:model="form.foods.{{$i}}.extend"--}}
{{--                                                                   name="extend"--}}

{{--                                                                   value="{{$form['foods'][$i]['extend'] ?? ''}}">--}}
{{--                                                        @endisset--}}
{{--                                                    </div>--}}
{{--                                                </td>--}}
{{--                                            @endunless--}}
                                            <td>
                                                <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                    <label for="name">میزان</label>

                                                    @isset($form['foods'][$i]['unit_name'])
                                                        <select wire:model.live="form.foods.{{$i}}.unit" dir="rtl"
                                                                class="form-control">
                                                            <option
                                                                value="{{ 0.25}}"> {!!  0.25 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                            <option
                                                                value="{{  0.5}}"> {!!   0.5 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                            <option
                                                                value="{{  0.75}}"> {!!  0.75 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                            @for($b = 01 ; $b < 40 ; $b++ )
                                                                <option
                                                                    value="{{$b}}"> {!!  $b !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .25}}"> {!!  $b +  .25 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .5}}"> {!!  $b +  .5 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .75}}"> {!!  $b +  .75 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                            @endfor
                                                        </select>
                                                    @endisset
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                    <label for="name">حداکثر تجویز</label>
                                                    @isset($form['foods'][$i]['unit_name'])
                                                        <select wire:model.live="form.foods.{{$i}}.max" dir="rtl"
                                                                class="form-control">
                                                            <option value=""> سیستمی</option>
                                                            @for($b = 01 ; $b < 40 ; $b++ )
                                                                @continue($b <$form['foods'][$i]['unit']  )
                                                                <option
                                                                    value="{{$b}}">   {!!  $b !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .25}}"> {!!  $b +  .25 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .5}}"> {!!  $b +  .5 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                                <option
                                                                    value="{{ $b +  .75}}"> {!!  $b +  .75 !!} {!!  $form['foods'][$i]['unit_name'] !!}</option>
                                                            @endfor
                                                        </select>
                                                    @endisset
                                                </div>
                                            </td>
                                            <td>
                                                <button wire:click="removeBasicFoodItem('{{ $form['foods'][$i]['row_key'] }}')" type="button"
                                                        class="btn btn-icon btn-danger">
                                                    <i class="fe fe-minus"></i>
                                                </button>

                                                @if($i == 0)
                                                    <button wire:click="addBasicFoodItem" type="button"
                                                            class="btn btn-icon btn-success ms-1">
                                                        <i class="fe fe-plus"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-row">
                            <h3>جزئیات :</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap">
                                    <tr>
                                        <td>حداقل کالری تجویزی</td>
                                        <td>
                                            <h3>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['min_calorie']}}</span>
                                                @if(isset($computData['maximum_amount_of_food']))
                                                    تا
                                                    <span
                                                        class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['maximum_amount_of_food'] * $computData['min_calorie']}}</span>

                                                @endif
                                                @if($this->getSmallestBasicFoodId() !== false)
                                                    <span
                                                        class="badge  my-1  font-weight-semibold bg-danger-transparent text-danger tx-15">( سیستمی تا ۲۰ برابر)</span>
                                                @endif
                                            </h3>
                                        </td>
                                        <td>حداکثر افزایش</td>
                                        <td>
                                            <h3>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['maximum_amount_of_food'] ?? $this->fetchData['max'] . ' برابر و سیستمی'}}</span>
                                                @if(isset($computData['maximum_amount_of_food']))
                                                    برابر
                                                @endif
                                            </h3>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td>پروتئین</td>
                                        <td>
                                            <h5>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['protein']}}</span>
                                                گرم
                                            </h5>
                                        </td>

                                        <td>کربوهیدرات</td>
                                        <td>
                                            <h5>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['carb']}}</span>
                                                گرم
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>فیبر</td>
                                        <td>
                                            <h5>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['fiber']}}</span>
                                                گرم
                                            </h5>
                                        </td>

                                        <td>چربی</td>
                                        <td>
                                            <h5>
                                                <span
                                                    class="badge font-weight-semibold bg-success-transparent text-danger tx-15">{{$computData['fat']}}</span>
                                                گرم
                                            </h5>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>دستور پخت</td>
                                        <td colspan="3">
                                            {!! $computData['recipe'] !!}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mt-3">
                                <div class="col-12 ">
                                    <div class="@error('form.meals') border border-danger @enderror ps-1">
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
                                         class="invalid-feedback d-block">{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>


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
                                        <label class="form-label"> {{$condition->key->getName()}} </label>
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

                            </div>
                            @unless($loop->last)
                                <hr>
                            @endunless
                        @endforeach
                        @error('*')
                        <div class="col-md-12 mb-3 alert alert-danger fade show" role="alert">
                            لطفا خطا موجود را برطرف کنید
                        </div>
                        @enderror
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

            Livewire.on('updateUi', () => {
                setTimeout(function () {
                    $('.basic_foods').select2({
                        dir: "rtl",
                        allowClear: false,
                        multiple: false,
                        searchInputPlaceholder: 'جستجو',
                        search: true,
                        width: '100%',
                    });
                }, 50);
            });

            $('.basic_foods').select2({
                dir: "rtl",
                allowClear: false,
                multiple: false,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });

            $('body').on('change', '.basic_foods', function () {

                var selectedOption = $(this).find(':selected');
                let value = selectedOption.val();
                let rowKey = $(this).data('row-key');
                @this.dispatch('addBasicFood', {basicFoodId: value, rowKey: rowKey});

            });

        });
    </script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('reload-page', () => {
                window.location.reload();
            });
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
