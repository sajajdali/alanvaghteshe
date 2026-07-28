<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت برنامه‌ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Exercise\Entities\ExercisePlanStrategy::class)
                <a href="{{ route('admin.exercise_plan_strategy.create') }}" class="btn btn-azure">افزودن برنامه
                    جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">برنامه‌های ورزشی</h3>
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
                                           placeholder="ایدی برنامه مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="name" class="col-md-2 form-label">نام</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="name" wire:model="search.name"
                                           placeholder="نام برنامه مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="session" class="col-md-2 form-label">تعداد جلسه</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="session" wire:model="search.session"
                                           placeholder="تعداد جلسه برنامه"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="gender" class="col-md-2 form-label">جنسیت</label>
                                <div class="col-md-10">
                                    <select name="gender" class="form-control form-select" id="gender"
                                            wire:model="search.gender">
                                        <option value="">جنست مورد نظر انتخاب کنید</option>
                                        @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum::cases() as $genderEnum)
                                            <option
                                                value="{{ $genderEnum->value }}">{{ $genderEnum->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="target" class="col-md-2 form-label">هدف برنامه</label>
                                <div class="col-md-10">
                                    <select name="target" class="form-control form-select" id="target"
                                            wire:model="search.target">
                                        <option value="">هدف برنامه را انتخاب کنید</option>
                                        @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum::cases() as $targetEnum)
                                            <option
                                                value="{{ $targetEnum->value }}">{{ $targetEnum->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="level" class="col-md-2 form-label">سطح</label>
                                <div class="col-md-10">
                                    <select name="level" class="form-control form-select" id="level"
                                            wire:model="search.level">
                                        <option value="">سطح برنامه مورد نظر انتخاب کنید</option>
                                        @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum::cases() as $levelEnum)
                                            <option
                                                value="{{ $levelEnum->value }}">{{ $levelEnum->getName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="status" class="col-md-2 form-label">وضعیت برنامه</label>
                                <div class="col-md-10">
                                    <select name="status" class="form-control form-select" id="status"
                                            wire:model="search.status">
                                        <option value="">وضعیت برنامه مورد نظر انتخاب کنید</option>
                                        <option value="1">
                                            فعال
                                        </option>
                                        <option value="0">
                                            غیرفعال
                                        </option>
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
                                <th scope="col">نام برنامه</th>
                                <th scope="col">جلسه در هفته</th>
                                <th scope="col">جنسیت</th>
                                <th scope="col">هدف</th>
                                <th scope="col">سطح</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($plans->isNotEmpty())
                                @foreach($plans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>
                                            {{ $plan->name }}
                                        </td>
                                        <td>
                                            {{ number_format($plan->session_count) }} جلسه
                                        </td>
                                        <td>
                                            {!! $plan->gender->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            {!! $plan->target->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            {!! $plan->level->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$plan)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button"
                                                            class="btn @if($plan->status) btn-success @else btn-danger @endif dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        @if($plan->status)
                                                            فعال
                                                        @else
                                                            غیرفعال
                                                        @endif
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$plan)
                                                            <li>
                                                                <a href="{{ route('admin.exercise_plan_strategy.edit',$plan) }}">ویرایش</a>
                                                            </li>
                                                            <li>
                                                                <a
                                                                    wire:click="copy({{ $plan->id }})"
                                                                    style="cursor: pointer"
                                                                >
                                                                    کپی برنامه</a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$plan)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="برنامه ورزشی"
                                                                   data-id="{{ $plan->id }}"
                                                                   href="">حذف</a>
                                                            </li>
                                                        @endcan
                                                        @can('create',\Modules\Exercise\Entities\ExercisePlanStrategy::class)
                                                            <li>
                                                                <a
                                                                    wire:click="createPlan({{ $plan->id }})"
                                                                    style="cursor: pointer">
                                                                    <span wire:loading.remove
                                                                          wire:target="createPlan({{ $plan->id }})">ایجاد برنامه</span>
                                                                    <div wire:loading
                                                                         wire:target="createPlan({{ $plan->id }})">
                                                                        در حال ایجاد...
                                                                    </div>
                                                                </a>
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
                        {{ $plans->links() }}
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
