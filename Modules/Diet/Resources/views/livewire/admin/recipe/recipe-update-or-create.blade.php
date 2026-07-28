<div>
    <div class="">
        <div class="page-header d-flex align-items-center justify-content-between">
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش غذا' : 'افزودن غذای جدید' }}
            </h1>

            <div class="d-flex align-items-center action-buttons">

            <button type="button"
                        class="btn btn-outline-danger"
                        wire:click="requestClearRecipeFormCaches"
                        wire:loading.attr="disabled">
                    <i class="fe fe-trash-2"></i>
                    حذف کش فرم
                </button>
            </div>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <form>
        <div class="row row-sm">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h3 class="card-title">
                            {{ $isEdited ? 'ویرایش غذا' : 'افزودن غذا جدید' }}
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- basic info --}}
                        <div class="row">
                            <div class="col-2" style="cursor: pointer" data-bs-toggle="collapse"
                                 href="#basicinfocontainer" role="button" aria-expanded="false"
                                 aria-controls="basicinfocontainer">
                                <h4 class="text-secondary">
                                    <i class="fa fa-info-circle me-1" aria-hidden="true"></i>
                                    اطلاعات کلی
                                </h4>
                            </div>
                            <div class="col-10">
                                <hr class="w-100 " style="height: 2px;background-color: black;opacity: 0.2;">
                            </div>

                            <div class="col-12 row mt-3 collaps show" id="basicinfocontainer">
                                <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                    <label for="name">نام غذا</label>
                                    <input type="text" class="form-control @error('form.name') is-invalid @enderror"
                                           id="name" wire:model="form.name" placeholder="نام">
                                    @error('form.name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="time">مدت زمان مورد نیاز برای طبخ</label>
                                    <input type="text" class="form-control @error('form.time') is-invalid @enderror"
                                           id="time" wire:model="form.time" placeholder="دقیقه">
                                    @error('form.time')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="serving">برای چند نفر</label>
                                    <input type="number"
                                           class="form-control @error('form.serving') is-invalid @enderror"
                                           id="serving" wire:model="form.serving" placeholder="به عدد">
                                    @error('form.serving')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="weight">وزن غذا (از جمع موارد تشکیل میشود)</label>
                                    <input type="number"
                                           class="form-control @error('form.weight') is-invalid @enderror custom-input-bg"
                                           id="weight" wire:model="form.weight" placeholder="به گرم">
                                    @error('form.weight')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="calorie">کالری (با انتخاب غذا اپدیت میشود)</label>
                                    <input type="number"
                                           class="form-control @error('form.calorie') is-invalid @enderror"
                                           id="calorie" wire:model="form.calorie" placeholder="به گرم">
                                    @error('form.calorie')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mt-2 mt-md-4">
                                    <label for="description">توضیحات</label>
                                    <textarea placeholder="توضیحات" wire:model='form.description'
                                              class="form-control @error('form.description') is-invalid @enderror"
                                              id="description" rows="3"></textarea>
                                    @error('form.description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="dificalityLevel">سطح سختی</label>
                                    <select wire:model='form.difficulty'
                                            class="form-control @error('form.difficulty') is-invalid @enderror"
                                            id="dificalityLevel">
                                        <option value="">انتخاب کنید...</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                    @error('form.difficulty')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2 mt-md-4">
                                    <label for="category_select">دسته بندی</label>

                                    <div wire:ignore>
                                        <select id="category_select"
                                                class="form-control select2-show-search recepieCategory selec2 @error('form.category') is-invalid @enderror">
                                            <option value="">انتخاب کنید...</option>
                                            @foreach ($fetchData['categories'] as $category)
                                                <option value="{{ $category->id }}"
                                                        @if (isset($form['category']) && $form['category'] == $category->id) selected @endif>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @error('form.category')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- instructions --}}
                        <div class="row my-5">
                            <div class="col-2" style="cursor: pointer" data-bs-toggle="collapse"
                                 href="#howtoCookcontainer" role="button" aria-expanded="false"
                                 aria-controls="howtoCookcontainer">
                                <h4 class="text-secondary">
                                    <i class="fa fa-cutlery me-1" aria-hidden="true"></i>
                                    دستور پخت
                                </h4>
                            </div>

                            <div class="col-10">
                                <hr class="w-100 " style="height: 2px;background-color: black;opacity: 0.2;">
                            </div>

                            <div class="col-12 mt-3 collaps show" id="howtoCookcontainer">
                                <div class="table-responsive">
                                    <table class="table border text-nowrap text-md-nowrap">
                                        <tbody>
                                        @for ($i = 0; $i < $form['instructionsCounter']; $i++)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                        <label for="instroduction-{{ $i }}">دستور پخت مرحله ی {{ $i + 1 }}</label>
                                                        <textarea wire:model='form.instroduction.{{ $i }}'
                                                                  placeholder="توضیحات"
                                                                  class="form-control @error("form.instroduction.$i") is-invalid @enderror"
                                                                  id="instroduction-{{ $i }}" rows="3"></textarea>
                                                        @error("form.instroduction.$i")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($i == 0)
                                                        <button wire:click='addcounter("instructionsCounter")'
                                                                type="button" class="btn btn-icon btn-success">
                                                            <i class="fe fe-plus"></i>
                                                        </button>
                                                    @else
                                                        <button wire:click='removeCounter("instructionsCounter","{{ $i }}")'
                                                                type="button" class="btn btn-icon btn-danger">
                                                            <i class="fe fe-minus"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- main food --}}
                        <div class="row my-5">
                            <div class="col-2" style="cursor: pointer" data-bs-toggle="collapse"
                                 href="#forMainFood" role="button" aria-expanded="false"
                                 aria-controls="forMainFood">
                                <h4 class="text-secondary">
                                    <i class="fa fa-free-code-camp me-1" aria-hidden="true"></i>
                                    غذای اصلی
                                </h4>
                            </div>

                            <div class="col-10">
                                <hr class="w-100 " style="height: 2px;background-color: black;opacity: 0.2;">
                            </div>

                            <div class="col-12 mt-3 collaps show" id="forMainFood">
                                <div class="col-12 mb-3">
                                    <label for="forMainFood">غذای اصلی این دستور</label>

                                    <div wire:ignore>
                                        <select dir="rtl" id="main_food"
                                                class="form-control select2-show-search form-select main_food selec2"
                                                data-placeholder="انتخاب کنید">
                                            <option label="انتخاب کنید"></option>
                                            @foreach ($fetchData['foods'] as $food)
                                                <option value="{{ $food->id }}"
                                                        @if(isset($form['mainFood']) && $form['mainFood'] == $food->id) selected @endif>
                                                    {{ $food->name }}
                                                    [{{ $food->calories }} کالری]
                                                    [id={{ $food->id }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <br>
                                    <blockquote>
                                        در صورتی که در این قسمت یک غذا را انتخاب کنید ، این دستور وارد شده ، به عنوان دستور پخت غذای انتخاب شده شما اضافه میشود
                                    </blockquote>
                                </div>
                            </div>
                        </div>

                        {{-- basic foods --}}
                        <div class="row my-5">
                            <div class="col-3" style="cursor: pointer" data-bs-toggle="collapse"
                                 href="#ingridientContainer" role="button" aria-expanded="false"
                                 aria-controls="ingridientContainer">
                                <h4 class="text-secondary">
                                    <i class="fa fa-list-ul" aria-hidden="true"></i>
                                    مواد تشکیل دهنده
                                </h4>
                            </div>
                            <div class="col-9">
                                <hr class="w-100" style="height: 2px;background-color: black;opacity: 0.2;">
                            </div>

                            <div class="col-12 mt-3 collapse show" id="ingridientContainer">
                                <div class="table-responsive">
                                    <table class="table border text-nowrap text-md-nowrap">
                                        <tbody>
                                        @for ($i = 0; $i < $form['countBasicFoods']; $i++)
                                            <tr
                                                style="
                                                @error("form.foods.{$i}.basic_food_id") background-color : red @enderror
                                                @error("form.foods.{$i}.unit_type") background-color : red @enderror
                                                @error("form.foods.{$i}.unit") background-color : red @enderror
                                                ">
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                        <label for="foodname-{{ $i }}">نام غذا</label>

                                                        <div wire:ignore>
                                                            <select dir="rtl" id="foodname-{{ $i }}"
                                                                    class="form-control select2-show-search form-select basic_foods selec2"
                                                                    data-placeholder="انتخاب کنید">
                                                                <option label="انتخاب کنید"></option>
                                                                @foreach ($fetchData['BasicFoods'] as $basicFood)
                                                                    <option data-number="{{ $i }}"
                                                                            value="{{ $basicFood->id }}"
                                                                            @if (isset($form['foods'][$i]['basic_food_id']) && $basicFood->id == $form['foods'][$i]['basic_food_id']) SELECTED @endif>
                                                                        {{ $basicFood->name }}
                                                                        [{{ $basicFood->food_fact['CALORIE'] ?? 0 }} کالری]
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                        <label for="form.foods.{{ $i }}.type-id">نوع</label>

                                                        @isset($form['foods'][$i]['basic_food_id'])
                                                            <div wire:ignore>
                                                                <select dir="rtl"
                                                                        id="form.foods.{{ $i }}.unit-id"
                                                                        class="form-control select2-show-search unit_type form-select selec2">
                                                                    <option value="" label="انتخاب کنید">انتخاب کنید</option>

                                                                    @foreach ($form['foods'][$i]['units'] as $unit)
                                                                        <option data-number="{{ $i }}"
                                                                                value="{{ $unit->id }}"
                                                                                @if (isset($form['foods'][$i]['unit_type']) && (int)$form['foods'][$i]['unit_type'] === (int)$unit->id) selected @endif>
                                                                            {{ $unit->name }}
                                                                        </option>
                                                                    @endforeach

                                                                    <option data-number="{{ $i }}"
                                                                            value="3"
                                                                            @if (isset($form['foods'][$i]['unit_type']) && (int)$form['foods'][$i]['unit_type'] === 3) selected @endif>
                                                                        گرم
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        @else
                                                            <input type="text" class="form-control" disabled placeholder="ابتدا نام غذا را انتخاب کنید">
                                                        @endisset

                                                        @error("form.foods.{$i}.unit_type")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="col-xl-12 col-lg-12 col-md-6 col-sm-12 mb-3">
                                                        <label for="form.foods.{{ $i }}.unit-id">میزان (گرم)</label>

                                                        @isset($form['foods'][$i]['unit_name'], $form['foods'][$i]['unit_type'])
                                                            @if((int)$form['foods'][$i]['unit_type'] === 3)
                                                                <input type="number" class="form-control"
                                                                       wire:model.live.debounce.1500ms="form.foods.{{ $i }}.unit"
                                                                       placeholder="میزان به گرم">
                                                            @else
                                                                <label for="form.foods.{{ $i }}.unit-id">میزان</label>
                                                                <select wire:model.live="form.foods.{{ $i }}.unit"
                                                                        dir="rtl"
                                                                        id="form.foods.{{ $i }}.unit-id"
                                                                        class="form-control">
                                                                    <option value="0" label="انتخاب کنید">انتخاب کنید</option>

                                                                    <option value="0.25">{!! 0.25 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                    <option value="0.5">{!! 0.5 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                    <option value="0.75">{!! 0.75 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>

                                                                    @for ($b = 1; $b < 40; $b++)
                                                                        <option value="{{ $b }}">{!! $b !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                        <option value="{{ $b + 0.25 }}">{!! $b + 0.25 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                        <option value="{{ $b + 0.5 }}">{!! $b + 0.5 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                        <option value="{{ $b + 0.75 }}">{!! $b + 0.75 !!} {!! $form['foods'][$i]['unit_name'] !!}</option>
                                                                    @endfor
                                                                </select>
                                                            @endif
                                                        @else
                                                            <input type="text" class="form-control" disabled placeholder="ابتدا نام غذا را انتخاب کنید">
                                                        @endisset

                                                        @error("form.foods.{$i}.unit")
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </td>

                                                <td>
                                                    @if ($i == 0)
                                                        <button wire:click='addcounter("countBasicFoods")'
                                                                type="button" class="btn btn-icon btn-success">
                                                            <i class="fe fe-plus"></i>
                                                        </button>
                                                    @else
                                                        <button wire:click='removeCounter("countBasicFoods","{{ $i }}")'
                                                                type="button" class="btn btn-icon btn-danger">
                                                            <i class="fe fe-minus"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>

                                            @error("form.foods.{$i}.basic_food_id")
                                            <tr>
                                                <td colspan="5">
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                </td>
                                            </tr>
                                            @enderror
                                        @endfor
                                        </tbody>
                                    </table>
                                </div>

                                <h3>جزئیات :</h3>
                                <div class="table-responsive">
                                    <table class="table table-bordered text-nowrap">
                                        <tr>
                                            <td>حداقل کالری تجویزی</td>
                                            <td>
                                                <h3>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['min_calorie'] }}
                                                    </span>
                                                </h3>
                                            </td>

                                            <td>وزن کل به گرم</td>
                                            <td>
                                                <h3>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['total_quantity_per_gram'] ?? '0' }} گرم
                                                    </span>
                                                </h3>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>پروتئین</td>
                                            <td>
                                                <h5>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['protein'] }}
                                                    </span> گرم
                                                </h5>
                                            </td>

                                            <td>کربوهیدرات</td>
                                            <td>
                                                <h5>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['carb'] }}
                                                    </span> گرم
                                                </h5>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>فیبر</td>
                                            <td>
                                                <h5>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['fiber'] }}
                                                    </span> گرم
                                                </h5>
                                            </td>

                                            <td>چربی</td>
                                            <td>
                                                <h5>
                                                    <span class="badge font-weight-semibold bg-success-transparent text-danger tx-15">
                                                        {{ $computData['fat'] }}
                                                    </span> گرم
                                                </h5>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>دستور پخت</td>
                                            <td colspan="3">{!! $computData['recipe'] !!}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- settings --}}
                        <div class="row my-5">
                            <div class="col-2" style="cursor: pointer" data-bs-toggle="collapse"
                                 href="#settingcontainer" role="button" aria-expanded="false"
                                 aria-controls="settingcontainer">
                                <h4 class="text-secondary">
                                    <i class="fa fa-cogs me-1" aria-hidden="true"></i>
                                    تنظیمات
                                </h4>
                            </div>

                            <div class="col-10 mb-3">
                                <hr class="w-100" style="height: 2px;background-color: black;opacity: 0.2;">
                            </div>

                            <div class="col-12 row collapse show" id="settingcontainer">
                                <div class="col-12 mb-3">
                                    <label for="priority">اولیت نمایش</label>
                                    <input type="number"
                                           class="form-control @error('form.priority') is-invalid @enderror"
                                           id="priority" wire:model="form.priority" placeholder="به عدد">
                                    @error('form.priority')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label for="recipeUnit">واحد غذا</label>

                                    <div wire:ignore>
                                        <select id="recipeUnit" dir="rtl"
                                                class="form-control select2-show-search form-select selec2 recepieUnit @error('form.recipe.unit') is-invalid @enderror"
                                                data-placeholder="انتخاب کنید">
                                            <option label="انتخاب کنید"></option>
                                            @foreach ($fetchData['fooduits'] as $unit)
                                                <option value="{{ $unit->id }}"
                                                        @if (isset($form['recipe']['unit']) && $form['recipe']['unit'] == $unit->id) selected @endif>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @error('form.recipe.unit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mt-2">
                                    <label for="unit.gram">مقدار در واحد</label>
                                    <input type="number"
                                           class="form-control @error('form.recipe.gram') is-invalid @enderror"
                                           id="unit.gram" wire:model="form.recipe.gram" placeholder="به گرم">
                                    @error('form.recipe.gram')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-2 mb-4">
                                    <x-admin.core.form.image-upload
                                        label="عکس غذا"
                                        uploadedPhotoUrl="{{ $uploadedPhotoUrl }}"
                                        uploadedFileName="{{ $uploadedFileName }}"
                                        uploadedFileType="{{ $uploadedFileType }}"
                                        deleteAction="deletePhoto"
                                        model="photo"
                                        id="formPhoto" />
                                </div>

                                <div class="col-md-6 mt-2">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="material-switch">
                                            <input wire:model='form.active' id="active" name="siwtch04" type="checkbox" />
                                            <label for="active" class="label-info"></label>
                                        </div>
                                        <p class="card-sub-title">فعال بودن</p>
                                    </div>
                                </div>

                                <div class="col-md-6 mt-2">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <div class="material-switch">
                                            <input wire:model='form.sugested' id="sugested" name="siwtch04" type="checkbox" />
                                            <label for="sugested" class="label-info"></label>
                                        </div>
                                        <p class="card-sub-title">این غذا پیشنهاد شده باشد؟</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @error('*')
                        <div class="col-md-12 alert alert-danger fade show" role="alert">
                            لطفا خطا های بالا را برطرف کنید!
                        </div>
                        @enderror

                        <button type="button" class="btn btn-primary"
                                wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="Recepie">
                            @if ($isEdited) ویرایش غذا @else ایجاد غذا @endif
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <livewire:admin::file-manager-modal />
</div>

@push('scripts')
    <script src="{{ admin_asset('plugins/select2/select2.full.min.js') }}"></script>

    <script>
        function initSelect2Safe() {
            const $els = $('.selec2');

            $els.each(function () {
                const $el = $(this);

                // اگر قبلا select2 شده، destroy کن
                if ($el.hasClass("select2-hidden-accessible")) {
                    $el.select2('destroy');
                }

                $el.select2({
                    dir: "rtl",
                    allowClear: false,
                    multiple: false,
                    searchInputPlaceholder: 'جستجو',
                    search: true,
                    width: '100%',
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            initSelect2Safe();

            Livewire.on('updateUi', () => {
                setTimeout(() => initSelect2Safe(), 150);
            });

            // confirm cache clear
            Livewire.on('confirm-clear-recipe-form-caches', () => {
                if (confirm('کش‌های فرم دستور پخت پاک شود؟ (برای بروزرسانی لیست‌ها)')) {
                    @this.call('clearRecipeFormCaches');
                }
            });

            $('body').on('change', '.recepieCategory', function () {
                @this.set('form.category', $(this).val());
            });

            $('body').on('change', '.basic_foods', function () {
                let value = $(this).val();
                let number = $(this).find(':selected').data('number');
                @this.dispatch('addBasicFood', { basicFoodId: value, foodNumber: number });
            });

            $('body').on('change', '.main_food', function () {
                @this.set('form.mainFood', $(this).val());
            });

            $('body').on('change', '.recepieUnit', function () {
                @this.set('form.recipe.unit', $(this).val());
            });

            $('body').on('change', '.unit_type', function () {
                let value = $(this).val();
                let number = $(this).find(':selected').data('number');
                @this.dispatch('addUnitType', { unitTypeId: value, unitNumber: number });
            });

            Livewire.on('select_file', (param) => {
                @this.set('form.recipe.img', param.url);
                $('#file-selector-modal').modal('hide');
            });
        });
    </script>

    <style>
        .select2-container { width: 100% !important; }
        .custom-input-bg { background-color: #f6f6f6; }
    </style>
@endpush
