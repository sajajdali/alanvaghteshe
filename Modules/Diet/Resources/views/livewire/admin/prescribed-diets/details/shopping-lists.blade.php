@php
    $statusLabels = [
        'pending' => ['در انتظار', 'warning'],
        'processing' => ['در حال ساخت', 'info'],
        'ready' => ['آماده', 'success'],
        'failed' => ['ناموفق', 'danger'],
    ];

    $listsByDay = [];
    foreach ($dietDays as $dietDay) {
        $listsByDay[$dietDay] = $shoppingLists->filter(
            fn ($list) => $dietDay === $list->start_date->toDateString()
        );
    }
@endphp

<div class="card shopping-list-admin">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h4 class="card-title mb-1">تقویم سبد خرید رژیم</h4>
            <p class="text-muted mb-0">یک روز از رژیم را انتخاب کنید تا سبد خرید همان روز نمایش داده شود.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary">{{ $shoppingLists->count() }} سبد ساخته‌شده</span>
            <span class="badge bg-light text-dark">{{ $dietDays->count() }} روز رژیم</span>
        </div>
    </div>

    <div class="card-body">
        @if($dietDays->isNotEmpty())
            <div class="shopping-calendar mb-4" dir="rtl">
                <div class="shopping-calendar-line"></div>
                @foreach($dietDays as $index => $dietDay)
                    @php
                        $dayLists = $listsByDay[$dietDay] ?? collect();
                        $dailyList = $dayLists->first(fn ($list) => $list->period->value === 'daily');
                        $weeklyList = $dayLists->first(fn ($list) => $list->period->value === 'weekly');
                        $hasReadyList = $dayLists->contains(fn ($list) => $list->status->value === 'ready');
                        $hasFailedList = $dayLists->contains(fn ($list) => $list->status->value === 'failed');
                        $isSelected = $selectedShoppingDate === $dietDay;
                        $date = \Carbon\Carbon::parse($dietDay);
                    @endphp

                    <button type="button"
                            wire:key="shopping-day-{{ $dietDay }}"
                            wire:click="selectShoppingDate('{{ $dietDay }}')"
                            class="shopping-day {{ $isSelected ? 'is-selected' : '' }} {{ $hasReadyList ? 'has-list' : ($hasFailedList ? 'has-error' : '') }}">
                        <span class="shopping-day-number">روز {{ $index + 1 }}</span>
                        <strong>{{ verta($date)->format('m/d') }}</strong>
                        <small>{{ verta($date)->format('%A') }}</small>
                        <span class="shopping-day-dot"></span>
                        <span class="shopping-day-types">
                            @if($dailyList)<span class="shopping-type shopping-type-daily">روزانه</span>@endif
                            @if($weeklyList)<span class="shopping-type shopping-type-weekly">۷ روزه</span>@endif
                            @if(!$dailyList && !$weeklyList)<span class="shopping-type shopping-type-empty">ساخته نشده</span>@endif
                        </span>
                    </button>
                @endforeach
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <span class="text-muted">روز انتخاب‌شده:</span>
                    <strong>{{ verta(\Carbon\Carbon::parse($selectedShoppingDate))->format('Y/m/d - %A') }}</strong>
                </div>
                <div class="shopping-calendar-legend">
                    <span><i class="legend-dot bg-success"></i> دارای سبد</span>
                    <span><i class="legend-dot bg-danger"></i> ساخت ناموفق</span>
                    <span><i class="legend-dot bg-light border"></i> ساخته نشده</span>
                </div>
            </div>

            @forelse($selectedShoppingLists as $shoppingList)
                @php
                    $status = $shoppingList->status->value;
                    [$statusTitle, $statusColor] = $statusLabels[$status] ?? [$status, 'secondary'];
                    $result = is_array($shoppingList->result) ? $shoppingList->result : [];
                    $groups = $result['groups'] ?? [];
                    $collapseId = 'shopping-list-'.$shoppingList->id;
                    $isDaily = $shoppingList->period->value === 'daily';
                @endphp

                <div class="shopping-result-card mb-3">
                    <div class="shopping-result-header">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <span class="shopping-period-icon {{ $isDaily ? 'daily' : 'weekly' }}">
                                <i class="fe {{ $isDaily ? 'fe-sun' : 'fe-calendar' }}"></i>
                            </span>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <h5 class="mb-0">سبد خرید {{ $isDaily ? 'روزانه' : '۷ روزه' }}</h5>
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusTitle }}</span>
                                </div>
                                <div class="text-muted small">
                                    از {{ verta($shoppingList->start_date)->format('Y/m/d') }}
                                    @if(!$isDaily) تا {{ verta($shoppingList->end_date)->format('Y/m/d') }} @endif
                                    · {{ $result['total_items'] ?? 0 }} قلم
                                </div>
                            </div>
                        </div>

                        @if($status === 'ready')
                            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#{{ $collapseId }}" aria-expanded="true">
                                مشاهده اقلام
                            </button>
                        @endif
                    </div>

                    @if($status === 'ready')
                        <div class="collapse show" id="{{ $collapseId }}">
                            <div class="shopping-result-body">
                                <div class="d-flex flex-wrap gap-3 text-muted small mb-3">
                                    <span>عنوان: {{ $result['title'] ?? 'سبد خرید' }}</span>
                                    <span>مدل: {{ $shoppingList->model ?: '-' }}</span>
                                    <span>زمان ساخت: {{ $shoppingList->generated_at ? verta($shoppingList->generated_at)->format('Y/m/d ساعت H:i') : '-' }}</span>
                                </div>

                                <div class="row g-3">
                                    @forelse($groups as $group)
                                        <div class="col-xl-4 col-md-6">
                                            <div class="shopping-group h-100">
                                                <div class="shopping-group-title">
                                                    <strong>{{ $group['title'] ?? 'سایر' }}</strong>
                                                    <span>{{ count($group['items'] ?? []) }} قلم</span>
                                                </div>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($group['items'] ?? [] as $item)
                                                        <li class="shopping-item">
                                                            <span>
                                                                <i class="fe {{ ($item['is_checked'] ?? false) ? 'fe-check-circle text-success' : 'fe-circle text-muted' }} me-1"></i>
                                                                {{ $item['name'] ?? '-' }}
                                                            </span>
                                                            <strong>{{ $item['display_quantity'] ?? '-' }}</strong>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12"><div class="alert alert-info mb-0">این سبد خرید قلمی ندارد.</div></div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @elseif($status === 'failed')
                        <div class="alert alert-danger border-0 mb-0">
                            <strong>ساخت این سبد ناموفق بوده است.</strong>
                            @if($shoppingList->error_message)<div class="mt-1">{{ $shoppingList->error_message }}</div>@endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="shopping-empty-state">
                    <span class="shopping-empty-icon"><i class="fe fe-shopping-cart"></i></span>
                    <h5>برای این روز هنوز سبد خریدی ساخته نشده است</h5>
                    <p class="text-muted mb-0">بعد از درخواست کاربر، سبد روزانه یا ۷ روزه اینجا نمایش داده می‌شود.</p>
                </div>
            @endforelse
        @else
            <div class="shopping-empty-state">
                <span class="shopping-empty-icon"><i class="fe fe-calendar"></i></span>
                <h5>برای این رژیم روزی ثبت نشده است</h5>
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            .shopping-calendar{position:relative;display:flex;gap:12px;overflow-x:auto;padding:8px 4px 18px}.shopping-calendar-line{position:absolute;right:30px;left:30px;top:78px;height:2px;background:#e8ecf2}.shopping-day{position:relative;z-index:1;flex:0 0 118px;min-height:150px;padding:12px 8px;background:#fff;border:1px solid #e5e9f2;border-radius:8px;color:#495057;text-align:center;transition:.2s ease}.shopping-day:hover{border-color:var(--primary-bg-color);transform:translateY(-2px)}.shopping-day.is-selected{border:2px solid var(--primary-bg-color);background:rgba(45,189,210,.07);box-shadow:0 5px 16px rgba(45,189,210,.14)}.shopping-day-number,.shopping-day small{display:block;color:#8a93a6;font-size:11px}.shopping-day strong{display:block;margin:5px 0 2px;font-size:16px}.shopping-day-dot{display:block;width:12px;height:12px;margin:13px auto 8px;border:3px solid #fff;border-radius:50%;background:#cbd2dc;box-shadow:0 0 0 2px #e8ecf2}.shopping-day.has-list .shopping-day-dot{background:#13bfa6;box-shadow:0 0 0 2px rgba(19,191,166,.25)}.shopping-day.has-error .shopping-day-dot{background:#d12c47;box-shadow:0 0 0 2px rgba(209,44,71,.2)}.shopping-day-types{display:flex;justify-content:center;flex-wrap:wrap;gap:4px}.shopping-type{display:inline-block;padding:2px 7px;border-radius:4px;font-size:10px}.shopping-type-daily{color:#087f6d;background:rgba(19,191,166,.12)}.shopping-type-weekly{color:#6f42c1;background:rgba(111,66,193,.12)}.shopping-type-empty{color:#8a93a6;background:#f1f3f7}.shopping-calendar-legend{display:flex;flex-wrap:wrap;gap:14px;color:#727b8c;font-size:12px}.legend-dot{display:inline-block;width:9px;height:9px;margin-left:4px;border-radius:50%}.shopping-result-card{overflow:hidden;border:1px solid #e5e9f2;border-radius:8px;background:#fff}.shopping-result-header{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px;background:#f8fafc}.shopping-period-icon{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:8px;font-size:20px}.shopping-period-icon.daily{color:#e69b17;background:rgba(230,155,23,.12)}.shopping-period-icon.weekly{color:#6f42c1;background:rgba(111,66,193,.12)}.shopping-result-body{padding:16px;border-top:1px solid #e5e9f2}.shopping-group{overflow:hidden;border:1px solid #e8ecf2;border-radius:7px;background:#fff}.shopping-group-title{display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#f5f7fb}.shopping-group-title span{color:#8a93a6;font-size:11px}.shopping-item{display:flex;justify-content:space-between;align-items:center;gap:10px;padding:10px 12px;border-bottom:1px solid #eef1f5}.shopping-item:last-child{border-bottom:0}.shopping-item strong{white-space:nowrap}.shopping-empty-state{padding:48px 20px;border:1px dashed #d8deea;border-radius:8px;text-align:center;background:#fafbfd}.shopping-empty-icon{display:inline-flex;align-items:center;justify-content:center;width:58px;height:58px;margin-bottom:14px;border-radius:50%;color:#8a93a6;background:#edf1f6;font-size:24px}@media(max-width:767px){.shopping-result-header{align-items:flex-start;flex-direction:column}.shopping-result-header .btn{width:100%}}
        </style>
    @endpush
@endonce
