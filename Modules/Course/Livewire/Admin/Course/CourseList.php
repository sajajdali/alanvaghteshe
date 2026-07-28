<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Course\app\Models\Course;

#[Title('مدیریت دوره ها')]
class CourseList extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url]
    public $search = [];

    public $searchPanel = '';

    #[On('delete')]
    public function delete(Course $model)
    {
        $this->authorize('delete', $model);

        try {
            $model->delete();
        } catch (\Exception $e) {
        }

        return redirect()->route('admin.course.index')->with('success', 'دوره با موفقیت حذف شد.');
    }

    public function startSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $courses = Course::query()
            ->with('category.parent')
            ->withCount('sections')
            ->withCount('faqs')
            ->withCount('comments')
            ->withCount([
                'purchases as active_purchases_count' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where(function ($subQuery) {
                        $subQuery->whereNull('end_at')
                            ->orWhereDate('end_at', '>=', now()->toDateString());
                    }),
                'comments as new_comments_count' => fn ($query) => $query->where('is_approved', false),
                'comments as approved_comments_count' => fn ($query) => $query->where('is_approved', true),
            ])
            ->when(isset($this->search['id']) && (int) $this->search['id'] !== 0, function ($query) {
                return $query->where('id', $this->search['id']);
            })
            ->when(isset($this->search['title']) && ! empty($this->search['title']), function ($query) {
                return $query->where('title', 'LIKE', "%{$this->search['title']}%");
            })
            ->when(isset($this->search['slug']) && ! empty($this->search['slug']), function ($query) {
                return $query->where('slug', 'LIKE', "%{$this->search['slug']}%");
            })
            ->when(isset($this->search['category']) && ! empty($this->search['category']), function ($query) {
                return $query->whereHas('category', function ($subQuery) {
                    $subQuery->where('title', 'LIKE', '%' . $this->search['category'] . '%');
                });
            })
            ->when(isset($this->search['level']) && $this->search['level'] !== '', function ($query) {
                return $query->where('level', (int) $this->search['level']);
            })
            ->when(isset($this->search['is_latest']) && $this->search['is_latest'] !== '', function ($query) {
                return $query->where('is_latest', (int) $this->search['is_latest']);
            })
            ->when(isset($this->search['is_popular']) && $this->search['is_popular'] !== '', function ($query) {
                return $query->where('is_popular', (int) $this->search['is_popular']);
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20);

        return view('course::livewire.admin.course.course-list', compact('courses'));
    }
}
