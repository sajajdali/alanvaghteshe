<div>
    <div class="page-header">
        <div class="d-flex align-items-center justify-content-between w-100">
            <h1 class="page-title">
                {{ $isEdited ? 'ویرایش دوره' : 'افزودن دوره جدید' }}
            </h1>
            @if($isEdited && $course)
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.course.content', $course) }}" class="btn btn-outline-primary">
                        مدیریت بخش‌ها
                    </a>
                    <a href="{{ route('admin.course.faqs', $course) }}" class="btn btn-outline-info">
                        سوالات متداول
                    </a>
                    <a href="{{ route('admin.course.comments', $course) }}" class="btn btn-outline-secondary">
                        نظرات کاربران
                    </a>
                </div>
            @endif
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $isEdited ? 'ویرایش دوره' : 'افزودن دوره جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="course-form-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h4 class="mb-0">اطلاعات اصلی دوره</h4>
                                <small class="text-muted">عنوان، اسلاگ، تصویر و توضیحات را در این بخش وارد کنید.</small>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="title">عنوان دوره (الزامی)</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title"
                                           wire:model.live="title" placeholder="عنوان دوره">
                                    @error('title')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="slug">اسلاگ دوره (الزامی)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                           id="slug"
                                           wire:model.live="slug" dir="ltr" placeholder="course-slug">
                                    @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-12 mb-3">
                                    <x-admin.core.form.image-upload
                                        label="تصویر دوره"
                                        uploadedPhotoUrl="{{ $uploadedPhotoUrl }}"
                                        uploadedFileName="{{ $uploadedFileName }}"
                                        uploadedFileType="{{ $uploadedFileType }}"
                                        deleteAction="deletePhoto"
                                        model="photo"
                                        id="coursePhoto" />
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="category_id">گروه‌بندی دوره</label>
                                    <select id="category_id" class="form-control @error('category_id') is-invalid @enderror" wire:model.live="category_id">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->parent?->title ? ($category->parent->title . ' / ' . $category->title) : $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="level">سطح دوره</label>
                                    <select id="level" class="form-control @error('level') is-invalid @enderror" wire:model.live="level">
                                        @foreach($levels as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('level')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <label for="short_description">توضیح کوتاه</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description"
                                              wire:model.live="short_description"
                                              rows="3"
                                              placeholder="توضیح کوتاه دوره"></textarea>
                                    @error('short_description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                    <label for="description">توضیحات کامل</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description"
                                              wire:model.live="description"
                                              rows="7"
                                              placeholder="توضیحات کامل دوره"></textarea>
                                    @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="course-form-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h4 class="mb-0">قیمت و مدت</h4>
                                <small class="text-muted">قیمت نهایی به محض تغییر قیمت اصلی یا قیمت پس از تخفیف بروزرسانی می‌شود.</small>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="price">قیمت</label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price"
                                           wire:model.live.debounce.300ms="price" dir="ltr" placeholder="0">
                                    @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="discounted_price">قیمت پس از تخفیف</label>
                                    <input type="number" class="form-control @error('discounted_price') is-invalid @enderror"
                                           id="discounted_price"
                                           wire:model.live.debounce.300ms="discounted_price" dir="ltr" placeholder="اختیاری">
                                    @error('discounted_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="final_price">قیمت نهایی</label>
                                    <input type="number" class="form-control @error('final_price') is-invalid @enderror bg-light"
                                           id="final_price"
                                           wire:model.live="final_price" dir="ltr" readonly>
                                    @error('final_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3 d-flex align-items-end">
                                    <div class="course-price-preview w-100 text-center">
                                        <div class="small text-muted">نمایش قیمت نهایی</div>
                                        <div class="mt-1 fw-bold text-success">{{ number_format((int) ($final_price ?: 0)) }} تومان</div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="duration_minutes">مدت دوره (دقیقه)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror"
                                           id="duration_minutes"
                                           wire:model.live="duration_minutes" dir="ltr" placeholder="0">
                                    @error('duration_minutes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="access_days">مدت دسترسی دوره (روز)</label>
                                    <input type="number" class="form-control @error('access_days') is-invalid @enderror"
                                           id="access_days"
                                           wire:model.live="access_days" dir="ltr" placeholder="مثلاً 50">
                                    @error('access_days')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="capacity">ظرفیت</label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror"
                                           id="capacity"
                                           wire:model.live="capacity" dir="ltr" placeholder="نامحدود">
                                    @error('capacity')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-3 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="sort_order">اولویت نمایش</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                           id="sort_order"
                                           wire:model.live="sort_order" dir="ltr" placeholder="اولویت نمایش">
                                    @error('sort_order')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="course-form-section mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h4 class="mb-0">انتشار و خرید</h4>
                                <small class="text-muted">وضعیت فعال بودن، انتشار و امکان خرید دوره را از این قسمت کنترل کن.</small>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-3">
                                    <label for="published_at">تاریخ انتشار</label>
                                    <input type="text" class="form-control @error('published_at') is-invalid @enderror"
                                           id="published_at"
                                           wire:model.live="published_at"
                                           autocomplete="off"
                                           data-jdp
                                           placeholder="1404/01/01">
                                    @error('published_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                    <div class="custom-control custom-switch mt-4 pr-4">
                                        <input type="checkbox" class="custom-control-input" id="is_active"
                                               wire:model.live="is_active">
                                        <label class="custom-control-label me-2 ms-6" for="is_active">دوره فعال باشد</label>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-3 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                    <div class="custom-control custom-switch mt-4 pr-4">
                                        <input type="checkbox" class="custom-control-input" id="is_published"
                                               wire:model.live="is_published">
                                        <label class="custom-control-label me-2 ms-6" for="is_published">دوره منتشر شود</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                    <div class="custom-control custom-switch mt-2 pr-4">
                                        <input type="checkbox" class="custom-control-input" id="is_purchasable"
                                               wire:model.live="is_purchasable">
                                        <label class="custom-control-label me-2 ms-6" for="is_purchasable">امکان خرید فراهم باشد</label>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                    <small class="text-muted">
                                        اگر این گزینه خاموش باشد، دوره نمایش داده می‌شود اما کاربر امکان خرید آن را نخواهد داشت.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="course-form-section mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <h4 class="mb-0">ویترین دوره</h4>
                                <small class="text-muted">قرار گرفتن در لیست جدیدترین‌ها و محبوب‌ترین‌ها با اولویت مستقل برای هر بخش.</small>
                            </div>

                            <div class="form-row">
                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4">
                                    <div class="course-showcase-card h-100">
                                        <div class="custom-control custom-switch pr-4 mb-3">
                                            <input type="checkbox" class="custom-control-input" id="is_latest"
                                                   wire:model.live="is_latest">
                                            <label class="custom-control-label me-2 ms-6" for="is_latest">جزو جدیدترین‌ها باشد</label>
                                        </div>
                                        <label for="latest_sort_order">اولویت جدیدترین</label>
                                        <input type="number" class="form-control @error('latest_sort_order') is-invalid @enderror"
                                               id="latest_sort_order"
                                               wire:model.live="latest_sort_order" dir="ltr" placeholder="مثلاً 1"
                                               {{ $is_latest ? '' : 'disabled' }}>
                                        @error('latest_sort_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-4">
                                    <div class="course-showcase-card h-100">
                                        <div class="custom-control custom-switch pr-4 mb-3">
                                            <input type="checkbox" class="custom-control-input" id="is_popular"
                                                   wire:model.live="is_popular">
                                            <label class="custom-control-label me-2 ms-6" for="is_popular">جزو محبوب‌ترین‌ها باشد</label>
                                        </div>
                                        <label for="popular_sort_order">اولویت محبوب‌ترین</label>
                                        <input type="number" class="form-control @error('popular_sort_order') is-invalid @enderror"
                                               id="popular_sort_order"
                                               wire:model.live="popular_sort_order" dir="ltr" placeholder="مثلاً 1"
                                               {{ $is_popular ? '' : 'disabled' }}>
                                        @error('popular_sort_order')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.class="bg-gray btn-loading disabled"
                                wire:click="updateOrCreate">
                            @if($isEdited)
                                ویرایش دوره
                            @else
                                ایجاد دوره
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ admin_asset('js/jalalidatepicker.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:init', function () {
            jalaliDatepicker.startWatch();
        });

        document.addEventListener('livewire:navigated', function () {
            jalaliDatepicker.startWatch();
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet"
          href="{{ admin_asset('css/jalalidatepicker.min.css') }}">

    <style>
        .select2-container {
            width: 100% !important;
        }

        .course-form-section {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
        }

        .course-price-preview {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #f8fafc;
        }

        .course-showcase-card {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
        }
    </style>
@endpush
