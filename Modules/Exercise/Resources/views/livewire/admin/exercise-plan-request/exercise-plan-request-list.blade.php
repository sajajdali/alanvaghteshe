<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">درخواست ‌های برنامه</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه درخواست‌ها</h3>

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
                                    <input class="form-control" id="ID" wire:model.defer="search.id"
                                           placeholder="ایدی درخواست مورد نظر"
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
                                <th scope="col">کاربر</th>
                                <th scope="col">برنامه</th>
                                <th scope="col">تعداد جلسه</th>
                                <th scope="col">وضعیت</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($exercisePlanRequests->isNotEmpty())
                                @foreach($exercisePlanRequests as $plan_request)
                                    <tr>
                                        <td class="text-center">{{ $plan_request->id }}</td>
                                        <td>
                                            @if($plan_request->user)
                                                {{ $plan_request->user->full_name }}
                                            @else
                                                <span class="badge text-danger">
                                                کاربر یافت نشد
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plan_request->exercisePlanStrategy)
                                                {{ $plan_request->exercisePlanStrategy->name }}
                                            @else
                                                <span class="badge text-danger">
                                                برنامه یافت نشد
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $plan_request->session_count }}
                                        </td>
                                        <td>
                                            {!! $plan_request->status->getAdminBadge() !!}
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$plan_request)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$plan_request)
                                                            <li>
                                                                <a href="{{ route('admin.exercise_plan_request.edit',$plan_request) }}">مشاهده
                                                                    برنامه</a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$plan_request)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="برنامه"
                                                                   data-id="{{ $plan_request->id }}"
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
                        {{ $exercisePlanRequests->links() }}
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
