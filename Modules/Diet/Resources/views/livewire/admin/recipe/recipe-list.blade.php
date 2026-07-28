<div>
    <div class="page-header d-flex align-items-center justify-content-between">
        <h1 class="page-title mb-0">
            لیست پلن های اضافه شد
        </h1>

        <div class="d-flex align-items-center action-buttons">
            <button type="button" class="btn btn-outline-danger me-2"
                    wire:click="requestClearRecipeListCaches">
                <i class="fe fe-trash-2 me-1"></i>
                حذف کش لیست
            </button>

            <a href="{{ route('admin.recipe.create') }}" class="btn btn-primary">
                افزودن دستور غذای جدید
            </a>
        </div>
    </div>
    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه پلن ها</h3>

                    <div class="card-options">
                        @if(isset($form['search']) && (filled($form['search']['id'] ?? null) || filled($form['search']['name'] ?? null)))
                            <button class="btn btn-secondary me-2" type="button" data-bs-toggle="collapse"
                                    wire:click='ignoresearch'
                                    data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                                نمایش همه
                            </button>
                        @endif

                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-5 collapse" id="advanceSearch" wire:ignore.self>
                        <div class="alert alert-info">
                            نتایج یافت شده {{ $recipes->total() }} مورد
                        </div>

                        <form class="form-horizontal example" onsubmit="return false;">
                            <div class="row mb-4">
                                <label for="id" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="id"
                                           wire:model.debounce.500ms="form.search.id"
                                           placeholder="ایدی درخواست مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="name" class="col-md-2 form-label">نام پلن</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="name"
                                           wire:model.debounce.500ms="form.search.name"
                                           placeholder="نام وعده مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <br>

                            <button class="btn btn-primary" type="button"
                                    wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled">
                                جست و جو
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center"
                               wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">نام دستور</th>
                                <th scope="col">دسته بندی</th>
                                <th scope="col">کالری</th>
                                <th scope="col">یشنهاد شده</th>
                                <th scope="col">فعال</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if($recipes->isNotEmpty())
                                @foreach($recipes as $recipe)
                                    <tr>
                                        <td class="text-center">{{ $recipe->id }}</td>

                                        <td class="text-right">
                                            @php
                                                $imgUrl = $this->recipeImageUrl($recipe->detail ?? []);
                                            @endphp
                                            <img src="{{ $imgUrl }}"
                                                 alt="Recipe Image"
                                                 width="50"
                                                 height="50"
                                                 style="border-radius:6px;object-fit:cover;margin-left:8px;">

                                            {{ $recipe->name }}
                                        </td>

                                        <td>
                                            {{ $recipe->category?->name }}
                                        </td>

                                        <td>
                                            {{ $recipe->calorie }}
                                            <br>
                                            <span class="tag tag-light"> هر نفر : {{ round($recipe->calorie / 2) }}</span>
                                        </td>

                                        <td>
                                            <span class="badge {{ $recipe->getSuggestedColor() }}">
                                                {{ $recipe->suggested == true ? 'بله' : 'خیر' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge {{ $recipe->getBadgeColor() }}">
                                                {{-- عمداً همون رفتار قبلی شما نگه داشته شده --}}
                                                {{ $recipe->suggested == true ? 'بله' : 'خیر' }}
                                            </span>
                                        </td>

                                        <td>
                                            @canany(['update','delete'],$recipe)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>

                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$recipe)
                                                            <li>
                                                                <a href="{{ route('admin.recipe.edit',$recipe) }}">
                                                                    ویرایش
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('update',$recipe)
                                                            <li>
                                                                <a href="#"
                                                                   wire:confirm="آیا مطمئین هستید میخواهید یک کپی از این دستور پخت ایجاد کنید؟"
                                                                   wire:click="duplicateRecipe({{ $recipe->id }})">
                                                                    کپی از دستور پخت
                                                                </a>
                                                            </li>
                                                        @endcan

                                                        @can('delete',$recipe)
                                                            <li>
                                                                <a class="delete_confirm_alert"
                                                                   data-label="حذف"
                                                                   data-id="{{ $recipe->id }}"
                                                                   href="">
                                                                    حذف
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

                    <div class="d-flex justify-content-center">
                        {{ $recipes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .text-right { text-align: right !important; }
    </style>
@endpush

@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myCollapsible = document.getElementById('advanceSearch');
            if (myCollapsible) {
                myCollapsible.addEventListener('show.bs.collapse', function () {
                    @this.set('searchPanel', 'show');
                });
                myCollapsible.addEventListener('hide.bs.collapse', function () {
                    @this.set('searchPanel', '');
                });
            }

            Livewire.on('confirm-clear-recipe-list-caches', () => {
                if (confirm('کش‌های لیست دستور پخت پاک شود؟')) {
                    @this.call('clearRecipeListCaches');
                }
            });
        });
    </script>
@endpush
