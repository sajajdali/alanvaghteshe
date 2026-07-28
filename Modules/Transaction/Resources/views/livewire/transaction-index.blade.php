<div>
    @error('success')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <span class="alert-inner--icon me-2"><i class="fe fe-thumbs-up"></i></span> <span
                class="alert-inner--text">{{ $message }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span
                    aria-hidden="true">×</span> </button>
        </div>
    @enderror
    @error('areUsure')
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert"> <span
                class="alert-inner--icon me-2"><i class="fe fe-slash"></i></span> <span
                class="alert-inner--text"><strong>اخطار!</strong> {{ $message }}</span> <button
                @click="$dispatch('deleteHasBeenconfiremd')" class="btn btn-sm btn-danger">بله</button> <button
                type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <span
                    aria-hidden="true">×</span> </button> </div>
    @enderror
    <div class="row row-sm mt-5">
        <div class="col-lg-12">
            <div class="card custom-card ">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">درخواست های رژیم</h3>
                    <div>
                        @if (!empty($search))
                            <button class="btn btn-secondary" type="button" data-bs-toggle="collapse"
                                wire:click='resetSearch' data-bs-target="#advanceSearch" aria-expanded="false"
                                aria-controls="advanceSearch">نمایش همه</button>
                        @endif
                        <button class="btn btn-indigo" type="button" data-bs-toggle="collapse"
                            data-bs-target="#advanceSearch" aria-expanded="false"
                            aria-controls="advanceSearch">فیلتر</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-5 collapse" id="advanceSearch" wire:ignore>
                        <form class="form-horizontal example">
                            <div class="row mb-4">
                                <label for="ID" class="col-md-2 form-label">شرایط</label>
                                <div class="col-md-10">
                                    <select class="form-select" wire:model='search.condition'
                                        aria-label="Default select example">
                                        <option>انتخاب کنید...</option>
                                        <option value="1">دارای پرداخت و پکیج فعال</option>
                                        <option value="2"> اتمام پکیج تا 5 روز دیگر</option>
                                        <option value="3"> پکیج تمام شده</option>
                                        <option value="4">15 روز از خرید گذشته</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label for="payment_for" class="col-md-2 form-label">بابت</label>
                                <div class="col-md-10">
                                    <select class="form-select" id="payment_for" wire:model="search.payment_for">
                                        <option value="">همه</option>
                                        @foreach($paymentForOptions as $option)
                                            <option value="{{ $option['value'] }}">{{ $option['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="ID" class="form-label">شروع</label>
                                    <input class="form-control" id="ID" data-name="search.startDate" data-jdp wire:model='search.startDate'
                                        autocomplete="off" placeholder="انتخاب کنید..." type="text">
                                </div>
                                <div class="col-md-6">
                                    <label for="ID" class="col-md-2 form-label">پایان</label>
                                    <input class="form-control" id="ID" data-name="search.endDate" data-jdp wire:model='search.endDate'
                                        autocomplete="off" placeholder="انتخاب کنید..." type="text">
                                </div>
                            </div>
                            <div class="row mb-4 d-flex justify-content-end">
                                <button class="btn btn-primary" type="button" wire:click="startSearch"
                                    wire:loading.class="bg-gray btn-loading disabled" wire:click="updateOrCreate">جست و
                                    جو
                                </button>
                            </div>

                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-nowrap text-md-nowrap table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>#</th>
                                    <th>نام کاربر</th>
                                    <th>بابت</th>
                                    <th>نوع پرداخت</th>
                                    <th>وضعیت</th>
                                    <th>هزینه</th>
                                    <th>تخفیف</th>
                                    <th>هزینه ی کل</th>
                                    <th>زمان </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr class="text-center">
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
                                        <td> {{ number_format($transaction->cost) }} تومان</td>
                                        <td> {{ number_format($transaction->discount) }} تومان</td>
                                        <td class="fw-semibold text-success"> {{ number_format($transaction->total_cost) }} تومان</td>
                                        <td> {{ verta($transaction->created_at)->format('Y/m/d ساعت H:i دقیقه') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex mt-3 justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        jalaliDatepicker.startWatch();
        $(document).on('change', '[data-jdp]', function() {
            let selectedDate = $(this).val();
            let seterValue = $(this).data('name');
            @this.set(seterValue, selectedDate);
        });
    </script>
@endpush
