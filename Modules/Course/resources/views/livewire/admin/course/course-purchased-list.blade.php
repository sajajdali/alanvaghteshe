<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">لیست دوره‌های خریداری شده</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه خریدهای دوره</h3>
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
                                <label for="search-purchase-id" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-purchase-id" wire:model.defer="search.id" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-purchase-user" class="col-md-2 form-label">کاربر</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-purchase-user" wire:model.defer="search.user" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-purchase-course" class="col-md-2 form-label">دوره</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-purchase-course" wire:model.defer="search.course" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-purchase-paid-by" class="col-md-2 form-label">نحوه خرید</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search-purchase-paid-by" wire:model.defer="search.paid_by">
                                        <option value="">همه</option>
                                        @foreach($paidByOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-purchase-active" class="col-md-2 form-label">وضعیت دسترسی</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search-purchase-active" wire:model.defer="search.is_active">
                                        <option value="">همه</option>
                                        <option value="1">فعال</option>
                                        <option value="0">غیرفعال</option>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled">جست و جو</button>
                        </form>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered text-center" wire:loading.class="op-0-3">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>کاربر</th>
                                <th>دوره</th>
                                <th>نوع پرداخت</th>
                                <th>تراکنش</th>
                                <th>شروع</th>
                                <th>پایان</th>
                                <th>وضعیت دسترسی</th>
                                <th>پیشرفت</th>
                                <th>جزئیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->id }}</td>
                                    <td>
                                        {{ filled(trim((string) ($purchase->user?->full_name ?? ''))) ? $purchase->user->full_name : '-' }}
                                        [{{ $purchase->user?->mobile ?? '-' }}]
                                    </td>
                                    <td>{{ $purchase->course?->title ?? '-' }}</td>
                                    <td>{{ $purchase->paid_by?->getName() ?? '-' }}</td>
                                    <td>{{ $purchase->transaction?->transaction_code ?? ('#' . ($purchase->transaction_id ?? '-')) }}</td>
                                    <td>{{ $purchase->start_at ? verta($purchase->start_at)->format('Y/m/d') : '-' }}</td>
                                    <td>{{ $purchase->end_at ? verta($purchase->end_at)->format('Y/m/d') : '-' }}</td>
                                    <td>
                                        <span class="badge {{ $purchase->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $purchase->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $purchase->progress_count }} / {{ $purchase->course?->lessons_count ?? 0 }}</div>
                                        <span class="badge bg-info">{{ $purchase->progress_percent }}٪ تکمیل</span>
                                        <span class="badge bg-secondary">{{ $purchase->remaining_percent }}٪ مانده</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.course.purchased.show', $purchase) }}"
                                           class="btn btn-sm btn-primary">
                                            مشاهده
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="alert alert-info mb-0">هنوز خریدی برای دوره‌ها ثبت نشده است.</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
