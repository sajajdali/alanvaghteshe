<?php

namespace Modules\Onboarding\Livewire\Admin;

use Livewire\Attributes\Title;
use Livewire\Component;
use Modules\Coupon\Entities\Coupon;
use Modules\Onboarding\Entities\OnboardingSetting;
use Modules\Onboarding\Enum\OnboardingModeEnum;
use Modules\Package\Entities\Package;

#[Title('مدیریت آن‌بوردینگ اپلیکیشن')]
class OnboardingSettingForm extends Component
{
    public bool $isActive = false;

    public string $mode = OnboardingModeEnum::NONE->value;

    public ?int $packageId = null;

    public ?int $couponId = null;

    public array $contents = [];

    public function mount(): void
    {
        $setting = OnboardingSetting::current();

        $this->isActive = $setting->is_active;
        $this->mode = $setting->mode->value;
        $this->packageId = $setting->package_id;
        $this->couponId = $setting->coupon_id;
        $this->contents = $setting->getContentsWithDefaults();
    }

    public function updatedMode(): void
    {
        $this->resetValidation();

        if ($this->mode !== OnboardingModeEnum::FREE_PACKAGE->value) {
            $this->packageId = null;
        }

        if ($this->mode !== OnboardingModeEnum::COUPON->value) {
            $this->couponId = null;
        }
    }

    public function save(): void
    {
        $rules = [
            'isActive' => ['required', 'boolean'],
            'mode' => ['required', 'in:'.implode(',', array_column(OnboardingModeEnum::cases(), 'value'))],
            'packageId' => [
                'nullable',
                'integer',
                'exists:packages,id',
            ],
            'couponId' => [
                'nullable',
                'integer',
                'exists:coupons,id',
            ],
            'contents' => ['required', 'array'],
        ];

        if ($this->isActive) {
            foreach (array_keys(OnboardingSetting::DEFAULT_CONTENTS[$this->mode]) as $field) {
                $rules["contents.{$this->mode}.{$field}"] = ['required', 'string', 'max:500'];
            }
        }

        $validated = $this->validate($rules, [
            'mode.required' => 'نوع پیشنهاد را انتخاب کنید.',
            'mode.in' => 'نوع پیشنهاد انتخاب‌شده معتبر نیست.',
            'packageId.required_if' => 'پکیج رایگان را انتخاب کنید.',
            'packageId.exists' => 'پکیج انتخاب‌شده معتبر نیست.',
            'couponId.required_if' => 'کد تخفیف را انتخاب کنید.',
            'couponId.exists' => 'کد تخفیف انتخاب‌شده معتبر نیست.',
            'contents.*.*.required' => 'تکمیل این متن الزامی است.',
            'contents.*.*.max' => 'متن نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد.',
        ]);

        $mode = OnboardingModeEnum::from($validated['mode']);

        if ($this->isActive && $mode === OnboardingModeEnum::FREE_PACKAGE && ! $this->packageId) {
            $this->addError('packageId', 'پکیج رایگان را انتخاب کنید.');
            return;
        }

        if ($this->isActive && $mode === OnboardingModeEnum::COUPON && ! $this->couponId) {
            $this->addError('couponId', 'کد تخفیف را انتخاب کنید.');
            return;
        }

        OnboardingSetting::current()->update([
            'is_active' => $validated['isActive'],
            'mode' => $mode,
            'package_id' => $mode === OnboardingModeEnum::FREE_PACKAGE ? $this->packageId : null,
            'coupon_id' => $mode === OnboardingModeEnum::COUPON ? $this->couponId : null,
            'contents' => $this->contents,
        ]);

        session()->flash('success', 'تنظیمات آن‌بوردینگ با موفقیت ذخیره شد.');
    }

    public function render()
    {
        return view('onboarding::livewire.admin.onboarding-setting-form', [
            'modes' => OnboardingModeEnum::cases(),
            'packages' => Package::query()->active()->priority()->get(['id', 'name', 'days', 'type']),
            'coupons' => Coupon::query()->active()->orderByDesc('id')->get(['id', 'title', 'code', 'value', 'is_percent']),
        ]);
    }
}
