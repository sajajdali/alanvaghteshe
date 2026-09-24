<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">مدیریت آن‌بوردینگ اپلیکیشن</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-xl-8 col-lg-10 col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div>
                        <h3 class="card-title">پیشنهاد بعد از ثبت‌نام</h3>
                        <p class="text-muted mb-0 mt-1">فقط یکی از گزینه‌های زیر برای کاربران جدید فعال می‌شود.</p>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-row mb-4">
                        <div class="col-md-8 col-12 mb-3 mb-md-0">
                            <label class="form-label mb-2">وضعیت آن‌بوردینگ</label>
                            <p class="text-muted mb-0">
                                {{ $isActive ? 'پیشنهاد انتخاب‌شده برای کاربران جدید فعال است.' : 'در حالت غیرفعال هیچ پیشنهادی به کاربران نمایش داده نمی‌شود.' }}
                            </p>
                        </div>
                        <div class="col-md-4 col-12">
                            <label class="rdiobox mb-2" for="onboarding-active">
                                <input
                                    id="onboarding-active"
                                    type="radio"
                                    name="onboarding-status"
                                    value="1"
                                    wire:model.live="isActive"
                                    class="radio-success">
                                <span>فعال</span>
                            </label>
                            <label class="rdiobox mb-0" for="onboarding-inactive">
                                <input
                                    id="onboarding-inactive"
                                    type="radio"
                                    name="onboarding-status"
                                    value="0"
                                    wire:model.live="isActive"
                                    class="radio-danger">
                                <span>غیرفعال</span>
                            </label>
                        </div>
                    </div>

                    <hr class="my-4">

                    @if($isActive)
                    <div class="mb-4">
                        <label class="form-label">نوع پیشنهاد</label>
                        @foreach($modes as $modeOption)
                            <label class="rdiobox mb-3" for="onboarding-mode-{{ $modeOption->value }}">
                                <input
                                    id="onboarding-mode-{{ $modeOption->value }}"
                                    type="radio"
                                    name="onboarding-mode"
                                    value="{{ $modeOption->value }}"
                                    wire:model.live="mode"
                                    class="radio-primary">
                                <span>{{ $modeOption->getName() }}</span>
                            </label>
                        @endforeach
                        @error('mode')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($mode === \Modules\Onboarding\Enum\OnboardingModeEnum::FREE_PACKAGE->value)
                        <div class="mb-4">
                            <label for="onboarding-package" class="form-label">پکیج رایگان</label>
                            <select
                                id="onboarding-package"
                                wire:model="packageId"
                                class="form-control form-select @error('packageId') is-invalid @enderror">
                                <option value="">انتخاب پکیج</option>
                                @foreach($packages as $package)
                                    <option value="{{ $package->id }}">
                                        {{ $package->name }} — {{ $package->days }} روز — {{ $package->type->getName() }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-2">
                                مدت دسترسی از تعداد روز تعریف‌شده روی خود پکیج خوانده می‌شود.
                            </small>
                            @error('packageId')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @elseif($mode === \Modules\Onboarding\Enum\OnboardingModeEnum::COUPON->value)
                        <div class="mb-4">
                            <label for="onboarding-coupon" class="form-label">کد تخفیف</label>
                            <select
                                id="onboarding-coupon"
                                wire:model="couponId"
                                class="form-control form-select @error('couponId') is-invalid @enderror">
                                <option value="">انتخاب کد تخفیف</option>
                                @foreach($coupons as $coupon)
                                    <option value="{{ $coupon->id }}">
                                        {{ $coupon->title }} ({{ $coupon->code }}) —
                                        {{ number_format($coupon->value) }}{{ $coupon->is_percent ? '٪' : ' ریال' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-2">
                                درصد، تاریخ اعتبار و محدودیت مصرف از تنظیمات خود کد تخفیف خوانده می‌شود.
                            </small>
                            @error('couponId')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="alert alert-info mb-4">
                            این حالت پیشنهاد مالی ندارد، اما صفحه معرفی Onboarding با متن‌های زیر نمایش داده می‌شود.
                        </div>
                    @endif

                    <hr class="my-4">

                    <div wire:key="onboarding-content-{{ $mode }}">
                        <h4 class="mb-1">متن‌های صفحه</h4>
                        <p class="text-muted mb-4">
                            متن‌های این بخش فقط برای حالت «{{ \Modules\Onboarding\Enum\OnboardingModeEnum::from($mode)->getName() }}» ذخیره می‌شوند.
                            در متن‌ها می‌توانی از <code>{name}</code> برای نام کاربر و <code>{days}</code> برای تعداد روز پکیج استفاده کنی.
                        </p>

                        @if($mode === \Modules\Onboarding\Enum\OnboardingModeEnum::COUPON->value)
                            <div class="form-row">
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-badge">متن برچسب بالای صفحه</label>
                                    <input id="coupon-badge" type="text" class="form-control @error('contents.coupon.badge') is-invalid @enderror" wire:model="contents.coupon.badge">
                                    @error('contents.coupon.badge') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-title">عنوان خوش‌آمدگویی</label>
                                    <input id="coupon-title" type="text" class="form-control @error('contents.coupon.title') is-invalid @enderror" wire:model="contents.coupon.title">
                                    @error('contents.coupon.title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="coupon-subtitle">توضیح خوش‌آمدگویی</label>
                                    <textarea id="coupon-subtitle" rows="2" class="form-control @error('contents.coupon.subtitle') is-invalid @enderror" wire:model="contents.coupon.subtitle"></textarea>
                                    @error('contents.coupon.subtitle') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-offer-title">عنوان تخفیف</label>
                                    <input id="coupon-offer-title" type="text" class="form-control @error('contents.coupon.offer_title') is-invalid @enderror" wire:model="contents.coupon.offer_title">
                                    @error('contents.coupon.offer_title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-label">عنوان کد تخفیف</label>
                                    <input id="coupon-label" type="text" class="form-control @error('contents.coupon.coupon_label') is-invalid @enderror" wire:model="contents.coupon.coupon_label">
                                    @error('contents.coupon.coupon_label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-scope">متن محدوده استفاده</label>
                                    <input id="coupon-scope" type="text" class="form-control @error('contents.coupon.scope_text') is-invalid @enderror" wire:model="contents.coupon.scope_text">
                                    @error('contents.coupon.scope_text') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-timer">متن زمان باقی‌مانده</label>
                                    <input id="coupon-timer" type="text" class="form-control @error('contents.coupon.timer_text') is-invalid @enderror" wire:model="contents.coupon.timer_text">
                                    @error('contents.coupon.timer_text') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-primary-button">متن دکمه اصلی</label>
                                    <input id="coupon-primary-button" type="text" class="form-control @error('contents.coupon.primary_button') is-invalid @enderror" wire:model="contents.coupon.primary_button">
                                    @error('contents.coupon.primary_button') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="coupon-secondary-button">متن دکمه رد کردن</label>
                                    <input id="coupon-secondary-button" type="text" class="form-control @error('contents.coupon.secondary_button') is-invalid @enderror" wire:model="contents.coupon.secondary_button">
                                    @error('contents.coupon.secondary_button') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @elseif($mode === \Modules\Onboarding\Enum\OnboardingModeEnum::FREE_PACKAGE->value)
                            <div class="form-row">
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-badge">متن برچسب بالای صفحه</label>
                                    <input id="package-badge" type="text" class="form-control @error('contents.free_package.badge') is-invalid @enderror" wire:model="contents.free_package.badge">
                                    @error('contents.free_package.badge') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-title">عنوان خوش‌آمدگویی</label>
                                    <input id="package-title" type="text" class="form-control @error('contents.free_package.title') is-invalid @enderror" wire:model="contents.free_package.title">
                                    @error('contents.free_package.title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-offer-suffix">عنوان پیشنهاد بعد از تعداد روز</label>
                                    <input id="package-offer-suffix" type="text" class="form-control @error('contents.free_package.offer_suffix') is-invalid @enderror" wire:model="contents.free_package.offer_suffix">
                                    @error('contents.free_package.offer_suffix') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-timer">متن زمان فعال‌سازی</label>
                                    <input id="package-timer" type="text" class="form-control @error('contents.free_package.timer_text') is-invalid @enderror" wire:model="contents.free_package.timer_text">
                                    @error('contents.free_package.timer_text') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="package-description">توضیحات پیشنهاد رایگان</label>
                                    <textarea id="package-description" rows="2" class="form-control @error('contents.free_package.description') is-invalid @enderror" wire:model="contents.free_package.description"></textarea>
                                    @error('contents.free_package.description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="package-benefits-title">عنوان امکانات</label>
                                    <input id="package-benefits-title" type="text" class="form-control @error('contents.free_package.benefits_title') is-invalid @enderror" wire:model="contents.free_package.benefits_title">
                                    @error('contents.free_package.benefits_title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                @foreach([1, 2, 3] as $benefitIndex)
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="package-benefit-{{ $benefitIndex }}">امکان {{ $benefitIndex }}</label>
                                        <input id="package-benefit-{{ $benefitIndex }}" type="text" class="form-control @error('contents.free_package.benefit_'.$benefitIndex) is-invalid @enderror" wire:model="contents.free_package.benefit_{{ $benefitIndex }}">
                                        @error('contents.free_package.benefit_'.$benefitIndex) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                @endforeach
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-primary-button">متن دکمه اصلی</label>
                                    <input id="package-primary-button" type="text" class="form-control @error('contents.free_package.primary_button') is-invalid @enderror" wire:model="contents.free_package.primary_button">
                                    @error('contents.free_package.primary_button') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="package-secondary-button">متن دکمه رد کردن</label>
                                    <input id="package-secondary-button" type="text" class="form-control @error('contents.free_package.secondary_button') is-invalid @enderror" wire:model="contents.free_package.secondary_button">
                                    @error('contents.free_package.secondary_button') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @else
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="normal-title">عنوان اصلی</label>
                                    <textarea id="normal-title" rows="2" class="form-control @error('contents.none.title') is-invalid @enderror" wire:model="contents.none.title"></textarea>
                                    @error('contents.none.title') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="normal-description">توضیحات</label>
                                    <textarea id="normal-description" rows="3" class="form-control @error('contents.none.description') is-invalid @enderror" wire:model="contents.none.description"></textarea>
                                    @error('contents.none.description') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                @foreach([1, 2, 3] as $statIndex)
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="normal-stat-value-{{ $statIndex }}">مقدار آمار {{ $statIndex }}</label>
                                        <input id="normal-stat-value-{{ $statIndex }}" type="text" class="form-control @error('contents.none.stat_'.$statIndex.'_value') is-invalid @enderror" wire:model="contents.none.stat_{{ $statIndex }}_value">
                                        @error('contents.none.stat_'.$statIndex.'_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 col-12 mb-3">
                                        <label for="normal-stat-label-{{ $statIndex }}">عنوان آمار {{ $statIndex }}</label>
                                        <input id="normal-stat-label-{{ $statIndex }}" type="text" class="form-control @error('contents.none.stat_'.$statIndex.'_label') is-invalid @enderror" wire:model="contents.none.stat_{{ $statIndex }}_label">
                                        @error('contents.none.stat_'.$statIndex.'_label') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                @endforeach
                                <div class="col-md-6 col-12 mb-3">
                                    <label for="normal-primary-button">متن دکمه اصلی</label>
                                    <input id="normal-primary-button" type="text" class="form-control @error('contents.none.primary_button') is-invalid @enderror" wire:model="contents.none.primary_button">
                                    @error('contents.none.primary_button') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <button
                        type="button"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save"
                        class="btn btn-success">
                        <span wire:loading.remove wire:target="save">ذخیره تنظیمات</span>
                        <span wire:loading wire:target="save">در حال ذخیره...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
