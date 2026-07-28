<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش برنامه' : 'افزودن برنامه جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')
    @if($parentCategories->isEmpty())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert"><span
                                class="alert-inner--icon me-2"><i class="fe fe-slash"></i></span> <span
                                class="alert-inner--text"><strong>خطا!</strong> برای ایجاد و ویرایش استراتژی حداقل می‌بایست یک دسته‌بندی بدن ایجاد کرده باشد.</span>
                            @can('create',ExerciseBodyCategory::class)
                                <a href="{{ route('admin.exercise_body_category.create') }}"
                                   class="btn btn-sm btn-danger float-end">
                                    <span class="btn-inner--text">ایجاد دسته‌بندی بدن</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card @if($step !== 1) card-collapsed @endif">
                    <div class="card-header @if($step !== 1) bg-success text-white @endif border-bottom">
                        <h3 class="card-title">مرحله اول</h3>
                        @if($step !== 1)
                            <div class="card-options">
                                <a
                                    wire:loading.class="btn btn-light btn-loading"
                                    wire:loading.class.remove="btn-secondary"
                                    wire:click="backToStart()" class="btn btn-secondary btn-sm ms-2">
                                    ویرایش اطلاعات مرحله ۱
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <form>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="name">نام برنامه (الزامی)</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name"
                                           wire:model="name" placeholder="نام">
                                    @error('name')
                                    <div id="validationuserName"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <label for="session_count">تعداد جلسه در هفته (الزامی)</label>
                                    <input type="number"
                                           class="form-control @error('session_count') is-invalid @enderror"
                                           id="session_count"
                                           wire:model="session_count" placeholder="تعداد جلسه">
                                    @error('session_count')
                                    <div id="validationuserSessionCount"
                                         class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <p>گروه جنسیت:</p>
                                    @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyGenderEnum::cases() as $gender)
                                        <label class="rdiobox" for="gender-{{ $gender->value }}">
                                            <input
                                                name="gender"
                                                value="{{ $gender->value }}"
                                                type="radio"
                                                wire:model="gender"
                                                class="radio-primary"
                                                id="gender-{{ $gender->value }}">
                                            <span>{{ $gender->getName() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <p>هدف برنامه:</p>
                                    @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyTargetEnum::cases() as $targetEnum)
                                        <label class="rdiobox" for="target-{{ $targetEnum->value }}">
                                            <input
                                                name="target"
                                                value="{{ $targetEnum->value }}"
                                                type="radio"
                                                wire:model="target"
                                                class="radio-secondary"
                                                id="target-{{ $targetEnum->value }}">
                                            <span>{{ $targetEnum->getName() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <p>سطح برنامه:</p>
                                    @foreach(\Modules\Exercise\Enum\ExercisePlanStrategyLevelEnum::cases() as $levelEnum)
                                        <label class="rdiobox" for="level-{{ $levelEnum->value }}">
                                            <input
                                                name="level"
                                                value="{{ $levelEnum->value }}"
                                                type="radio"
                                                wire:model="level"
                                                class="radio-info"
                                                id="level-{{ $levelEnum->value }}">
                                            <span>{{ $levelEnum->getName() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <p>وضعیت برنامه:</p>
                                    <label class="rdiobox" for="status-1">
                                        <input
                                            name="status"
                                            value="1"
                                            type="radio"
                                            wire:model="status"
                                            class="radio-warning"
                                            id="status-1">
                                        <span>فعال</span>
                                    </label>
                                    <label class="rdiobox" for="status-0">
                                        <input
                                            name="status"
                                            value="0"
                                            type="radio"
                                            wire:model="status"
                                            class="radio-warning"
                                            id="status-0">
                                        <span>غیرفعال</span>
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <a
                            wire:loading.class="btn btn-light btn-loading"
                            wire:loading.class.remove="btn-success"
                            wire:click="goToNextStep()"
                            class="btn btn-success">
                            مرحله بعد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($step !== 1)
        @for($i=0;$i<$session_count;$i++)
            <div class="row row-sm" wire:key="day_{{ $i }}">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-status card-status-left {{ $bgHeadersClasses[$i%6] }} br-bl-7 br-tl-7"></div>
                        <div class="card-header text-white {{ $bgHeadersClasses[$i%6] }}">
                            <h3 class="card-title">برنامه روز {{ $i+1 }}</h3>
                            <div class="card-options">
                                <a href="#" class="card-options-collapse" data-bs-toggle="card-collapse"><i
                                        class="fe fe-chevron-up text-white"></i></a>
                            </div>
                        </div>
                        @if(\in_array($i,$dayCopy))
                            <div class="card-body">
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        این روز از سایر روز‌ها کپی شد.
                                    </div>
                                    <div class="col-12 mb-5">
                                        <label for="after_{{ $i }}">روز مورد نظر (الزامی)</label>
                                        <div wire:ignore>
                                            <select class="form-control copy_day_select2"
                                                    data-day="{{ $i }}"
                                                    id="copy_day_select_{{ $i }}">
                                                @for($k=0;$k<$i;$k++)
                                                    <option
                                                        @if(isset($dayCopyTo[$i]) && $dayCopyTo[$i] == $k) SELECTED
                                                        @endif
                                                        value="{{ $k }}">
                                                        روز {{ ($k+1) }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card-body">
                                <a
                                    class="btn btn-secondary mb-5"
                                    wire:click="addPlanDetail({{ $i }})"
                                    wire:loading.class="btn btn-light btn-loading"
                                    wire:loading.class.remove="btn-secondary"
                                    wire:target="addPlanDetail({{ $i }})"
                                >افزودن حرکت</a>
                                @if($beforeAfterExercises->isNotEmpty())
                                    <div class="form-row">
                                        <div class="col-12 mb-5">
                                            <label for="before_{{ $i }}">ورزش قبل از تمرین (الزامی)</label>
                                            <div wire:ignore>
                                                <select class="form-control before_after_exercise before_exercise"
                                                        data-day="{{ $i }}"
                                                        data-placeholder="انتخاب ورزش"
                                                        id="before_{{ $i }}">
                                                    @foreach($beforeAfterExercises as $exe_item)
                                                        <option value="">بدون ورزش</option>
                                                        <option
                                                            @if(isset($beforeAfter[$i]['exercise_before_id']) && $exe_item->id == $beforeAfter[$i]['exercise_before_id']) SELECTED
                                                            @endif value="{{ $exe_item->id }}">
                                                            {{ $exe_item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label for="rep_before_exercise_{{ $i }}">دستور برنامه (الزامی)</label>
                                            <input type="text"
                                                   class="form-control" id="rep_before_exercise_{{ $i }}"
                                                   wire:model="beforeAfter.{{ $i }}.exercise_before_reps"
                                                   placeholder="دستور برنامه">
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        حرکتی برای قبل یا بعد از تمرین تعریف نشده است.
                                    </div>
                                @endif
                                <br>
                                @for($j=0,$iMax=count($planDetails[$i]);$j<$iMax;$j++)
                                    <div class="example mt-5" wire:key="plan_detail_{{ $i }}_{{ $j }}">
                                        <div class="form-row">
                                            <div class="col-12 mb-5">
                                                <label for="exercise_{{ $i }}_{{ $j }}">انتخاب حرکت شماره {{ $j+1 }}
                                                    (الزامی)</label>
                                                <div wire:ignore>
                                                    <select class="form-control is_exercise"
                                                            data-day="{{ $i }}"
                                                            data-index="{{ $j }}"
                                                            data-placeholder="انتخاب حرکت"
                                                            id="exercise_{{ $i }}_{{ $j }}">
                                                        @foreach($parentCategories as $cat)
                                                            @include('exercise::livewire.admin.exercise-plan-strategy.cat-item',['cat'=>$cat,'level'=>0,'selected'=>[
        $planDetails[$i][$j]['exercise_category_id']
                                                              ]])
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-5">
                                            <div class="form-row">
                                                <div class="col-12">
                                                    <p>نوع ورزش:</p>
                                                    <label class="rdiobox" for="type-{{ $i }}-{{ $j }}-base">
                                                        <input
                                                            name="type-{{ $i }}-{{ $j }}"
                                                            value="1"
                                                            type="radio"
                                                            wire:model="planDetails.{{ $i }}.{{ $j }}.type"
                                                            class="radio-warning"
                                                            id="type-{{ $i }}-{{ $j }}-base">
                                                        <span>مادر</span>
                                                    </label>
                                                    <label class="rdiobox" for="type-{{ $i }}-{{ $j }}-complementary">
                                                        <input
                                                            name="type-{{ $i }}-{{ $j }}"
                                                            value="2"
                                                            type="radio"
                                                            wire:model="planDetails.{{ $i }}.{{ $j }}.type"
                                                            class="radio-warning"
                                                            id="type-{{ $i }}-{{ $j }}-complementary">
                                                        <span>مکمل</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-5">
                                            <div class="alert alert-info">
                                                با صفر قرار دادن میزان یک ست، ست غیرفعال می‌شود. به عنوان نمونه برای
                                                حرکت با
                                                ۳ ست، فقط برای ست ۱ تا ۳ عدد وارد کنید.
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-12 mb-5">
                                                <div class="text-center d-sm-flex flex-wrap justify-content-around">
                                                    @for($k=0;$k<5;$k++)
                                                        <div class="form-group"
                                                             wire:key="set_{{ $i }}_{{ $j }}_{{ $k }}">
                                                            <label
                                                                for="set_{{ $i }}_{{ $j }}_{{ $k }}">ست {{ $k+1 }}</label>
                                                            <input type="text"
                                                                   wire:model="planDetails.{{ $i }}.{{ $j }}.reps.{{ $k }}"
                                                                   class="form-control"
                                                                   id="set_{{ $i }}_{{ $j }}_{{ $k }}"
                                                                   placeholder="ست {{ $k+1 }}">
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-5">
                                            <div class="form-row">
                                                <div class="col-12">
                                                    <label class="ckbox" for="ckbox-{{ $i }}-{{ $j }}">
                                                        <input
                                                            wire:model.live="planDetails.{{ $i }}.{{ $j }}.has_super"
                                                            type="checkbox"
                                                            value="1"
                                                            id="ckbox-{{ $i }}-{{ $j }}">
                                                        <span>سوپر ست است</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        @if($planDetails[$i][$j]['has_super'])
                                            <div class="form-row">
                                                <div class="col-12 mb-5">
                                                    <label for="exercise_{{ $i }}_{{ $j }}_super_set">انتخاب حرکت
                                                        (الزامی)</label>
                                                    <div wire:ignore>
                                                        <select class="form-control is_exercise_super_set"
                                                                data-day="{{ $i }}"
                                                                data-index="{{ $j }}"
                                                                data-placeholder="انتخاب حرکت"
                                                                id="exercise_{{ $i }}_{{ $j }}_super_set">
                                                            @foreach($parentCategories as $cat)
                                                                @include('exercise::livewire.admin.exercise-plan-strategy.cat-item',['cat'=>$cat,'level'=>0,'selected'=>[
            $planDetails[$i][$j]['super_set']['exercise_category_id'],
                                                                  ]])
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-5">
                                                <div class="form-row">
                                                    <div class="col-12">
                                                        <p>نوع ورزش:</p>
                                                        <label class="rdiobox"
                                                               for="type-{{ $i }}-{{ $j }}-base-superset">
                                                            <input
                                                                name="type-superset-{{ $i }}-{{ $j }}"
                                                                value="1"
                                                                type="radio"
                                                                wire:model="planDetails.{{ $i }}.{{ $j }}.super_set.type"
                                                                class="radio-warning"
                                                                id="type-{{ $i }}-{{ $j }}-base-superset">
                                                            <span>مادر</span>
                                                        </label>
                                                        <label class="rdiobox"
                                                               for="type-{{ $i }}-{{ $j }}-complementary-superset">
                                                            <input
                                                                name="type-superset-{{ $i }}-{{ $j }}"
                                                                value="2"
                                                                type="radio"
                                                                wire:model="planDetails.{{ $i }}.{{ $j }}.super_set.type"
                                                                class="radio-warning"
                                                                id="type-{{ $i }}-{{ $j }}-complementary-superset">
                                                            <span>مکمل</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-5">
                                                <div class="alert alert-info">
                                                    با صفر قرار دادن میزان یک ست، ست غیرفعال می‌شود. به عنوان نمونه برای
                                                    حرکت با ۳ ست، فقط برای ست ۱ تا ۳ عدد وارد کنید.
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-12 mb-5">
                                                    <div class="text-center d-sm-flex flex-wrap justify-content-around">
                                                        @for($m=0;$m<5;$m++)
                                                            <div class="form-group"
                                                                 wire:key="super_set_{{ $i }}_{{ $j }}_{{ $m }}">
                                                                <label
                                                                    for="super_set_{{ $i }}_{{ $j }}_{{ $m }}">ست {{ $m+1 }}</label>
                                                                <input type="text"
                                                                       wire:model="planDetails.{{ $i }}.{{ $j }}.super_set.reps.{{ $m }}"
                                                                       class="form-control"
                                                                       id="super_set_{{ $i }}_{{ $j }}_{{ $m }}"
                                                                       placeholder="ست {{ $m+1 }}">
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <br>
                                        <br>
                                        <a class="btn btn-danger"
                                           wire:click="removePlanDetail({{ $i }},{{ $j }})"
                                           wire:loading.class="btn btn-light btn-loading"
                                           wire:loading.class.remove="btn-danger"
                                           wire:target="removePlanDetail({{ $i }},{{ $j }})"
                                        >حذف این حرکت</a>
                                    </div>
                                @endfor

                                @if($beforeAfterExercises->isNotEmpty())
                                    <div class="form-row mt-5">
                                        <div class="col-12 mb-5">
                                            <label for="after_{{ $i }}">ورزش بعد از تمرین (الزامی)</label>
                                            <div wire:ignore>
                                                <select class="form-control before_after_exercise after_exercise"
                                                        data-day="{{ $i }}"
                                                        data-placeholder="انتخاب ورزش"
                                                        id="after_{{ $i }}">
                                                    @foreach($beforeAfterExercises as $exe_item)
                                                        <option value="">بدون ورزش</option>
                                                        <option
                                                            @if(isset($beforeAfter[$i]['exercise_after_id']) && $exe_item->id == $beforeAfter[$i]['exercise_after_id']) SELECTED
                                                            @endif value="{{ $exe_item->id }}">
                                                            {{ $exe_item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-12 mb-3">
                                            <label for="rep_after_exercise_{{ $i }}">دستور برنامه (الزامی)</label>
                                            <input type="text"
                                                   class="form-control" id="rep_after_exercise_{{ $i }}"
                                                   wire:model="beforeAfter.{{ $i }}.exercise_after_reps"
                                                   placeholder="دستور برنامه">
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        حرکتی برای قبل یا بعد از تمرین تعریف نشده است.
                                    </div>
                                @endif
                                <br>
                            </div>
                        @endif
                        @if($i>0)
                            <div class="card-footer">
                                <div class="form-group">
                                    <div class="checkbox">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox"
                                                   value="{{ $i }}"
                                                   wire:model.live="dayCopy"
                                                   class="custom-control-input"
                                                   id="copy_from_{{ $i }}">
                                            <label
                                                for="copy_from_{{ $i }}" class="custom-control-label">
                                                کپی این روز از سایر روز‌ها
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
        <div class="row">
            <button
                class="btn btn-success btn-lg col-12 mt-5"
                wire:loading.class="btn btn-light btn-loading"
                wire:loading.class.remove="btn-success"
                wire:loading.attr="disabled"
                wire:target="planDetails,removePlanDetail,setExerciseCategory,setExerciseCategorySuperSet,submit,addPlanDetail"
                wire:click="submit()"
            >
                ذخیره استراتژی
            </button>
        </div>
    @endif
</div>

@push('scripts')
    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{admin_asset('plugins/sweet-alert/sweetalert.min.js')}}"></script>

    <script>

        Livewire.on('error', param => {
            swal({
                title: "خطا!",
                text: "" + param.message,
                type: "error",
                showCancelButton: true,
                allowOutsideClick: true,
                showConfirmButton: false,
                cancelButtonText: "متوجه شدم",
                closeOnConfirm: false
            });
        });

        Livewire.on('updateUi', () => {
            //delay
            setTimeout(function () {
                $('.is_exercise').select2({
                    dir: "rtl",
                    width: '100%',
                });

                $('.is_exercise_super_set').select2({
                    dir: "rtl",
                    width: '100%',
                });
                $('.before_after_exercise').select2({
                    dir: "rtl",
                    allowClear: true,
                    width: '100%',
                });

                $('.copy_day_select2').select2({
                    dir: "rtl",
                    allowClear: true,
                    width: '100%',
                });
            }, 500);
        });

        $('body').on('change', '.is_exercise', function (e) {
            let data = $(this).select2("val");
            let day = $(this).data('day');
            let index = $(this).data('index');
        @this.setExerciseCategory(data, day, index)
            ;
        });

        $('body').on('change', '.is_exercise_super_set', function (e) {
            let data = $(this).select2("val");
            let day = $(this).data('day');
            let index = $(this).data('index');
        @this.setExerciseCategorySuperSet(data, day, index)
            ;
        });

        $('body').on('change', '.before_exercise', function (e) {
            let data = $(this).select2("val");
            let day = $(this).data('day');
        @this.setBeforeExercise(data, day)
            ;
        });

        $('body').on('change', '.after_exercise', function (e) {
            let data = $(this).select2("val");
            let day = $(this).data('day');
        @this.setAfterExercise(data, day)
            ;
        });

        $('body').on('change', '.copy_day_select2', function (e) {
            let data = $(this).select2("val");
            let day = $(this).data('day');
        @this.setCopyTo(data, day)
            ;
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

