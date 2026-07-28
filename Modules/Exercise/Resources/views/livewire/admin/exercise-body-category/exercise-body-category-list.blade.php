<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت دسته‌بندی بدن</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Exercise\Entities\ExerciseBodyCategory::class)
                <a href="{{ route('admin.exercise_body_category.create') }}" class="btn btn-azure">افزودن دسته جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه دسته‌ها</h3>

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
                                           placeholder="ایدی کاربر مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="name" class="col-md-2 form-label">نام دسته</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="name" wire:model="search.name"
                                           placeholder="نام دسته مورد نظر"
                                           type="text">
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
                                <th scope="col">نام دسته</th>
                                <th scope="col">تعداد ورزش</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($bodyCategories->isNotEmpty())
                                @foreach($bodyCategories as $cat)
                                    @include('exercise::livewire.admin.exercise-body-category.exercise-body-category-list-item',
                                      ['cat'=>$cat,
                                      'depth'=>0,
                                      'bg' => $rowBg[$loop->index % count($rowBg)]
                                      ])
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ دسته‌ای یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
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
