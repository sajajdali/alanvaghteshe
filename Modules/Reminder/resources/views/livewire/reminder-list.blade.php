<div>
    <div class="page-header mb-5">
        <div>
            <h1 class="page-title">تمامی یادآور ها</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <a class="btn btn-success" href="{{ route('admin.reminder.create') }}">اضافه کردن یادآور</a>
        </div>
    </div>
    <!-- PAGE-HEADER END -->
    @include('admin::layouts.components.alert')
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header d-flex justify-content-between border-bottom">
                    <h3 class="card-title">مدیریت یادآور ها</h3>
                    <div class="card-options">
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#advanceSearch" aria-expanded="false" aria-controls="advanceSearch">
                            جست و جوی پیشرفته
                        </button>
                        @if (isset($search['id']) || isset($search['sendtype']) || isset($search['status']))
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
                                        placeholder="ایدی یادآور مورد نظر" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-name" class="col-md-2 form-label">نوع ارسال</label>
                                <div class="col-md-10">
                                    <select wire:model='search.sendtype' name="country" class="form-control form-select"
                                        id="default-dropdown" data-bs-placeholder="انتخاب کنید..">
                                        <option label="انتخاب کنید"></option>
                                        @foreach (Modules\Reminder\Enum\ReminderStatusEnum::cases() as $sendType)
                                            <option value={{ $sendType }}>{{ $sendType->getName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="form-label col-md-2" for="default-dropdown">وضعیت</label>
                                <div class="col-md-10">
                                    <select wire:model='search.status' name="country" class="form-control form-select"
                                        id="default-dropdown" data-bs-placeholder="انتخاب کنید..">
                                        <option label="انتخاب کنید"></option>
                                        <option value={{ Modules\Reminder\Enum\ActiveEnum::ACTIVE }}>فعال
                                        </option>
                                        <option value={{ Modules\Reminder\Enum\ActiveEnum::DEACTIVE }}>
                                            غیرفعال</option>
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
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center"
                            wire:loading.class="op-0-3">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ارسال برای</th>
                                    <th scope="col">نوع یادآور</th>
                                    <th scope="col">زمان ارسال</th>
                                    <th scope="col">وضعیت</th>
                                    <th scope="col">عملیات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($Reminders->isNotEmpty())
                                    @foreach ($Reminders as $reminder)
                                        <tr class="text-center">
                                            <td>{{ $reminder->id }}</td>
                                            <td>
                                                <span>
                                                    {{ $reminder->send_for->getName() }}
                                                </span>
                                            </td>
                                            <td>{{ $reminder->status->getName() }}</td>

                                            <td>{{ $reminder->send_day . ' روز ' . $reminder->send_time . ' ساعت بعد' }}</td>
                                            <td>
                                               <span class="{{$reminder->active->getBadgeClass()}}">
                                              {{ $reminder->active->getName() }}  </span>
                                            </td>
                                            <td>
                                                @canany(['update', 'delete'], $reminder)
                                                    <div class="btn-group mt-2 mb-2">
                                                        <button type="button" class="btn btn-primary dropdown-toggle"
                                                            data-bs-toggle="dropdown">
                                                            عملیات <span class="caret"></span>
                                                        </button>
                                                        <ul class="dropdown-menu" role="menu">
                                                            @can('delete', $reminder)
                                                                <li><a class="delete_confirm_alert" data-label="حذف "
                                                                        data-id="{{ $reminder->id }}" href="#">حذف</a>
                                                                </li>
                                                            @endcan
                                                            @can('update', $reminder)
                                                                <li><a href="{{ route('admin.reminder.edit', ['reminder' => $reminder->id]) }}"
                                                                        data-label="ویرایش">ویرایش</a>
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
                        {{ $Reminders->links() }}
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
            Livewire.on('closeCollaps', function() {
                $('#advanceSearch').removeClass('show');
            });
        });
    </script>
@endpush
