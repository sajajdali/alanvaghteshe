<div>
    <div class="page-header"><div><h1 class="page-title">پیشنهاد رژیم</h1><div class="text-muted mt-1">انتخاب پلن رژیم بر اساس بودجه کاربر و شماره جلسه</div></div></div>

    @include('admin::layouts.components.alert')

    <div class="card mb-4">
        <div class="card-header border-bottom"><h3 class="card-title">{{ $editingId ? 'ویرایش قاعده' : 'قاعده جدید' }}</h3></div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">بودجه برنامه غذایی</label>
                        <select class="form-select @error('foodBudget') is-invalid @enderror" wire:model="foodBudget">
                            <option value="">انتخاب کنید</option>
                            @foreach($budgetOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                        </select>
                        @error('foodBudget')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">آمپول یا داروی لاغری</label>
                        <select class="form-select @error('weightLossMedication') is-invalid @enderror" wire:model="weightLossMedication">
                            <option value="">مهم نیست</option>
                            @foreach($medicationOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('weightLossMedication')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">از جلسه</label>
                        <input type="number" min="1" class="form-control @error('sessionFrom') is-invalid @enderror" wire:model="sessionFrom">
                        @error('sessionFrom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">تا جلسه</label>
                        <input type="number" min="1" class="form-control @error('sessionTo') is-invalid @enderror" wire:model="sessionTo">
                        @error('sessionTo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">پلن رژیم</label>
                        <select class="form-select @error('dietPlanId') is-invalid @enderror" wire:model="dietPlanId">
                            <option value="">انتخاب کنید</option>
                            @foreach($dietPlans as $plan)<option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->day_count }} روز)</option>@endforeach
                        </select>
                        @error('dietPlanId')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">{{ $editingId ? 'ذخیره تغییرات' : 'افزودن قاعده' }}</button>
                    @if($editingId)<button class="btn btn-secondary" type="button" wire:click="resetForm">انصراف</button>@endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <h3 class="card-title">قواعد پیشنهاد</h3>
                <div class="text-muted mt-1">تنوع پلن‌ها و بازه جلسات هر سطح بودجه</div>
            </div>
            <div style="min-width: 280px">
                <label for="suggestion-budget-filter" class="form-label">فیلتر بر اساس بودجه</label>
                <select id="suggestion-budget-filter" class="form-select" wire:model.live="filterBudget">
                    <option value="">همه تنوع‌ها</option>
                    @foreach($budgetOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead><tr><th>بودجه</th><th>آمپول/داروی لاغری</th><th>جلسات</th><th>پلن پیشنهادی</th><th>عملیات</th></tr></thead>
                <tbody>
                @forelse($suggestions as $item)
                    <tr wire:key="diet-suggestion-{{ $item->id }}">
                        <td>{{ $budgetOptions[$item->food_budget] ?? '---' }}</td>
                        <td>
                            {{ $item->weight_loss_medication === null
                                ? 'مهم نیست'
                                : ($medicationOptions[$item->weight_loss_medication] ?? '---') }}
                        </td>
                        <td>از {{ $item->session_from }} تا {{ $item->session_to }}</td>
                        <td><a href="{{ route('admin.diet_plan.edit', $item->dietPlan) }}">{{ $item->dietPlan?->name ?: '---' }}</a></td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">ویرایش</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $item->id }})" wire:confirm="این قاعده حذف شود؟">حذف</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">هنوز قاعده‌ای ثبت نشده است.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
