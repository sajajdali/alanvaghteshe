<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">پرداخت‌های دوره</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">همه پرداخت‌های دوره</h3>
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
                                <label for="search-payment-id" class="col-md-2 form-label">ایدی</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-payment-id" wire:model.defer="search.id" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-payment-code" class="col-md-2 form-label">کد تراکنش</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-payment-code" wire:model.defer="search.transaction_code" type="text" dir="ltr">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-payment-user" class="col-md-2 form-label">کاربر</label>
                                <div class="col-md-10">
                                    <input class="form-control" id="search-payment-user" wire:model.defer="search.user" type="text">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="search-payment-type" class="col-md-2 form-label">نوع پرداخت</label>
                                <div class="col-md-10">
                                    <select class="form-control" id="search-payment-type" wire:model.defer="search.paid_by">
                                        <option value="">همه</option>
                                        @foreach($paidByOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                                        @endforeach
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
                                <th>بابت</th>
                                <th>کد تراکنش</th>
                                <th>نوع پرداخت</th>
                                <th>وضعیت</th>
                                <th>مبلغ</th>
                                <th>تخفیف</th>
                                <th>مبلغ نهایی</th>
                                <th>تاریخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        <div>{{ filled(trim((string) ($transaction->user?->full_name ?? ''))) ? $transaction->user->full_name : '-' }}</div>
                                        <small class="text-muted">{{ $transaction->user?->mobile ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->payment_for_badge_class }}">{{ $transaction->payment_for?->getName() ?? '-' }}</span>
                                        <div class="mt-2 fw-semibold">{{ $transaction->subject_title }}</div>
                                        @if($transaction->subject_description)
                                            <small class="text-muted">{{ $transaction->subject_description }}</small>
                                        @endif
                                    </td>
                                    <td dir="ltr">
                                        <div class="fw-semibold">{{ $transaction->transaction_code ?? '-' }}</div>
                                        <small class="text-muted">#{{ $transaction->id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->paid_by_badge_class }}">
                                            {{ $transaction->paid_by?->getName() ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $transaction->status_badge_class }}">
                                            {{ $transaction->status?->getName() ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($transaction->cost) }} تومان</td>
                                    <td>{{ number_format($transaction->discount) }} تومان</td>
                                    <td class="fw-semibold text-success">{{ number_format($transaction->total_cost) }} تومان</td>
                                    <td>{{ verta($transaction->created_at)->format('Y/m/d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="alert alert-info mb-0">هیچ پرداختی برای دوره‌ها ثبت نشده است.</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
