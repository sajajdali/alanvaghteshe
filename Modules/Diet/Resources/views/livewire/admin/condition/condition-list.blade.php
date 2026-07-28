<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست شرایط اضافه شد</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            @can('create', \Modules\Diet\Entities\Condition::class)
                <a href="{{ route('admin.condition.create') }}" class="btn btn-azure">افزودن شرایط جدید</a>
            @endcan
        </div>
    </div>


    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">نام الگو</th>
                                <th scope="col">اضافه شود به</th>
                                <th scope="col">تعداد فیلد ها</th>
                                <th scope="col">عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($conditions->isNotEmpty())
                                @foreach($conditions as $condition)
                                    <tr>
                                        <td class="text-center">{{ $condition->id }}</td>


                                        <td>
                                            {{ $condition->key->getName() }}
                                        </td>
                                        <td>
                                            @if(count($condition->apply_to))
                                                @foreach($condition->apply_to as $applyTo)
                                                    <span
                                                        class="badge rounded-pill bg-success my-1">{{\Modules\Diet\Enum\ConditionApplyItemEnum::getNameForCondition($applyTo)}}</span>

                                                    @if(!$loop->last)
                                                        -
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-info my-1"> {{ count($condition->options['items']) }}  عدد </span>
                                        </td>
                                        <td>
                                            @canany(['update','delete'],$condition)
                                                <div class="btn-group mt-2 mb-2">
                                                    <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                        عملیات <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu" role="menu">
                                                        @can('update',$condition)
                                                            <li>
                                                                <a href="{{ route('admin.condition.edit',$condition) }}">ویرایش
                                                                </a>
                                                            </li>
                                                        @endcan
                                                        @can('delete',$condition)
                                                            <li><a class="delete_confirm_alert"
                                                                   data-label="حذف "
                                                                   data-id="{{ $condition->id }}"
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
                                            هیچ دیتایی یافت نشد
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $conditions->links() }}
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
