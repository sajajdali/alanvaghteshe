<div>
    @include('admin::layouts.components.alert')
    @include('admin::components.loading')
    @include('admin::components.SupporterInterestPayoutModal')
    @include('admin::components.SupporterInterestPayoutHistoryModal')
    <div class="row row-sm mt-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">لیست پشتیبانان</h3>
                </div>
                <div class="card-body text-center">
                    <div class="table-responsive table-striped mb-3">
                        <table class="table text-nowrap text-md-nowrap table-bordered" wire:loading.class="op-0-3">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">نام پشتیبان</th>
                                    <th scope="col">موبایل</th>
                                    <th scope="col">موجودی</th>
                                    <th scope="col">تعداد سود واریزی</th>
                                    <th scope="col">تسویه حساب</th>
                                    <th scope="col">عملکرد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($supporters->isNotEmpty())
                                    @foreach ($supporters as $supporter)
                                        <tr wire:key="supporter_{{ $supporter->id }}">
                                            <td>{{ $supporter->id }}</td>
                                            <td>{{ $supporter->fullName }}</td>
                                            <td>{{ $supporter->mobile }}</td>
                                            <td>{{ number_format(num: $supporter->wallet) }} ریال</td>
                                            <td>{{ $supporter->inComeWalletTransactionsCount() }}</td>
                                            <td>
                                                <button data-bs-toggle="modal" data-bs-target="#supporterPayoutModal"
                                                    wire:click="lunchPayoutModal('{{ $supporter->id }}')"
                                                    class="btn btn-info-light">
                                                    ثبت تسویه
                                                </button>
                                                <button data-bs-toggle="modal" data-bs-target="#supporterPayoutHistory"
                                                    wire:click="lunchPayoutModal('{{ $supporter->id }}')"
                                                    class="btn btn-secondary-light">
                                                    سابقه تسویه
                                                </button>
                                            </td>
                                            <td>
                                                <button class="btn btn-light"
                                                    wire:click="supporterPerformance('{{ $supporter->id }}')">
                                                    مشاهده عملکرد
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="alert alert-info">
                                                هیچ پشتیبانی یافت نشد
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $supporters->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($activityLog))
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">گزارش فعالیت <span
                                class="text-primary">{{ $selectedSupporter->fullname }}</span> </h3>
                        <div class="card-options">
                            @if (!empty($search))
                                <button class="btn btn-secondary me-1" type="button" data-bs-toggle="collapse"
                                    wire:click='resetSearch' data-bs-target="#activityLogCollaps" aria-expanded="false"
                                    aria-controls="activityLogCollaps">
                                    نمایش همه
                                </button>
                            @endif
                            <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#activityLogCollaps" aria-expanded="false"
                                aria-controls="activityLogCollaps">
                                فیلتر
                            </button>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-5 collapse " id="activityLogCollaps" wire:ignore.self>
                            <form class="form-horizontal example">
                                <div class="row mb-4">
                                    <label for="first_name" class="col-md-2 form-label">بازه شروع</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="ID" data-name="form.startDate"
                                            data-jdp wire:model='form.startDate' autocomplete="off"
                                            placeholder="انتخاب کنید.." type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="first_name" class="col-md-2 form-label">بازه پایان</label>
                                    <div class="col-md-10">
                                        <input class="form-control" id="ID" data-name="form.endDate" data-jdp
                                            wire:model='form.endDate' autocomplete="off" placeholder="انتخاب کنید.."
                                            type="text">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="ID" class="col-md-2 form-label">نوع گزارش</label>
                                    <div class="col-md-10">
                                        <select class="form-select" wire:model='form.condition'
                                            aria-label="Default select example">
                                            <option>انتخاب کنید...</option>
                                            @foreach (\Modules\Admin\app\Enums\ActivityEventEnum::cases() as $logCases)
                                                <option value="{{ $logCases->value }}">{{ $logCases->getName() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-4 d-flex justify-content-end">
                                    <button class="btn btn-primary" type="button" wire:click="startLogSearch"
                                        wire:loading.class="bg-gray btn-loading disabled"
                                        wire:click="updateOrCreate">جست و
                                        جو
                                    </button>
                                </div>

                            </form>
                        </div>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap text-md-nowrap table-bordered"
                                wire:loading.class="op-0-3">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">فعالیت</th>
                                        <th scope="col">برای کاربر</th>
                                        <th scope="col">تاریخ</th>
                                        <th scope="col">توضیحات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($activityLog->isNotEmpty())
                                        @foreach ($activityLog as $log)
                                            <tr>
                                                <td>{{ $log->id }}</td>
                                                <td>{{ $log->event->getName() }}</td>
                                                <td>{{ $log->loggedFor?->fullName }}</td>
                                                <td>{{ verta($log->created_at)->format('Y/m/d ساعت H:i') }}</td>
                                                <td>{{ $log->description }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center">
                                                <div class="alert alert-info">
                                                    هیچ فعالیت قابل نمایشی یافت نشد!
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $supporters->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
        Livewire.on('closeModal', function() {
            const el = document.getElementById('supporterPayoutModal');
            if (!el) return;
            const modal = bootstrap.Modal.getInstance(el) ??
                bootstrap.Modal.getOrCreateInstance(el);

            modal.hide();
        });
    </script>
@endpush
