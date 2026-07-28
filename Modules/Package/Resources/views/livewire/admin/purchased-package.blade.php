<div>
    <div>
        <div class="page-header">
            <div>
                <h1 class="page-title">لیست بسته های خریداری شده</h1>
            </div>

        </div>

        @include('admin::layouts.components.alert')

        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">همه بسته های خریداری شده</h3>
                        <div class="card-options">
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                                جست و جوی پیشرفته
                            </button>
                            @if (isset($search['id'])                   ||
                                    isset($search['name'])              ||
                                    isset($search['packageName'])       ||
                                    isset($search['UserName'])          ||
                                    isset($search['userPhoneNumber'])   ||
                                    isset($search['startDate'])         ||
                                    isset($search['endDate']))
                                <button class="btn btn-secondary ms-2" type="button" wire:click="resetProperties"
                                        wire:loading.class="bg-gray btn-loading disabled">نمایش همه
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch" wire:ignore>
                            <form class="form-horizontal example" autocomplete="off">
                                <div class="row mb-4">
                                    <label for="search-id" class="col-md-2 form-label">ایدی</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-id" wire:model="search.id"
                                               placeholder="ایدی رژیم مورد نظر" type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="search-name" class="col-md-2 form-label">نام بسته</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-name" wire:model="search.packageName"
                                               placeholder="نام رژیم" type="text">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="search-username" class="col-md-2 form-label">نام خانوادگی کاربر</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-username" wire:model="search.UserName"
                                               placeholder="نام کاربر" type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="search-mobileName" class="col-md-2 form-label">شماره ی کاربر</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-mobileName"
                                               wire:model="search.userPhoneNumber" placeholder="نام کاربر" type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="search-startDate" class="col-md-2 form-label ">تاریخ شروع</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-startDate" wire:model="search.startDate"
                                               placeholder="انتخاب..." type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="search-endDate" class="col-md-2 form-label">تاریخ اتمام</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="search-endDate" wire:model="search.endDate"
                                               placeholder="انتخاب..." type="text">
                                    </div>
                                </div>
                                <button class="btn btn-primary" type="button" wire:click="startSearch"
                                        wire:loading.class="bg-gray btn-loading disabled">جست و
                                    جو
                                </button>
                            </form>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered text-center"
                                   wire:loading.class="op-0-3">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">نام کاربر</th>
                                    <th scope="col">نام بسته</th>
                                    <th scope="col">وضعیت</th>
                                    <th scope="col">تاریخ شروع</th>
                                    <th scope="col">تاریخ اتمام</th>
                                    <th scope="col">عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if ($packages->isNotEmpty())
                                    @foreach ($packages as $package)
                                        <tr class="text-center">
                                            <td>{{ $package->id }}</td>
                                            <td>
                                                @if($package->user)
                                                    <a href="{{ route('admin.user.document', $package->user) }}">
                                                        {{ (\Modules\User\Entities\User::find($package->user->id)->full_name ?? '-') . ' [' . ($package->user->mobile ?? '-') . ']' }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td>{{ $package->package->name }}</td>
                                            <td>{!! $package->type->getAdminBadge() !!}</td>
                                            <td>{{ verta($package->start_at)->format('Y/m/d') }}</td>
                                            <td>{{ verta($package->end_at)->format('Y/m/d') }}</td>
                                            <td>
                                                @canany(['update', 'delete'], $package)
                                                    <div class="btn-group mt-2 mb-2">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                                data-bs-toggle="dropdown">
                                                            عملیات <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            @can('delete', $package)
                                                                <li><a class="delete_confirm_alert" data-label="حذف "
                                                                       data-id="{{ $package->id }}"
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
                            {{ $packages->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ admin_asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ admin_asset('plugins/sweet-alert/admin.sweetalert.js') }}"></script>

    <script>
        $(document).ready(function () {
            Livewire.on('closeCollaps', function () {
                $('#advanceSearch').removeClass('show');
            });
            $('#search-startDate').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format: 'L',
                onSelect: function (unix) {
                    @this.
                    set('search.startDate', $('#search-startDate').val());
                }
            });
            $('#search-endDate').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format: 'L',
                onSelect: function (unix) {
                    @this.
                    set('search.endDate', $('#search-endDate').val());
                }
            });
        });
    </script>
@endpush
