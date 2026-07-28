<div>
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-title">نظرات دوره</h1>
            <p class="text-muted mb-0">{{ $course->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.course.content', $course) }}" class="btn btn-outline-primary">مدیریت بخش‌ها</a>
            <a href="{{ route('admin.course.index') }}" class="btn btn-outline-secondary">بازگشت به لیست</a>
        </div>
    </div>

    @include('admin::layouts.components.alert')

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">مدیریت نظرات کاربران</h3>
            <div class="d-flex gap-2">
                <span class="badge bg-warning text-dark">جدید {{ $newCommentsCount }}</span>
                <span class="badge bg-success">تایید شده {{ $approvedCommentsCount }}</span>
            </div>
        </div>
        <div class="card-body">
            @if($course->comments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead>
                        <tr>
                            <th>کاربر</th>
                            <th>نظر</th>
                            <th>وضعیت</th>
                            <th>پاسخ مدیر</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($course->comments as $comment)
                            <tr class="{{ $comment->is_approved ? '' : 'table-warning' }}">
                                <td>
                                    <div class="fw-bold">{{ $comment->user?->name ?? $comment->author_name ?? 'کاربر ناشناس' }}</div>
                                    <small class="text-muted">{{ $comment->created_at?->format('Y/m/d H:i') }}</small>
                                </td>
                                <td>
                                    @if(! $comment->is_approved)
                                        <span class="badge bg-warning text-dark mb-2">نظر جدید</span>
                                    @endif
                                    <div class="course-text-preline">{{ $comment->body }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $comment->is_approved ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $comment->is_approved ? 'تایید شده' : 'در انتظار تایید' }}
                                    </span>
                                </td>
                                <td>
                                    @if(filled($comment->admin_reply))
                                        <div class="course-text-preline">{{ $comment->admin_reply }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="editComment({{ $comment->id }})">پاسخ / ویرایش</button>
                                        <button type="button" class="btn btn-sm btn-outline-success" wire:click="approveComment({{ $comment->id }})">
                                            {{ $comment->is_approved ? 'لغو تایید' : 'تایید' }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteComment({{ $comment->id }})"
                                                onclick="confirm('این نظر حذف شود؟') || event.stopImmediatePropagation()">
                                            حذف
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @if($editingCommentId === $comment->id)
                                <tr>
                                    <td colspan="5" style="background: #f8fafc;">
                                        <form wire:submit.prevent="saveCommentModeration">
                                            <div class="mb-3">
                                                <label for="comment_reply_{{ $comment->id }}">پاسخ مدیر</label>
                                                <textarea id="comment_reply_{{ $comment->id }}" rows="4"
                                                          class="form-control @error('commentForm.admin_reply') is-invalid @enderror"
                                                          wire:model.defer="commentForm.admin_reply"></textarea>
                                                @error('commentForm.admin_reply')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="mb-3">
                                                <div class="custom-control custom-switch pr-4">
                                                    <input type="checkbox" class="custom-control-input" id="comment_is_approved_{{ $comment->id }}"
                                                           wire:model="commentForm.is_approved">
                                                    <label class="custom-control-label me-2 ms-6" for="comment_is_approved_{{ $comment->id }}">نظر تایید شود</label>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary">ذخیره</button>
                                                <button type="button" class="btn btn-outline-secondary" wire:click="cancelCommentEdit">انصراف</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light mb-0">هنوز نظری ثبت نشده است.</div>
            @endif
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
