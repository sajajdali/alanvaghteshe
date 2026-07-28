<div class="cr-wrap">


    <form wire:submit.prevent="submit" class="d-grid gap-3">

        {{-- HERO / TOP (خوشگل‌تر) --}}
        <div class="cr-hero">
            <div class="d-flex align-items-start justify-content-between gap-3">
                <div>
                    <div class="fw-bold fs-5">تکمیل پروفایل سلامتی</div>
                    <div class="cr-sub">این اطلاعات برای طراحی برنامه اختصاصی شما استفاده می‌شود</div>
                </div>
                <span class="cr-badge text-white"
                      style="background: rgba(255,255,255,.18); border-color: rgba(255,255,255,.25);">
                    مرحله ۱ از ۲
                </span>
            </div>

            <div class="mt-3 cr-progress">
                <span></span>
            </div>
        </div>

        {{-- اطلاعات فردی --}}
        <div class="card cr-section">
            <div class="card-body">
                <div class="cr-title"><span class="cr-dot"></span> اطلاعات فردی</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">نام</label>
                        <input class="form-control form-control-lg" wire:model="form.first_name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">نام خانوادگی</label>
                        <input class="form-control form-control-lg" wire:model="form.last_name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">جنسیت</label>
                        <select class="form-select form-select-lg" wire:model="form.gender">
                            <option value="">انتخاب کنید</option>
                            @foreach($genderOptions as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تاریخ تولد (شمسی)</label>

                        <div class="position-relative" wire:ignore>
                            <input
                                id="birthdayJ"
                                type="text"
                                class="form-control form-control-lg"
                                data-jdp data-jdp-only-date
                                placeholder="مثلاً ۱۳۶۶/۰۲/۲۸"
                                autocomplete="off"
                            >
                            <span class="position-absolute top-50 translate-middle-y"
                                  style="left:12px; opacity:.55;">
            <i class="bi bi-calendar3"></i>
        </span>
                        </div>

                        {{-- hidden برای نگهداری در Livewire --}}
                        <input type="hidden" wire:model="form.birthday.year">
                        <input type="hidden" wire:model="form.birthday.month">
                        <input type="hidden" wire:model="form.birthday.day">

                        <small class="text-muted d-block mt-1">
                            @if(($form['birthday']['year'] ?? null) && ($form['birthday']['month'] ?? null) && ($form['birthday']['day'] ?? null))
                                انتخاب شده: {{ $form['birthday']['year'] }}/{{ $form['birthday']['month'] }}
                                /{{ $form['birthday']['day'] }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- هدف و سبک زندگی --}}
        <div class="card cr-section">
            <div class="card-body">
                <div class="cr-title"><span class="cr-dot"></span> هدف و سبک زندگی</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">هدف شما</label>
                        <select class="form-select" wire:model="form.diet_type">
                            <option value="">انتخاب کنید</option>
                            @foreach($dietTypeOptions as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">فعالیت روزانه</label>
                        <select class="form-select" wire:model="form.activity_per_week">
                            @foreach($activityOptions as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ورزشکار هستید؟</label>
                        <select class="form-select" wire:model="form.athlete_or_not">
                            <option value="">انتخاب کنید</option>
                            @foreach($athleteOptions as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{--                    <div class="col-md-4">--}}
                    {{--                        <label class="form-label">روش رسیدن به هدف</label>--}}
                    {{--                        <select class="form-select" wire:model="form.target_plan">--}}
                    {{--                            @foreach($targetPlanOptions as $k => $v)--}}
                    {{--                                <option value="{{ $k }}">{{ $v }}</option>--}}
                    {{--                            @endforeach--}}
                    {{--                        </select>--}}
                    {{--                    </div>--}}

                    <div class="col-md-4">
                        <label class="form-label">میزان تغییر وزن در هفته</label>
                        <select class="form-select" wire:model="form.weight_change_per_week">
                            @foreach($weightChangeOptions as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{--                    <div class="col-md-4">--}}
                    {{--                        <label class="form-label">کد دعوت</label>--}}
                    {{--                        <input class="form-control" wire:model="form.invitation_code" placeholder="اختیاری">--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>

        {{-- مشخصات بدن --}}
        <div class="card cr-section">
            <div class="card-body">
                <div class="cr-title"><span class="cr-dot"></span> مشخصات بدن</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">قد (cm)</label>
                        <input type="number" class="form-control" wire:model="form.tall" min="0" max="250">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">وزن اولیه (kg)</label>
                        <input type="number" step="0.1" class="form-control" wire:model="form.weight" min="0" max="400">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">وزن هدف (kg)</label>
                        <input type="number" step="0.1" class="form-control" wire:model="form.target_weight" min="0"
                               max="400">
                    </div>
                </div>
            </div>
        </div>

        {{-- پایین (خوشگل‌تر): محدودیت + حساسیت + بیماری‌ها --}}
        <div  class="card  ">
            <div class="card-body">
                <div class="cr-title"><span class="cr-dot"></span> محدودیت، حساسیت و بیماری</div>

                <div class="mb-2 text-muted small">محدودیت غذایی</div>
                <div id="checkbox_cover">
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($foodRestrictionOptions as $k => $v)
                            <label class="pill @if(in_array($k, $form['food_restriction'] ?? [])) is-active @endif">
                                <input class="form-check-input" type="checkbox" value="{{ $k }}"
                                       wire:model="form.food_restriction">
                                <span>{{ $v }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-2 text-muted small">حساسیت غذایی</div>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        @foreach($foodAllergyOptions as $k => $v)
                            <label class="pill @if(in_array($k, $form['food_allergy'] ?? [])) is-active @endif">
                                <input class="form-check-input" type="checkbox" value="{{ $k }}"
                                       wire:model="form.food_allergy">
                                <span>{{ $v }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mb-2 text-muted small">بیماری‌ها (از سرور)</div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($diseaseOptions as $k => $v)
                            <label class="pill @if(in_array($k, $form['diseases'] ?? [])) is-active @endif">
                                <input class="form-check-input" type="checkbox" value="{{ $k }}"
                                       wire:model="form.diseases">
                                <span>{{ $v }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Sticky bottom CTA (پایین خوشگل شد) --}}
            <div class="cr-footer d-flex align-items-center justify-content-between gap-2">
                <div class="text-muted small">
                    با ذخیره کردن، اطلاعات به شکل استاندارد ثبت می‌شود.
                    <span wire:loading class="ms-2">در حال پردازش…</span>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit"
                            class="btn btn-primary"
                            style="background: linear-gradient(135deg,#2563eb,#06b6d4); border:0"
                            wire:loading.attr="disabled">
                        ذخیره و ادامه
                    </button>
                </div>
            </div>

    </form>
</div>
@push('scripts')
    <script src="{{ admin_asset('js/jalalidatepicker.min.js') }}"></script>

    <script>
        document.addEventListener('livewire:init', () => {

            // فقط یک بار datepicker رو فعال کن (مثل کد خودت)
            if (!window.__jdp_inited) {
                window.__jdp_inited = true;
                jalaliDatepicker.startWatch({
                    minDate: "attr",
                    maxDate: "attr",
                    zIndex: 99999
                });
            }

            const el = document.getElementById('birthdayJ');
            if (!el) return;

            // 1) مقدار اولیه را از form به input نمایش بده
            const y = @json($form['birthday']['year'] ?? null);
            const m = @json($form['birthday']['month'] ?? null);
            const d = @json($form['birthday']['day'] ?? null);

            if (y && m && d) {
                const mm = String(m).padStart(2, '0');
                const dd = String(d).padStart(2, '0');
                el.value = `${y}/${mm}/${dd}`;
            }

            // 2) هر وقت کاربر تاریخ انتخاب کرد => sync با Livewire
            const syncBirthday = () => {
                const val = (el.value || '').trim();
                if (!val) return;

                // ۱۳۶۶/۰۲/۲۸ یا 1366/02/28
                const parts = val.split('/');
                if (parts.length !== 3) return;

                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);

                @this.
                set('form.birthday.year', year);
                @this.
                set('form.birthday.month', month);
                @this.
                set('form.birthday.day', day);
            };

            el.addEventListener('change', syncBirthday);
            el.addEventListener('input', syncBirthday);
        });
    </script>
@endpush
@push('styles')
    <link rel="stylesheet" href="{{ admin_asset('css/jalalidatepicker.min.css') }}">

    <style>
        .cr-wrap {
            direction: rtl;
        }

        .cr-hero {
            border-radius: 18px;
            padding: 18px 18px 14px;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #06b6d4 100%);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(2, 132, 199, .18);
        }

        .cr-hero:before {
            content: "";
            position: absolute;
            inset: -40px -80px auto auto;
            width: 240px;
            height: 240px;
            background: rgba(255, 255, 255, .14);
            border-radius: 999px;
            transform: rotate(18deg);
        }

        .cr-hero .cr-sub {
            opacity: .85;
            font-size: .9rem
        }

        .cr-progress {
            height: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .25);
            overflow: hidden;
        }

        .cr-progress > span {
            display: block;
            height: 100%;
            width: 68%;
            background: rgba(255, 255, 255, .95);
            border-radius: 999px;
        }

        .cr-section {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .06);
        }

        .cr-section .card-body {
            padding: 18px;
        }

        .cr-title {
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cr-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            box-shadow: 0 0 0 6px rgba(37, 99, 235, .12);
        }

        /* Pills */
        .pill {
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 999px;
            margin-right: 2px;
            padding: 10px 12px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            transition: all .15s ease;
        }

        #checkbox_cover span {
            margin-right: 20px !important;
        }

        .pill:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(2, 6, 23, .06);
        }

        .pill input {
            margin: 0;
        }

        .pill.is-active {
            border-color: rgba(37, 99, 235, .45);
            background: rgba(37, 99, 235, .06);
        }

        /* Sticky footer */
        .cr-footer {
            position: sticky;
            bottom: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(10px);
            border-top: 1px solid #eef2ff;
            padding: 12px;
            border-radius: 14px;
            box-shadow: 0 -10px 30px rgba(15, 23, 42, .06);
        }

        .cr-footer .btn {
            border-radius: 14px;
            padding: 12px 18px;
            font-weight: 700;
        }

        .cr-badge {
            font-size: .82rem;
            padding: 8px 10px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }

    </style>

@endpush
