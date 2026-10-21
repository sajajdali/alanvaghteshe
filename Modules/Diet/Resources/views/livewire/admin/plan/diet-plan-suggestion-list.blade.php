<div class="diet-suggestion-page">
    <div class="suggestion-hero">
        <div class="suggestion-hero__content">
            <span class="suggestion-hero__icon" aria-hidden="true"><i class="uil uil-clipboard-notes"></i></span>
            <div>
                <div class="suggestion-hero__eyebrow">مدیریت هوشمند پلن‌ها</div>
                <h1 class="suggestion-hero__title">پیشنهاد رژیم</h1>
                <p class="suggestion-hero__description">پلن مناسب هر کاربر را بر اساس بودجه، هدف، داروی لاغری، ورزش و شماره جلسه تعیین کنید.</p>
            </div>
        </div>
        <div class="suggestion-hero__stat">
            <span class="suggestion-hero__stat-value">{{ number_format($suggestions->count()) }}</span>
            <span class="suggestion-hero__stat-label">قاعده نمایش داده‌شده</span>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <section class="suggestion-panel suggestion-form-panel">
        <div class="suggestion-panel__header">
            <div class="suggestion-panel__heading">
                <span class="suggestion-panel__icon suggestion-panel__icon--primary" aria-hidden="true">
                    <i class="uil {{ $editingId ? 'uil-edit' : 'uil-plus-circle' }}"></i>
                </span>
                <div>
                    <h2>{{ $editingId ? 'ویرایش قاعده' : 'ایجاد قاعده جدید' }}</h2>
                    <p>{{ $editingId ? 'مقادیر موردنظر را اصلاح و تغییرات را ذخیره کنید.' : 'شرایط را مشخص کنید تا پلن مناسب به‌صورت خودکار پیشنهاد شود.' }}</p>
                </div>
            </div>
            @if($editingId)
                <span class="suggestion-badge suggestion-badge--edit"><i class="uil uil-pen"></i> حالت ویرایش</span>
            @endif
        </div>

        <form wire:submit="save" class="suggestion-form">
            <div class="suggestion-form__grid">
                <div class="suggestion-field">
                    <label for="food-budget" class="suggestion-field__label"><span class="suggestion-field__number">۱</span> بودجه برنامه غذایی</label>
                    <select id="food-budget" class="form-select @error('foodBudget') is-invalid @enderror" wire:model="foodBudget">
                        <option value="">انتخاب بودجه</option>
                        @foreach($budgetOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                    @error('foodBudget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="suggestion-field">
                    <label for="diet-type" class="suggestion-field__label"><span class="suggestion-field__number">۲</span> هدف کاربر</label>
                    <select id="diet-type" class="form-select @error('dietType') is-invalid @enderror" wire:model="dietType">
                        <option value="">همه هدف‌ها</option>
                        @foreach($dietTypeOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                    @error('dietType')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="suggestion-field">
                    <label for="weight-loss-medication" class="suggestion-field__label"><span class="suggestion-field__number">۳</span> آمپول یا داروی لاغری</label>
                    <select id="weight-loss-medication" class="form-select @error('weightLossMedication') is-invalid @enderror" wire:model="weightLossMedication">
                        <option value="">تفاوتی ندارد</option>
                        @foreach($medicationOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                    @error('weightLossMedication')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="suggestion-field">
                    <label for="athlete-or-not" class="suggestion-field__label"><span class="suggestion-field__number">۴</span> وضعیت ورزش</label>
                    <select id="athlete-or-not" class="form-select @error('athleteOrNot') is-invalid @enderror" wire:model="athleteOrNot">
                        <option value="">تفاوتی ندارد</option>
                        @foreach($athleteOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                    @error('athleteOrNot')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="suggestion-field suggestion-field--session">
                    <label class="suggestion-field__label"><span class="suggestion-field__number">۵</span> بازه جلسات</label>
                    <div class="session-range">
                        <div class="session-range__field">
                            <label class="visually-hidden" for="session-from">از جلسه</label>
                            <input id="session-from" type="number" min="1" inputmode="numeric" placeholder="از جلسه، مثلاً ۱"
                                   class="form-control @error('sessionFrom') is-invalid @enderror" wire:model="sessionFrom">
                            @error('sessionFrom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <span class="session-range__separator" aria-hidden="true"><i class="uil uil-arrow-left"></i></span>
                        <div class="session-range__field">
                            <label class="visually-hidden" for="session-to">تا جلسه</label>
                            <input id="session-to" type="number" min="1" inputmode="numeric" placeholder="تا جلسه، مثلاً ۴"
                                   class="form-control @error('sessionTo') is-invalid @enderror" wire:model="sessionTo">
                            @error('sessionTo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="suggestion-field suggestion-field--plan">
                    <label for="diet-plan" class="suggestion-field__label"><span class="suggestion-field__number">۶</span> پلن رژیم پیشنهادی</label>
                    <select id="diet-plan" class="form-select @error('dietPlanId') is-invalid @enderror" wire:model="dietPlanId">
                        <option value="">یک پلن رژیم انتخاب کنید</option>
                        @foreach($dietPlans as $plan)<option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->day_count }} روز)</option>@endforeach
                    </select>
                    @error('dietPlanId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="suggestion-form__actions">
                <button class="btn suggestion-btn suggestion-btn--primary" type="submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save"><i class="uil {{ $editingId ? 'uil-check' : 'uil-plus' }}"></i> {{ $editingId ? 'ذخیره تغییرات' : 'افزودن قاعده' }}</span>
                    <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> در حال ذخیره...</span>
                </button>
                @if($editingId)
                    <button class="btn suggestion-btn suggestion-btn--secondary" type="button" wire:click="resetForm"><i class="uil uil-times"></i> انصراف</button>
                @endif
            </div>
        </form>
    </section>

    <section class="suggestion-panel suggestion-list-panel">
        <div class="suggestion-panel__header suggestion-panel__header--list">
            <div class="suggestion-panel__heading">
                <span class="suggestion-panel__icon suggestion-panel__icon--soft" aria-hidden="true"><i class="uil uil-list-ul"></i></span>
                <div><h2>قواعد پیشنهاد</h2><p>قاعده‌ها را فیلتر، بررسی و مدیریت کنید.</p></div>
            </div>
            <div class="suggestion-filters">
                <div class="suggestion-filter">
                    <label for="suggestion-budget-filter">بودجه</label>
                    <select id="suggestion-budget-filter" class="form-select" wire:model.live="filterBudget">
                        <option value="">همه بودجه‌ها</option>
                        @foreach($budgetOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="suggestion-filter">
                    <label for="suggestion-diet-type-filter">هدف</label>
                    <select id="suggestion-diet-type-filter" class="form-select" wire:model.live="filterDietType">
                        <option value="">همه هدف‌ها</option>
                        @foreach($dietTypeOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
                <div class="suggestion-filter">
                    <label for="suggestion-athlete-filter">وضعیت ورزش</label>
                    <select id="suggestion-athlete-filter" class="form-select" wire:model.live="filterAthlete">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($athleteOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="suggestion-table-wrap" wire:loading.class="is-loading" wire:target="filterBudget,filterDietType,filterAthlete,delete">
            <table class="suggestion-table">
                <thead><tr><th>بودجه</th><th>هدف</th><th>داروی لاغری</th><th>ورزش</th><th>بازه جلسات</th><th>پلن پیشنهادی</th><th>عملیات</th></tr></thead>
                <tbody>
                @forelse($suggestions as $item)
                    <tr wire:key="diet-suggestion-{{ $item->id }}">
                        <td data-label="بودجه"><span class="suggestion-badge suggestion-badge--budget">{{ $budgetOptions[$item->food_budget] ?? '---' }}</span></td>
                        <td data-label="هدف">{{ $item->diet_type === null ? 'همه هدف‌ها' : ($dietTypeOptions[$item->diet_type] ?? '---') }}</td>
                        <td data-label="داروی لاغری">{{ $item->weight_loss_medication === null ? 'تفاوتی ندارد' : ($medicationOptions[$item->weight_loss_medication] ?? '---') }}</td>
                        <td data-label="ورزش">{{ $item->athlete_or_not === null ? 'تفاوتی ندارد' : ($athleteOptions[$item->athlete_or_not] ?? '---') }}</td>
                        <td data-label="بازه جلسات"><span class="suggestion-session-pill">جلسه {{ $item->session_from }} تا {{ $item->session_to }}</span></td>
                        <td data-label="پلن پیشنهادی">
                            <a class="suggestion-plan-link" href="{{ route('admin.diet_plan.edit', $item->dietPlan) }}"><i class="uil uil-file-medical-alt"></i> {{ $item->dietPlan?->name ?: '---' }}</a>
                        </td>
                        <td data-label="عملیات">
                            <div class="suggestion-row-actions">
                                <button type="button" class="suggestion-icon-btn suggestion-icon-btn--edit" wire:click="edit({{ $item->id }})" title="ویرایش قاعده" aria-label="ویرایش قاعده"><i class="uil uil-pen"></i><span>ویرایش</span></button>
                                <button type="button" class="suggestion-icon-btn suggestion-icon-btn--delete" wire:click="delete({{ $item->id }})" wire:confirm="این قاعده حذف شود؟" title="حذف قاعده" aria-label="حذف قاعده"><i class="uil uil-trash-alt"></i><span>حذف</span></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="suggestion-empty-row"><td colspan="7">
                        <div class="suggestion-empty">
                            <span class="suggestion-empty__icon"><i class="uil uil-clipboard-blank"></i></span>
                            <h3>هنوز قاعده‌ای ثبت نشده است</h3>
                            <p>از فرم بالا اولین قاعده پیشنهاد رژیم را ایجاد کنید.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @push('styles')
        <style>
            .diet-suggestion-page{--sg-primary:#0878c9;--sg-primary-dark:#0564aa;--sg-ink:#17233c;--sg-muted:#74809a;--sg-line:#e7ebf2;--sg-surface:#fff;padding:.75rem 0 1.75rem;color:var(--sg-ink)}
            .suggestion-hero{position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:1rem;padding:1.1rem 1.35rem;border:1px solid rgba(8,120,201,.12);border-radius:16px;background:radial-gradient(circle at 8% 15%,rgba(74,187,255,.2),transparent 24%),linear-gradient(135deg,#f7fcff 0%,#eef8ff 52%,#fff 100%);box-shadow:0 10px 26px rgba(35,63,93,.06)}
            .suggestion-hero::after{content:'';position:absolute;inset-inline-end:-45px;inset-block-end:-85px;width:230px;height:230px;border:34px solid rgba(8,120,201,.05);border-radius:50%;pointer-events:none}
            .suggestion-hero__content,.suggestion-panel__heading{display:flex;align-items:center;gap:.75rem;min-width:0}
            .suggestion-hero__icon{display:grid;flex:0 0 48px;width:48px;height:48px;place-items:center;border-radius:14px;color:#fff;background:linear-gradient(145deg,#1599e5,#066cb9);box-shadow:0 8px 18px rgba(8,120,201,.2);font-size:1.5rem}
            .suggestion-hero__eyebrow{margin-bottom:.15rem;color:var(--sg-primary);font-size:.68rem;font-weight:700}
            .suggestion-hero__title{margin:0;color:var(--sg-ink);font-size:clamp(1.2rem,1.7vw,1.55rem);font-weight:800;line-height:1.4}
            .suggestion-hero__description{max-width:720px;margin:.2rem 0 0;color:var(--sg-muted);font-size:.8rem;line-height:1.7}
            .suggestion-hero__stat{position:relative;z-index:1;display:flex;flex:0 0 auto;align-items:center;gap:.5rem;padding:.55rem .75rem;border:1px solid rgba(8,120,201,.12);border-radius:11px;background:rgba(255,255,255,.78);backdrop-filter:blur(8px)}
            .suggestion-hero__stat-value{color:var(--sg-primary);font-size:1.05rem;font-weight:800}.suggestion-hero__stat-label{color:var(--sg-muted);font-size:.68rem;white-space:nowrap}
            .suggestion-panel{overflow:hidden;margin-bottom:1rem;border:1px solid var(--sg-line);border-radius:15px;background:var(--sg-surface);box-shadow:0 8px 24px rgba(24,38,67,.05)}
            .suggestion-panel__header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.9rem 1.1rem;border-bottom:1px solid var(--sg-line)}
            .suggestion-panel__heading h2{margin:0 0 .1rem;color:var(--sg-ink);font-size:.92rem;font-weight:800}.suggestion-panel__heading p{margin:0;color:var(--sg-muted);font-size:.72rem;line-height:1.6}
            .suggestion-panel__icon{display:grid;flex:0 0 34px;width:34px;height:34px;place-items:center;border-radius:10px;font-size:1rem}.suggestion-panel__icon--primary{color:#fff;background:linear-gradient(145deg,#1599e5,#0878c9);box-shadow:0 6px 14px rgba(8,120,201,.18)}.suggestion-panel__icon--soft{color:var(--sg-primary);background:#eaf6ff}
            .suggestion-form{padding:1.1rem}.suggestion-form__grid{display:grid;grid-template-columns:repeat(12,minmax(0,1fr));gap:.9rem 1rem}.suggestion-field{grid-column:span 3;min-width:0}.suggestion-field--session,.suggestion-field--plan{grid-column:span 6}
            .suggestion-field__label{display:flex;align-items:center;gap:.35rem;min-height:22px;margin-bottom:.4rem;color:#34415b;font-size:.74rem;font-weight:700}.suggestion-field__number{display:inline-grid;width:18px;height:18px;place-items:center;border-radius:5px;color:var(--sg-primary);background:#eaf6ff;font-size:.6rem;font-weight:800}
            .diet-suggestion-page .form-select,.diet-suggestion-page .form-control{min-height:40px;border-color:#dfe5ee;border-radius:9px;color:#34415b;background-color:#fbfcfe;font-size:.78rem;box-shadow:none;transition:border-color .2s ease,box-shadow .2s ease,background-color .2s ease}.diet-suggestion-page .form-select:hover,.diet-suggestion-page .form-control:hover{border-color:#c7d4e4;background-color:#fff}.diet-suggestion-page .form-select:focus,.diet-suggestion-page .form-control:focus{border-color:rgba(8,120,201,.7);background-color:#fff;box-shadow:0 0 0 3px rgba(8,120,201,.08)}
            .session-range{display:grid;grid-template-columns:minmax(0,1fr) 24px minmax(0,1fr);align-items:start;gap:.4rem}.session-range__field{min-width:0}.session-range__separator{display:grid;width:24px;height:40px;place-items:center;color:#9ba7ba;font-size:.85rem}
            .suggestion-form__actions{display:flex;align-items:center;gap:.55rem;margin-top:1rem;padding-top:.85rem;border-top:1px dashed var(--sg-line)}
            .suggestion-btn{display:inline-flex;min-height:38px;align-items:center;justify-content:center;gap:.35rem;padding:.45rem .9rem;border-radius:9px;font-size:.76rem;font-weight:700;transition:transform .2s ease,box-shadow .2s ease,background-color .2s ease}.suggestion-btn span{display:inline-flex;align-items:center;gap:.35rem}.suggestion-btn--primary{border-color:var(--sg-primary);color:#fff;background:linear-gradient(135deg,#1599e5,#0878c9);box-shadow:0 7px 16px rgba(8,120,201,.2)}.suggestion-btn--primary:hover,.suggestion-btn--primary:focus{border-color:var(--sg-primary-dark);color:#fff;background:linear-gradient(135deg,#108dd5,#0564aa);box-shadow:0 9px 20px rgba(8,120,201,.25);transform:translateY(-1px)}.suggestion-btn--secondary{border-color:#e1e6ee;color:#59657a;background:#f6f8fb}.suggestion-btn--secondary:hover{color:#34415b;background:#edf1f6}
            .suggestion-badge{display:inline-flex;align-items:center;gap:.25rem;width:fit-content;padding:.25rem .5rem;border-radius:999px;font-size:.64rem;font-weight:700;white-space:nowrap}.suggestion-badge--edit{color:#9a6400;background:#fff5d8}.suggestion-badge--budget{color:#0871bb;background:#eaf6ff}
            .suggestion-panel__header--list{align-items:flex-end}.suggestion-filters{display:flex;align-items:flex-end;gap:.55rem}.suggestion-filter{width:175px}.suggestion-filter label{display:block;margin-bottom:.25rem;color:var(--sg-muted);font-size:.64rem;font-weight:700}.suggestion-filter .form-select{min-height:36px;font-size:.72rem}
            .suggestion-table-wrap{position:relative;overflow-x:auto;transition:opacity .2s ease}.suggestion-table-wrap.is-loading{opacity:.55;pointer-events:none}.suggestion-table{width:100%;min-width:780px;margin:0;border-collapse:collapse}.suggestion-table th{padding:.65rem .8rem;border-bottom:1px solid var(--sg-line);color:#77839a;background:#f8fafc;font-size:.64rem;font-weight:700;text-align:right;white-space:nowrap}.suggestion-table td{padding:.75rem .8rem;border-bottom:1px solid #edf0f5;color:#3f4b61;font-size:.72rem;vertical-align:middle}.suggestion-table tbody tr:not(.suggestion-empty-row){transition:background-color .2s ease}.suggestion-table tbody tr:not(.suggestion-empty-row):hover{background:#fbfdff}.suggestion-table tbody tr:last-child td{border-bottom:0}
            .suggestion-session-pill{display:inline-flex;padding:.25rem .48rem;border:1px solid #e4e9f0;border-radius:7px;color:#5f6c82;background:#fafbfd;white-space:nowrap}.suggestion-plan-link{display:inline-flex;align-items:center;gap:.3rem;color:var(--sg-primary);font-weight:700}.suggestion-plan-link:hover{color:var(--sg-primary-dark)}
            .suggestion-row-actions{display:flex;align-items:center;gap:.35rem}.suggestion-icon-btn{display:inline-flex;min-height:29px;align-items:center;justify-content:center;gap:.25rem;padding:.28rem .48rem;border:1px solid transparent;border-radius:7px;background:transparent;font-family:inherit;font-size:.64rem;font-weight:700;cursor:pointer;transition:background-color .2s ease,border-color .2s ease,transform .2s ease}.suggestion-icon-btn:hover{transform:translateY(-1px)}.suggestion-icon-btn--edit{border-color:#cde8fa;color:#0878c9;background:#f0f9ff}.suggestion-icon-btn--delete{border-color:#f5d5d9;color:#cf4252;background:#fff6f7}
            .suggestion-empty{display:flex;min-height:180px;flex-direction:column;align-items:center;justify-content:center;padding:1.5rem;text-align:center}.suggestion-empty__icon{display:grid;width:46px;height:46px;margin-bottom:.65rem;place-items:center;border-radius:14px;color:#7b91ad;background:#f0f4f8;font-size:1.4rem}.suggestion-empty h3{margin:0 0 .25rem;color:#3b485f;font-size:.82rem;font-weight:800}.suggestion-empty p{margin:0;color:#8b96a8;font-size:.68rem}
            @media(max-width:1199.98px){.suggestion-form__grid{grid-template-columns:repeat(2,minmax(0,1fr))}.suggestion-field,.suggestion-field--session,.suggestion-field--plan{grid-column:span 1}.suggestion-field--plan{grid-column:span 2}.suggestion-panel__header--list{align-items:flex-start;flex-direction:column}.suggestion-filters{width:100%}.suggestion-filter{width:auto;flex:1 1 0}}
            @media(max-width:767.98px){.diet-suggestion-page{padding-top:.5rem}.suggestion-hero{align-items:flex-start;flex-direction:column;padding:.9rem;border-radius:13px}.suggestion-hero__content{align-items:flex-start}.suggestion-hero__icon{flex-basis:42px;width:42px;height:42px;border-radius:12px;font-size:1.3rem}.suggestion-hero__description{font-size:.74rem}.suggestion-hero__stat{width:100%;justify-content:center}.suggestion-panel{border-radius:13px}.suggestion-panel__header,.suggestion-form{padding:.85rem}.suggestion-panel__header{align-items:flex-start;flex-direction:column}.suggestion-panel__heading{align-items:flex-start}.suggestion-form__grid{grid-template-columns:minmax(0,1fr);gap:.8rem}.suggestion-field,.suggestion-field--session,.suggestion-field--plan{grid-column:span 1}.suggestion-form__actions{align-items:stretch;flex-direction:column}.suggestion-btn{width:100%}.suggestion-filters{align-items:stretch;flex-direction:column}.suggestion-filter{width:100%}.suggestion-table-wrap{overflow:visible;padding:.6rem;background:#f7f9fc}.suggestion-table{min-width:0}.suggestion-table thead{display:none}.suggestion-table,.suggestion-table tbody,.suggestion-table tr,.suggestion-table td{display:block;width:100%}.suggestion-table tbody{display:grid;gap:.6rem}.suggestion-table tbody tr:not(.suggestion-empty-row){overflow:hidden;padding:.2rem .75rem;border:1px solid var(--sg-line);border-radius:12px;background:#fff;box-shadow:0 5px 16px rgba(26,46,73,.045)}.suggestion-table tbody tr:not(.suggestion-empty-row):hover{background:#fff}.suggestion-table td{display:flex;min-height:40px;align-items:center;justify-content:space-between;gap:.75rem;padding:.55rem 0;border-bottom:1px dashed #e8ecf2;text-align:left}.suggestion-table td::before{content:attr(data-label);flex:0 0 82px;color:#8792a5;font-size:.62rem;font-weight:700;text-align:right}.suggestion-table td:last-child{border-bottom:0}.suggestion-row-actions{justify-content:flex-end}.suggestion-empty-row td{display:block;padding:0;border:0}.suggestion-empty-row td::before{display:none}.suggestion-empty{min-height:165px;border:1px solid var(--sg-line);border-radius:12px;background:#fff}}
            @media(max-width:420px){.suggestion-hero__content{gap:.6rem}.suggestion-hero__eyebrow{display:none}.suggestion-hero__title{font-size:1.1rem}.session-range{grid-template-columns:minmax(0,1fr) 18px minmax(0,1fr);gap:.25rem}.suggestion-table td{align-items:flex-start;flex-direction:column;gap:.35rem;text-align:right}.suggestion-table td::before{flex-basis:auto}.suggestion-row-actions,.suggestion-icon-btn{width:100%}}
        </style>
    @endpush
</div>
