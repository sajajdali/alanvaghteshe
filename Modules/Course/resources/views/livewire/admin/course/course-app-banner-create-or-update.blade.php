<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">{{ $isEdited ? 'ویرایش بنر اپلیکیشن' : 'افزودن بنر اپلیکیشن' }}</h1>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">{{ $isEdited ? 'ویرایش بنر' : 'ثبت بنر جدید' }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="title">نام بنر</label>
                                <input type="text" id="title" class="form-control @error('title') is-invalid @enderror" wire:model.live="title">
                                @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 mb-3">
                                <label for="course_id">دوره مقصد</label>
                                <select id="course_id" class="form-control @error('course_id') is-invalid @enderror" wire:model.live="course_id">
                                    <option value="">انتخاب کنید...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3">
                                <label for="position">جایگاه</label>
                                <select id="position" class="form-control @error('position') is-invalid @enderror" wire:model.live="position">
                                    @foreach($positions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3">
                                <label for="sort_order">ترتیب</label>
                                <input type="number" id="sort_order" class="form-control @error('sort_order') is-invalid @enderror" wire:model.live="sort_order" dir="ltr">
                                @error('sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 mb-3">
                                <label for="priority">اولویت</label>
                                <input type="number" id="priority" class="form-control @error('priority') is-invalid @enderror" wire:model.live="priority" dir="ltr">
                                @error('priority')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <x-admin.core.form.image-upload
                                    label="تصویر بنر"
                                    uploadedPhotoUrl="{{ $uploadedPhotoUrl }}"
                                    uploadedFileName="{{ $uploadedFileName }}"
                                    uploadedFileType="{{ $uploadedFileType }}"
                                    deleteAction="deletePhoto"
                                    model="photo"
                                    id="courseBannerPhoto" />
                                @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 mb-3 d-flex align-items-center">
                                <div class="custom-control custom-switch mt-2 pr-4">
                                    <input type="checkbox" class="custom-control-input" id="is_active" wire:model.live="is_active">
                                    <label class="custom-control-label me-2 ms-6" for="is_active">بنر فعال باشد</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:click="updateOrCreate">
                            {{ $isEdited ? 'ویرایش بنر' : 'ایجاد بنر' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
