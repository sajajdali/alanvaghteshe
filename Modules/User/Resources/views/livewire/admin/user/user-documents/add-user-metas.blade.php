<div>
    @if ($msgAddMeta)
        <div class="alert alert-primary alert-dismissible fade show" role="alert"> <span
                class="alert-inner--text">{{ $msgAddMeta }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button>
        </div>
    @endif
    <form wire:submit='saveUserMetas'>
        <div class="row mb-4">
            <div class="col-md-4">
                <label for="diet_plan" class="form-label">هدف کلی</label>
                <select wire:model='diet_plan' class="form-select" id="diet_plan">
                    <option value="">انتخاب کنید...</option>
                    <option value="0">کاهش
                        وزن
                    </option>
                    <option value="1">افزایش
                        وزن
                    </option>
                    <option value="2">تثبیت
                        وزن
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="target_plan" class="form-label">روش رسیده به هدف</label>
                <select wire:model='target_plan' class="form-select" id="target_plan">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="10">رژیم
                    </option>
                    <option value="20">ورزش
                    </option>
                    <option value="30">هر دو
                    </option>
                </select>
            </div>
            <div class="col-md-4  mt-md-2">
                <div class="form-group">
                    <label for="birthday_picker" class="form-label">تاریخ تولد</label>
                    <input autocomplete="off" wire:model='birthday' type="text" class="form-control"
                           id="birthday_picker">
                </div>
            </div>
            <div class="col-md-4  mt-md-2">
                <div class="form-group">
                    <label for="tall" class="form-label">قد</label>
                    <input wire:model='tall' type="text" class="form-control" id="tall">
                </div>
            </div>
            <div class="col-md-4  mt-md-2">
                <div class="form-group">
                    <label for="weight" class="form-label">وزن</label>
                    <input wire:model='weight' type="text" class="form-control" id="weight">
                </div>
            </div>
            <div class="col-md-4  mt-md-2">
                <div class="form-group">
                    <label for="target_weight" class="form-label">وزن هدف</label>
                    <input wire:model='target_weight' type="text" class="form-control" id="target_weight">
                </div>
            </div>
            <div class="col-md-4  mt-md-2">
                <div class="form-group">
                    <label for="body_fat" class="form-label">درصد چربی</label>
                    <input wire:model='body_fat' type="text" class="form-control" id="body_fat">
                </div>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="daily_water_consumption" class="form-label">میزان مصرف مایعات</label>
                <select wire:model='daily_water_consumption' class="form-select" id="daily_water_consumption">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">فقط چای و قهوه</option>
                    <option value="1"> ۲ تا ۴ لیوان آب</option>
                    <option value="2">۴ تا ۶ لیوان آب</option>
                    <option value="3">بیشتر از ۶ لیوان</option>
                </select>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="body_physical_style" class="form-label">استایل بدن</label>
                <select wire:model='body_physical_style' class="form-select" id="body_physical_style">
                    <option selected value="">انتخاب کنید...</option>
                    @foreach(\Modules\Diet\Entities\Condition::bodyStyle()->first()->options['items']['values'] as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
{{--            <div class="col-md-4 mt-md-2">--}}
{{--                <label for="type_daily_work" class="form-label">نوع کار روزانه</label>--}}
{{--                <select wire:model='type_daily_work' class="form-select" id="type_daily_work">--}}
{{--                    <option selected value="">انتخاب کنید...</option>--}}
{{--                    <option value="0">اکثر مواقع نشته ام</option>--}}
{{--                    <option value="1"> اکثر مواقع ایستاده ام</option>--}}
{{--                    <option value="2"> در خانه کار میکنم</option>--}}
{{--                    <option value="3"> بسیار فعال هستم</option>--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="col-md-4 mt-md-2">--}}
{{--                <label for="wake_up" class="form-label">چند ساعت میخوابی</label>--}}
{{--                <select wire:model='wake_up' class="form-select" id="wake_up">--}}
{{--                    <option selected value="">انتخاب کنید...</option>--}}
{{--                    <option value="0">کمتر از ۵</option>--}}
{{--                    <option value="1">بین ۵ تا ۷</option>--}}
{{--                    <option value="2">بین ۷ تا ۸</option>--}}
{{--                    <option value="3"> بیشتر از ۸</option>--}}
{{--                </select>--}}
{{--            </div>--}}
            <div class="col-md-4 mt-md-2">
                <label for="how_much_experience_sports" class="form-label">چند وقته ورزش
                    میکنی</label>
                <select wire:model='how_much_experience_sports' class="form-select" id="how_much_experience_sports">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">تازه میخوام شروع کنم</option>
                    <option value="1"> کمتر از ۳ ماه</option>
                    <option value="2">بیشتر از ۳ ماه</option>
                </select>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="target_of_exercise" class="form-label">هدف از ورزش
                    کردن</label>
                <select wire:model='target_of_exercise' class="form-select" id="target_of_exercise">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">کات و تفکیک عضلات</option>
                    <option value="1"> افزایش توده عضلات</option>
                    <option value="2">افزایش قدرت و حجم عضلات</option>
                </select>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="weight_change_per_week" class="form-label">چقدر در هفته میخواهید وزن کم کنید</label>
                <select wire:model='weight_change_per_week' class="form-select" id="weight_change_per_week">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">نیم کیلو در هفته</option>
                    <option value="1"> یک کیلو در هفته</option>
                    <option value="2">یک و نیم کیلو در هفته</option>
                </select>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="activity_per_week" class="form-label">میزان فعالیت</label>
                <select wire:model='activity_per_week' class="form-select" id="activity_per_week">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">فعالیت فیزیکی بسیار کم</option>
                    <option value="1"> فعالیت فیزیکی کم</option>
                    <option value="2">فعالیت فیزیکی متوسط</option>
                    <option value="3">فعالیت فیزیکی زیاد</option>
                    <option value="4">فعالیت فیزیکی بسیار زیاد</option>
                </select>
            </div>
            <div class="col-md-4 mt-md-2">
                <label for="how_many_days_week_exercise" class="form-label">چند روز در هفته ورزش
                    میکنی </label>
                <select wire:model='how_many_days_week_exercise' class="form-select"
                        id="how_many_days_week_exercise">
                    <option selected value="">انتخاب کنید...</option>
                    <option value="0">۱ بار</option>
                    <option value="1">۲ بار</option>
                    <option value="2">۳ بار</option>
                    <option value="3">۴ بار</option>
                    <option value="4">۵ بار</option>
                    <option value="5">۶ بار</option>
                </select>
            </div>
{{--            <div class="col-md-4 mt-md-2">--}}
{{--                <label for="habits" class="form-label">کدام مورد را روزانه استفاده--}}
{{--                    میکنید</label>--}}
{{--                <select multiple wire:model='habits' class="form-select" id="habits">--}}
{{--                    <option selected value="">انتخاب کنید...</option>--}}
{{--                    <option value="0">دخانیات</option>--}}
{{--                    <option value="1">نوشابه</option>--}}
{{--                    <option value="2">غذای شور</option>--}}
{{--                    <option value="3">غذای چرب</option>--}}
{{--                    <option value="4">الکل</option>--}}
{{--                    <option value="5">شیرینی جات</option>--}}
{{--                    <option value="6">شب بیداری</option>--}}
{{--                    <option value="7">هیچدام را استفاده نمیکنم</option>--}}
{{--                </select>--}}
{{--            </div>--}}

            <div class="col-md-4 mt-md-2">
                {{--                محدودیت غذایی--}}
                <label for="food_restriction"
                       class="form-label">{{\Modules\Diet\Enum\ConditionKeyEnum::getNameForId(3)}}</label>
                <select wire:model='food_restriction' class="form-select" id="food_restriction" multiple>
                    <option selected value="">انتخاب کنید...</option>
                    @foreach(\Modules\Diet\Entities\Condition::foodRestriction()->first()->options['items']['values'] as $key => $value)
                        <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
{{--            <div class="col-md-4 mt-md-2">--}}
{{--                <label for="weaknesses_body" class="form-label">نقاط ضعف بدن</label>--}}
{{--                <select multiple wire:model='weaknesses_body' class="form-select" id="weaknesses_body">--}}
{{--                    <option selected value="">انتخاب کنید...</option>--}}
{{--                    <option value="0">سینه ها</option>--}}
{{--                    <option value="1">بازو ها</option>--}}
{{--                    <option value="2">پاهای لاغر</option>--}}
{{--                    <option value="3">شکم چاق</option>--}}
{{--                    <option value="4">هیچ کدام از مشکالات بالا را ندارم</option>--}}
{{--                </select>--}}
{{--            </div>--}}
            <div class="col-md-12">
                <label for="user_disease" class="form-label">بیماری های کاربر</label>
                <select id="user_disease" wire:model='user_disease' class="form-control select2-style1"
                        data-placeholder="Choose One" multiple>
                    @foreach ($srerver_disease as $disease)
                        <option
                            {{ in_array($disease->id, $user_disease) ? 'selected' : '' }}  value="{{ $disease->id }}">{{ $disease->name }}</option>
                    @endforeach
                </select>
                <p class="text-muted ms-2">با انتخاب بیماری جدید برای کاربر ، بیماری های قبلی کاربر حذف خواهند شد</p>
            </div>
            <div class="col-md-12 d-flex justify-content-end mt-4">
                <button wire:loading.attr="disabled" type="submit" class="btn btn-success">
                    <span>اضافه کردن</span>
                </button>
            </div>
    </form>
</div>
</div>
@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            $('#birthday_picker').persianDatepicker({
                initialValue: false,
                autoClose: true,
                format: 'LLLL',
                onSelect: function (unix) {
                @this.set('birthday', unix / 1000)
                    ;
                },
            });
        });
    </script>
@endpush
