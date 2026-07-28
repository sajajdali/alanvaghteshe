<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست نکات تغذیه اضافه شد</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Diet\app\Models\NutritionTip::class)
                <a href="{{ route('admin.nutrition_tip.create') }}" class="btn btn-azure">افزودن نکته جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه وعده ها</h3>

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
                                    <input class="form-control" id="id" wire:model.defer="search.id"
                                           placeholder="ایدی درخواست مورد نظر"
                                           type="text">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">نکته</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="description" wire:model.defer="search.description"
                                           placeholder="نکته"
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
                                <th scope="col">نام وعده</th>
                                <th scope="col">اولویت نمایش</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($nutritionTips->isNotEmpty())
                                @foreach($nutritionTips as $nutritionTip)
                                    <tr>
                                        <td class="text-center">{{ $nutritionTip->id }}</td>


                                        <td>
                                            {{ $nutritionTip->name }}
                                        </td>
                                        <td>
                                            {!!  nl2br($nutritionTip->description)  !!}
                                        </td>

                                        <td>
                                            @canany(['update','delete'],$nutritionTip)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$nutritionTip)
                                                            <li>
                                                                <a href="{{ route('admin.nutrition_tip.edit',$nutritionTip) }}">ویرایش
                                                                    وعده
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$nutritionTip)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="حذف برنامه"
                                                                   data-id="{{ $nutritionTip->id }}"
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
                                    <td colspan="6" class="text-center">
                                        <div class="alert alert-info">
                                            هیچ درخواستی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $nutritionTips->links() }}
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
            @this.
            set('searchPanel', 'show')
            ;
        });
        myCollapsible.addEventListener('hide.bs.collapse', function () {
            @this.
            set('searchPanel', '')
            ;
        })
    </script>
@endpush
