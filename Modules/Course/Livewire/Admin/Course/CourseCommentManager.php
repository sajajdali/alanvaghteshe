<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Course\app\Models\Course;

class CourseCommentManager extends Component
{
    use AuthorizesRequests;

    public Course $course;
    public ?int $editingCommentId = null;

    public array $commentForm = [
        'admin_reply' => '',
        'is_approved' => false,
    ];

    public function mount(Course $course): void
    {
        $this->authorize('update', $course);
        $this->course = $course;
    }

    public function editComment(int $commentId): void
    {
        $comment = $this->course->comments()->findOrFail($commentId);

        $this->editingCommentId = $comment->id;
        $this->commentForm = [
            'admin_reply' => $comment->admin_reply ?? '',
            'is_approved' => (bool) $comment->is_approved,
        ];
    }

    public function saveCommentModeration(): void
    {
        $validated = $this->validate($this->commentRules(), [], $this->commentAttributes())['commentForm'];

        $comment = $this->course->comments()->findOrFail($this->editingCommentId);
        $comment->update($validated);

        $this->resetCommentForm();
        session()->flash('success', 'نظر کاربر بروزرسانی شد.');
    }

    public function approveComment(int $commentId): void
    {
        $comment = $this->course->comments()->findOrFail($commentId);
        $newStatus = ! $comment->is_approved;
        $comment->update(['is_approved' => $newStatus]);

        if ($this->editingCommentId === $commentId) {
            $this->commentForm['is_approved'] = $newStatus;
        }

        session()->flash('success', $newStatus ? 'نظر کاربر تایید شد.' : 'تایید نظر لغو شد.');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = $this->course->comments()->findOrFail($commentId);
        $comment->delete();

        if ($this->editingCommentId === $commentId) {
            $this->resetCommentForm();
        }

        session()->flash('success', 'نظر کاربر حذف شد.');
    }

    public function cancelCommentEdit(): void
    {
        $this->resetCommentForm();
    }

    private function resetCommentForm(): void
    {
        $this->editingCommentId = null;
        $this->commentForm = [
            'admin_reply' => '',
            'is_approved' => false,
        ];
        $this->resetErrorBag();
    }

    private function commentRules(): array
    {
        return [
            'commentForm.admin_reply' => ['nullable', 'string'],
            'commentForm.is_approved' => ['required', 'boolean'],
        ];
    }

    private function commentAttributes(): array
    {
        return [
            'commentForm.admin_reply' => 'پاسخ مدیر',
            'commentForm.is_approved' => 'وضعیت تایید',
        ];
    }

    public function render()
    {
        $course = $this->course->fresh()->load('comments.user');
        $newCommentsCount = $course->comments->where('is_approved', false)->count();
        $approvedCommentsCount = $course->comments->where('is_approved', true)->count();

        return view('course::livewire.admin.course.course-comment-manager', compact('course', 'newCommentsCount', 'approvedCommentsCount'))
            ->title('مدیریت نظرات دوره');
    }
}
