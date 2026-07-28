<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست رژیم های تجویز شده</h1>
        </div>

    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه رژیم های تجویز شده</h3>
                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                        @if (isset($search['id']) ||
                                isset($search['name']) ||
                                isset($search['requestDate']) ||
                                isset($search['sendDate']) ||
                                isset($search['endDate']))
                            <button class="btn btn-secondary ms-2" type="button" wire:click="resetProperties"
                                wire:loading.class="bg-gray btn-loading disabled">نمایش همه
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse {{ $searchPanel }}" id="advanceSearch" wire:ignore>
                        <form class="form-horizontal example">
                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-id" wire:model="search.id"
                                        placeholder="ایدی رژیم مورد نظر" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class=" col-md-2  form-label">نام رژیم</label>
                                <div class="col-md-10" wire:ignore>
                                    <select id="search-name" wire:model="search.name"
                                        class="form-control js-example-basic-single form-select"
                                        data-placeholder="انتخاب کنید">
                                        <option label="انتخاب کنید"></option>
                                        @foreach (Modules\Diet\Entities\DietPlan::all() as $key => $dietPlans)
                                            <option value="{{ $dietPlans->id }}">{{ $dietPlans->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-requestDate" class="col-md-2 form-label ">تاریخ درخواست</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-requestDate" wire:model="dateSearch.requestDate"
                                        placeholder="انتخاب..." type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-sendDate" class="col-md-2 form-label">تاریخ ارسال</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-sendDate" wire:model="dateSearch.sendDate"
                                        placeholder="انتخاب..." type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-endDate" class="col-md-2 form-label">تاریخ اتمام</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-endDate" wire:model="dateSearch.endDate"
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
                                    <th scope="col">نام رژیم</th>
                                    <th scope="col">وضعیت</th>
                                    <th scope="col">میزان کالری</th>
                                    <th scope="col">تاریخ شروع</th>
                                    <th scope="col">تاریخ اتمام</th>
                                    <th scope="col">تاریخ درخواست</th>
                                    <th scope="col">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($diets->isNotEmpty())
                                    @foreach ($diets as $dietReqeust)
                                        <tr>

                                            <td class="text-center">{{ $dietReqeust->id }}</td>
                                            <td class="text-center"><a
                                                    href="{{ route('admin.presCribed-diets.detail', $dietReqeust) }}">
                                                    {!! $dietReqeust->dietPlan?->name ?? ' <span class="rounded-pill bg-warning p-1">یافت نشد</span>' !!} </a>
                                            </td>
                                            <td>
                                                {!! $dietReqeust->status->getName() !!}
                                            </td>
                                            <td>
                                                {{ number_format($dietReqeust->calories) }}
                                            </td>
                                            <td>
                                                {{ verta($dietReqeust->start_date)->format('Y/m/d') }}
                                            </td>
                                            <td>
                                                {{ verta($dietReqeust->end_date)->format('Y/m/d') }}
                                            </td>
                                            <td>
                                                {{ verta($dietReqeust->created_at)->format('Y/m/d') }}
                                            </td>

                                            <td>
                                                @canany(['update', 'delete'], $dietReqeust)
                                                    <div class="btn-group mt-2 mb-2">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            عملیات <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">

                                                            @can('requestDiet.detail', $dietReqeust)
                                                                <li>
                                                                    <a
                                                                        href="{{ route('admin.presCribed-diets.detail', $dietReqeust) }}">جزئیات
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('delete', $dietReqeust)
                                                                <li>
                                                            <button type="button" class="dropdown-item ms-2"
                                                                x-data
                                                                x-on:click="if (confirm('آیا از حذف این رژیم مطمئن هستید؟')) { $wire.deleteDiet({{ $dietReqeust->id }}) }">
                                                                حذف
                                                            </button>
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
                        {{ $diets->links() }}
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
        $(document).ready(function() {
            $('#search-requestDate').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format : 'L',
                onSelect: function(unix) {
                    @this.set('dateSearch.requestDate', $('#search-requestDate').val())
                }
            });
            $('#search-sendDate').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format : 'L',
                onSelect: function(unix) {
                    @this.set('dateSearch.sendDate',$('#search-sendDate').val())
                }
            });
            $('#search-endDate').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format : 'L',
                onSelect: function(unix) {
                    @this.set('dateSearch.endDate', $('#search-endDate').val())
                }
            });
            $('#search-name').select2();
        });
    </script>
@endpush
