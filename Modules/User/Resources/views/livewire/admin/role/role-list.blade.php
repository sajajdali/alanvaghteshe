<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت نقش‌ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\User\Entities\Role::class)
                <a href="{{ route('admin.role.create') }}" class="btn btn-azure">افزودن نقش جدید</a>
            @endcan
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h3 class="card-title">نکات مهم</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-info fade show p-0 mb-4" role="alert">
                    <p class="py-3 px-5 mb-0 border-bottom border-bottom-info-light">
                        <span class="alert-inner--icon me-2"><i class="fe fe-info"></i></span>
                        <strong>نکات مهم در رابطه با نقش‌ها</strong>
                    </p>
                    <div class="py-3 px-5">
                        <ul>
                            <li><i class="fa fa-angle-double-right mb-2 me-2"></i>سیستم به حداقل یک نقش کاربری پیشفرض
                                نیاز دارد.
                            </li>
                            <li><i class="fa fa-angle-double-right mb-2 me-2"></i>امکان حذف نقش کاربری پیشفرض وجود
                                ندارد. برای حذف این نقش ابتدا نقش کاربری پیشفرض جدید ایجاد کنید
                            </li>
                            <li><i class="fa fa-angle-double-right mb-2 me-2"></i>حداکثر یک نقش کاربری پیشفرض خواهد بود.
                            </li>
                            <li><i class="fa fa-angle-double-right mb-2 me-2"></i>امکان حذف نقش با شناسه ۱ وجود ندارد.
                            </li>
                            <li><i class="fa fa-angle-double-right mb-2 me-2"></i>
                                در صورت حذف یک نقش تمام کاربران آن به نقش کاربری پیشفرض منتقل خواهند شد.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- COL END -->

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">نقش‌ها</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap border-bottom" id="basic-datatable">
                            <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0">شناسه</th>
                                <th class="wd-15p border-bottom-0">عنوان</th>
                                <th class="wd-20p border-bottom-0">کاربران نقش</th>
                                <th class="wd-20p border-bottom-0">نوع</th>
                                <th class="wd-25p border-bottom-0">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ number_format($role->users()->count()) }} کاربر</td>
                                    <td>
                                        @if($role->hasPermissionTo('ADMIN_ACCESS'))
                                            <span class="badge bg-primary my-1">نقش مدیریتی</span>
                                        @endif
                                        @if($role->hasPermissionTo('USER_ACCESS'))
                                            <span class="badge bg-danger my-1">نقش کاربری</span>
                                        @endif
                                        @if($role->hasPermissionTo('USER_DEFAULT'))
                                            <span class="badge bg-warning my-1">نقش پیشفرض</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->id === 1)
                                            <div class="btn-group mt-2 mb-2">
                                                <button type="button" class="btn btn-default dropdown-toggle"
                                                        data-bs-toggle="dropdown">
                                                    عملیات <span class="caret"></span>
                                                </button>
                                            </div>

                                        @else
                                            @canany(['update','delete'],$role)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$role)
                                                            @unless($role->hasPermissionTo('USER_DEFAULT'))
                                                                <li><a href="{{ route('admin.role.edit',$role) }}">ویرایش</a>
                                                                </li>
                                                            @else
                                                                <li><a class="admin_sweet_alert op-0-4"
                                                                       data-title="خطا"
                                                                       data-type="error"
                                                                       data-description="امکان ویرایش نقش پیشفرض وجود ندارد."
                                                                       href="#">ویرایش</a>
                                                                </li>
                                                            @endunless
                                                        @endcan
                                                        @can('delete',$role)
                                                            @unless($role->hasPermissionTo('USER_DEFAULT'))
                                                                <li><a class="delete_confirm_alert"
                                                                       data-label="نقش {{ $role->name }}"
                                                                       data-id="{{ $role->id }}"
                                                                       href="">حذف</a>
                                                                </li>
                                                            @else
                                                                <li><a class="admin_sweet_alert op-0-4"
                                                                       data-title="خطا"
                                                                       data-type="error"
                                                                       data-description="امکان حذف نقش پیشفرض وجود ندارد."
                                                                       href="#">حذف</a>
                                                                </li>
                                                            @endunless
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
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->
    <form action="" method="POST" id="delete_form" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')

    <!-- INTERNAL SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <!-- DATA TABLE JS-->
    <script src="{{admin_asset('plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{admin_asset('plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
    <script src="{{admin_asset('plugins/datatable/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{admin_asset('js/table-data.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/admin.sweetalert.js')}}"></script>
@endpush
