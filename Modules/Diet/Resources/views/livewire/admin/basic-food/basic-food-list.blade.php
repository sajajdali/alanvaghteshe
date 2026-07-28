<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست غذاهای پایه اضافه شد</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Diet\Entities\BasicFood::class)
                <a href="{{ route('admin.basic_food.create') }}" class="btn btn-azure">افزودن غذای جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه تایپ ها</h3>

                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch">
                        <div class="alert alert-info">
                            نتایج یافت شده
                            {{$basicFoods->total()}}
                            مورد
                        </div>
                        <form class="form-horizontal example">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="ID" class="col-md-2 form-label">ایدی</label>
                                        <div class="col-md-10">
                                            <input class="form-control" id="id" wire:model.defer="search.id"
                                                   placeholder="ایدی درخواست مورد نظر"
                                                   type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="ID" class="col-md-2 form-label">نام گروه</label>
                                        <div class="col-md-10">
                                            <input class="form-control" id="name" wire:model.defer="search.name"
                                                   placeholder="نام نوع مورد نظر"
                                                   type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="food_type" class="col-md-2 form-label">نوع غذا</label>
                                        <div class="col-md-10">
                                            <select wire:model.defer="search.food_type"
                                                    class="form-control"
                                                    data-placeholder="انتخاب نشده" id="food_type">
                                                <option value="">انتخاب کنید</option>
                                                @foreach(\Modules\Diet\Enum\FoodTypeEnum::cases() as $foodTypeEnum)
                                                    <option
                                                        value="{{ $foodTypeEnum->value }}">{{$foodTypeEnum->getName()}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="main_nutrition" class="col-md-2 form-label">نوع تقسیم بندی</label>
                                        <div class="col-md-10">
                                            <select wire:model.defer="search.main_nutrition"
                                                    class="form-control"
                                                    data-placeholder="انتخاب نشده" id="main_nutrition">
                                                <option value="">انتخاب کنید</option>
                                                @foreach(\Modules\Diet\Enum\MainNutritionEnum::cases() as $mainNutritionEnum)
                                                    <option
                                                        value="{{ $mainNutritionEnum->value }}">{{$mainNutritionEnum->getName()}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="food_type" class="col-md-2 form-label">مربوط به وعده</label>
                                        <div class="col-md-10">
                                            <select wire:model.defer="search.meal"
                                                    class="form-control"
                                                    data-placeholder="انتخاب نشده" id="food_type">
                                                <option value="">انتخاب کنید</option>
                                                @foreach(\Modules\Diet\Entities\Meal::orderBy('priority')->get() as $meal)
                                                    <option
                                                        value="{{ $meal->id }}">{{$meal->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row mb-4">
                                        <label for="recipe" class="col-md-2 form-label">دستور پخت</label>
                                        <div class="col-md-10">
                                            <input class="form-control" id="id" wire:model.defer="search.recipe"
                                                   placeholder="دستور پخت"
                                                   type="text">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="row col-md-12 bg-gray-100 py-4">
                                    <div class="col-md-12 mb-4">
                                        <h4>شرایط و ویژگی ها</h4>
                                    </div>
                                    @foreach($conditions as $condition)
                                        <div class="col-md-6">
                                            <label for="ID"
                                                   class="col-md-12 form-label">{{$condition->key->getName()}}</label>
                                            <div class="col-md-12">

                                                @if($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                                    <select wire:model.defer="search.conditions.{{$condition->key}}"
                                                            class="form-control"
                                                            data-placeholder="انتخاب نشده" id="food_unit">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach($disease as $mealKey => $metaValue)
                                                            <option
                                                                value="{{ $metaValue->id }}">{{$metaValue->name}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select wire:model.defer="search.conditions.{{$condition->key}}"
                                                            class="form-control"
                                                            data-placeholder="انتخاب نشده" id="food_unit">
                                                        <option value="">انتخاب کنید</option>
                                                        @foreach($condition->options['items']['values'] as $optionKey => $optionValue)
                                                            <option value="{{ $optionKey }}">{{$optionValue}}</option>
                                                        @endforeach
                                                    </select>

                                                @endif
                                            </div>
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            <br>
                            <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled">جست و
                                جو
                            </button>
                        </form>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">نام نوع وعده</th>
                                <th scope="col">تقسیم بندی اصلی</th>
                                <th scope="col">کالری در ۱۰۰ گرم</th>
                                <th scope="col">ساده/ترکیبی</th>
                                <th scope="col">P</th>
                                <th scope="col">C</th>
                                <th scope="col">FAT</th>
                                <th scope="col">Fib</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($basicFoods->isNotEmpty())
                                @foreach($basicFoods as $basicFood)
                                    <tr>
                                        <td class="text-center">{{ $basicFood->id }}</td>


                                        <td>
                                            {{ $basicFood->name }}
                                        </td>
                                        <td>
                                            @if($basicFood->units()->count())
                                                {!!  $basicFood->units()->where('is_primary' , '1')->first()?->name ?? $basicFood->units()->first()?->name . '<br/> <span class="badge rounded-pill bg-info  my-1">پیش فرض ندارد</span>'  !!}
                                            @else
                                                <span class="badge rounded-pill bg-warning  my-1">بدون تقسیم بندی</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ validData($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::CALORIE->name]) }}
                                        </td>
                                        <td>
                                            {!!  $basicFood->type->getAdminBadge() !!}
                                        </td>

                                        {{--                                        <td>--}}
                                        {{--                                            {{ $basicFood->foodUnit->name }}--}}
                                        {{--                                            <span--}}
                                        {{--                                                class="tag tag-light">--}}
                                        {{--                                                {{ $basicFood->calories_per_unit }}--}}
                                        {{--                                                کالری در هر--}}
                                        {{--                                                                                            {{ $basicFood->foodUnit->name }}--}}
                                        {{--                                            </span>--}}
                                        {{--                                        </td>--}}
                                        <td>
                                            {{ isset($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::PROTEIN->name]) ?  validData($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::PROTEIN->name]) : ' - ' }}
                                        </td>
                                        <td>
                                            {{ isset($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::CARBOHYDRATE->name]) ? validData($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::CARBOHYDRATE->name]) : '-' }}
                                        </td>
                                        <td>
                                            {{ isset($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::FAT->name]) ?  validData($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::FAT->name])  : ' - '}}
                                        </td>
                                        <td>
                                            {{ isset($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::FIBER->name]) ?  validData($basicFood->food_fact[\Modules\Diet\Enum\FoodFactEnum::FIBER->name]) : ' - '}}
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$basicFood)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$basicFood)
                                                            <li>
                                                                <a href="{{ route('admin.basic_food.edit',$basicFood) }}">ویرایش
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$basicFood)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="حذف "
                                                                   data-id="{{ $basicFood->id }}"
                                                                   href="">حذف</a>
                                                            </li>
                                                        @endcan
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-default dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                </div>
                                            @endcanany
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ اطلاعاتی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $basicFoods->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@push('scripts')
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/admin.sweetalert.js')}}"></script>

    <script>
        var myCollapsible = document.getElementById('advanceSearch')
        myCollapsible.addEventListener('show.bs.collapse', function () {
        @this.set('searchPanel', 'show')
            ;
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
        @this.set('searchPanel', '')
            ;
        })
    </script>
@endpush
