<div>
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">{{ $isGeneralPage ? 'سوالات متداول دوره‌ها' : 'سوالات متداول دوره' }}</h1>
            <p class="text-muted mb-0">{{ $isGeneralPage ? 'FAQهای عمومی دوره‌ها و FAQهای متصل به هر دوره' : $course->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.course.index') }}" class="btn btn-outline-secondary">بازگشت به لیست</a>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-xl-4 col-lg-5">
            <div class="card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">{{ $editingFaqId ? 'ویرایش سوال متداول' : 'افزودن سوال متداول' }}</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveFaq">
                        @if($isGeneralPage)
                            <div class="mb-3">
                                <label for="faq_course_id">اتصال به دوره</label>
                                <select id="faq_course_id" class="form-control @error('faqForm.course_id') is-invalid @enderror"
                                        wire:model.live="faqForm.course_id">
                                    <option value="">بدون دوره - FAQ عمومی دوره‌ها</option>
                                    @foreach($courses as $selectCourse)
                                        <option value="{{ $selectCourse->id }}">{{ $selectCourse->title }}</option>
                                    @endforeach
                                </select>
                                @error('faqForm.course_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            <label for="faq_question">سوال</label>
                            <input id="faq_question" type="text" class="form-control @error('faqForm.question') is-invalid @enderror"
                                   wire:model.defer="faqForm.question">
                            @error('faqForm.question')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="faq_answer">پاسخ</label>
                            <textarea id="faq_answer" rows="5" class="form-control @error('faqForm.answer') is-invalid @enderror"
                                      wire:model.defer="faqForm.answer"></textarea>
                            @error('faqForm.answer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="faq_sort_order">ترتیب</label>
                                <input id="faq_sort_order" type="number" min="1" class="form-control @error('faqForm.sort_order') is-invalid @enderror"
                                       wire:model.defer="faqForm.sort_order">
                                @error('faqForm.sort_order')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 mb-3 d-flex align-items-center">
                                <div class="custom-control custom-switch mt-4 pr-4">
                                    <input type="checkbox" class="custom-control-input" id="faq_is_active" wire:model="faqForm.is_active">
                                    <label class="custom-control-label me-2 ms-6" for="faq_is_active">فعال باشد</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">ذخیره</button>
                            @if($editingFaqId)
                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelFaqEdit">انصراف</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">لیست سوالات</h3>
                    <span class="badge bg-info">{{ $faqs->count() }} سوال</span>
                </div>
                <div class="card-body">
                    @if($isGeneralPage)
                        <div class="row row-sm mb-4">
                            <div class="col-md-4">
                                <label for="filter_scope">نوع نمایش</label>
                                <select id="filter_scope" class="form-control" wire:model.live="filterScope">
                                    <option value="all">همه سوالات</option>
                                    <option value="general">فقط سوالات بدون دوره</option>
                                    <option value="assigned">فقط سوالات متصل به دوره</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter_course">فیلتر دوره</label>
                                <select id="filter_course" class="form-control" wire:model.live="filterCourseId">
                                    <option value="">همه دوره‌ها</option>
                                    @foreach($courses as $selectCourse)
                                        <option value="{{ $selectCourse->id }}">{{ $selectCourse->title }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">برای سوالات عمومی این فیلتر خالی بماند.</small>
                            </div>
                            <div class="col-md-4">
                                <label for="filter_status">وضعیت</label>
                                <select id="filter_status" class="form-control" wire:model.live="filterStatus">
                                    <option value="all">همه</option>
                                    <option value="active">فعال</option>
                                    <option value="inactive">غیرفعال</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    @if($faqs->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead>
                                <tr>
                                    @if($isGeneralPage)
                                        <th>دوره</th>
                                    @endif
                                    <th>ترتیب</th>
                                    <th>سوال</th>
                                    <th>پاسخ</th>
                                    <th>وضعیت</th>
                                    <th>عملیات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($faqs as $faq)
                                    <tr>
                                        @if($isGeneralPage)
                                            <td>
                                                @if($faq->course)
                                                    <a href="{{ route('admin.course.faqs', $faq->course) }}" class="badge bg-primary text-white">
                                                        {{ $faq->course->title }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-light text-dark">عمومی</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            <span class="badge bg-light text-dark">ترتیب {{ $faq->sort_order }}</span>
                                        </td>
                                        <td>{{ $faq->question }}</td>
                                        <td><div class="course-text-preline">{{ $faq->answer }}</div></td>
                                        <td>
                                            <span class="badge {{ $faq->is_active ? 'bg-success' : 'bg-danger' }}">{{ $faq->is_active ? 'فعال' : 'غیرفعال' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editFaq({{ $faq->id }})">ویرایش</button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        wire:click="deleteFaq({{ $faq->id }})"
                                                        onclick="confirm('این سوال متداول حذف شود؟') || event.stopImmediatePropagation()">
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
                        <div class="alert alert-light mb-0">هنوز سوالی ثبت نشده است.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .course-text-preline {
            white-space: pre-line;
        }
    </style>
@endpush
