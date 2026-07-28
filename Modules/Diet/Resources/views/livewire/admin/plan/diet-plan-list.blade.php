<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست پلن های اضافه شد</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Diet\Entities\DietPlan::class)
                <a href="{{ route('admin.diet_plan.create') }}" class="btn btn-azure">افزودن پلن جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه پلن ها</h3>

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
                            {{$dietPlans->total()}}
                            مورد
                        </div>
                        <form class="form-horizontal example">
                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="id" wire:model.defer="search.id"
                                           placeholder="ایدی درخواست مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">نام پلن</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="name" wire:model.defer="search.name"
                                           placeholder="نام وعده مورد نظر"
                                           type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">تعداد روز</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="day_count" wire:model.defer="search.day_count"
                                           placeholder="نام وعده مورد نظر"
                                           type="text">
                                </div>
                            </div>


                            <div class="row mb-4">
                                <div class="row col-md-12 bg-gray-100">
                                    <div class="col-md-12">
                                        <hr>
                                        <h4>شرایط و ویژگی ها</h4>
                                    </div>
                                    @foreach($conditions as $condition)
                                        <div class="col-md-6">
                                            <label for="ID"
                                                   class="col-md-12 form-label">{{$condition->key->getName()}}</label>
                                            <div class="col-md-12">

                                                @if($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                                    <select wire:model.deger="search.conditions.{{$condition->key}}"
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
                                <th scope="col">نام پلن</th>
                                <th scope="col">نوع</th>
                                <th scope="col">تعداد روز</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($dietPlans->isNotEmpty())
                                @foreach($dietPlans as $dietPlan)
                                    <tr>
                                        <td class="text-center">{{ $dietPlan->id }}</td>


                                        <td>
                                            {{ $dietPlan->name }}
                                        </td>
                                        <td>
                                            @if($dietPlan->general_pattern)
                                                <span class="badge bg-primary rounded-pill ms-1">نمایش وعده ها</span>
                                            @else
                                                <span class="badge bg-danger rounded-pill ms-1">پلن رژیم دهی</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $dietPlan->general_pattern == 1  ? ' - ' : $dietPlan->day_count }}
                                        </td>

                                        <td>
                                            {!!  $dietPlan->status ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-danger">غیر فعال</span>' !!}
                                        </td>

                                        <td>
                                            @canany(['update','delete'],$dietPlan)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$dietPlan)
                                                            <li>
                                                                <a href="{{ route('admin.diet_plan.edit',$dietPlan) }}">ویرایش
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$dietPlan)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="حذف "
                                                                   data-id="{{ $dietPlan->id }}"
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
                                            هیچ موردی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $dietPlans->links() }}
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
