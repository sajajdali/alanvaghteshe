<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش ورزش' : 'افزودن ورزش جدید' }}
            </h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <!-- Row -->
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش ورزش' : 'افزودن ورزش جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-row">
                            <div class="col-12 mb-3">
                                <label for="name">نام ورزش (الزامی)</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                       wire:model="name" placeholder="نام">
                                @error('name')
                                <div id="validationuserName"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-9 mb-3">
                                <label for="videoUrl">ویدیو آموزش</label>
                                <input type="text" class="form-control @error('videoUrl') is-invalid @enderror"
                                       id="videoUrl"
                                       wire:model="videoUrl" placeholder="آدرس فایل ویدیو">
                                @error('videoUrl')
                                <div id="validationVideoUrl"
                                     class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <button
                                data-for="videoUrl"
                                data-variable="videoUrl"
                                class="btn btn-primary select_file col-3 mb-3 mt-5"
                                data-bs-target="#file-selector-modal"
                                data-bs-toggle="modal"
                                type="button">
                                انتخاب فایل
                            </button>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3" wire:ignore>
                                <label for="categories">بخش‌های بدن</label>
                                <select multiple class="form-control" data-placeholder="انتخاب بخش‌های بدن مرتبط"
                                        id="categories">
                                    @foreach($parentCategories as $cat)
                                        @include('exercise::livewire.admin.exercise.exercise-create-or-update-category-item',['cat'=>$cat,'level'=>0])
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3" wire:ignore>
                                <label for="superable">
                                    حرکاتی که
                                    <mark>نمی‌تواند</mark>
                                    با این حرکت سوپر باشد
                                </label>
                                <select multiple class="form-control" data-placeholder="انتخاب حرکات"
                                        id="superable">
                                    @foreach($superableExercises as $exItem)
                                        <option value="{{ $exItem->id }}"
                                                @if(in_array($exItem->id,$superExercises,false)) SELECTED @endif>
                                            {{ $exItem->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mb-3" wire:ignore>
                                <label for="diseases">
                                    بیماری‌هایی که نباید این حرکت را انجام دهند
                                </label>
                                <select multiple class="form-control" data-placeholder="انتخاب بیماری"
                                        id="diseases">
                                    @foreach($diseases as $disease)
                                        <option value="{{ $disease->id }}"
                                                @if(in_array($disease->id,$diseasesExercise,false)) SELECTED @endif>
                                            {{ $disease->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p class="card-sub-title">شرایط ورزش</p>
                        <div class="example">
                            <div class="d-sm-flex flex-wrap justify-content-around">
                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_women">زن</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle @if($isForWomen) on @endif" id="is_for_women">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_men">آقایان</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle @if($isForMen) on @endif" id="is_for_men">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_base">مادر</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle toggle-danger @if($isForBase) on @endif" id="is_for_base">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_complementary">مکمل</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle toggle-danger @if($isForComplementary) on @endif"
                                             id="is_for_complementary">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_advanced">حرفه‌ای</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle toggle-success @if($isForAdvanced) on @endif"
                                             id="is_for_advanced">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center my-sm-0 my-2">
                                    <label for="is_for_beginner">آماتور</label>
                                    <div class="main-toggle-group d-flex flex-wrap justify-content-center">
                                        <div class="toggle toggle-success @if($isForBeginner) on @endif"
                                             id="is_for_beginner">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <p class="card-sub-title">نوع ورزش</p>
                        <div class="example">
                            <div class="form-group">
                                <label class="rdiobox" for="type-main">
                                    <input name="type" type="radio" id="type-main" value="main" wire:model="type">
                                    <span>ورزش اصلی</span></label>
                                <label class="rdiobox" for="type-before-after"><input name="type" type="radio"
                                                                                      value="before_after"
                                                                                      wire:model="type"
                                                                                      id="type-before-after"> <span>ورزش قبل و بعد تمرین</span></label>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click.prevent="updateOrCreate">
                            @if($isEdited)
                                ویرایش دسته
                            @else
                                ایجاد دسته
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <livewire:admin::file-manager-modal/>

</div>

@push('scripts')
    <!-- SELECT2 JS -->
    <script src="{{admin_asset('plugins/select2/select2.full.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#categories').select2({
                dir: "rtl",
                placeholder: 'انتخاب بخش های مرتبط بدن',
                allowClear: true,
                multiple: true,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });
            $('#categories').on('change', function (e) {
                var data = $('#categories').select2("val");
            @this.set('category', data)
                ;
            });
            $('#superable').select2({
                dir: "rtl",
                placeholder: 'انتخاب ورزش',
                allowClear: true,
                multiple: true,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });
            $('#superable').on('change', function (e) {
                var data = $('#superable').select2("val");
            @this.set('superExercises', data)
                ;
            });
            $('#diseases').select2({
                dir: "rtl",
                placeholder: 'انتخاب بیماری',
                allowClear: true,
                multiple: true,
                searchInputPlaceholder: 'جستجو',
                search: true,
                width: '100%',
            });
            $('#diseases').on('change', function (e) {
                var data = $('#diseases').select2("val");
            @this.set('diseasesExercise', data)
                ;
            });

            $('#is_for_men').on('click', function () {
            @this.set('isForMen', $('#is_for_men').hasClass('on'))
                ;
            });

            $('#is_for_women').on('click', function () {
            @this.set('isForWomen', $('#is_for_women').hasClass('on'))
                ;
            });

            $('#is_for_base').on('click', function () {
            @this.set('isForBase', $('#is_for_base').hasClass('on'))
                ;
            });

            $('#is_for_complementary').on('click', function () {
            @this.set('isForComplementary', $('#is_for_complementary').hasClass('on'))
                ;
            });

            $('#is_for_advanced').on('click', function () {
            @this.set('isForAdvanced', $('#is_for_advanced').hasClass('on'))
                ;
            });

            $('#is_for_beginner').on('click', function () {
            @this.set('isForBeginner', $('#is_for_beginner').hasClass('on'))
                ;
            });
        });

        Livewire.on('select_file', (param) => {
        @this.set('videoUrl', param.url)
            ;
            //close modal
            $('#file-selector-modal').modal('hide');
        });
    </script>
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
