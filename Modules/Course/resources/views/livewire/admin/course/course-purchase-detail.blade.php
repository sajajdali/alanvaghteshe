<div>
    <div class="page-header">
        <div>
            <h1 class="page-title">جزئیات مشاهده دوره</h1>
            <div class="text-muted mt-1">
                {{ $purchase->course?->title ?? '-' }} | {{ $purchase->user?->full_name ?: ($purchase->user?->mobile ?? '-') }}
            </div>
        </div>
        <div class="ms-auto pageheader-btn">
            <a href="{{ route('admin.course.purchased', ['search' => ['course_id' => $purchase->course_id]]) }}" class="btn btn-outline-primary">
                بازگشت به خریدها
            </a>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="row row-sm">
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <div class="fs-12 text-muted mb-2">کاربر</div>
                    <div class="fw-bold">{{ $purchase->user?->full_name ?: '-' }}</div>
                    <div class="text-muted mt-1">{{ $purchase->user?->mobile ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <div class="fs-12 text-muted mb-2">پیشرفت</div>
                    <div class="fw-bold">{{ $seenLessonsCount }} / {{ $totalLessonsCount }}</div>
                    <span class="badge bg-info mt-2">{{ $purchase->progress_percent }}٪ تکمیل</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <div class="fs-12 text-muted mb-2">شروع دسترسی</div>
                    <div class="fw-bold">{{ $purchase->start_at ? verta($purchase->start_at)->format('Y/m/d') : '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="card custom-card">
                <div class="card-body text-center">
                    <div class="fs-12 text-muted mb-2">پایان دسترسی</div>
                    <div class="fw-bold">{{ $purchase->end_at ? verta($purchase->end_at)->format('Y/m/d') : 'بدون محدودیت' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-sm">
        <div class="col-lg-12">
            <div class="card custom-card">
                <div class="card-header border-bottom">
                    <h3 class="card-title">بخش‌ها و زیر‌بخش‌های دوره</h3>
                </div>
                <div class="card-body">
                    @forelse($sections as $item)
                        <div class="card border mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold">{{ $item['section']->title }}</div>
                                    @if($item['section']->description)
                                        <div class="text-muted small mt-1 course-text-preline">{{ $item['section']->description }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-primary">
                                    {{ $item['seen_count'] }} / {{ $item['total_count'] }} دیده شده
                                </span>
                            </div>
                            <div class="card-body">
                                @if($item['lessons']->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered text-center align-middle mb-0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>زیر‌بخش</th>
                                                <th>وضعیت مشاهده</th>
                                                <th>تاریخ مشاهده</th>
                                                <th>ساعت مشاهده</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($item['lessons'] as $lessonItem)
                                                <tr class="{{ $lessonItem['is_seen'] ? '' : 'opacity-50' }}">
                                                    <td>{{ $lessonItem['lesson']->sort_order }}</td>
                                                    <td class="text-end">
                                                        <div>{{ $lessonItem['lesson']->title }}</div>
                                                        @if($lessonItem['lesson']->description)
                                                            <div class="small text-muted mt-1 course-text-preline">{{ $lessonItem['lesson']->description }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($lessonItem['is_seen'])
                                                            <span class="badge bg-success">دیده شده</span>
                                                        @else
                                                            <span class="badge bg-light text-dark">دیده نشده</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $lessonItem['progress']?->completed_at ? verta($lessonItem['progress']->completed_at)->format('Y/m/d') : '-' }}
                                                    </td>
                                                    <td dir="ltr">
                                                        {{ $lessonItem['progress']?->completed_at ? $lessonItem['progress']->completed_at->format('H:i') : '-' }}
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
                        <div class="alert alert-info mb-0">برای این دوره هنوز بخشی ثبت نشده است.</div>
                    @endforelse
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
