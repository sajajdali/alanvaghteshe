<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                مشاهده برنامه
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')
    @unless($exercisePlanRequest)
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="alert alert-danger alert-dismissible fade show p-0 mb-0" role="alert"><p
                        class="py-3 px-5 mb-0 border-bottom border-bottom-danger-light"><span
                            class="alert-inner--icon me-2"><i
                                class="fe fe-slash"></i></span> <strong>خطا</strong></p>
                    <p class="py-3 px-5">درخواست مورد نظر یافت نشد</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
            </div>
        </div>
    @else
        @if($exercisePlanRequest->status->is(\Modules\Exercise\Enum\ExercisePlanRequestEnum::REQUESTED))
            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">
                                در حال آماده سازی برنامه
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ admin_default_asset('loading-exercise-plan.gif') }}" class="mb-2"
                                 style="width: 168px">
                            <p class="mb-2">هوش مصنوعی سایت در حال آماده سازی برنامه است.</p>
                            <div class="progress progress-md">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                     style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($exercisePlanRequest->status->is(\Modules\Exercise\Enum\ExercisePlanRequestEnum::COMPUTING))
            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h3 class="card-title">
                                در حال آماده سازی برنامه
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ admin_default_asset('loading-exercise-plan.gif') }}" class="mb-2"
                                 style="width: 168px">
                            <p class="mb-2">هوش مصنوعی سایت در حال آماده سازی برنامه است.</p>
                            <div class="progress progress-md">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                     style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($exercisePlanRequest->status->is(\Modules\Exercise\Enum\ExercisePlanRequestEnum::FAILED))
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom bg-danger">
                            <h3 class="card-title text-white">
                                خطا در آماده سازی برنامه
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger alert-dismissible fade show p-0 mb-0" role="alert"><p
                                    class="py-3 px-5 mb-0 border-bottom border-bottom-danger-light">
                                    <span
                                        class="alert-inner--icon me-2">
                                        <i class="fe fe-slash"></i></span> <strong>خطا در تجویز برنامه</strong></p>
                                <p class="py-3 px-5">
                                    برای تجویز برنامه ورزشی خطا زیر را برطرف کنید:
                                </p>
                                <p class="pb-3 px-5">
                                    {{ $exercisePlanRequest->message }}
                                </p>
                                <p class="pb-3 px-5">
                                    بعد از برطرف کردن خطا‌ها می‌توانید مجدداً برنامه را تجویز کنید.
                                </p>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">×</span></button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button
                                class="btn btn-success py-2"
                                wire:click="rePlan"
                                wire:loading.class="btn-loading btn-light disabled"
                                wire:loading.class.remove="btn-success">
                                برنامه‌دهی مجدد
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <div class="col-xl col-md-12">
                                <h4 class="my-2 card-title">
                                    جزییات درخواست
                                </h4>
                            </div>
                            <div class="col-xl-auto col-md0-1">
                                <div class="btn-list">
                                    <a href="{{ route('admin.exercise_plan_request.print',$exercisePlanRequest) }}" target="print_frame" class="btn btn-primary-light text-center me-2"
                                       data-bs-placement="top" data-bs-toggle="tooltip"
                                       data-bs-original-title="پرینت"><i class="fe fe-printer"></i></a>
                                    <a href="{{$donloadPdf ?? '#'}}" class="btn btn-danger-light text-center me-2 @if(empty($donloadPdf)) disabled @endif"
                                       data-bs-placement="top" data-bs-toggle="tooltip"
                                       data-bs-original-title="دانلود"><i class="fe fe-download"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-12 mb-3" wire:ignore>
                                    <label for="status">وضعیت تمرین</label>
                                    <select class="form-control before_select"
                                            id="status">
                                        <option value="2" @if($exercisePlanRequest->status->value===2) SELECTED @endif>
                                            تایید شده
                                        </option>
                                        <option value="5" @if($exercisePlanRequest->status->value===5) SELECTED @endif>
                                            در انتظار
                                        </option>
                                        <option value="4" @if($exercisePlanRequest->status->value===4) SELECTED @endif>
                                            پایان یافته
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3" wire:ignore>
                                    <label for="started_at">تاریخ شروع</label>
                                    <input type="text"
                                           value="{{ $exercisePlanRequest->start_at?->format('Y-m-d') }}"
                                           class="form-control" id="started_at">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3" wire:ignore>
                                    <label for="ended_at">تاریخ پایان</label>
                                    <input type="text"
                                           value="{{ $exercisePlanRequest->end_at?->format('Y-m-d') }}"
                                           class="form-control" id="ended_at">
                                </div>
                            </div>


                            <div class="p-5"><h3 class="card-title">اطلاعات کاربر</h3>
                                <div class="d-sm-flex">
                                    @if($exercisePlanRequest->user)
                                        <div>
                                            <div class="main-profile-contact-list">
                                                <div class="media mx-2">
                                                    <div class="media-icon bg-primary-transparent text-primary"><i
                                                            class="fe fe-user fs-21"></i></div>
                                                    <div class="media-body ms-2"><span
                                                            class="text-muted">کاربر</span>
                                                        <p class="mb-0">
                                                            {{ $exercisePlanRequest->user->full_name }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="main-profile-contact-list">
                                                <div class="media mx-2">
                                                    <div class="media-icon bg-success-transparent text-success"><i
                                                            class="fe fe-edit fs-21"></i></div>
                                                    <div class="media-body ms-2"><span
                                                            class="text-muted">ایمیل کاربر</span>
                                                        <p class="mb-0"> {{ $exercisePlanRequest->user->email }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="main-profile-contact-list">
                                                <div class="media mx-2">
                                                    <div class="media-icon bg-info-transparent text-info"><i
                                                            class="fe fe-phone fs-21"></i></div>
                                                    <div class="media-body ms-2"><span
                                                            class="text-muted">
                                                        شماره موبایل
                                                        </span>
                                                        <p class="mb-0"> {{ $exercisePlanRequest->user->mobile }} </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info col-12">
                                            کاربر برای درخواست یافت نشد.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button
                                wire:loading.class.remove="btn-success"
                                wire:loading.class="btn-loading btn-light disabled"
                                wire:click="update"
                                wire:loading.attr="disabled"
                                class="col-12 btn btn-success">
                                ذخیره تغییرات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @for($i=0;$i<$session_count;$i++)
                <div class="row row-sm" wire:key="day_content_{{ $i }}">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header border-bottom">
                                <h3 class="card-title">
                                    برنامه روز {{ $i+1 }}
                                </h3>
                                <div class="card-options">
                                    <a href="#" class="card-options-collapse" data-bs-toggle="card-collapse"><i
                                            class="fe fe-chevron-up"></i></a>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::BEFORE_SET)->isNotEmpty())
                                    @php($beforeContent=$exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::BEFORE_SET)->first())
                                    <p class="text-muted">
                                        ورزش قبل از تمرین
                                    </p>
                                    <div class="example mb-5">
                                        <div class="form-row">
                                            <div class="col-6 mb-3" wire:ignore>
                                                <label for="before_{{ $i }}">ورزش قبل از تمرین </label>
                                                <select class="form-control before_select"
                                                        data-id="{{ $beforeContent->id }}"
                                                        id="before_{{ $i }}">
                                                    @foreach($beforeAfterExercise as $before_exercise)
                                                        <option value="{{ $before_exercise->id }}"
                                                                @if($beforeContent?->id==$before_exercise->id) SELECTED @endif>{{ $before_exercise->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="name">دستور انجام (الزامی)</label>
                                                <input type="text"
                                                       class="form-control @error('shouldUpdate.'.$beforeContent->id.'.reps.0') is-invalid @enderror"
                                                       id="name"
                                                       wire:model="shouldUpdate.{{ $beforeContent->id }}.reps.0"
                                                       placeholder="دستور انجام">
                                                @error('shouldUpdate.'.$beforeContent->id.'.reps.0')
                                                <div id="validationuserName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-5 border-bottom-1 border-danger"></div>
                                @endif
                                @if($exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::MAIN_SET)->isNotEmpty())
                                    @foreach($exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::MAIN_SET)->sortBy('id') as $dayContent)
                                        <div class="example mb-5" wire:key="day_item_{{ $dayContent->id }}">
                                            @if($dayContent->has_super_set)
                                                <span class="badge bg-danger my-1">سوپر ست</span>
                                            @else
                                                <span class="badge bg-success my-1">عادی</span>
                                            @endif
                                            <div class="form-row">
                                                <div class="col-12 mb-3" wire:ignore>
                                                    <label for="exercise_detail_{{ $dayContent->id }}">ورزش</label>
                                                    <select
                                                        class="form-control day_content_exercise"
                                                        data-id="{{ $dayContent->id }}"
                                                        id="exercise_detail_{{ $dayContent->id }}">
                                                        @forelse($dayContent->exerciseBodyCategory?->exercises as $possibleExercise)
                                                            <option value="{{ $possibleExercise->id }}"
                                                                    @if($possibleExercise->id==$dayContent->exercise_id) SELECTED @endif>{{ $possibleExercise->name }}</option>
                                                        @empty
                                                            <option value="{{ $possibleExercise->id }}"
                                                                    SELECTED>{{ $dayContent->exercise_name }}</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-5">
                                                <div class="alert alert-info">
                                                    با صفر قرار دادن میزان یک ست، ست غیرفعال می‌شود. به عنوان نمونه
                                                    برای
                                                    حرکت با
                                                    ۳ ست، فقط برای ست ۱ تا ۳ عدد وارد کنید.
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-12 mb-5">
                                                    <div
                                                        class="text-center d-sm-flex flex-wrap justify-content-around">
                                                        @for($k=0;$k<5;$k++)
                                                            <div class="form-group"
                                                                 wire:key="set_content_{{ $dayContent->id }}_{{ $k }}">
                                                                <label
                                                                    for="set_content_{{ $dayContent->id }}_{{ $k }}">ست {{ $k+1 }}</label>
                                                                <input type="text"
                                                                       wire:model="shouldUpdate.{{ $dayContent->id }}.reps.{{ $k }}"
                                                                       class="form-control"
                                                                       id="set_content_{{ $dayContent->id }}_{{ $k }}"
                                                                       placeholder="ست {{ $k+1 }}">
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            @if($dayContent->has_super_set)
                                                @php($superSet=$dayContent->superSet)
                                                <div class="form-row">
                                                    <div class="col-12 mb-3" wire:ignore>
                                                        <label
                                                            for="exercise_detail_{{ $superSet->id }}">ورزش</label>
                                                        <select
                                                            class="form-control day_content_exercise"
                                                            data-id="{{ $superSet->id }}"
                                                            id="exercise_detail_{{ $superSet->id }}">
                                                            @forelse($superSet->exerciseBodyCategory?->exercises as $possibleExercise)
                                                                <option value="{{ $possibleExercise->id }}"
                                                                        @if($possibleExercise->id==$superSet->exercise_id) SELECTED @endif>{{ $possibleExercise->name }}</option>
                                                            @empty
                                                                <option value="{{ $superSet->id }}"
                                                                        SELECTED>{{ $superSet->exercise_name }}</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-5">
                                                    <div class="alert alert-info">
                                                        با صفر قرار دادن میزان یک ست، ست غیرفعال می‌شود. به عنوان
                                                        نمونه برای
                                                        حرکت با
                                                        ۳ ست، فقط برای ست ۱ تا ۳ عدد وارد کنید.
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="col-12 mb-5">
                                                        <div
                                                            class="text-center d-sm-flex flex-wrap justify-content-around">
                                                            @for($k=0;$k<5;$k++)
                                                                <div class="form-group"
                                                                     wire:key="set_content_{{ $superSet->id }}_{{ $k }}">
                                                                    <label
                                                                        for="set_content_{{ $superSet->id }}_{{ $k }}">ست {{ $k+1 }}</label>
                                                                    <input type="text"
                                                                           wire:model="shouldUpdate.{{ $superSet->id }}.reps.{{ $k }}"
                                                                           class="form-control"
                                                                           id="set_content_{{ $superSet->id }}_{{ $k }}"
                                                                           placeholder="ست {{ $k+1 }}">
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>

                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                                @if($exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::AFTER_SET)->isNotEmpty())
                                    @php($afterContent=$exercisePlanRequest->details->where('day',$i)->where('set_type',\Modules\Exercise\Enum\ExerciseSetTypeEnum::AFTER_SET)->first())
                                    <div class="mt-5 mb-5 border-top-1 border-danger"></div>
                                    <p class="text-muted">
                                        ورزش بعد از تمرین
                                    </p>
                                    <div class="example mb-5">
                                        <div class="form-row">
                                            <div class="col-6 mb-3" wire:ignore>
                                                <label for="after_{{ $i }}">ورزش بعد از تمرین</label>
                                                <select class="form-control after_select"
                                                        data-id="{{ $afterContent?->id }}"
                                                        id="after_{{ $i }}">
                                                    @foreach($beforeAfterExercise as $before_exercise)
                                                        <option value="{{ $before_exercise->id }}"
                                                                @if($afterContent?->id==$before_exercise->id) SELECTED @endif>{{ $before_exercise->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="name">دستور انجام (الزامی)</label>
                                                <input type="text"
                                                       class="form-control @error('shouldUpdate.'.$afterContent->id.'.reps.0') is-invalid @enderror"
                                                       id="name"
                                                       wire:model="shouldUpdate.{{ $afterContent->id }}.reps.0"
                                                       placeholder="دستور انجام">
                                                @error('shouldUpdate.'.$afterContent->id.'.reps.0')
                                                <div id="validationuserName"
                                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        @endif
    @endunless
</div>

@push('scripts')
    <link rel="stylesheet" href="{{admin_asset('plugins/persiandate/persian-datepicker.min.css')}}"/>

    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{admin_asset('plugins/persiandate/persian-date.min.js')}}"></script>
    <script src="{{admin_asset('plugins/persiandate/persian-datepicker.min.js')}}"></script>
    <script>

        $(document).ready(function () {
            $("#status").select2({
                dir: "rtl",
            });

            $('.before_select').select2({
                dir: "rtl",
                placeholder: 'بدون ورزش قبل',
                width: '100%',
            });
            $('.after_select').select2({
                dir: "rtl",
                placeholder: 'بدون ورزش قبل',
                width: '100%',
            });
            $('.day_content_exercise').select2({
                dir: "rtl",
                width: '100%',
            });

            $('.day_content_exercise').on('change', function (e) {
                var data = $(this).select2("val");
                var id = $(this).data('id');
                var name = $(this).find(':selected').text();
            @this.set('shouldUpdate.' + id + '.exercise_name', name)
                ;
            @this.set('shouldUpdate.' + id + '.exercise_id', data)
                ;
            });

            $('.before_select').on('change', function (e) {
                var data = $(this).select2("val");
                var id = $(this).data('id');
                var name = $(this).find(':selected').text();
            @this.set('shouldUpdate.' + id + '.exercise_name', name)
                ;
            @this.set('shouldUpdate.' + id + '.exercise_id', data)
                ;
            });
            $('.after_select').on('change', function (e) {
                var data = $(this).select2("val");
                var id = $(this).data('id');
                var name = $(this).find(':selected').text();
            @this.set('shouldUpdate.' + id + '.exercise_name', name)
                ;
            @this.set('shouldUpdate.' + id + '.exercise_id', data)
                ;
            });
            $('#status').on('change', function (e) {
                var data = $(this).select2("val");
            @this.set('status', data)
                ;
            });
            var to, from;

            from = $('#started_at').persianDatepicker({
                initialValueType: 'gregorian',
                autoClose: true,
                format: 'dddd DD MMMM YYYY',
                onSelect: function (unix) {
                    from.touched = true;
                    if (to && to.options && to.options.minDate != unix) {
                        var cachedValue = to.getState().selected.unixDate;
                        to.options = {minDate: unix};
                        if (to.touched) {
                            to.setDate(cachedValue);
                        }
                    }
                @this.set('start_at', unix / 1000)
                    ;
                }
            });

            to = $('#ended_at').persianDatepicker({
                initialValueType: 'gregorian',
                autoClose: true,
                format: 'dddd DD MMMM YYYY',
                onSelect: function (unix) {
                @this.set('end_at', unix / 1000)
                    ;
                }
            });
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }

        .month-grid-box .header .title {
            min-height: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }
        .tooltip {
            font-family: IRANSansX !important;
        }
    </style>
@endpush
