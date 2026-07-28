<?php

namespace Modules\Course\Livewire\Admin\Course;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Modules\Course\app\Models\Course;
use Modules\Course\app\Models\CourseFaq;

class CourseFaqManager extends Component
{
    use AuthorizesRequests;

    public ?Course $course = null;
    public ?int $editingFaqId = null;
    public string $filterCourseId = '';
    public string $filterScope = 'all';
    public string $filterStatus = 'all';

    public array $faqForm = [
        'course_id' => '',
        'question' => '',
        'answer' => '',
        'sort_order' => 1,
        'is_active' => true,
    ];

    public function mount($course = null): void
    {
        if ($course !== null && ! $course instanceof Course) {
            $course = Course::query()->findOrFail((int) $course);
        }

        if ($course) {
            $this->authorize('update', $course);
        } else {
            $this->authorize('viewAny', Course::class);
        }

        $this->course = $course;
        $this->resetFaqForm();
    }

    public function saveFaq(): void
    {
        $validated = $this->validate($this->faqRules(), [], $this->faqAttributes())['faqForm'];
        $isEditing = (bool) $this->editingFaqId;

        $selectedCourseId = $this->course?->id ?? (filled($validated['course_id']) ? (int) $validated['course_id'] : null);

        $faq = $this->editingFaqId ? $this->faqQuery()->findOrFail($this->editingFaqId) : new CourseFaq();

        $faq->fill($validated);
        $faq->course_id = $selectedCourseId;
        $faq->save();

        $this->resetFaqForm();
        session()->flash('success', $isEditing ? 'سوال متداول ویرایش شد.' : 'سوال متداول جدید اضافه شد.');
    }

    public function editFaq(int $faqId): void
    {
        $faq = $this->faqQuery()->findOrFail($faqId);

        $this->editingFaqId = $faq->id;
        $this->faqForm = [
            'course_id' => $faq->course_id ? (string) $faq->course_id : '',
            'question' => $faq->question,
            'answer' => $faq->answer ?? '',
            'sort_order' => $faq->sort_order,
            'is_active' => (bool) $faq->is_active,
        ];
    }

    public function deleteFaq(int $faqId): void
    {
        $faq = $this->faqQuery()->findOrFail($faqId);
        $faq->delete();

        if ($this->editingFaqId === $faqId) {
            $this->resetFaqForm();
        }

        session()->flash('success', 'سوال متداول حذف شد.');
    }

    public function cancelFaqEdit(): void
    {
        $this->resetFaqForm();
    }

    private function resetFaqForm(): void
    {
        $this->editingFaqId = null;
        $this->faqForm = [
            'course_id' => $this->course?->id ? (string) $this->course->id : '',
            'question' => '',
            'answer' => '',
            'sort_order' => (int) $this->faqQuery()->max('sort_order') + 1,
            'is_active' => true,
        ];
        $this->resetErrorBag();
    }

    private function faqRules(): array
    {
        return [
            'faqForm.course_id' => ['nullable', 'exists:courses,id'],
            'faqForm.question' => ['required', 'string', 'max:255'],
            'faqForm.answer' => ['nullable', 'string'],
            'faqForm.sort_order' => ['required', 'integer', 'min:1'],
            'faqForm.is_active' => ['required', 'boolean'],
        ];
    }

    private function faqAttributes(): array
    {
        return [
            'faqForm.course_id' => 'دوره',
            'faqForm.question' => 'سوال',
            'faqForm.answer' => 'پاسخ',
            'faqForm.sort_order' => 'ترتیب',
            'faqForm.is_active' => 'وضعیت',
        ];
    }

    public function render()
    {
        $course = $this->course?->fresh();
        $faqs = $this->faqQuery()
            ->with('course')
            ->when($this->course === null && $this->filterCourseId !== '', fn ($query) => $query->where('course_id', (int) $this->filterCourseId))
            ->when($this->course === null && $this->filterScope === 'general', fn ($query) => $query->whereNull('course_id'))
            ->when($this->course === null && $this->filterScope === 'assigned', fn ($query) => $query->whereNotNull('course_id'))
            ->when($this->filterStatus === 'active', fn ($query) => $query->where('is_active', 1))
            ->when($this->filterStatus === 'inactive', fn ($query) => $query->where('is_active', 0))
            ->orderBy('sort_order')
            ->get();

        return view('course::livewire.admin.course.course-faq-manager', [
            'course' => $course,
            'faqs' => $faqs,
            'courses' => Course::query()->orderBy('title')->get(['id', 'title']),
            'isGeneralPage' => $this->course === null,
        ])->title($this->course ? 'مدیریت سوالات متداول دوره' : 'سوالات متداول دوره‌ها');
    }

    private function faqQuery()
    {
        return CourseFaq::query()
            ->when($this->course, fn ($query) => $query->where('course_id', $this->course->id));
    }
}
