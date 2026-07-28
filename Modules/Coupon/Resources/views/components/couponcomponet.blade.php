<div>
    <form autocomplete="off" action="{{ $action }}" class="forms-sample" method="POST">
        @csrf
        @method('POST')
        <div class="row row-sm mt-5">
            <div class="col-md-12 col-sm-12">
                <div class="card box-shadow-0">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">ایجاد کد تخفیف جدید</h3>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex align-items-center">
                            <div class="form-group col-12">
                                <label for="inputName">عنوان</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="inputName" name="title" value="{{ old('title', $coupon?->title ?? '') }}"
                                       placeholder="عنوان کد تخفیف">
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="Code">کد تخفیف</label>
                                    <input type="text" class="form-control  @error('code') is-invalid @enderror"
                                           id="Code" name="code" value="{{ old('code', $coupon?->code ?? '') }}"
                                           placeholder="مثال: BG-58 ">
                                </div>
                                @error('code')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="value">مقدار</label>
                                    <input type="text" class="form-control  @error('value') is-invalid @enderror"
                                           id="value" name="value" value="{{ old('value', $coupon?->value ?? '') }}"
                                           placeholder="به درصد یا تومان">
                                </div>
                                @error('value')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label" for="type-dropdown">نوع تخفیف</label>
                                    <select name="is_percent" @error('is_percent') required @enderror
                                    class="form-control form-select" id="type-dropdown"
                                            data-bs-placeholder="Select Country">
                                        <option label="انتخاب کنید..."></option>
                                        <option @if (old('is_percent', $coupon?->is_percent ?? '') == '0') selected
                                                @endif value="0">ثابت
                                        </option>
                                        <option @if (old('is_percent', $coupon?->is_percent ?? '') == '1') selected
                                                @endif value="1">
                                            درصدی
                                        </option>
                                    </select>
                                </div>
                                @error('is_percent')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label class="form-label" for="available_for-dropdown">قابل استفاده برای</label>
                                    <select name="can_used_for" @error('can_used_for') required @enderror
                                    class="form-control form-select" id="available_for-dropdown"
                                            data-bs-placeholder="Select Country">
                                        <option label="انتخاب کنید..."></option>
                                        @foreach (\Modules\Coupon\Enum\CouponCanUsedForEnum::cases() as $couponTarget)
                                            <option
                                                @selected((int) old('can_used_for', $coupon?->can_used_for?->value ?? $coupon?->can_used_for ?? 0) === $couponTarget->value)
                                                value="{{ $couponTarget->value }}">
                                                {{ $couponTarget->getName() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('can_used_for')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="counter">تعداد قابل استفاده</label>
                                    <input type="text"
                                           class="form-control @error('usable_count') is-invalid @enderror" id="counter"
                                           name="usable_count"
                                           value="{{ old('usable_count', $coupon?->usable_count ?? '') }}">
                                </div>
                                @error('usable_count')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="counter">حداقل مبلغ برای استفاده</label>
                                    <input type="text"
                                           class="form-control @error('minimum_spend') is-invalid @enderror"
                                           id="counter" name="minimum_spend"
                                           value="{{ old('minimum_spend', $coupon?->minimum_spend ?? '') }}">
                                </div>
                                @error('minimum_spend')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="counter">حداکثر مبلغ برای استفاده </label>
                                    <input type="text"
                                           class="form-control @error('maximum_spend') is-invalid @enderror"
                                           id="counter" name="maximum_spend"
                                           value="{{ old('maximum_spend', $coupon?->maximum_spend ?? '') }}">
                                </div>
                                @error('maximum_spend')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="startDate">تاریخ شروع </label>
                                    <input type="text" class="form-control  @error('start_at') is-invalid @enderror"
                                           id="startDate" name="start_at"
                                           value="{{ old('start_at', $coupon?->start_at ? verta($coupon?->start_at)->format('Y-m-d')  :'') }}">
                                </div>
                                @error('start_at')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6 mt-2">
                                <div class="form-group">
                                    <label for="endDate">تاریخ پایان </label>
                                    <input type="text" class="form-control @error('end_at') is-invalid @enderror"
                                           id="endDate" name="end_at"
                                           value="{{ old('end_at', $coupon?->end_at ? verta($coupon?->end_at)->format('Y-m-d')  :'') }}">
                                </div>
                                @error('end_at')
                                <span class="text-danger mb-2 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <div class="form-group ms-3 mt-2">
                                    <div class="checkbox">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" name="active" data-checkboxes="mygroup"
                                                   {{ old('active', $coupon?->active ?? '') == 1 ? 'checked' : '' }}
                                                   class="custom-control-input" id="checkbox-2">
                                            <label for="checkbox-2" class="custom-control-label"> <span
                                                    class="text-bold">وضعیت فعال بودن کد تخفیف</span></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-10"></div>
                            <div class="col-md-2">
                                <div class="form-group mt-3">
                                    <div>
                                        <a href="{{ route('admin.coupon.index') }}" class="btn btn-secondary">بازگشت</a>
                                        <button type="submit" class="btn btn-success ms-2">ذخیره</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
    <style>
        .datepicker-day-view .title {
            font-weight: 100;
            margin-bottom: 0;
            margin-top: 0;
            min-height: 0;
        }
    </style>
    <link href="{{admin_asset('plugins/persiandate/persian-datepicker.min.css')}}" rel="stylesheet"/>
@endpush
@push('scripts')
    <script src="{{admin_asset('plugins/persiandate/persian-datepicker.min.js')}}"></script>
    <script src="{{admin_asset('plugins/persiandate/persian-date.min.js')}}"></script>
    <script>
        var to, from;
        to = $("#endDate").persianDatepicker({
            altField: '.range-to-example-alt',
            initialValue: false,
            format: 'L',
            autoClose: true,
            onSelect: function (unix) {
                to.touched = true;
                if (from && from.options && from.options.maxDate != unix) {
                    var cachedValue = from.getState().selected.unixDate;
                    from.options = {
                        maxDate: unix
                    };
                    if (from.touched) {
                        from.setDate(cachedValue);
                    }
                }
            }
        });
        from = $("#startDate").persianDatepicker({
            altField: '.range-from-example-alt',
            initialValue: false,
            format: 'L',
            autoClose: true,
            onSelect: function (unix) {
                from.touched = true;
                if (to && to.options && to.options.minDate != unix) {
                    var cachedValue = to.getState().selected.unixDate;
                    to.options = {
                        minDate: unix
                    };
                    if (to.touched) {
                        to.setDate(cachedValue);
                    }
                }
            }
        });
    </script>
@endpush
