<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت ورزش‌ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Exercise\Entities\Exercise::class)
                <a href="{{ route('admin.exercise.create') }}" class="btn btn-azure">افزودن ورزش جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">ورزش‌ها</h3>
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
                                           placeholder="ایدی ورزش مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="first_name" class="col-md-2 form-label">نام</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="first_name" wire:model="search.name"
                                           placeholder="نام ورزش مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="gender" class="col-md-2 form-label">جنسیت</label>
                                <div class="col-md-10">
                                    <select name="gender" class="form-control form-select" id="gender"
                                            wire:model="search.gender">
                                        <option value="">جنست مورد نظر انتخاب کنید</option>
                                        <option value="1">مرد</option>
                                        <option value="2">زن</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="type" class="col-md-2 form-label">نوع</label>
                                <div class="col-md-10">
                                    <select name="type" class="form-control form-select" id="type"
                                            wire:model="search.type">
                                        <option value="">نوع حرکت مورد نظر انتخاب کنید</option>
                                        <option value="1">مادر</option>
                                        <option value="2">مکمل</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="level" class="col-md-2 form-label">سطح</label>
                                <div class="col-md-10">
                                    <select name="level" class="form-control form-select" id="level"
                                            wire:model="search.level">
                                        <option value="">سطح حرکت مورد نظر انتخاب کنید</option>
                                        <option value="1">آماتور</option>
                                        <option value="2">حرفه‌ای</option>
                                    </select>
                                </div>
                            </div>
                            @if($bodyCategories->isNotEmpty())
                                <div class="row mb-4">
                                    <label for="category" class="col-md-2 form-label">بخش‌های بدن</label>
                                    <div class="col-md-10">
                                        <select name="category" class="form-control form-select" id="category"
                                                wire:model="search.category">
                                            <option value="">انتخاب بخش بدن</option>
                                            @foreach($bodyCategories as $bodyCat)
                                                <option value="{{ $bodyCat->id }}">{{ $bodyCat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if($diseases->isNotEmpty())
                                <div class="row mb-4">
                                    <label for="diseases" class="col-md-2 form-label">حرکات ممنوعه بیماری</label>
                                    <div class="col-md-10">
                                        <select name="diseases" class="form-control form-select" id="diseases"
                                                wire:model="search.diseases">
                                            <option value="">انتخاب بیماری</option>
                                            @foreach($diseases as $disease)
                                                <option value="{{ $disease->id }}">{{ $disease->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
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
                                <th scope="col">نام ورزش</th>
                                <th scope="col">جنسیت</th>
                                <th scope="col">نوع</th>
                                <th scope="col">سطح</th>
                                <th scope="col">بخش‌های بدن</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($exercises->isNotempty())
                                @foreach($exercises as $exerciseItem)
                                    <tr wire:key="exercise_{{ $exerciseItem->id }}">
                                        <td>{{ $exerciseItem->id }}</td>
                                        <td>
                                            {{ $exerciseItem->name }}
                                            @if($exerciseItem->is_main)
                                                <span class="badge rounded-pill bg-primary my-1">ورزش اصلی</span>
                                            @else
                                                <span
                                                    class="badge rounded-pill bg-secondary my-1">ورزش قبل و بعد تمرین</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exerciseItem->is_for_men)
                                                <span
                                                    class="badge bg-success my-1 text-bold">مرد</span>
                                            @endif
                                            @if($exerciseItem->is_for_women)
                                                <span
                                                    class="badge bg-success my-1 text-bold">زن</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exerciseItem->is_base)
                                                <span
                                                    class="badge bg-danger my-1 text-bold">مادر</span>
                                            @endif
                                            @if($exerciseItem->is_complementary)
                                                <span
                                                    class="badge bg-danger my-1 text-bold">مکمل</span>
                                            @endif
                                        </td>
                                        <td>

                                            @if($exerciseItem->is_for_beginner)
                                                <span
                                                    class="badge bg-info my-1 text-bold">آماتور</span>
                                            @endif
                                            @if($exerciseItem->is_for_advanced)
                                                <span
                                                    class="badge bg-info my-1 text-bold">حرفه‌ای</span>
                                            @endif

                                        </td>
                                        <td>
                                            @if($exerciseItem->bodyCategories->isNotEmpty())
                                                @foreach($exerciseItem->bodyCategories as $cat)
                                                    <span
                                                        class="badge bg-warning-gradient my-1 text-bold">{{ $cat->name }}</span>
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$exerciseItem)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$exerciseItem)
                                                            <li>
                                                                <a href="{{ route('admin.exercise.edit',$exerciseItem) }}">ویرایش</a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$exerciseItem)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="ورزش"
                                                                   data-id="{{ $exerciseItem->id }}"
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
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ ورزشی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $exercises->links() }}
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
