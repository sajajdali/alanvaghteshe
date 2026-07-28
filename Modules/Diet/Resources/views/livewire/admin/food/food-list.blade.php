<div>
    <div class="page-header">
        <div>
            @if($samleDietList == null)
                <h1 class="page-title">لیست غذاها های اضافه شد</h1>
            @else
                <h1 class="page-title">گام های تجویز</h1>
            @endif

            <a href="{{ route('admin.food.index', ['page' => request('old_page') ?? 1]) }}"
               class="btn btn-outline-info">
                بازگشت به لیست غذاها >>
            </a>
        </div>

        <div class="ms-auto pageheader-btn">
            @if($samleDietList == null)
                @can('create', \Modules\Diet\Entities\Food::class)
                    <a href="{{ route('admin.food.create') }}" class="btn btn-azure">افزودن غذای جدید</a>
                @endcan
            @else
                <a href="{{ route('admin.food.index') }}" class="btn btn-azure">بازگشت</a>
            @endif
        </div>
    </div>

    @include('admin::layouts.components.alert')

    @if($samleDietList)

        @for($i = 1; $i <= $iteration; $i++)
            <div class="col-xl-12 col-md-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">گام شماره {{$i}}</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <tbody>
                                @foreach($food->basicFoodPivot as $basicFood)
                                    @php
                                        // بدون query: استفاده از eager-loaded units
                                        $unitName = optional($basicFood->units->firstWhere('is_primary', 1))->name
                                            ?? optional($basicFood->units->first())->name;
                                    @endphp

                                    @if(isset($basicFood->pivot->maximum) && $basicFood->pivot->maximum < ($basicFood->pivot->quantity * $i))
                                        <tr>
                                            <td colspan="3" class="wp-15">
                                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                    <span class="alert-inner--text">
                                                        غذای {{$basicFood->name}} به حداکثر مقدار رسیده است
                                                    </span>
                                                    <br>
                                                    حداکثر مقدار {{$basicFood->pivot->maximum}} {{$unitName}} میباشد
                                                </div>
                                            </td>
                                        </tr>

                                        @php($i = $iteration)
                                        @break(1)
                                    @else
                                        <tr>
                                            <td class="wp-15"><b>{{$basicFood->name}}</b></td>
                                            <td>
                                                <code>{{$basicFood->pivot->quantity * $i}} {{$unitName}}</code>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        @endfor

    @else

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header border-bottom">
                        <h3 class="card-title">همه غذا ها</h3>

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
                                نتایج یافت شده {{$foods->total()}} مورد
                            </div>

                            <form class="form-horizontal example">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row mb-4">
                                            <label for="id" class="col-md-2 form-label">ایدی</label>
                                            <div class="col-md-10">
                                                <input class="form-control" id="id" wire:model.defer="search.id"
                                                       placeholder="ایدی درخواست مورد نظر" type="text">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row mb-4">
                                            <label for="name" class="col-md-2 form-label">نام غذا</label>
                                            <div class="col-md-10">
                                                <input class="form-control" id="name" wire:model.defer="search.name"
                                                       placeholder="نام وعده مورد نظر" type="text">
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
                                                        data-placeholder="انتخاب نشده"
                                                        id="food_type">
                                                    <option value="">انتخاب کنید</option>
                                                    @foreach($meals as $meal)
                                                        <option value="{{ $meal->id }}">{{ $meal->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row mb-4"></div>
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
                                                <label class="col-md-12 form-label">{{ $condition->key->getName() }}</label>
                                                <div class="col-md-12">
                                                    @if($condition->key->value == \Modules\Diet\Enum\ConditionKeyEnum::DISEASE->value)
                                                        <select wire:model.defer="search.conditions.{{ $condition->key }}"
                                                                class="form-control"
                                                                data-placeholder="انتخاب نشده">
                                                            <option value="">انتخاب کنید</option>
                                                            @foreach($disease as $metaValue)
                                                                <option value="{{ $metaValue->id }}">{{ $metaValue->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <select wire:model.defer="search.conditions.{{ $condition->key }}"
                                                                class="form-control"
                                                                data-placeholder="انتخاب نشده">
                                                            <option value="">انتخاب کنید</option>
                                                            @foreach(($condition->options['items']['values'] ?? []) as $optionKey => $optionValue)
                                                                <option value="{{ $optionKey }}">{{ $optionValue }}</option>
                                                            @endforeach
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <button class="btn btn-primary" type="button"
                                        wire:click="startSearch"
                                        wire:loading.class="bg-gray btn-loading disabled">
                                    جست و جو
                                </button>
                            </form>
                        </div>

                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                                <thead>
                                <tr>
                                    <th scope="col" class="text-center">
                                        <input id="selectAll" type="checkbox" wire:model.live="selectAll">
                                    </th>
                                    <th scope="col">id</th>
                                    <th scope="col">نام غذا</th>

                                    @if(!$parent_id)
                                        <th scope="col">دستورهای متفاوت</th>
                                    @endif

                                    <th scope="col">حداقل کالری</th>
                                    <th scope="col">حداکثر کالری</th>
                                    <th scope="col">تعداد وعده</th>
                                    <th scope="col">P</th>
                                    <th scope="col">C</th>
                                    <th scope="col">FAT</th>
                                    <th scope="col">Fib</th>
                                    <th scope="col">عملیات</th>
                                </tr>
                                </thead>

                                <tbody>
                                @if($foods->isNotEmpty())
                                    @foreach($foods as $food)
                                        <tr>
                                            <td rowspan="2" class="text-center">
                                                <input type="checkbox"
                                                       wire:model.live="selectedFoods"
                                                       value="{{ $food->id }}">
                                            </td>

                                            <td rowspan="2" class="text-center">{{ $food->id }}</td>

                                            <td>{{ $food->name }}</td>

                                            @if(!$parent_id)
                                                <td>
                                                    <a href="{{ route('admin.food.index', ['old_page' => $foods->currentPage(), 'parent_id' => $food->id]) }}"
                                                       class="btn btn-success">
                                                        {{ $food->children_count ?? 0 }} دستور
                                                    </a>
                                                </td>
                                            @endif

                                            <td>{{ $food->calories }}</td>
                                            <td>{{ $food->parent_id == null ? $food->max_calories : '-' }}</td>
                                            <td>{{ $food->basic_food_pivot_count }} عدد</td>

                                            <td>{{ $food->protein }}</td>
                                            <td>{{ $food->carb }}</td>
                                            <td>{{ $food->fat }}</td>
                                            <td>{{ $food->fiber }}</td>

                                            <td>
                                                @canany(['update','delete'],$food)
                                                    <div class="btn-group mt-2 mb-2">
                                                        <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                            عملیات <span class="caret"></span>
                                                        </button>

                                                        <ul class="dropdown-menu" role="menu">
                                                            @can('update',$food)
                                                                <li><a href="{{ route('admin.food.edit',$food) }}">ویرایش</a></li>

                                                                @unless($parent_id)
                                                                    <li>
                                                                        <a href="#"
                                                                           wire:confirm="آیا مطمئین هستید میخواهید یک کپی از این غذا ایجاد کنید؟"
                                                                           wire:click="duplicateRow({{ $food->id }})">
                                                                            کپی از غذا
                                                                        </a>
                                                                    </li>
                                                                @endunless
                                                            @endcan

                                                            @can('delete',$food)
                                                                <li>
                                                                    <a class="delete_confirm_alert"
                                                                       data-label="حذف"
                                                                       data-id="{{ $food->id }}"
                                                                       href="">
                                                                        حذف
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="btn-group mt-2 mb-2">
                                                        <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown">
                                                            عملیات <span class="caret"></span>
                                                        </button>
                                                    </div>
                                                @endcanany
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="border-right: 1px solid #eaedf1 !important" colspan="9">
                                                <ul>
                                                    @foreach (($complexResults[$food->id] ?? []) as $items)
                                                        <li style="padding-right: 10px; padding-bottom: 8px">
                                                            {!! $items !!}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="12" class="text-center">
                                            <div class="alert alert-info">هیچ درخواستی یافت نشد</div>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>

                            {{-- دکمه حذف فقط وقتی انتخاب داریم (مثل قبل) --}}
                            @if(count($selectedFoods))
                                @can('delete', \Modules\Diet\Entities\Food::class)
                                    <button wire:confirm="آیا از حذف اطمینان دارید؟"
                                            wire:click="deleteSelected"
                                            class="btn btn-danger">
                                        حذف موارد انتخاب شده ({{ count($selectedFoods) }})
                                    </button>
                                @endcan
                            @endif
                        </div>

                        <div>
                            {{ $foods->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    @endif
</div>

@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>

    <script>
        var myCollapsible = document.getElementById('advanceSearch')
        myCollapsible.addEventListener('show.bs.collapse', function () {
            @this.set('searchPanel', 'show');
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            @this.set('searchPanel', '');
        })
    </script>

    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('sampleDietChanged', event => {
                history.pushState(null, '', `/new-url/${event.foodId}`);
            });
        });
    </script>
@endpush
