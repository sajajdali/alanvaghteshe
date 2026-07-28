<div class="mt-4" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-12 col-xxl-10">

            {{-- هدر صفحه --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
                <div>
                    <h5 class="mb-1 fw-bold">
                        وضعیت مصرف غذایی
                        <span class="text-primary">
                            {{ $user->full_name ?? $user->name ?? $user->mobile }}
                        </span>
                    </h5>
                    <small class="text-muted">
                        مشاهده‌ی غذاهای مصرف‌شده به تفکیک روز و وعده، همراه با جمع کالری و درشت‌مغذی‌ها.
                    </small>
                </div>
            </div>

            {{-- کارت کنترل تاریخ + جمع روزانه --}}
            <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
                <div class="card-body">

                    {{-- نوار تاریخ --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">

                        {{-- کنترل روز قبل/بعد + تاریخ --}}

                        {{-- نوار تاریخ --}}
                        <div class="d-flex justify-content-center mb-3">
                            <div class="diet-date-nav d-flex align-items-center justify-content-between gap-3">

                                {{-- دکمه روز قبل --}}
                                <button type="button"
                                        class="diet-date-btn"
                                        wire:click="goToPrevDay">
                                    <span class="arrow">→</span>

                                    <span class="label">روز قبل</span>
                                </button>

                                {{-- تاریخ (شمسی) وسط --}}
                                <input type="text"
                                       class="diet-date-input text-center"
                                       wire:model.live="selectedDate"
                                       autocomplete="off"
                                       data-jdp>

                                {{-- دکمه روز بعد --}}
                                <button type="button"
                                        class="diet-date-btn {{ $isToday ? 'disabled' : '' }}"
                                        @if(!$isToday) wire:click="goToNextDay" @endif>
                                    <span class="label">روز بعد</span>
                                    <span class="arrow">←</span>

                                </button>

                            </div>
                        </div>
                        {{-- نمایش تاریخ انتخاب‌شده --}}
                        <div class="text-md-end text-muted small">
                            تاریخ انتخاب‌شده:
                            <span class="fw-semibold">
                                {{ \Hekmatinasser\Verta\Facades\Verta::parse($selectedDate)->format('Y/m/d') }}
                            </span>
                        </div>
                    </div>
                    {{-- جمع روزانه --}}
                    <div class="row g-3">
                        <div class="col-6 col-md">
                            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #fff5f5;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">کالری کل</span>
                                    <i class="fa-solid fa-fire-flame-curved text-danger small"></i>
                                </div>
                                <div class="fw-bold fs-5 text-danger">
                                    {{ number_format($dailyTotals['calories'], 1) }}
                                    <span class="small text-muted">kcal</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md">
                            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #f0f6ff;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">پروتئین</span>
                                    <i class="fa-solid fa-dumbbell text-primary small"></i>
                                </div>
                                <div class="fw-bold fs-5 text-primary">
                                    {{ number_format($dailyTotals['protein'], 1) }}
                                    <span class="small text-muted">g</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md">
                            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #f2fff5;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">کربوهیدرات</span>
                                    <i class="fa-solid fa-bread-slice text-success small"></i>
                                </div>
                                <div class="fw-bold fs-5 text-success">
                                    {{ number_format($dailyTotals['carb'], 1) }}
                                    <span class="small text-muted">g</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md">
                            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #fffaf0;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">چربی</span>
                                    <i class="fa-solid fa-droplet text-warning small"></i>
                                </div>
                                <div class="fw-bold fs-5 text-warning">
                                    {{ number_format($dailyTotals['fat'], 1) }}
                                    <span class="small text-muted">g</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md">
                            <div class="p-3 rounded-4 h-100 d-flex flex-column justify-content-between" style="background: #f0fbff;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted">فیبر</span>
                                    <i class="fa-solid fa-seedling text-info small"></i>
                                </div>
                                <div class="fw-bold fs-5 text-info">
                                    {{ number_format($dailyTotals['fiber'], 1) }}
                                    <span class="small text-muted">g</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- لیست غذاها به تفکیک وعده، جدولی --}}
            @php
                // گروه بدون وعده را آخر نمایش بدهیم
                $noMealGroup = $groupedByMeal->pull('no_meal');
            @endphp

            {{-- وعده‌های دارای نام (صبحانه، نهار، شام، ...) --}}
            @foreach($groupedByMeal as $mealId => $items)
                @php
                    /** @var \App\Models\FoodConsumption $first */
                    $first = $items->first();
                    $meal  = $first->meal;
                @endphp

                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <strong>{{ $meal->name ?? 'بدون وعده مشخص' }}</strong>
                            </div>
                            <small class="text-muted">
                                {{ $items->count() }} مورد ثبت شده در این وعده
                            </small>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">ساعت</th>
                                    <th>غذا</th>
                                    <th style="width: 140px;" class="text-center">مقدار</th>
                                    <th style="width: 90px;" class="text-center">کالری</th>
                                    <th style="width: 90px;" class="text-center">پروتئین</th>
                                    <th style="width: 100px;" class="text-center">کربوهیدرات</th>
                                    <th style="width: 90px;" class="text-center">چربی</th>
                                    <th style="width: 90px;" class="text-center">فیبر</th>
                                    <th style="width: 110px;" class="text-center">ویرایش</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($items as $item)
                                    <tr>
                                        {{-- ساعت مصرف با badge --}}
                                        <td>
                                            <span class="badge rounded-pill bg-light text-muted d-inline-flex align-items-center gap-1">
                                                <i class="fa-regular fa-clock small"></i>
                                                <span>{{ $item->consumed_at?->format('H:i') }}</span>
                                            </span>
                                        </td>

                                        {{-- نام غذا (لینک ویرایش) --}}
                                        <td>
                                            @if($item->consumable)
                                                <a href="{{ route('admin.basic_food.edit', $item->consumable->id) }}"
                                                   class="fw-semibold text-decoration-none">
                                                    {{ $item->consumable->name }}
                                                </a>
                                            @else
                                                <span class="fw-semibold">غذای نامشخص</span>
                                            @endif
                                        </td>

                                        {{-- مقدار + واحد: "۱ قاشق غذاخوری" --}}
                                        <td class="text-center text-muted">
                                            @php
                                                $qty = rtrim(rtrim(number_format($item->quantity, 2), '0'), '.');
                                            @endphp
                                            @if($item->unit)
                                                {{ $qty }} {{ $item->unit->name }}
                                            @else
                                                {{ $qty }}
                                            @endif
                                        </td>

                                        {{-- مقادیر تغذیه‌ای --}}
                                        <td class="text-center">
                                            {{ number_format($item->calories, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->protein, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->carb, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->fat, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->fiber, 1) }}
                                        </td>

                                        {{-- دکمه مشاهده / ویرایش --}}
                                        <td class="text-center">
                                            @if($item->consumable)
                                                <a href="{{ route('admin.basic_food.edit', $item->consumable->id) }}"
                                                   class="btn btn-outline-primary btn-xs rounded-pill">
                                                    مشاهده / ویرایش
                                                </a>
                                            @else
                                                <span class="text-muted small">ـ</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                {{-- جمع این وعده --}}
                                <tfoot class="table-light">
                                <tr>
                                    <th colspan="3" class="text-end">جمع این وعده:</th>
                                    <th class="text-center">{{ number_format($items->sum('calories'), 1) }}</th>
                                    <th class="text-center">{{ number_format($items->sum('protein'), 1) }}</th>
                                    <th class="text-center">{{ number_format($items->sum('carb'), 1) }}</th>
                                    <th class="text-center">{{ number_format($items->sum('fat'), 1) }}</th>
                                    <th class="text-center">{{ number_format($items->sum('fiber'), 1) }}</th>
                                    <th class="text-center"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- گروه "بدون وعده" در انتها به‌صورت جدولی --}}
            @if(isset($noMealGroup) && $noMealGroup && $noMealGroup->count())
                <div class="card border-0 shadow-sm rounded-4 mb-3">
                    <div class="card-header bg-white border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <strong>بدون وعده مشخص</strong>
                            </div>
                            <small class="text-muted">
                                {{ $noMealGroup->count() }} مورد ثبت شده
                            </small>
                        </div>
                    </div>

                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">ساعت</th>
                                    <th>غذا</th>
                                    <th style="width: 140px;" class="text-center">مقدار</th>
                                    <th style="width: 90px;" class="text-center">کالری</th>
                                    <th style="width: 90px;" class="text-center">پروتئین</th>
                                    <th style="width: 100px;" class="text-center">کربوهیدرات</th>
                                    <th style="width: 90px;" class="text-center">چربی</th>
                                    <th style="width: 90px;" class="text-center">فیبر</th>
                                    <th style="width: 110px;" class="text-center">ویرایش</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($noMealGroup as $item)
                                    <tr>
                                        <td>
                                            <span class="badge rounded-pill bg-light text-muted d-inline-flex align-items-center gap-1">
                                                <i class="fa-regular fa-clock small"></i>
                                                <span>{{ $item->consumed_at?->format('H:i') }}</span>
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->consumable)
                                                <a href="{{ route('admin.basic_food.edit', $item->consumable->id) }}"
                                                   class="fw-semibold text-decoration-none">
                                                    {{ $item->consumable->name }}
                                                </a>
                                            @else
                                                <span class="fw-semibold">غذای نامشخص</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted">
                                            @php
                                                $qty = rtrim(rtrim(number_format($item->quantity, 2), '0'), '.');
                                            @endphp
                                            @if($item->unit)
                                                {{ $qty }} {{ $item->unit->name }}
                                            @else
                                                {{ $qty }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->calories, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->protein, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->carb, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->fat, 1) }}
                                        </td>
                                        <td class="text-center">
                                            {{ number_format($item->fiber, 1) }}
                                        </td>
                                        <td class="text-center">
                                            @if($item->consumable)
                                                <a href="{{ route('admin.basic_food.edit', $item->consumable->id) }}"
                                                   class="btn btn-outline-primary btn-xs rounded-pill">
                                                    مشاهده / ویرایش
                                                </a>
                                            @else
                                                <span class="text-muted small">ـ</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                                {{-- جمع این گروه --}}
                                <tfoot>
                                <tr class="">
                                    <th colspan="3"
                                        class="text-end py-3"
                                        style="font-weight: 600; font-size: .95rem; border-top: 2px solid #e3e6ea;">

        <span class="d-inline-flex align-items-center gap-1 text-secondary">
            <i class="fa-solid fa-chart-pie"></i>
            جمع این وعده
        </span>
                                    </th>

                                    <th class="text-center fw-bold text-danger"
                                        style="border-top: 2px solid #e3e6ea;">
                                        {{ number_format($noMealGroup->sum('calories'), 1) }}
                                    </th>

                                    <th class="text-center fw-bold text-primary"
                                        style="border-top: 2px solid #e3e6ea;">
                                        {{ number_format($noMealGroup->sum('protein'), 1) }}
                                    </th>

                                    <th class="text-center fw-bold text-success"
                                        style="border-top: 2px solid #e3e6ea;">
                                        {{ number_format($noMealGroup->sum('carb'), 1) }}
                                    </th>

                                    <th class="text-center fw-bold text-warning"
                                        style="border-top: 2px solid #e3e6ea;">
                                        {{ number_format($noMealGroup->sum('fat'), 1) }}
                                    </th>

                                    <th class="text-center fw-bold text-info"
                                        style="border-top: 2px solid #e3e6ea;">
                                        {{ number_format($noMealGroup->sum('fiber'), 1) }}
                                    </th>

                                    <th style="border-top: 2px solid #e3e6ea;"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@push('styles')
    <style>
        .label {
            display: inline-block;
             margin-bottom: 0 !important;
            padding-left: .6em;
            padding-right: .6em;
        }
        .diet-date-nav {
            background: #f2f4f7;           /* خاکستری خیلی روشن */
            border-radius: 999px;
            padding: 6px 18px;
            min-width: 320px;
            max-width: 420px;
        }

        .diet-date-btn {
            border-radius: 999px;
            border: 1px solid #f48ab8;     /* صورتی ملایم */
            background: #ffffff;
            color: #f48ab8;
            font-size: 0.75rem;
            padding: 4px 10px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            white-space: nowrap;
        }

        .diet-date-btn .arrow {
            font-size: 0.85rem;
        }

        .diet-date-btn.disabled,
        .diet-date-btn:disabled {
            opacity: 0.4;
            cursor: default;
        }

        .diet-date-input {
            border: none;
            background: transparent;
            box-shadow: none !important;
            font-size: 0.9rem;
            color: #555;
            min-width: 120px;
        }

        .diet-date-input:focus {
            outline: none;
        }
    </style>
    <link rel="stylesheet"
          href="{{ admin_asset('css/jalalidatepicker.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ admin_asset('js/jalalidatepicker.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', function () {
            jalaliDatepicker.startWatch();
        });

        document.addEventListener('livewire:navigated', function () {
            jalaliDatepicker.startWatch();
        });
    </script>
@endpush
