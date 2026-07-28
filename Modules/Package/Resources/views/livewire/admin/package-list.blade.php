<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت بسته‌ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Package\Entities\Package::class)
                <a href="{{ route('admin.package.create') }}" class="btn btn-azure">افزودن بسته جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">بسته‌ها</h3>
                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch">
                        <form class="form-horizontal example">
                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="ID" wire:model="search.id"
                                           placeholder="ایدی بسته مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="first_name" class="col-md-2 form-label">نام</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="first_name" wire:model="search.name"
                                           placeholder="نام بسته مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="days" class="col-md-2 form-label">دوره</label>
                                <div class="col-md-10">
                                    <select name="days" class="form-control form-select" id="days"
                                            wire:model="search.days">
                                        <option value="">دوره مورد نظر انتخاب کنید</option>
                                        <option value="30">۱ ماهه</option>
                                        <option value="90">۳ ماهه</option>
                                        <option value="180">۶ ماهه</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="status" class="col-md-2 form-label">وضعیت</label>
                                <div class="col-md-10">
                                    <select name="status" class="form-control form-select" id="status"
                                            wire:model="search.status">
                                        <option value="">وضعیت بسته مورد نظر انتخاب کنید</option>
                                        <option value="1">فعال</option>
                                        <option value="0">غیرفعال</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="type" class="col-md-2 form-label">نوع برنامه</label>
                                <div class="col-md-10">
                                    <select name="type" class="form-control form-select" id="type"
                                            wire:model="search.type">
                                        <option value="">نوع برنامه مورد نظر انتخاب کنید</option>
                                        @foreach(\Modules\Package\Enum\PackageTypeEnum::cases() as $typeEnum)
                                            <option value="{{ $typeEnum->value }}">{{ $typeEnum->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

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
                                <th scope="col">نام برنامهءء</th>
                                <th scope="col">دوره</th>
                                <th scope="col">قیمت</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">نوع برنامه</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($packages->isNotempty())
                                @foreach($packages as $package)
                                    <tr wire:key="package_{{ $package->id }}">
                                        <td>{{ $package->id }}</td>
                                        <td>
                                            {{ $package->name }}
                                            @if(isset($package->detail[\Modules\Package\Entities\Package::JSON_DETAIL_SUGGESTED]))
                                                <span
                                                    class="badge bg-danger my-1 text-bold">پیشنهادی</span>
                                            @endif

                                        </td>
                                        <td>
                                            {{ $package->month }} ماهه
                                        </td>
                                        <td>
                                            @if($package->has_special_price)
                                                <p class="mb-0">
                                                    <span class="text-18">
                                                        {{ number_format($package->special_price) }}
                                                        ریالء
                                                    </span> <span class="text-decoration-line-through text-muted ms-1">{{ number_format($package->price) }}
                                                    ریالء
                                                    </span>
                                                </p>
                                            @else
                                                <p class="mb-0"> <span class="text-18">
                                                        {{ number_format($package->price) }} ریال
                                                    </span>
                                                </p>
                                            @endif
                                        </td>
                                        <td>

                                            @if($package->is_active)
                                                <span
                                                    class="badge bg-success my-1 text-bold">فعال</span>
                                            @else
                                                <span
                                                    class="badge bg-danger my-1 text-bold">غیرفعال</span>
                                            @endif

                                        </td>
                                        <td>
                                            {!! $package->type->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$package)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">

                                                        @can('update',$package)
                                                            <li>
                                                                <a href="{{ route('admin.package.edit',$package) }}">ویرایش</a>
                                                            </li>
                                                        @endcan

                                                            @if($package->id != 4)
                                                            @can('update',$package)
                                                                <li>
                                                                    <a href="#" wire:click="setSuggested({{$package->id}})">
                                                                        بسته
                                                                        پیشنهادی
                                                                    </a>
                                                                </li>
                                                            @endcan

                                                            @can('delete',$package)
                                                                <li><a class="delete_confirm_alert"
                                                                       data-label="پکیج"
                                                                       data-id="{{ $package->id }}"
                                                                       href="">حذف</a>
                                                                </li>
                                                            @endcan
                                                        @endif
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
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ برنامه‌ای یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $packages->links() }}
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
