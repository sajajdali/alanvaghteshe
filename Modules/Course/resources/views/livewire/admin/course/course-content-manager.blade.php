<div>
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">مدیریت محتوای دوره</h1>
            <p class="text-muted mb-0">{{ $course->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.course.edit', $course) }}" class="btn btn-outline-primary">ویرایش اطلاعات دوره</a>
            <a href="{{ route('admin.course.index') }}" class="btn btn-outline-secondary">بازگشت به لیست دوره‌ها</a>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">
                        {{ $editingSectionId ? 'ویرایش بخش' : 'افزودن بخش جدید' }}
                    </h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveSection">
                        <div class="mb-3">
                            <label for="section_title">عنوان بخش</label>
                            <input id="section_title" type="text" class="form-control @error('sectionForm.title') is-invalid @enderror"
                                   wire:model.defer="sectionForm.title" placeholder="مثلاً مقدمات دوره">
                            @error('sectionForm.title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="section_description">توضیحات بخش</label>
                            <textarea id="section_description" rows="4"
                                      class="form-control @error('sectionForm.description') is-invalid @enderror"
                                      wire:model.defer="sectionForm.description"
                                      placeholder="توضیح کوتاه برای این بخش"></textarea>
                            @error('sectionForm.description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="section_sort_order">ترتیب نمایش</label>
                                <input id="section_sort_order" type="number"
                                       class="form-control @error('sectionForm.sort_order') is-invalid @enderror"
                                       wire:model.defer="sectionForm.sort_order" min="1">
                                @error('sectionForm.sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-6 mb-3 d-flex align-items-center">
                                <div class="custom-control custom-switch mt-4 pr-4">
                                    <input type="checkbox" class="custom-control-input" id="section_is_active"
                                           wire:model="sectionForm.is_active">
                                    <label class="custom-control-label me-2 ms-6" for="section_is_active">بخش فعال باشد</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">ذخیره بخش</button>
                            @if($editingSectionId)
                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelSectionEdit">انصراف</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            @forelse($course->sections as $section)
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-start">
                        <div>
                            <h3 class="card-title mb-1">{{ $section->title }}</h3>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">ترتیب {{ $section->sort_order }}</span>
                                <span class="badge {{ $section->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $section->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                                <span class="badge bg-light text-dark">{{ $section->lessons->count() }} زیر‌بخش</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editSection({{ $section->id }})">ویرایش بخش</button>
                            <button type="button" class="btn btn-sm btn-primary" wire:click="startCreateLesson({{ $section->id }})">افزودن زیر‌بخش</button>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    wire:click="deleteSection({{ $section->id }})"
                                    onclick="confirm('این بخش و تمام زیر‌بخش‌ها و فایل‌های وابسته‌اش حذف می‌شوند. ادامه می‌دهید؟') || event.stopImmediatePropagation()">
                                حذف بخش
                            </button>
                        </div>
                    </div>

                    @if($section->description)
                        <div class="card-body border-bottom pb-0">
                            <p class="text-muted course-text-preline">{{ $section->description }}</p>
                        </div>
                    @endif

                    <div class="card-body">
                        @if($lessonSectionId === $section->id)
                            <div class="course-content-box course-content-box-muted mb-4">
                                <h5 class="mb-3">{{ $editingLessonId ? 'ویرایش زیر‌بخش' : 'افزودن زیر‌بخش جدید' }}</h5>

                                <form wire:submit.prevent="saveLesson">
                                    <input type="hidden" wire:model="lessonForm.course_section_id">

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="lesson_title_{{ $section->id }}">عنوان زیر‌بخش</label>
                                            <input id="lesson_title_{{ $section->id }}" type="text"
                                                   class="form-control @error('lessonForm.title') is-invalid @enderror"
                                                   wire:model.defer="lessonForm.title"
                                                   placeholder="مثلاً معرفی فصل اول">
                                            @error('lessonForm.title')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="lesson_type_{{ $section->id }}">نوع محتوا</label>
                                            <select id="lesson_type_{{ $section->id }}"
                                                    class="form-control @error('lessonForm.content_type') is-invalid @enderror"
                                                    wire:model.live="lessonForm.content_type">
                                                <option value="video">ویدئو</option>
                                                <option value="document">داکیومنت</option>
                                                <option value="text">فقط توضیحات</option>
                                            </select>
                                            @error('lessonForm.content_type')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="lesson_description_{{ $section->id }}">توضیحات</label>
                                        <textarea id="lesson_description_{{ $section->id }}" rows="4"
                                                  class="form-control @error('lessonForm.description') is-invalid @enderror"
                                                  wire:model.defer="lessonForm.description"
                                                  placeholder="اگر نوع محتوا فقط توضیحات باشد، همین متن نمایش داده می‌شود"></textarea>
                                        @error('lessonForm.description')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if($lessonForm['content_type'] === 'video')
                                        <div class="mb-3">
                                            <label for="lesson_video_upload_{{ $section->id }}">ویدئوی زیر‌بخش</label>

                                            <div class="course-content-box"
                                                 x-data="{ isUploading: false, progress: 0 }"
                                                 x-on:livewire-upload-start="isUploading = true; progress = 0"
                                                 x-on:livewire-upload-finish="isUploading = false; progress = 100"
                                                 x-on:livewire-upload-error="isUploading = false"
                                                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                                                @if($uploadedLessonVideoUrl)
                                                    <div class="mb-3">
                                                        <video controls preload="metadata" style="width: 100%; max-height: 280px; border-radius: 10px; background: #000;">
                                                            <source src="{{ $uploadedLessonVideoUrl }}">
                                                            مرورگر شما پخش ویدئو را پشتیبانی نمی‌کند.
                                                        </video>
                                                        <div class="d-flex align-items-center justify-content-between mt-2">
                                                            <small class="text-muted">{{ $uploadedLessonVideoName }}</small>
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    wire:click="deleteLessonVideo"
                                                                    onclick="confirm('ویدئو از این زیر‌بخش و از سرور حذف شود؟') || event.stopImmediatePropagation()">
                                                                حذف ویدئو
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif

                                                <input id="lesson_video_upload_{{ $section->id }}"
                                                       type="file"
                                                       class="form-control @error('lessonVideoFile') is-invalid @enderror"
                                                       wire:model="lessonVideoFile"
                                                       accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">

                                                <div x-show="isUploading" x-cloak class="mt-3">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span>در حال آپلود ویدئو...</span>
                                                        <span x-text="progress + '%'"></span>
                                                    </div>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                             role="progressbar"
                                                             x-bind:style="'width:' + progress + '%'"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            @error('lessonVideoFile')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('lessonForm.video_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="lesson_duration_{{ $section->id }}">مدت ویدئو (ثانیه)</label>
                                                <input id="lesson_duration_{{ $section->id }}" type="number"
                                                       class="form-control @error('lessonForm.duration_seconds') is-invalid @enderror"
                                                       wire:model.defer="lessonForm.duration_seconds" min="0">
                                                @error('lessonForm.duration_seconds')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif

                                    @if($lessonForm['content_type'] === 'document')
                                        <div class="mb-3">
                                            <label for="lesson_document_upload_{{ $section->id }}">داکیومنت زیر‌بخش</label>
                                            <div class="course-content-box"
                                                 x-data="{ isUploading: false, progress: 0 }"
                                                 x-on:livewire-upload-start="isUploading = true; progress = 0"
                                                 x-on:livewire-upload-finish="isUploading = false; progress = 100"
                                                 x-on:livewire-upload-error="isUploading = false"
                                                 x-on:livewire-upload-progress="progress = $event.detail.progress">
                                                @if($uploadedLessonDocumentName)
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div>
                                                            <div class="fw-bold">{{ $uploadedLessonDocumentName }}</div>
                                                            @if($uploadedLessonDocumentUrl)
                                                                <a href="{{ $uploadedLessonDocumentUrl }}" target="_blank" class="small text-primary">مشاهده فایل</a>
                                                            @endif
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                wire:click="deleteLessonDocument"
                                                                onclick="confirm('داکیومنت از این زیر‌بخش و از سرور حذف شود؟') || event.stopImmediatePropagation()">
                                                            حذف فایل
                                                        </button>
                                                    </div>
                                                @endif

                                                <input id="lesson_document_upload_{{ $section->id }}" type="file"
                                                       class="form-control @error('lessonDocumentFile') is-invalid @enderror"
                                                       wire:model="lessonDocumentFile">

                                                <div x-show="isUploading" x-cloak class="mt-3">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span>در حال آپلود داکیومنت...</span>
                                                        <span x-text="progress + '%'"></span>
                                                    </div>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                             role="progressbar"
                                                             x-bind:style="'width:' + progress + '%'"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            @error('lessonDocumentFile')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                            @error('lessonForm.document_url')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="lesson_sort_{{ $section->id }}">ترتیب نمایش</label>
                                            <input id="lesson_sort_{{ $section->id }}" type="number"
                                                   class="form-control @error('lessonForm.sort_order') is-invalid @enderror"
                                                   wire:model.defer="lessonForm.sort_order" min="1">
                                            @error('lessonForm.sort_order')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3 d-flex align-items-center">
                                            <div class="custom-control custom-switch mt-4 pr-4">
                                                <input type="checkbox" class="custom-control-input" id="lesson_is_active_{{ $section->id }}"
                                                       wire:model="lessonForm.is_active">
                                                <label class="custom-control-label me-2 ms-6" for="lesson_is_active_{{ $section->id }}">زیر‌بخش فعال باشد</label>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-3 d-flex align-items-center">
                                            <div class="custom-control custom-switch mt-4 pr-4">
                                                <input type="checkbox" class="custom-control-input" id="lesson_is_preview_{{ $section->id }}"
                                                       wire:model="lessonForm.is_preview"
                                                       @disabled($lessonForm['content_type'] !== 'video')>
                                                <label class="custom-control-label me-2 ms-6" for="lesson_is_preview_{{ $section->id }}">پیش‌نمایش رایگان</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">ذخیره زیر‌بخش</button>
                                        <button type="button" class="btn btn-outline-secondary" wire:click="cancelLessonEdit">انصراف</button>
                                    </div>
                                </form>
                            </div>
                        @endif

                        @if($section->lessons->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th>عنوان</th>
                                        <th>نوع محتوا</th>
                                        <th>منبع</th>
                                        <th>مدت</th>
                                        <th>وضعیت</th>
                                        <th>عملیات</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($section->lessons as $lesson)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $lesson->title }}</div>
                                                @if($lesson->description)
                                                    <small class="text-muted course-text-preline">{{ $lesson->description }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $contentTypeLabels[$lesson->content_type ?? 'video'] ?? 'ویدئو' }}</td>
                                            <td>
                                                @if(($lesson->content_type ?? 'video') === 'video')
                                                    <span class="badge bg-success">ویدئو آپلود شده</span>
                                                    @if($lesson->video_url)
                                                        <a href="{{ $lesson->video_url }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">نمایش</a>
                                                    @endif
                                                @elseif(($lesson->content_type ?? 'video') === 'document')
                                                    <span class="badge bg-info">داکیومنت ثبت شده</span>
                                                    @if($lesson->document_url)
                                                        <a href="{{ $lesson->document_url }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">نمایش</a>
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-dark">فقط توضیحات</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(($lesson->content_type ?? 'video') === 'video')
                                                    {{ $lesson->duration_seconds }} ثانیه
                                                    @if($lesson->is_preview)
                                                        <span class="badge bg-info">پیش‌نمایش</span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">ترتیب {{ $lesson->sort_order }}</span>
                                                <span class="badge {{ $lesson->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $lesson->is_active ? 'فعال' : 'غیرفعال' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editLesson({{ $lesson->id }})">ویرایش</button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            wire:click="deleteLesson({{ $lesson->id }})"
                                                            onclick="confirm('این زیر‌بخش و فایل‌های وابسته‌اش حذف شوند؟') || event.stopImmediatePropagation()">
                                                        حذف
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-light mb-0">برای این بخش هنوز زیر‌بخشی ثبت نشده است.</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    هنوز هیچ بخشی برای این دوره ثبت نشده است. ابتدا از ستون سمت راست یک بخش اضافه کن.
                </div>
            @endforelse
        </div>
    </div>

</div>

@push('styles')
    <style>
        .course-content-box {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
        }

        .course-content-box-muted {
            background: #f8fafc;
        }

        .course-text-preline {
            white-space: pre-line;
        }
    </style>
@endpush
