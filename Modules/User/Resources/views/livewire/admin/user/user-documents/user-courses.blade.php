<div>
    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h3 class="card-title">دوره های ورزشی کاربر</h3>
                    <button type="button" class="btn btn-success shadow" wire:click="openAssignCourseModal">
                        اختصاص دوره جدید
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border text-nowrap text-md-nowrap table-striped text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>نام دوره</th>
                                <th>نوع ثبت</th>
                                <th>اختصاص داده شده توسط</th>
                                <th>تراکنش</th>
                                <th>تاریخ شروع</th>
                                <th>تاریخ پایان</th>
                                <th>وضعیت</th>
                                <th>پیشرفت</th>
                                <th>جزئیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($courseUsers as $courseUser)
                                <tr>
                                    <td>{{ $courseUser->id }}</td>
                                    <td>{{ $courseUser->course?->title ?? '-' }}</td>
                                    <td>{{ $courseUser->paid_by?->getName() ?? '-' }}</td>
                                    <td>
                                        {{
                                            filled(trim((string) ($courseUser->assignedByAdmin?->full_name ?? '')))
                                                ? $courseUser->assignedByAdmin->full_name
                                                : ($courseUser->assignedByAdmin?->mobile ?? ($courseUser->assigned_by_admin_id ? ('کاربر #' . $courseUser->assigned_by_admin_id) : '-'))
                                        }}
                                    </td>
                                    <td dir="ltr">{{ $courseUser->transaction?->transaction_code ?? ('#' . ($courseUser->transaction_id ?? '-')) }}</td>
                                    <td>{{ $courseUser->start_at ? verta($courseUser->start_at)->format('Y/m/d') : '-' }}</td>
                                    <td>{{ $courseUser->end_at ? verta($courseUser->end_at)->format('Y/m/d') : 'بدون محدودیت' }}</td>
                                    <td>
                                        <span class="badge {{ $courseUser->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $courseUser->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $courseUser->progress_count }} / {{ $courseUser->course?->lessons_count ?? 0 }}</div>
                                        <span class="badge bg-info">{{ $courseUser->progress_percent }}٪ تکمیل</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.course.purchased.show', $courseUser) }}"
                                           class="btn btn-sm btn-primary">
                                            مشاهده
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                                            <span class="alert-inner--text">دوره‌ای برای این کاربر ثبت نشده است.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="assignUserCourseModal" tabindex="-1" aria-labelledby="assignUserCourseModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignUserCourseModalLabel">اختصاص دوره به کاربر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="selectedCourseId" class="form-label">انتخاب دوره</label>
                        <select id="selectedCourseId" class="form-control @error('selectedCourseId') is-invalid @enderror"
                                wire:model.defer="selectedCourseId">
                            <option value="">انتخاب کنید...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->title }} | {{ number_format($course->final_price) }} تومان
                                    @if($course->access_days)
                                        | {{ $course->access_days }} روز
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('selectedCourseId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" wire:click="assignCourse" wire:loading.attr="disabled">
                        ثبت دوره
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', function () {
            const courseModalEl = document.getElementById('assignUserCourseModal');
            if (!courseModalEl) {
                return;
            }

            const courseModal = new bootstrap.Modal(courseModalEl, {
                keyboard: false
            });

            Livewire.on('show-user-course-modal', () => {
                courseModal.show();
            });

            Livewire.on('hide-user-course-modal', () => {
                courseModal.hide();
            });
        });
    </script>
@endpush
